## 目录
   - [自动完成](#自动完成)
   - [注册服务](#注册服务)
   - [snapshot](#snapshot)
   - [elasticdump](#elasticdump)
   - [自定义词典](#自定义词典)
    
## 自动完成
**索引mapping**
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
            "type":"pinyin",
            "keep_first_letter":true,
            "keep_separate_first_letter":false,
            "keep_full_pinyin":false,
            "keep_original":false,
            "limit_first_letter_length":50,
            "lowercase":true
          },
          "pinyin_full_filter":{
            "type":"pinyin",
            "keep_first_letter":false,
            "keep_separate_first_letter":false,
            "keep_full_pinyin":true,
            "none_chinese_pinyin_tokenize":true,
            "keep_original":false,
            "limit_first_letter_length":50,
            "lowercase":true
          }
        },
        "tokenizer":{
          "ik_max":{
            "type":"ik_max_word",
            "use_smart":true
          }
        },
        "analyzer":{
          "pinyinSimpleIndexAnalyzer":{
            "tokenizer":"keyword",
            "filter":[
              "pinyin_simple_filter",
              "edge_ngram_filter",
              "lowercase"
            ]
          },
          "pinyinFullIndexAnalyzer":{
            "tokenizer":"keyword",
            "filter":[
              "pinyin_full_filter",
              "lowercase"
            ]
          },
          "autoAnalyzer":{
            "type" :"custom",
            "tokenizer" : "keyword",
            "filter": ["edge_ngram_filter","lowercase"]
          }
        }
      }
  },
  "mappings":{
      "list":{
        "properties":{
          "name":{
            "type":"keyword",
            "fields":{
              "fpy":{
                "type":"text",
                "analyzer":"pinyinFullIndexAnalyzer",
                "term_vector":"with_positions_offsets"
              },
              "spy":{
                "type":"text",
                "analyzer":"pinyinSimpleIndexAnalyzer",
                "term_vector":"with_positions_offsets"
              },
              "cn":{
                "type":"text",
                "analyzer":"autoAnalyzer",
                "term_vector":"with_positions_offsets"
              }
            }
          }
        }
      }
    }
}
```
**添加测试数据**
```bash
POST _bulk/?refresh=true
{ "index" : { "_index" : "search_text", "_type" : "list" } }
{ "name": "北京工业大学"}
{ "index" : { "_index" : "search_text", "_type" : "list" } }
{ "name": "北京欢迎您"}
{ "index" : { "_index" : "search_text", "_type" : "list" } }
{ "name": "北京大学欢迎您"}
{ "index" : { "_index" : "search_text", "_type" : "list" } }
{ "name": "北京夏天真热"}
{ "index" : { "_index" : "search_text", "_type" : "list" } }
{ "name": "北京欢迎你么"}
{ "index" : { "_index" : "search_text", "_type" : "list" } }
{ "name": "北京工厂很少"}
```
**搜索测试**
测试拼音简写搜索如:bjxt(北京夏天)
```bash
POST /search_text/_search
{
  "query": {
    "bool": {
      "must": [
        {
          "bool": {
            "should": [
              {
                "match_phrase": {
                  "name.fpy": {
                    "query": "bjxt"
                  }
                }
              },
              {
                "term": {
                  "name.spy": "bjxt"
                }
              }
            ]
          }
        }
      ],
      "filter": []
    }
  },
  "from": "0",
  "size": "10",
  "_source": []
}
#outout
#{
#  "hits": {
#    "total": 1,
#    "max_score": 2.3372269,
#    "hits": [
#      {
#        "_index": "search_text",
#        "_type": "list",
#        "_id": "AWRaOaE11777q5uTF4wf",
#        "_score": 2.3372269,
#        "_source": {
#          "name": "北京夏天真热"
#        }
#      }
#    ]
#  }
#}

#搜索全拼beijinhuanying(北京欢迎)
#output
#  "hits": {
#    "total": 2,
#    "max_score": 1.6201785,
#    "hits": [
#      {
#        "_index": "search_text",
#        "_type": "list",
#        "_id": "AWRaOaE11777q5uTF4wd",
#        "_score": 1.6201785,
#        "_source": {
#          "name": "北京欢迎您"
#        }
#      },
#      {
#        "_index": "search_text",
#        "_type": "list",
#        "_id": "AWRaOaE11777q5uTF4wg",
#        "_score": 1.4264462,
#        "_source": {
#          "name": "北京欢迎你么"
#        }
#      }
#    ]
#  }
#} 

#搜索 北京工
POST /search_text/_search
{
  "query": {
    "bool": {
      "must": [
        {
          "term": {
            "name.cn": "北京工"
          }
        }
      ],
      "filter": []
    }
  },
  "from": "0",
  "size": "10",
  "_source": []
}
# output
#  "hits": {
#    "total": 2,
#    "max_score": 1.5621812,
#    "hits": [
#      {
#        "_index": "search_text",
#        "_type": "list",
#        "_id": "AWRaOaE11777q5uTF4wc",
#        "_score": 1.5621812,
#        "_source": {
#          "name": "北京工业大学"
#        }
#      },
#      {
#        "_index": "search_text",
#        "_type": "list",
#        "_id": "AWRaOaE11777q5uTF4wh",
#        "_score": 1.5621812,
#        "_source": {
#          "name": "北京工厂很少"
#        }
#      }
#    ]
#  }
#}
```
