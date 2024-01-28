<?php
namespace fmihel\cache\drivers;

require_once __DIR__.'/SimpleCacheDriver.php';

use fmihel\lib\Dir;
use fmihel\lib\Arr;
use fmihel\console;

class SerCacheDriver extends SimpleCacheDriver{
    
    private $path = '/home/mike/work/fmihel/php-cache/cache';
    
    function __construct(string $path){
        $this->path = $path;

    }
    public function get(string $key)
    {
        if (parent::exists($key)){
            return parent::get($key);
        }else{
            $filename = Dir::join($this->path,$key.'.ser');
            $ser=file_get_contents($filename);
            $cache = unserialize($ser);
            parent::set($key,$cache);
            return $cache;
        }
        
    }
    public function set(string $key,$data){
        
        parent::set($key,$data);
        $filename = Dir::join($this->path,$key.'.ser');
        file_put_contents($filename,serialize($data));
    }


    public function exists(string $key):bool{
        $exists = parent::exists($key);
        if (!$exists){
            $exists = file_exists(Dir::join($this->path,$key.'.ser'));
        }
        return $exists;
        // return isset($this->cache[$key]);
    }

    public function clear(string $key = ''){

    }


}