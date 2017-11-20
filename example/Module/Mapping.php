<?php
/**
 * mapping相关操作
 * @author ji.xiaoming  
 * @date(2017-11-20)
 */
namespace Module;
use Module\Connection;
use Config\Mapping as M;
class Mapping extends \Module\BaseModule{
    
    private $_client;

    public function __construct() {
        $this->_client = Connection::getClient();
    }

    /**
     * 创建索引
     * @Author   ji.xiaoming
     * @DateTime 2017-11-20
     * @return   [type] [description]
     * array(2) {
     *["acknowledged"]=>
     * bool(true)
     * ["shards_acknowledged"]=>
     * bool(true)
     * }
     */
    public function create( $index , $force=true) {
        $mapping = M::$$index;
        if( !$index ) {
            return false;
        }
        $param  = [
            'index' => $mapping['index'],
        ];
        $isExists = $this->getMapping($param);
        if( $isExists && $force ) {
            $this -> delete( $param ); 
        }
        try{
            $response = $this->_client 
                -> indices()
                -> create($mapping);
            
        }catch(\Exception $e) {
            throw new $e -> getMessage();
        }
        return $response;
       
    }

    /**
     * update mapping
     * @Author   ji.xiaoming
     * @DateTime 2017-11-20
     * @param    [type] $index [description]
     * @param    [type] $type [description]
     * @return   [type] [description]
     *  array(1) {
     *     ["acknowledged"]=>
     *     bool(true)
     *   }
     */
    public function update( $index , $type ) {
        $mapping = M::$$index;
        if( !$index ) {
            return false;
        }
        $param  = [
            'index' => $mapping['index'],
            'type'  => $type,
            'body'  => $mapping['body']['mappings']
        ];
        return $this->_client->indices()
                ->putMapping($param);
    }

   /**
    * 删除索引
    * @Author   ji.xiaoming
    * @DateTime 2017-11-20
    * @param    array $param ['index' => 'test']
    * @return   [type] [description]
    */
    public function delete( array $param ) {
        return $this->_client
            -> indices()
            -> delete($param);
    }

    /**
     * get Mapping
     * @Author   ji.xiaoming
     * @DateTime 2017-11-20
     * @param    array $param ['index' => 'test']
     * @return   [type] [description]
     */
    public function getMapping( array $param ) {
        try{
            return $this->_client
                -> indices()
                -> getSettings($param);
        }catch(\Exception $e){
            return false;
        }
      
    }   

}