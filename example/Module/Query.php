<?php
namespace Module;

class Query extends \Module\BaseModule {


    /**
     * bool查询
     * @Author   ji.xiaoming
     * @DateTime 2017-11-20
     * @return   [type] [description]
     */
    public function boolQUery() {

    }

    /**
     * 相似文章
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2018-02-07
     * @param    [type] $content [description]
     * @param    [type] $index [description]
     * @param    [type] $type [description]
     * @return   [type] [description]
     */
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
                    ),
                    "aggs" => array(
                        "top_detail_hits" => array(
                            "top_hits" => array(
                                "_source" => array(
                                    "includes" => array('type')
                                ),
                                'size' => 1
                            )
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

    public function updateByQuery($index,$type) {
        $updateRequest = [
            'index' => $this->_index,
            'type'  => $this->_type,
            'body'  => [
                'query' => [ 
                    'bool' => [
                        'filter' => [
                            [
                                'term' => [ 'status' => 1 ],
                            ]
                        ]
                    ]
                ],
                'script' => [
                        'inline' => 'ctx._source.uts = params.value',
                         "lang"   => "painless",    
                        'params' => [
                            'value' => time()
                        ]
                ]
            ]
        ];
        return $this->_client
            -> updateByQuery([
                'index' => $index,
                'type'  => $type,
                'body'  => $updateRequest
            ]);
    }

}