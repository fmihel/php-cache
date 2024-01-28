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


console::line();

foreach (Config::get('base') as $base) {
    Base::connect($base);
}
//---------------------------------------------------------
Cache::setDriver(new FileCacheDriver(__DIR__.'/cache'));
//---------------------------------------------------------
$q = 'select NAME from DEALER where ID_DEALER = 21039';

$timer = 'asis ';
Stat::start($timer);
$value = Base::value($q,'deco',['coding'=>'utf8']);
console::log($value);
Stat::stop($timer);
//---------------------------------------------------------
$timer = 'wrap ';
Stat::start($timer);
$value = Cache::get('simple-1',[21039],function() use($q){
    return Base::value($q,'deco',['coding'=>'utf8']);
});
console::log($value);
Stat::stop($timer);
//---------------------------------------------------------
$timer = 'cache';
Stat::start($timer);
$value = Cache::get('simple-1',[21039],function() use($q){
    return Base::value($q,'deco',['coding'=>'utf8']);
});
console::log($value);
Stat::stop($timer);
//---------------------------------------------------------



console::line();
$q = 'select * from DEALER where ID_DEALER = 21039';

$timer = 'asis ';
Stat::start($timer);
$value = Base::row($q,'deco','utf8');
console::log($value['NAME']);
Stat::stop($timer);
//---------------------------------------------------------
$timer = 'wrap ';
Stat::start($timer);
$value = Cache::get('simple-2',[21039],function() use($q){
    return Base::row($q,'deco','utf8');
});
console::log($value['NAME']);
Stat::stop($timer);
//---------------------------------------------------------
$timer = 'cache';
Stat::start($timer);
$value = Cache::get('simple-2',[21039],function() use($q){
    return Base::row($q,'deco','utf8');
});
console::log($value['NAME']);
Stat::stop($timer);
//---------------------------------------------------------



console::line();
$q = 'select * from USER where ID_DEALER = 21039';

$timer = 'asis ';
Stat::start($timer);
$value = Base::rows($q,'deco','utf8');
console::log(count($value));
Stat::stop($timer);
//---------------------------------------------------------
$timer = 'wrap ';
Stat::start($timer);
$value = Cache::get('simple-3',[21039],function() use($q){
    return Base::rows($q,'deco','utf8');
});
console::log(count($value));
Stat::stop($timer);
//---------------------------------------------------------
$timer = 'cache';
Stat::start($timer);
$value = Cache::get('simple-3',[21039],function() use($q){
    return Base::rows($q,'deco','utf8');
});
console::log(count($value));
Stat::stop($timer);
//---------------------------------------------------------


console::line();

