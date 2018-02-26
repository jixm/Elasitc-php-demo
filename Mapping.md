## 自定义模板
```bash
index :
    analysis :
        analyzer :
            myAnalyzer2 :
                type : custom
                tokenizer : myTokenizer1
                filter : [myTokenFilter1, myTokenFilter2]
                char_filter : [my_html]
                position_increment_gap: 256
        tokenizer :
            myTokenizer1 :
                type : standard
                max_token_length : 900
        filter :
            myTokenFilter1 :
                type : stop
                stopwords : [stop1, stop2, stop3, stop4]
            myTokenFilter2 :
                type : length
                min : 0
                max : 2000
        char_filter :
              my_html :
                type : html_strip
                escaped_tags : [xxx, yyy]
                read_ahead : 1024
```
## 中文,拼音
``` bash
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
# mapping格式
PUT /search_text/_mapping/list
{
    # "dynamic":"false",          # 关闭自动添加字段，关闭后索引数据中如果有多余字段不会修改mapping,默认true
    # "_all":{"enabled":"false"}, # 禁用_all字段，_all字段包含了索引中所有其他字段的所有数据，便于搜索。默认启用
    # "_id" :{"index":"not_analyzed","store":"no"},  # 设置文档标识符可以被索引，默认不能被索引。可以设置为"_id":{"path":"book_id"},这样将使用字段book_id作为标识符
    # "_source":{"enabled":"false"},                 # 禁用_source字段，_source字段在生成索引过程中存储发送到elasticsearch的原始json文档。elasticsearch部分功能依赖此字段(如局部更新功能),因此建议开启。默认启用
    # "_index" :{"enabled":"true"},                  # 启用_index字段，index字段返回文档所在的索引名称。默认关闭。
    # "_timestamp":{
    #    "enabled": "true",
    #    "index"  : "not_analyzed",
    #    "store"  : "true",
    #    "format" : "YYYY-mm-dd"
    # },  # 启用时间戳并设置。时间戳记录文档索引时间，使用局部文档更新功能时，时间戳也会被更新。默认未经分析编入索引但不保存。
    # "_ttl":{"enabled":"true","default":"30d"},    # 定义文档的生命周期,周期结束后文档会自动删除。
    # "_routing":{"required":"true","path":"name"}  # 指定将name字段作为路由，且每个文档必须指定name字段。
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
                    "analyzer" : "pinyiFullIndexAnalyzer"          # 定义用于索引和搜索的分析器名称，默认为全局定义的分析器名称。可以开箱即用的分析器:standard,simple,whitespace,stop,keyword,pattern,language,snowball
                    # "index_analyzer":"pinyiFullIndexAnalyzer",   # 定义用于建立索引的分析器名称
                    # "search_analyzer":"pinyiFullIndexAnalyzer",  # 定义用于搜索时分析该字段的分析器名称
                    # "ignore_above":"255"                         # 定义字段中字符的最大值，字段的长度高于指定值时，分析器会将其忽略
                    # "null_value":"jim",                          # 当索引文档的此字段为空时填充的默认值，默认忽略该字段
                    # "include_in_all":"xxx"                       # 此属性是否包含在_all字段中,默认为包含 

                },
                'create_time' => [
                    'type'   => 'date',
                    "store"  => "yes",
                    'format' => 'yyyy-MM-dd HH:mm:ss',
                    'index'  => 'not_analyzed',
                     //日期特有属性
                    "precision_step":"0",                          # 指定为该字段生成的词条数，值越低，产生的词条数越多，查询会更快，但索引会更大。默认4
                ]
             }
          }
    }

}

```