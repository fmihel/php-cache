<?php

require_once __DIR__ . '/funcs.php';
require_once __DIR__ . '/../source/Cache.php';
require_once __DIR__ . '/../source/Stat.php';

use fmihel\base\Base;
use fmihel\cache\Cache;
use fmihel\cache\Stat;
use fmihel\console;

// //---------------------------------------------------------

// $q = 'select NAME from DEALER where ID_DEALER = 21039';
// console::log($q);

// $timer = 'asis ';
// Stat::start($timer);
// $value = Base::value($q, 'deco', ['coding' => 'utf8']);
// console::log($value);
// Stat::stop($timer);
// //---------------------------------------------------------
// $timer = 'wrap ';
// Stat::start($timer);
// $value = Cache::get('simple-1', [21039], function () use ($q) {
//     return Base::value($q, 'deco', ['coding' => 'utf8']);
// });
// console::log($value);
// Stat::stop($timer);
// //---------------------------------------------------------
// $timer = 'cache';
// Stat::start($timer);
// $value = Cache::get('simple-1', [21039], function () use ($q) {
//     return Base::value($q, 'deco', ['coding' => 'utf8']);
// });
// console::log($value);
// Stat::stop($timer);
// //---------------------------------------------------------

// console::line();
// $q = 'select * from DEALER where ID_DEALER = 21039';
// console::log($q);

// $timer = 'asis ';
// Stat::start($timer);
// $value = Base::row($q, 'deco', 'utf8');
// console::log($value['NAME']);
// Stat::stop($timer);
// //---------------------------------------------------------
// $timer = 'wrap ';
// Stat::start($timer);
// $value = Cache::get('simple-2', [21039], function () use ($q) {
//     return Base::row($q, 'deco', 'utf8');
// });
// console::log($value['NAME']);
// Stat::stop($timer);
// //---------------------------------------------------------
// $timer = 'cache';
// Stat::start($timer);
// $value = Cache::get('simple-2', [21039], function () use ($q) {
//     return Base::row($q, 'deco', 'utf8');
// });
// console::log($value['NAME']);
// Stat::stop($timer);
// //---------------------------------------------------------

// console::line();
// $q = 'select * from USER where ID_DEALER = 21039';
// console::log($q);

// $timer = 'asis ';
// Stat::start($timer);
// $value = Base::rows($q, 'deco', 'utf8');
// console::log(count($value));
// Stat::stop($timer);
// //---------------------------------------------------------
// $timer = 'wrap ';
// Stat::start($timer);
// $value = Cache::get('simple-3', [21039], function () use ($q) {
//     return Base::rows($q, 'deco', 'utf8');
// });
// console::log(count($value));
// Stat::stop($timer);
// //---------------------------------------------------------
// $timer = 'cache';
// Stat::start($timer);
// $value = Cache::get('simple-3', [21039], function () use ($q) {
//     return Base::rows($q, 'deco', 'utf8');
// });
// console::log(count($value));
// Stat::stop($timer);
// //---------------------------------------------------------

console::line();
$count = 10;
$q = 'select distinct * from DEALER d join USER u on d.ID_DEALER = u.ID_DEALER where d.ARCH<>1 limit ' . $count . ';';
console::log($q);

$timer = 'asis ';
Stat::start($timer);
$value = Base::rows($q, 'deco', 'utf8');
console::log(count($value));
Stat::stop($timer);
//---------------------------------------------------------
$timer = 'wrap ';
Stat::start($timer);
$value = Cache::get('simple-4', [21039, $count], function () use ($q) {
    return Base::rows($q, 'deco', 'utf8');
});
console::log(count($value));
Stat::stop($timer);
//---------------------------------------------------------
$timer = 'cache';
Stat::start($timer);
$value = Cache::get('simple-4', [21039, $count], function () use ($q) {
    return Base::rows($q, 'deco', 'utf8');
});
console::log(count($value));
Stat::stop($timer);
//---------------------------------------------------------
