<?php
namespace fmihel\cache\drivers;

require_once __DIR__.'/SimpleCacheDriver.php';

use fmihel\lib\Dir;
use fmihel\lib\Arr;

class FileCacheDriver extends SimpleCacheDriver{
    
    private $path = '/home/mike/work/fmihel/php-cache/cache';
    
    function __construct(string $path){
        $this->path = $path;

    }
    public function get(string $key)
    {
        if (parent::exists($key)){
            return parent::get($key);
        }else{
            $filename = Dir::join($this->path,$key.'.php');
            // $filename = $key.'.php';
            require_once $filename;
            //$data = $cache;
            parent::set($key,$cache);
            return $cache;
        }
        
    }
    public function set(string $key,$data){
        
        parent::set($key,$data);
        $php = '<?php $cache='.$this->asPhp($data).';';
        $filename = Dir::join($this->path,$key.'.php');
        // $filename = $key.'.php';

        file_put_contents($filename,$php);
    }


    public function exists(string $key):bool{
        $exists = parent::exists($key);
        if (!$exists){
            $exists = file_exists(Dir::join($this->path,$key.'.php'));
        }
        return $exists;
        // return isset($this->cache[$key]);
    }

    public function clear(string $key = ''){

    }

    private function asPhp($data):string{
        
        $type = gettype($data);
        if ($type = 'string'){
            return "'$data'";
        };
        if ($type = 'double' || $type = 'integer'){
            return "$data";
        };
        if ($type = 'boolean'){
            return $data?"true":"false";
        };


        if ($type = 'array'){
            if (Arr::is_assoc($data)){
                $out = '';
                foreach($data as $name=>$value){
                    $out.=($out?',':'')."'$name'=>".$this->asPhp($value);
                }
                return '['.$out.']';
            }else{
                $out='';
                for($i = 0;$i<count($data);$i++){
                    $out.=($out?',':'').$this->asPhp($data[$i]);
                };
                return $out;
            }
        };



        throw new \Exception('cnt cache type'.$type);

        
    }
}