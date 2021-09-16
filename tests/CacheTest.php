<?php
namespace fmihel\cache\test;

use PHPUnit\Framework\TestCase;
use fmihel\cache\Cache;
use fmihel\console;

final class CacheTest extends TestCase{
    
    protected $data = ['my'=>10,['story'=>15]];
    protected $key = 'mykey';

    public static function setUpBeforeClass(): void
    {
        //self::$cache = new Cache();
    }      
    public function test_key(){
        
        $key = Cache::obj()->key('10','20',__CLASS__,__FUNCTION__);

        $out = '67a7cd60718cae3491f896ec5617d4af';
        self::assertTrue($key === $out);
    }
    
    public function test_get_empty(){
        //------------------------------------------
        $res = Cache::obj()->get($this->key); 
        self::assertFalse($res);
    }
    /**
     * @depends test_get_empty
     */
    public function test_set(){
        Cache::obj()->set($this->key,$this->data);
        self::assertTrue(true);
    }    
    /**
     * @depends test_set
     */
    public function test_get(){
        $res = Cache::obj()->get($this->key); 
        self::assertSame($res,$this->data);
    }    
    /**
     * @depends test_get
     */
    public function test_clear(){
        Cache::obj()->clear($this->key);
        $res = Cache::obj()->get($this->key); 
        self::assertFalse($res);
        
    }
    
    public function test_caches(){
        
        $cache = Cache::obj();
        $cache1 = Cache::obj('other');
        $key = 'test_caches';
        $data = 'qgedhqweh qwedgqhwg';

        $res = $cache1->get($key); 
        self::assertFalse($res);
        
        $cache->set($key,$data); 
        
        $res = $cache1->get($key); 
        self::assertFalse($res);
        
        $res = $cache->get($key); 
        self::assertSame($res,$data);
    }

    public function test_false(){
        
        $cache = Cache::obj();
        $key = 'test';
        $data = false;

        $res = $cache->set($key,$data); 
        $res = $cache->get($key); 
        self::assertSame($res,$data);
        
    }
    

};

?>
