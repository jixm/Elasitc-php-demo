### 设置mapping
```bash
PUT /text
{
  "settings": {
    "analysis": {
      "analyzer": {
        "cnIndexAnalyzer": {
          "type": "custom",
          "tokenizer": "keyword",
          "filter": [
            "edge_ngram_filter",
            "lowercase"
          ],
          "char_filter": []
        },
        "pinyiSimpleIndexAnalyzer": {
          "tokenizer": "keyword",
          "filter": [
            "pinyin_simple_filter",
            "edge_ngram_filter",
            "lowercase"
          ]
        },
        "pinyinFullIndexAnalyzer": {
          "type": "custom",
          "tokenizer": "keyword",
          "filter": [
            "ik_full_pinyin",
            "edge_ngram_filter",
            "lowercase"
          ]
        }
      },
      "filter": {
        "edge_ngram_filter": {
          "type": "edge_ngram",
          "min_gram": 1,
          "max_gram": 50
        },
        "pinyin_simple_filter": {
          "type": "pinyin",
          "keep_first_letter": true,
          "keep_separate_first_letter": false,
          "keep_full_pinyin": false,
          "keep_original": false,
          "limit_first_letter_length": 50,
          "lowercase": true
        },
        "ik_full_pinyin": {
          "type": "pinyin",
          "keep_first_letter": false,
          "keep_separate_first_letter": false,
          "keep_full_pinyin": true,
          "keep_joined_full_pinyin": true,
          "keep_original": false,
          "limit_first_letter_length": 50,
          "lowercase": true
        }
      }
    }
  },
  "mappings": {
    "list": {
      "properties": {
        "title": {
          "type": "keyword",
          "fields": {
            "cn": {
              "type": "text",
              "analyzer": "cnIndexAnalyzer"
            },
            "fpy": {
              "type": "text",
              "analyzer": "pinyinFullIndexAnalyzer"
            },
            "spy": {
              "type": "text",
              "analyzer": "pinyiSimpleIndexAnalyzer"
            }
          }
        }
      }
    }
  }
}

```


### 添加数据
```bash
PUT /test/list/1
{
  "title":"齐齐哈尔"
}

PUT /test/list/2
{
  "title":"北京"
}
PUT /test/list/3
{
  "title":"哈尔滨"
}
PUT /test/list/4
{
  "title":"北方工业"
}

```

### Search Query
* 中文

```bash
GET /test/_search
{
  "query":{
      "term":{
          "title.cn":"北" 
      }
  }
}
#  "hits": {
#    "total": 2,
#    "max_score": 0.25069216,
#    "hits": [
#      {
#        "_index": "test",
#        "_type": "list",
#        "_id": "2",
#        "_score": 0.25069216,
#        "_source": {
#          "title": "北京"
#        }
#      },
#      {
#        "_index": "test",
#        "_type": "list",
#        "_id": "4",
#        "_score": 0.25069216,
#        "_source": {
#          "title": "北方工业"
#        }
#      }
#    ]
#  }


GET /test/_search
{
  "query":{
      "term":{
          "title.cn":"北方" 
      }
  }
}
#  "hits": {
#    "total": 1,
#    "max_score": 0.9530774,
#    "hits": [
#      {
#        "_index": "test",
#        "_type": "list",
#        "_id": "4",
#        "_score": 0.9530774,
#        "_source": {
#          "title": "北方工业"
#        }
#      }
#    ]
#  }

```

* 拼音简写

```bash
GET /test/_search
{
  "query":{
      "term":{
          "title.spy":"bf" 
      }
  }
}
GET /test/_search
{
  "query":{
      "term":{
          "title.spy":"bfgy" 
      }
  }
}

# "hits": {
#    "total": 1,
#    "max_score": 0.9530774,
#    "hits": [
#      {
#        "_index": "test",
#        "_type": "list",
#        "_id": "4",
#        "_score": 0.9530774,
#        "_source": {
#          "title": "北方工业"
#        }
#      }
#    ]
#  }
```

* 全拼

```bash
GET /test/_search
{
  "query":{
      "term":{
          "title.fpy":"bei" 
      }
  }
}
#"hits": {
#    "total": 2,
#    "max_score": 0.33215258,
#    "hits": [
#      {
#        "_index": "test",
#        "_type": "list",
#        "_id": "2",
#        "_score": 0.33215258,
#        "_source": {
#          "title": "北京"
#        }
#      },
#      {
#        "_index": "test",
#        "_type": "list",
#        "_id": "4",
#        "_score": 0.32347375,
#        "_source": {
#          "title": "北方工业"
#        }
#      }
#    ]
#  }



GET /test/_search
{
  "query":{
      "term":{
          "title.fpy":"beijing" 
      }
  }
}


#  "hits": {
#    "total": 1,
#    "max_score": 1.0775324,
#    "hits": [
#      {
#        "_index": "test",
#        "_type": "list",
#        "_id": "2",
#        "_score": 1.0775324,
#        "_source": {
#          "title": "北京"
#        }
#      }
#    ]
#  }

```



