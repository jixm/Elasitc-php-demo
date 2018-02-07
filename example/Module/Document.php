<?php
namespace Module;

class Document extends \Module\BaseModule {


    public function mget( array $idsList ) {
        $query = array();
        foreach( $idsList as $key => $value ) {
            $query[$key]['_index']= $value['index'];
            $query[$key]['_type'] = $value['type'];   
            $query[$key]['_id']   = $value['id'];   
            $query[$key]['_source'] = ['title','url']; 
        }
        return $this->_client
            ->mget('','',$query);
    }


    /**
     * 按id查询
     * @Author   ji.xiaoming
     * @DateTime 2017-11-20
     * @return   [type] [description]
     */
    public function idsQuery( array $ids , $index, $type ) {
        $query = [
            'query' => array(
                'ids' => array(
                        'type' => 'type',
                        'values'=> $ids
                    )
                )
        ];
        return $this->_client
            ->search([
                'index' => $index,
                'type'  => $type,
                'body'  => $query
                ]);
    }

    /**
     * id未存在插入否则更新
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2018-02-07
     * @param    [type] $index [description]
     * @param    [type] $type [description]
     * @param    [type] $id [description]
     * @return   [type] [description]
     */
    public function upinsert($index,$type,$id) {
        $query = array (
            'index' => $index,
            'type'  => $type,
            'id'    => $id,
            'retry_on_conflict' => 3,
            'body'  => array(
                "script" => array(
                    "inline" => "ctx._source.status = 0;ctx._source.uts = params.uts;",
                    "lang"   => "painless",            
                    "params" => array(
                        'uts' => time()
                    )
                ),
                //插入内容
                'upsert' => array(
                    'id'      => $id,
                    'title'   => 'test',
                    'content' => 'content'
                )
            )
        );
        return $this->_client
            ->update($query);  
    }

    /**
     * 局部更新
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2018-02-07
     * @param    [type] $index [description]
     * @param    [type] $type [description]
     * @param    [type] $id [description]
     * @return   [type] [description]
     */
    public function update($index,$type,$id) {
        $data   = array (
            'index' => $index,
            'type'  => $type,
            'id'    => $id,
            'body'  => array(
                'doc' => array(
                    'name' => 'new name'
                )
            )
        );
        return $this->_client
            -> update($data);
    }

     /**
     * 删除
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2018-02-07
     * @param    [type] $index [description]
     * @param    [type] $type [description]
     * @param    [type] $id [description]
     * @return   [type] [description]
     */
    public function delete($index,$type,$id) {
        $data   = array (
            'index' => $index,
            'type'  => $type,
            'id'    => $id,
        );
        return $this->_client
            -> delete($data);
    }

     /**
     * 检查数据是否存在
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2018-02-07
     * @param    [type] $index [description]
     * @param    [type] $type [description]
     * @param    [type] $id [description]
     * @return   [type] [description]
     */
    public function exists($index,$type,$id) {
        $data   = array (
            'index' => $index,
            'type'  => $type,
            'id'    => $id,
        );
        return $this->_client
            -> exists($data);
    }

    /**
     * 索引
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2018-02-07
     * @param    string $index [description]
     * @param    string $type [description]
     * @param    array $document 字段=>值
     * @return   [type] [description]
     */
    public function insert( string $index , string $type , array $document ) {
        $data   = array (
            'index' => $index,
            'type'  => $type,
            'body'  => $document
        );
        return $this->_client
            ->index($data);
    }

    public function bulkUpdate($listInfo) {
        $action = $bulk = array();
        foreach( $listInfo as $info) {
            $action = array(
                    'doc'  => $info
                );
            $bulk[] = array(
                    'upate' => array(
                            '_id' => $info['id']
                        )
                );
            $bulk[] = $action;
        }
        $query   = array (
            'index' => $index,
            'type'  => $type,
            'body'  => $bulk
        );
        return $this->_client
            -> bulk( $query );
    }

    public function bulkInsert( $data ) {
        $tmp = array();
        foreach( $data as $key => $value ) {
            $tmp = [
                'id'    => $value['id'],
                'name'  => $value['name'],
                'status'=> $value['state'], 
            
            ];
            $bulk[]  = array(
                'index' => array(
                    '_id' => $value['id']
                )
            );
            $bulk[]=$tmp;
        }
        $query   = array (
            'index' => $index,
            'type'  => $type,
            'body'  => $bulk
        );
        return $this->_client
            -> bulk( $query );

        

    }

}