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
    private static $enable = true;
    private static $now = time();

    public static function get(string $key, array $params, $onCreate, $lifetime = 0)
    {

        if (self::$enable) {

            $skey = self::key($key, ...$params);
            if (self::exists($skey)) {
    
                $result = self::$driver->get($skey);
                
                if (self::isOld($result)){
                    self::$driver->clear($skey);
                    $result = false;
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

        } else {

            if (is_null($onCreate)) {
                throw new \Exception('onCreate is null');
            }
            $data = $onCreate(...$params);

        }

        return $data;

    }
    public static function clearOld()
    {
        self::$driver->each(function($key,$data){
            if (self::isOld($data)){
                self::$driver->clear($key);
            }
        });
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

    public static function enable($set = null): bool
    {

        if (gettype($set) === 'boolean') {
            self::$enable = $set;
        }
        return self::$enable;

    }

    private static function isOld($data):bool{
        if ($data[LIFETIME] > 0) {
            $srok = strtotime('' . $data[LIFETIME] . ' hour', $data[CURRENT]);
            return self::$now > $srok;
        }
        return false;
    }

}

Cache::setDriver(new SimpleCacheDriver());
