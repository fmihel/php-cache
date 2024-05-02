<?php
namespace fmihel\cache\drivers;

require_once __DIR__ . '/../iCacheDriver.php';

use fmihel\cache\iCacheDriver;
use fmihel\lib\Arr;
use fmihel\lib\Dir;

class FileCacheDriver implements iCacheDriver
{

    private $cache = [];
    private $path = '';
    public function __construct(string $path = '')
    {
        $this->path = $path === '' ? Dir::pathinfo($_SERVER['SCRIPT_FILENAME'])['dirname'] . '/cache' : $path;
        if (!Dir::exist($this->path)) {
            Dir::mkdir($this->path);
        }

    }
    public function get(string $key)
    {

        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        } else {
            $filename = $this->filename($key);

            require_once $filename;

            $this->cache[$key] = $cache;
            return $cache;
        }

    }
    public function set(string $key, $data)
    {
        $this->cache[$key] = $data;
        $php = '<?php $cache=' . $this->asPhp($data) . ';';
        file_put_contents($this->filename($key), $php);
    }

    public function exists(string $key): bool
    {
        if (!($exists = isset($this->cache[$key]))) {
            $exists = file_exists($this->filename($key));
        }
        return $exists;
    }

    public function clear(string $key = '')
    {
        if ($key === '') {
            $this->cache = [];
            Dir::clear($this->path);
        } else {

            if (isset($this->cache[$key])) {
                unset($this->cache[$key]);
            }
            $filename = $this->filename($key);
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
    }

    public function asPhp($data): string
    {

        $type = gettype($data);

        if ($type === 'boolean') {
            return $data ? "true" : "false";
        };

        if ($type === 'string') {
            return "'$data'";
        };
        if ($type === 'double' || $type === 'integer') {
            return "$data";
        };
        if ($type === 'NULL') {
            return "NULL";
        };

        if ($type === 'array') {
            if (Arr::is_assoc($data)) {
                $out = '';
                foreach ($data as $name => $value) {
                    $out .= ($out ? ',' : '') . "'$name'=>" . $this->asPhp($value);
                }
                return '[' . $out . ']';
            } else {
                $out = '';
                for ($i = 0; $i < count($data); $i++) {
                    $out .= ($out ? ',' : '') . $this->asPhp($data[$i]);
                };
                return '[' . $out . ']';
            }
        };

        throw new \Exception('cnt cache type' . $type);

    }

    private function filename(string $key): string
    {
        return Dir::join($this->path, $key . '.php');
    }

    public function each($callback)
    {
        $list = Dir::files($this->path, ['php'], false, true);
        foreach ($list as $file) {
            $key = trim(str_replace('.php', '', $file));
            if ($callback($key, $this->get($key)) === false) {
                break;
            }
        }
    }

}
