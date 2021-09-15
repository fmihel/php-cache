<?php
namespace fmihel\cache\drivers;

interface iCacheDriver{
    public function __construct(array $o=[]);
    public function get(string $key,array $o=[]);
    public function set(string $key,$data,array $o=[]);
    public function clear($key,array $o=[]);
    public function reset(array $o=[]);
};

?>