<?php
/**
 * 索引数据
 * @author ji.xiaoming  
 * @date(2017-11-20)
 */
namespace Module;

use Module\Connection;
class Index extends \Module\BaseModule{


    /**
     * add index
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2017-11-20
     * @param    [type] $index [description]
     * @param    [type] $type [description]
     * @param    [type] $param [description]
     * @return   [type] [description]
     */
    public function index( $index, $type,$doc) {
        return $this->_client
            -> index([
            'index' => $index,
            'type'  => $type,
            'body'  => $doc
            ]);
    }

    /**
     * update index
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2017-11-20
     * @param    [type] $id [description]
     * @param    array $update [description]
     * 'query'  =>[]
     * 'script' => ['inline','lang'=>'painless',params['name'=>1]]
     * 'upsert'
     * @return   [type] [description]
     */
    public function update( $index , $type , $id , array $doc ) {
        return $this->_client
            -> update([
                'index' => $index,
                'type'  => $type,
                'id'    => $id,
                'body'  => ['doc'=>$doc]
                ]);
    }

    /**
     * 删除index
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2017-11-20
     * @param    [type] $index [description]
     * @param    [type] $type [description]
     * @param    [type] $id [description]
     * @return   [type] [description]
     */
    public function delete( $index, $type, $id) {
        return $this->_client()
            -> delete([
                'index' => $index,
                'type'  => $type,
                'id'    => $id
                ]);
    }

    /**
     * clear data
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2017-11-20
     * @param    [type] $index [description]
     * @param    [type] $type [description]
     * @return   [type] [description]
     */
    public function clear( $index , $type ) {
        $body = [
            'index' => $index,
            'type'  => $type,
            'body'  => []
        ];
        return $this->_client
            -> deleteByQuery($body);

    }

   
    
}

