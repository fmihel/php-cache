<?php
namespace fmihel\cache\test;

use PHPUnit\Framework\TestCase;
use fmihel\cache\Cache;
use fmihel\console;
use fmihel\cache\drivers\FileCacheDriver;

require_once __DIR__.'/funcs.php';
require_once __DIR__.'/../source/drivers/FileCacheDriver.php';


final class CacheTest extends TestCase{
    
    
    public static function setUpBeforeClass(): void
    {
        //self::$cache = new Cache();
    }

    public function test_get(){
        
        // Cache::setDriver(new FileCacheDriver('~/work/fmihel/php-cache'));

        Cache::setDriver(new FileCacheDriver('/home/mike/work/fmihel/php-cache/'));
        $expect = 125000000;
        $count = 500;        
        \start('asis');
        $result = \heavy1($count);
        \stop('asis');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        \start('wrap');
        $result = Cache::get('heavy1',[$count],function() use ($count){
            return \heavy1($count);
        });
        \stop('wrap');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        \start('cach');
        $result = Cache::get('heavy1',[$count],function() use ($count){
            return \heavy1($count);
        });
        \stop('cach');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------



        console::line();

        $expect = 1000;
        $count = 10;        
        \start('asis');
        $result = \heavy1($count);
        \stop('asis');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        \start('wrap');
        $result = Cache::get('heavy1',[$count],function() use ($count){
            return \heavy1($count);
        });
        \stop('wrap');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        \start('cach');
        $result = Cache::get('heavy1',[$count],function() use ($count){
            return \heavy1($count);
        });
        \stop('cach');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------



    }
    

    

    

};

?>
