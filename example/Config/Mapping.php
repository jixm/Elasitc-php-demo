<?php
/**
 * 索引mapping
 * @author ji.xiaoming  
 * @date(2017-11-20)
 */
namespace Config;
class Mapping {
    public static $user = [
        'index' => 'user',
        'body' => [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'filter' => [
                        'user_pinyin' => [
                            'type' => 'pinyin',
                            'firs_letter' => 'prefix',
                            'padding_char' => ' '
                        ]
                    ],
                    'analyzer' => [
                        'ik_pinyin_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'ik_smart',
                            'filter' => ['user_pinyin','word_delimiter']
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'list' => [
                    'properties' => [
                        'name' =>[
                            "type"=> "keyword",
                            "fields"=> [
                                "pinyin"=> [
                                    "type"=> "text",
                                    "store"=> "yes",
                                    "term_vector"=> "with_positions_offsets",
                                    "analyzer"=> "ik_pinyin_analyzer",
                                ],
                                "cn" =>  [
                                    "type"=> "text",
                                    "store"=> "yes",
                                    "term_vector"=> "with_positions_offsets",
                                    "analyzer"=> "ik_max_word",
                                ],
                            ]
                        ],
                       
                        'id'  => [
                            'type' => 'long',
                            'index'=> 'not_analyzed',
                        ],
                        'create_time' => [
                            'type' => 'date',
                            "store"=> "yes",
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                            'index' => 'not_analyzed'
                        ],
                    ]
                ],
            ]
        ]
    ];
}
