# php-cache
simple cache for php functions result

### Install
```composer require fmihel/php-cache```

### Example 

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';
use fmihel\cache\Cache;
use fmihel\cache\drivers\FileCacheDriver;

const KEY_1 = 'key_1';

class MyClass{

    static function strong($count1 = 10000,$count2 = 10000){
        sleep(1);  
        $out = 0;
        for($i=0;$i<$count1;$i++){
            for($j=0;$j<$count2;$j++){
                $out++;
            };
        };
        return $out;
    }

    static function cached_strong($count1 = 10000,$count2 = 10000){
        global $cache;
        return $cache->get(KEY_1,func_get_args(),function() use($count1,$count2){
            
            return self::strong($count1,$count2);

        });
    }
};


$cache = new Cache(new FileCacheDriver(/*path*/));// default story cache to $_SERVER['PWD'].'/cache';

$get = MyClass::cached_strong(1000000,100000);


?>
```
## class Cache
|name|params|note|
|---|---|---|
|get(string $key,array $params,$callback,$lifetime):any|$key - unique key<br>$params - array of input params <br> $callback - wrap function for caching method<br>$lifetime - period in hours | caching function result |
|clear()||clear cache|
|setDriver(iCacheDriver $driver)| $driver - cache engine driver, implements `iCacheDriver` interface|set cache driver,<br> exists `SimpleCacheDriver`,`FileCacheDriver`,`SerCacheDriver`|
|enable(bool=null)|bool - if boolean set | enable/disable caching, or return curent set if bool===null |
|clearOld()||removes outdated caches|


