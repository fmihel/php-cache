<?php
namespace fmihel\cache;

interface iCacheDriver
{
    public function get(string $key);
    public function set(string $key, $data);
    public function exists(string $key): bool;
    public function clear(string $key = '');
    public function each($callback);

};
