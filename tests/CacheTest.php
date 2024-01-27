<?php
namespace fmihel\cache\test;

use PHPUnit\Framework\TestCase;
use fmihel\cache\Cache;
use fmihel\console;
require_once __DIR__.'/funcs.php';


final class CacheTest extends TestCase{
    
    
    public static function setUpBeforeClass(): void
    {
        //self::$cache = new Cache();
    }      
    public function test_get(){
        
        $expect = 8000000;
        $count = 200;        
        \start('start');
        $result = \heavy1($count);
        \stop('start');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        \start('start');
        $result = Cache::get('heavy1',[$count],function() use ($count){
            return \heavy1($count);
        });
        \stop('start');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        \start('start');
        $result = Cache::get('heavy1',[$count],function() use ($count){
            return \heavy1($count);
        });
        \stop('start');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------



        console::line();
            
        $expect = 1000;
        $count = 10;        
        \start('start');
        $result = \heavy1($count);
        \stop('start');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        \start('start');
        $result = Cache::get('heavy1',[$count],function() use ($count){
            return \heavy1($count);
        });
        \stop('start');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        \start('start');
        $result = Cache::get('heavy1',[$count],function() use ($count){
            return \heavy1($count);
        });
        \stop('start');
        self::assertEquals($expect,$result);
        //-----------------------------------------------------



    }
    

    

};

?>
