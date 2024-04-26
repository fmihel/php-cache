<?php
namespace fmihel\cache;

require_once __DIR__ . '/iCacheDriver.php';
require_once __DIR__ . '/drivers/SimpleCacheDriver.php';

use fmihel\cache\drivers\SimpleCacheDriver;

const LIFETIME = 'l';
const DATA = 'd';
const CURRENT = 'c';

class Cache
{
    private static $driver;

    public static function get(string $key, array $params, $onCreate, $lifetime = 0)
    {
        $skey = self::key($key, ...$params);
        if (self::exists($skey)) {

            $result = self::$driver->get($skey);
            if ($result[LIFETIME] > 0) {
                $now = time();
                $srok = strtotime('' . $result[LIFETIME] . ' hour', $result[CURRENT]);
                if ($now > $srok) {
                    self::$driver->clear($skey);
                    $result = false;
                }
            }

            if ($result) {
                return $result[DATA];
            }

        }

        if (is_null($onCreate)) {
            throw new \Exception('not define cache method for "' . $key . ' ' . print_r($params, true) . '"');
        }

        $data = $onCreate(...$params);
        self::$driver->set($skey, [CURRENT => time(), LIFETIME => $lifetime, DATA => $data]);

        return $data;

    }
    public static function clearOld()
    {

    }
    public static function clear(string $key = '', array $params = [])
    {
        if ($key) {
            $skey = self::key($key, ...$params);
            self::$driver->clear($skey);
        } else {
            self::$driver->clear();
        }
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
