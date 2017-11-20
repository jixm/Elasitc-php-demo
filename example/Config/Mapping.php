<?php
/**
 * 索引mapping
 * @author ji.xiaoming  
 * @date(2017-11-20)
 */
namespace Config;
class Mapping {
     public static $test123  = [
        'index' => 'test123',
        'body' => [
            'settings' => [
                'number_of_shards' => 5,
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
                        'id' =>[
                            "type" => "integer",
                            'index'=> 'not_analyzed',
                        ],
                        'title'  => [
                            'type' => 'text',
                            "analyzer" => "ik_max_word",
                        ],
                        'name'  => [
                            'type'     => 'text',
                            "analyzer" => "ik_max_word",
                        ],
                    ]
                ],
                
            ]
        ]
   
    ];
}
