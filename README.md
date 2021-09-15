# php-cache
simple cache for php functions result

### Install
```composer require fmihel/cache```
### Use (simple)
```php
<?php
use fmihel\cache\Cache;

class MyClass{
    function strong($a1,$a2,...$a3){

        $cache = Cache::obj();
        $key = $cache->key(__CLASS__,__FUNCTION__,func_get_args());
        
        if (!( $res = $cache->get($key) )){

            // longtime code, $res = ...;

            $cache->set($key,$res);
        };
        
        return $res;

    }
};




?>
```