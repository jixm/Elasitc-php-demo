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
                    "keep_separate_first_letter" : false,
                    "keep_full_pinyin" : true,  
                    "keep_joined_full_pinyin":true,
                    "keep_original" : false,
                    "limit_first_letter_length" : 50,
                    "lowercase" : true
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
                'pinyinFullIndexAnalyzer' : [
                    'type' : 'custom',
                    'tokenizer' : 'keyword',
                    'filter' : ["ik_full_pinyin","lowercase"]
                ],
                'ikSmartAnalyzer' : [
                    'type' : 'custom',
                    'tokenizer' : 'ik_smart',
                    'filter' : []
                ], 
          }
      }
  }
}
## mapping格式
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
                'create_time': [
                    'type'   : 'date',
                    "store"  : "yes",
                    'format' : 'yyyy-MM-dd HH:mm:ss',
                    'index'  : 'not_analyzed',
                     //日期特有属性
                    "precision_step":"0",                          # 指定为该字段生成的词条数，值越低，产生的词条数越多，查询会更快，但索引会更大。默认4
                ]
             }
          }
    }

}

```
## 树形结构
统计目录下所有子目录
```bash
PUT index_test
{
  "settings": {
    "analysis": {
      "analyzer": {
        "my_analyzer": {
          "tokenizer": "my_tokenizer"
        }
      },
      "tokenizer": {
        "my_tokenizer": {
          "type": "path_hierarchy",
          "delimiter": "/",          # 分隔符
          "replacement" : "/"
          "skip": 0                  #  The number of initial tokens to skip. Defaults to 0.
        }
      }
    }
  }
}
PUT /index_test/_mapping/list
{
  "properties":{
      "category":{
            "type":"text",
            "fields":{
                "name":{
                    "type":"text",
                    "index":"not_analyzed"
                },
                "path": {
                    "type": "text",
                    "analyzer" : "my_analyzer"
                }
               
             }
          }
  } 
}

POST index_test/_analyze
{
  "analyzer": "my_analyzer",
  "text": "/one/two/three/four/five"
}

#{
#  "tokens": [
#    {
#      "token": "/one",
#      "start_offset": 0,
#      "end_offset": 4,
#      "type": "word",
#      "position": 0
#    },
#    {
#      "token": "/one/two",
#      "start_offset": 0,
#      "end_offset": 8,
#      "type": "word",
#      "position": 0
#    },
#    {
#      "token": "/one/two/three",
#      "start_offset": 0,
#      "end_offset": 14,
#      "type": "word",
#      "position": 0
#    },
#    {
#      "token": "/one/two/three/four",
#      "start_offset": 0,
#      "end_offset": 19,
#      "type": "word",
#      "position": 0
#    },
#    {
#      "token": "/one/two/three/four/five",
#      "start_offset": 0,
#      "end_offset": 24,
#      "type": "word",
#      "position": 0
#    }
#  ]
}

```

## nested
> nested_pa​​th - 定义要排序的嵌套对象。实际的排序字段必须是此嵌套对象内的直接字段。当按嵌套字段排序时，该字段是强制性的。
> nested_filter——一个过滤器，该过滤器内部的对象在嵌套路径中应该与它的字段值相匹配，以便通过排序来考虑它的字段值。常见的情况是在嵌套的过滤器或查询中重复查询/过滤。默认情况下，不激活nested_filter。
```bash
curl -XPUT 'ES_HOST:ES_PORT/test?pretty' -H 'Content-Type: application/json' -d '{
 "mappings": {
   "list": {
     "properties": {
       "title": { "type": "text" },
       "comments": {
         "type": "nested",
         "properties": {
           "name":    { "type": "text"  },
           "comment": { "type": "text"  },
           "age":     { "type": "short"   },
           "rating":   { "type": "short"  },
           "date":    { "type": "date"    }
         }
       }
     }
   }
 }
}'

# search title = 'test' , 平均年龄从小到大,评分为5
curl -XPOST 'ES_HOST:ES_PORT/test/_search?pretty' -H 'Content-Type: application/json' -d '{
  "query" : {
     "term" : { "title" : "test" }
  },
  "sort" : [
      {
         "comments.age" : {
            "mode" :  "avg",
            "order" : "asc",
            "nested_path" : "comments",
            "nested_filter" : {
               "term" : { "comments.rating" : 5 }
            }
         }
      }
   ]
}
'
# 我们希望检索5月份收到评论的博客文章，并按照每篇博客帖子收到的最低数量的star排序。搜索请求将如下所示：
curl -XGET 'ES_HOST:ES_PORT/test/_search?pretty' -H 'Content-Type: application/json' -d '{
  "query": {
    "nested": { 
      "path": "comments",
      "filter": {
        "range": {
          "comments.date": {
            "gte": "2017-05-01",
            "lt":  "2017-06-01"
          }
        }
      }
    }
  },
  "sort": {
    "comments.rating": {
      "order": "asc",   
      "mode":  "min",   
      "nested_filter": { 
        "range": {
          "comments.date": {
            "gte": "2017-05-01",
            "lt":  "2017-06-01"
          }
        }
      }
    }
  }
}'
```


```bash
PUT /my_index
{
  "settings": {
    "analysis": {
      "filter": {
        "autocomplete_filter" : {
          "type" : "edge_ngram",
          "min_gram" : 1,
          "max_gram" : 20
        }
      },
      "analyzer": {
        "autocomplete" : {
          "type" : "custom",
          "tokenizer" : "standard",
          "filter" : [
            "lowercase",
            "autocomplete_filter"
          ]
        }
      }
    }
  }
}

PUT /my_index/_mapping/my_type
{
  "properties": {
      "title": {
          "type":     "text",
          "analyzer": "autocomplete",
          "search_analyzer": "standard"
      }
  }
}

GET /my_index/my_type/_search
{
  "query": {
    "match_phrase": {
      "title": "hello wi"
    }
  }
}

```
