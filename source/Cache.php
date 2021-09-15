<?php
namespace fmihel\cache;

class Cache {
    private $_driver=null;
    private $_enable = true;
    private $_preload = [];
    static private $_cache = null;
    /**
     * Создание экземпляра
     * @param string $_driver драйвер кеша
     */
    function __construct(object $_driver=null){
        $this->_driver = $_driver;
    }
    function __destruct()
    {
        //$this->clear();
    }
    /**
     * Получени данных из кеша
     * @param string $key уникальный идентификатор кеша, для формирования кеша из параметров кешируемой ф-ции используй key(...)
     * @param mixed[] $o необязательный список параметров, который переопределит параметры по умолчанию в драйвере
     * @return mixed Если кеш существует то возвращается его содержимое, если нет - false
     */
    public function get($key,$o=[]){
        if (!$this->_enable)
            return false;
        
        if (isset($this->_preload[$key]))
            return $this->_preload[$key];
        
        if ( !is_null($this->_driver) && ($data = $this->_driver->get($key,$o))){
            $this->_preload[$key] = unserialize($data);
            return $this->_preload[$key];
        }    
        
        return false;
    }

    /**
     * Помещение данных в кеш
     * @param string  $key уникальный идентификатор кеша, для формирования кеша из параметров кешируемой ф-ции используй key(...)
     * @param mixed   $data кешируемые данные
     * @param mixed[] $o необязательный список параметров, который переопределит параметры по умолчанию в драйвере
     * @return bool 
     */
    public function set($key,$data,$o=[]){
        if (!$this->_enable)
            return;

         $this->_preload[$key] = $data;
         
         if (!is_null($this->_driver))
            $this->_driver->set($key,serialize($data),$o);
    }
    /** 
     * Выборочная очистка кеша
     * @param string  $key уникальный идентификатор кеша, для формирования кеша из параметров кешируемой ф-ции используй key(...)
     * если параметр не установить, то будут удалены все неактуальные скешированные данные (см timeout)
     * @param mixed[] $o необязательный список параметров, который переопределит параметры по умолчанию в драйвере
     * 
    */
    public function clear($key=null,$o=[]){
        if (!$this->_enable)
            return;
        
        if ( is_null($key) ){
            $this->_preload=[];
        }elseif (gettype($key)==='array'){
            foreach($key as $k)
                $this->clear($k,$o);
        }else{ 
            unset($this->_preload[$key]);
            if (!is_null($this->_driver))    
                $this->_driver->clear($key,$o);
        };
    }
    /**
     * Преобразование массива аргументов в идентификатор кеша.
     * @param mixed[] $args - массив аргуметов
     * @example $cache->key(__CLASS__,__FUNCTION__,func_get_args());
     * @return string строка не более 32 символов соотвествующая ключу
     */
    public function key(...$args){
        $ret = serialize($args);
        if (mb_strlen($ret)>32) 
            $ret = md5($ret);

        return $ret;
    }

    /** 
     * Полная очистка кеша. Стирает все данные
     * @param mixed[] $o необязательный список параметров, который переопределит параметры по умолчанию в драйвере
     * 
    */
    public function reset($o=[]){
        if (!$this->_enable)
            return;        
        $this->_preload = array();    
        if (!is_null($this->_driver))    
            $this->_driver->reset($o);        
    }
    
    /**
     * Устанвавливает или возвращает признак что кеширование включено
     * @example enable() - возвращает признак
     * @example enable(true) - разрешает кеширование
     * @return bool признак того что кеширование включено
     */
    public function enable(/**bool */){
        if (func_num_args()>0){
            $args = func_get_args();
            $this->_enable = $args[0]?true:false;
        }
        return $this->_enable;
    }
    /** установить/получить  сбросить драйвер 
     *  $cache->driver() - получить драйвер
     *  $cache->driver(new Driver) - установить новый
     *  $cache->driver(null) - сбросить драйвер
     * 
    */
    public function driver($driver=false){
        
        if ($driver===false){
            return $this->_driver;
        }else{
            $this->_driver = $driver;
        }

    }

    /** получение  статического объекта кэша ( объект будет создан на момент первого вызова )*/
    static public function obj($driver=null){
        if (self::$_cache === null)
            self::$_cache=new Cache($driver);
        return self::$_cache;
    }
}

?>