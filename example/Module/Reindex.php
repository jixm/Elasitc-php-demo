<?php
namespace Module;
/**
 * 重建索引
 * @author ji.xiaoming  
 * @date(2017-11-20)
 */
class Reindex  extends \Module\BaseModule{

    /**
     * 调用原生API
     * @Author   ji.xiaoming
     * @DateTime 2017-11-20
     * @return   [type] [description]
     */
    public function reindex_origin( $origin_index,$dest_index) {
        $params = array(
            'body' => array(
                'source' => array(
                    'index'  => $origin_index
                ),
                'dest' => array(
                    'index' => $dest_index
                )
            )
        );
        return $this->_client->reindex($params);
    }

    /**
     * scroll查数据重新索引数据
     * @Author   ji.xiaoming
     * @DateTime 2017-11-20
     * @return   [type] [description]
     */
    public function reindex_scroll() {
        $response = $this->_client($params);
        while( isset($response['hits']['hits']) && count($response['hits']['hits'])>0) {
            $newData = array();
            $bulk    = ['index' => $dest_index,'type'=>$dest_type];
            $scroll_id = $response['_scroll_id'];

            $data = $response['hits']['hits'];
            foreach( $data as $value ) {
                $newData[$value['_id']] = $value['_source'];
            }
            $bulk['body'] =  $this->getBulkSql( $newData );
            $this->_client->bulk($bulk);
            $resposne = $this->_client
                -> scroll([
                    'scroll_id' => $scroll_id,
                    'scroll' => '30s'
                ]);
        }
    }

    /**
     * @Author   ji.xiaoming
     * @DateTime 2017-11-20
     * @param    array $data [description]
     * @param    [type] $index [description]
     * @return   [type] [description]
     */
    public function getBulkSql( array $data ) {
        $bulk = array();
        foreach( $data as $key => $value ) {
            $bulk[] = array(
                'index' => array(
                    '_id' => $key
                )
            );
            $bulk[] = $value;
        }
        return $bulk;
    }

}