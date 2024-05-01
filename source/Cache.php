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
    private $driver = null;
    private $enable = true;
    private $now = null;

    public function __construct(iCacheDriver $driver)
    {
        $this->now = time();
        $this->setDriver($driver);
    }

    public function get(string $key, array $params, $onCreate, $lifetime = 0)
    {

        if ($this->enable) {

            $skey = $this->key($key, ...$params);
            if ($this->exists($skey)) {
    
                $result = $this->driver->get($skey);
                
                if ($this->isOld($result)){
                    $this->driver->clear($skey);
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
            $this->driver->set($skey, [CURRENT => $this->now, LIFETIME => $lifetime, DATA => $data]);

        } else {

            if (is_null($onCreate)) {
                throw new \Exception('onCreate is null');
            }
            $data = $onCreate(...$params);

        }

        return $data;

    }
    public function clearOld()
    {
        $this->driver->each(function($key,$data){
            if ($this->isOld($data)){
                $this->driver->clear($key);
            }
        });
    }
    public function clear(string $key = '', array $params = [])
    {
        if ($key) {
            $skey = $this->key($key, ...$params);
            $this->driver->clear($skey);
        } else {
            $this->driver->clear();
        }
    }

    public function setDriver(iCacheDriver $driver)
    {
        $this->driver = $driver;
    }

    private function exists($key): bool
    {
        return $this->driver->exists($key);
    }

    private function key(...$keys): string
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

    public function enable($set = null): bool
    {

        if (gettype($set) === 'boolean') {
            $this->enable = $set;
        }
        return $this->enable;

    }

    private function isOld($data):bool{
        if ($data[LIFETIME] > 0) {
            $srok = strtotime('' . $data[LIFETIME] . ' hour', $data[CURRENT]);
            return $this->now > $srok;
        }
        return false;
    }

}

