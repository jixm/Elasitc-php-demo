## 中文,拼音
```bash
PUT /search_text
{
  "settings":{
      "refresh_interval":"5s",
      "number_of_shards":1,
      "number_of_replicas":1,
      "analysis":{
          "filter":{
                "edge_ngram_filter":{
                    "type":"edge_ngram",
                    "min_gram":1,
                    "max_gram":50
                },
                "pinyin_simple_filter":{
                    "type" : "pinyin",
                    "keep_first_letter":true,
                    "keep_separate_first_letter" : false,
                    "keep_full_pinyin" : false,
                    "keep_original" : false,
                    "limit_first_letter_length" : 50,
                    "lowercase" : true
                },
                "ik_full_pinyin":{
                    "type" => "pinyin",
                    "keep_first_letter"=>false,
                    "keep_separate_first_letter" => false,
                    "keep_full_pinyin" => true,  
                    "keep_joined_full_pinyin"=>true,
                    "keep_original" => false,
                    "limit_first_letter_length" => 50,
                    "lowercase" => true
                }
          },
          "tokenizer":{
              "ik_smart":{
                  "type":"ik_smart",
                  "use_smart":true
              }
          },
          "analyzer":{
                # 类似前缀搜索
                "ngramIndexAnalyzer": {
                    "type": "custom",
                    "tokenizer": "keyword",
                    "filter": ["edge_ngram_filter","lowercase"],
                    "char_filter" : ["charconvert"]
                },
                "pinyiSimpleIndexAnalyzer":{                   
                    "tokenizer" : "keyword",
                    "filter": ["pinyin_simple_filter","edge_ngram_filter","lowercase"]                                    
                },
                'pinyinFullIndexAnalyzer' => [
                    'type' => 'custom',
                    'tokenizer' => 'keyword',
                    'filter' => ["ik_full_pinyin","lowercase"]
                ],
                'ikSmartAnalyzer' => [
                    'type' => 'custom',
                    'tokenizer' => 'ik_smart',
                    'filter' => []
                ], 
          }
      }
  }
}
# mapping
PUT /search_text/_mapping/list
{
    "properties":{
        "words":{
            "type":"keyword",
            "fields":{
                "cn":{
                    "type":"text",
                    "index":true,
                    "analyzer":"ikSmartAnalyzer"
                },
                "full_pinyin": {
                    "type": "text",
                    "index":true,
                    "analyzer" : "pinyiFullIndexAnalyzer"
                }
             }
          }
    }

}

```