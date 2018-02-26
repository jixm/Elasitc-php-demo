## match
```bash
POST /search_text/list/_search
{
  "query":{
      "match":{
          "name.fpy":{
              "query":"mingtian",
              "operator": "and",
              "boost":1,
              "type":"boolean" # boolean,phrase,phrase_prefix,
              "slop":1
          }
      }
  }
}
```


## multi_match
```bash
{
    "query":{
        "bool":{
            "must":{
                "multi_match":{
                    "query":"测试",
                    "type":"best_fields",
                    "fields":[
                        "description^5",
                        "title^8"
                    ]
                }
            },
            "must_not":[],
            "filter":[]
        }
    },
    "highlight":{
        "pre_tags":[
            "<#@ style='adsf'>"
        ],
        "post_tags":[
            "<#@>"
        ],
        "fields":{
            "title":{
                "type":"fvh"
            },
            "description":{
                "type":"fvh"
            }
        }
    },
    "from":"0",
    "size":"10",
    "_source":[
        "id",
        "title",
        "description"
    ]
}
```
#### 希望完全匹配的文档占的评分比较高，则需要使用best_fields
```bash
{
  "query": {
    "multi_match": {
      "query": "明天天气晴朗",
      "type": "best_fields",
      "fields": [
        "title",
        "content"
      ],
      "tie_breaker": 0.3
    }
  }
}

```
意思就是完全匹配的文档评分会比较靠前，如果只匹配一个词的文档评分乘以0.3的系数

#### 我们希望越多字段匹配的文档评分越高，就要使用most_fields
```bash
{
  "query": {
    "multi_match": {
      "query": "明天天气晴朗",
      "type": "most_fields",
      "fields": [
        "title",
        "content"
      ]
    }
  }

```
#### 我们会希望这个词条的分词词汇是分配到不同字段中的，那么就使用cross_fields
```bash
{
  "query": {
    "multi_match": {
      "query": "明天天气晴朗",
      "type": "cross_fields",
      "fields": [
        "tag",
        "content"
      ]
    }
  }
}
```
## function_score
```bash
{
    "query":{
        "function_score":{
            "query":{
                "bool":{
                    "must":{
                        "multi_match":{
                            "query":"明",
                            "type":"best_fields",
                            "fields":[
                                "career.pinyin^3",
                                "career.cn^8",
                                "name.pinyin^3",
                                "name.cn^8"
                            ]
                        }
                    },
                    "must_not":[],
                    "filter":[]
                }
            },
            "field_value_factor":{
                "field":"weight",
                "modifier":"log2p"
            }
        }
    },
    "from":"0",
    "size":"10",
    "_source":[
        "id"
    ]
}

```