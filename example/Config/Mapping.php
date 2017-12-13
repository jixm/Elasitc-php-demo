<?php
/**
 * 索引mapping
 * @author ji.xiaoming  
 * @date(2017-11-20)
 */
namespace Config;
class Mapping {
    public static $test_v1 = array(
		 'index' => 'test_v1',
        'body' => [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    "analyzer" => [
                        "ik" => [
                            "tokenizer" => "ik_max_word"
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'test' => [
                    'properties' => [
                        'content' =>[
                            "type"=> "integer",
                            'index' => 'not_analyzed'
                        ],
                        'title' =>[
                            "type"=> "text",
                            "analyzer"=> "ik_max_word",
                        ],
                        'description' =>[
                            "type"=> "text",
                            "analyzer"=> "ik_max_word",
                        ],
                        'create_time'  => [
                            'type' => 'integer',
                            'index' => 'not_analyzed'
                        ],
                    ]
                ]
            ]
        ]

    );
}
