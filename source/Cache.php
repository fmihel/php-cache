<?php
namespace fmihel\cache;

require_once __DIR__ . '/iCacheDriver.php';
require_once __DIR__ . '/drivers/SimpleCacheDriver.php';

use fmihel\cache\drivers\SimpleCacheDriver;

class Cache
{
    private static $driver;

    public static function get(string $key, array $params, $onCreate = null)
    {
        $skey = self::key($key, ...$params);
        if (self::exists($skey)) {
            return self::$driver->get($skey);
        }

        if (is_null($onCreate)) {
            throw new \Exception('no cache  for "' . $skey . '"');
        }

        $data = $onCreate(...$params);
        self::$driver->set($skey, $data);

        return $data;

    }
    public static function clear()
    {
        self::$driver->clear();
    }

    public static function setDriver(iCacheDriver $driver)
    {
        self::$driver = $driver;
    }

    private static function exists($key): bool
    {
        return self::$driver->exists($key);
    }

    private static function key(...$keys): string
    {
        $out = '';

        foreach ($keys as $key) {
            $type = gettype($key);
            $out .= $out ? '-' : '';
            if ($type === 'string' || $type === 'integer' || $type === 'double') {
                $out .= "$key";
            } else {
                $out .= print_r($key, true);
            }
        }

        return md5($out);
    }

}

Cache::setDriver(new SimpleCacheDriver());
