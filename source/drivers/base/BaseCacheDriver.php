<?php
namespace fmihel\cache\drivers\base;

include_once __DIR__.'../iCacheDriver.php';

use fmihel\cache\drivers\iCacheDriver;
use fmihel\base\Base;


class BaseCacheDriver implements iCacheDriver{
    private $param = [];
    
    public function __construct(array $o=[]){
        $this->param = array_merge_recursive([
            'base'=>'deco',
            'table'=>'buffer',
            'timeout'=>600,
            'fields'=>[
                'id'=>'ID_BUFFER',
                'key'=>'KEY_BUFFER',
                'buffer'=>'BUFFER',
                'date'=>'LAST_UPDATE',
                'time'=>'timeout_sec',
                'group'=>'part',
                'notes'=>'notes',
                'group_id'=>'part_id'
            ],
            'group'=>'common',
            'group_id'=>0,
            'notes'=>'',
        ],$o);
    }

    public function get(string $key,array $o=[]){
        $a = array_merge_recursive($this->param,$o);
        $fields = $a['fields'];
        $q = 'select *,CURRENT_TIMESTAMP-`'.$fields['date'].'` `delta` from '.$a['table'].' where '.$fields['key']." = '".$key."'";
        if ($row = Base::row($q,$a['base'])){
            if ( $row['delta'] < $row[$fields['time']] )
                return $row[$fields['buffer']];        
        };
        return false;
    }
    public function set($key,$data,$o=array()){
        $a = array_merge_recursive($this->param,$o);
        $fields = $a['fields'];
        $data = Base::real_escape($data,$this->param['base']);
        try{
        
            $q = 'insert into '.$a['table'];
            $q.=' (';
            $q.= ' `'.$fields['key'].'`';
            $q.= ',`'.$fields['buffer'].'`';
            $q.= ',`'.$fields['date'].'`';
            $q.= ',`'.$fields['time'].'`';
            $q.= ',`'.$fields['group'].'`';
            $q.= ',`'.$fields['group_id'].'`';
            $q.= ',`'.$fields['notes'].'`';
            $q.=' )';
            $q.=' values';
            $q.=' (';
            $q.= " '".$key."'";
            $q.= ",'".$data."'";
            $q.= ",CURRENT_TIMESTAMP";
            $q.= ",".$a['timeout'];
            $q.= ",'".$a['group']."'";
            $q.= ",".$a['group_id'];
            $q.= ",'".$a['notes']."'";

            $q.=' )';
            $q.=' on duplicate key update';
            $q.=' `'.$fields['key']."`='".$key."'";
            $q.=',`'.$fields['buffer']."`='".$data."'";
            $q.=',`'.$fields['date']."`= CURRENT_TIMESTAMP";
            $q.=',`'.$fields['time']."`= ".$a['timeout'];
            $q.=',`'.$fields['group']."`= '".$a['group']."'";
            $q.=',`'.$fields['group_id']."`= ".$a['group_id'];
            $q.=',`'.$fields['notes']."`= '".$a['notes']."'";

            if (!Base::query($q,$this->param['base']))
                throw new \Exception(base::error($this->param['base']).' ['.$q.']');

            return true;

        }catch(Exception $e){
            return false;            
        }

    }
    /** 
     * Очистка буффера
     * Если первый параметр не указан или '' или array(), то будут удалены неактуальные данные кеша
     * Если $key - строка, то интерпретируется как значение ключа для очистки
     * Если $key - массив =  array('key'=>'ключь') или array('group'=>'значение группы для очистки') или array('where'=>'занчение блока WHERE в запросе MySQL')
     * @param mixed $key - строка или массив с указанием правила очистки
     * 
    */
    public function clear($key = '',array $o=[]){
        $a = array_merge_recursive($this->param,$o);
        $fields = $a['fields'];
        if (($key==='')||($key===array()))
            $q = 'delete from '.$a['table'].' where CURRENT_TIMESTAMP-`'.$fields['date'].'` >  `'.$fields['time'].'`';
        else{
            if (gettype($key)==='string')
                $q = 'delete from '.$a['table'].' where `'.$fields['key']."`='".$key."'";
            else{
                $key = array_merge_recursive(array(
                    'key'=>false,
                    'group'=>false,
                    'where'=>false
                ),$key);
                
                if($key['key']!==false)
                    $q = 'delete from '.$a['table'].' where `'.$fields['key']."`='".$key['key']."'";    
                elseif($key['group']!==false){
                    $q = 'delete from '.$a['table'].' where `'.$fields['group']."`='".$key['group']."'";
                }elseif($key['where']!==false){
                    $where = $key['where'];
                    // замена алиасов имен полей 
                    // т.е можно писать    clear(array('where'=>':group LIKE "price%"'))
                    $from = array();;
                    $to = array();
                    foreach($fields as $k=>$v){
                        $from[] = ':'.$k;
                        $to[]   = '`'.$v.'`';
                    }
                    $where = str_replace($from,$to,$where);
                    // -------------------------

                    $q = 'delete from '.$a['table'].' where '.$where;    
                }else
                    return false;
                
            }    
        }    
        
        return base::query($q,$a['base']);
    }
    public function reset(array $o=[]){

        $a = array_merge_recursive($this->param,$o);
        $q = 'delete from '.$a['table'].' where 1>0';
        return base::query($q,$a['base']);
        
    }
}

?>