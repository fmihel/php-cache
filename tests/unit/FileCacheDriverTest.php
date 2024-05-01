<?php
namespace fmihel\cache\test;

use PHPUnit\Framework\TestCase;
use fmihel\console;
use fmihel\cache\drivers\FileCacheDriver;


final class FileCacheDriverTest extends TestCase{
    
    
    public static function setUpBeforeClass(): void
    {
        //self::$cache = new Cache();
    }

    public function test_asPhp(){
        
        $fd = new FileCacheDriver('');
        //-----------------------------------------------------
        $result = $fd->asPhp('string');
        $expect = "'string'";
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        $result = $fd->asPhp(10);
        $expect = 10;
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        $result = $fd->asPhp(true);
        $expect = 'true';
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        $result = $fd->asPhp([1]);
        $expect = '[1]';
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        $result = $fd->asPhp([]);
        $expect = '[]';
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        $result = $fd->asPhp(['a'=>10,'b'=>'s']);
        $expect = "['a'=>10,'b'=>'s']";
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        $result = $fd->asPhp(['a'=>10,'b'=>'s','aa'=>['m'=>'a']]);
        $expect = "['a'=>10,'b'=>'s','aa'=>['m'=>'a']]";
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
        
        $result = $fd->asPhp(['a'=>10,'b'=>'s','aa'=>[10,20,'m'=>'a']]);
        $expect = "['a'=>10,'b'=>'s','aa'=>['0'=>10,'1'=>20,'m'=>'a']]";
        self::assertEquals($expect,$result);
        //-----------------------------------------------------
    }
    

};

?>
