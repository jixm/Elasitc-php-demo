<?php
namespace Module;

class Query extends \Module\BaseModule {

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
     * bool查询
     * @Author   ji.xiaoming
     * @DateTime 2017-11-20
     * @return   [type] [description]
     */
    public function boolQUery() {

    }

    public function getMorelikeQueryByContent( $content , $index, $type ) {
        $query = [];
        $query['more_like_this'] = array(
                'fields' => array(
                        "content"
                    ),
                'like' => [
                    '_index' => $index,
                    '_type'  => $type,
                    'doc'    => array(
                            'content' => $content,
                        )
                ],
                'min_term_freq'   => 1,
                'max_query_terms' => 100,
                "minimum_should_match" => "100%",

            );
        return $this->_client 
            -> search( $query );
    }

    public function test( $index, $type ) {
        //cardinality 去重
        $query['aggs'] = array(
            "group" => array(
                "terms" => array(
                    "field" => "type",
                    "size"  => 10,
                    "order" => array(
                        "_count" => 'desc'
                        )
                    )
                )

            );
        $query["query"]["bool"] = array(
            "filter" => array(
                "term" => array(
                    "month" => $month
                    )
                )
            );
        $query['size'] = 0;

        return $this->_client
            -> search([
                'index' => $index,
                'type'  => $type,
                'body'  => $query
            ]);
    }

    public function collapseQuery( $index , $type ) {
        $aggsQuery = array();
        $aggsQuery['collapse']['field'] = $field;
        if( $size > 1 ) {
            $aggsQuery['collapse']['inner_hits'] = [
                'name' => 'top',
                'size' => $size,
                "sort" => array(
                    "cts" => "desc"
                )
            ];

        }
        return $this->_client
            -> search([
                'index' => $index,
                'type'  => $type,
                'body'  => $aggsQuery
            ]);
    }
}