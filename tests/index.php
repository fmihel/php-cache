<?php 
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/funcs.php';
require_once __DIR__.'/../source/Cache.php';
require_once __DIR__.'/../source/Stat.php';

use fmihel\console;
use fmihel\config\Config;
use fmihel\base\Base;
use fmihel\cache\Cache;
use fmihel\cache\Stat;
use fmihel\cache\drivers\FileCacheDriver;
use fmihel\cache\drivers\SerCacheDriver;


console::line();

foreach (Config::get('base') as $base) {
    Base::connect($base);
}

console::line();
console::log('FileCacheDriver');
Cache::setDriver(new FileCacheDriver(__DIR__.'/cache'));
include __DIR__.'/file.php';

console::line();
console::log('SerCacheDriver');
Cache::setDriver(new SerCacheDriver(__DIR__.'/cache'));
include __DIR__.'/file.php';

console::line();

