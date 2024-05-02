<?php
namespace fmihel\cache\drivers;

require_once __DIR__ . '/../iCacheDriver.php';

use fmihel\cache\iCacheDriver;
use fmihel\lib\Dir;

class SerCacheDriver implements iCacheDriver
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

            $ser = file_get_contents($filename);
            $cache = unserialize($ser);

            $this->cache[$key] = $cache;
            return $cache;
        }

    }
    public function set(string $key, $data)
    {
        $this->cache[$key] = $data;
        file_put_contents($this->filename($key), serialize($data));
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

    private function filename(string $key): string
    {
        return Dir::join($this->path, $key . '.ser');
    }

    public function each($callback)
    {
        $list = Dir::files($this->path, ['ser'], false, true);
        foreach ($list as $file) {
            $key = trim(str_replace('.ser', '', $file));
            if ($callback($key, $this->get($key)) === false) {
                break;
            }
        }
    }

}
