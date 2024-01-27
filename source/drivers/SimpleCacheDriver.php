<?php
namespace fmihel\cache\drivers;

require_once __DIR__.'/../iCacheDriver.php';

use fmihel\cache\iCacheDriver;

class SimpleCacheDriver implements iCacheDriver{
    private $cache = [];

    public function get(string $key)
    {
        return $this->cache[$key];
    }
    public function set(string $key,$data){
        $this->cache[$key] = $data;
    }
    public function exists(string $key):bool{
        return isset($this->cache[$key]);
    }
    public function clear(string $key = ''){
        if ($key){
            if (isset($this->cache[$key])){
                unset($this->cache['$key']);
            };
        }else{
            $this->cache=[];
        }
    }
}