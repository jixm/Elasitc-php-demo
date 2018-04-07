## 目录
- [match](#match)
- [multi_match](#multi_match)
- [function_score](#function_score)
- [Bool Query](#Bool Query)
- [Ids Query](#Ids Query)
- [验证查询](#验证查询)
- [是否存在](#是否存在)
- [Delete By Query](#Delete By Query)
- [查询调试](#查询调试)
- [分词查看](#分词查看)

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
**希望完全匹配的文档占的评分比较高，则需要使用best_fields**

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

**我们希望越多字段匹配的文档评分越高，就要使用most_fields**
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
**我们会希望这个词条的分词词汇是分配到不同字段中的，那么就使用cross_fields**
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

## Bool Query
```bash
# 用于某个字段有值的情况和某个字段缺值的情况
{
    "exists":   {
        "field":    "title"
    }
}
# 下面的查询用于查找 title 字段匹配 how to make millions 并且不被标识为 spam 的文档。那些被标识为 starred 或在2014之后的文档，将比另外那些文档拥有更高的排名。如果 _两者_ 都满足，那么它排名将更高：
{
    "bool": {
        "must":     { "match": { "title": "how to make millions" }},
        "must_not": { "match": { "tag":   "spam" }},
        "should": [
            { "match": { "tag": "starred" }},
            { "range": { "date": { "gte": "2014-01-01" }}}
        ]
    }
}
# bool过滤
#  SELECT product
#  FROM   products 
#  WHERE  (price = 20 OR productID = "XHDK-A-1293-#fJ3")
#  AND  (price != 30)
GET /my_store/products/_search
{
   "query" : {
      "filtered" : { 
         "filter" : {
            "bool" : {
              "should" : [
                 { "term" : {"price" : 20}}, 
                 { "term" : {"productID" : "XHDK-A-1293-#fJ3"}} 
              ],
              "must_not" : {
                 "term" : {"price" : 30} 
              }
           }
         }
      }
   }
}
# 嵌套过滤  
#SELECT document
#FROM   products
#WHERE  productID      = "KDKE-B-9947-#kL5"
#  OR (     productID = "JODL-X-1937-#pV7"
#       AND price     = 30 )
GET /my_store/products/_search
{
   "query" : {
      "filtered" : {
         "filter" : {
            "bool" : {
              "should" : [
                { "term" : {"productID" : "KDKE-B-9947-#kL5"}}, 
                { "bool" : { 
                  "must" : [
                    { "term" : {"productID" : "JODL-X-1937-#pV7"}}, 
                    { "term" : {"price" : 30}} 
                  ]
                }}
              ]
           }
         }
      }
   }
}

# 通过多个不同的标准来过滤你的文档，
#range不参与评分,性能好些,结果会被缓存到内存中以便快速读取,评分查询（scoring queries）不仅仅要找出 
# 匹配的文档，还要计算每个匹配文档的相关性，计算相关性使得它们比不评分查询费力的多。同时，查询结果并不缓存
# bool也可以构建不参与评分
{
    "bool": {
        "must":     { "match": { "title": "how to make millions" }},
        "must_not": { "match": { "tag":   "spam" }},
        "should": [
            { "match": { "tag": "starred" }}
        ],
        "filter": {
          "bool": { 
              "must": [
                  { "range": { "date": { "gte": "2014-01-01" }}},
                  { "range": { "price": { "lte": 29.99 }}}
              ],
              "must_not": [
                  { "term": { "category": "ebooks" }}
              ]
          }
        }
    }
}
# 可以使用它来取代只有 filter 语句的 bool 查询。在性能上是完全相同的，但对于提高查询简洁性和清晰度有很大帮助。
{
    "constant_score":   {
        "filter": {
            "term": { "category": "ebooks" } 
        }
    }
}

```

## Ids Query
```bash
#Filters documents that only have the
curl -XGET 'localhost:9200/_search?pretty' -H 'Content-Type: application/json' -d'
{
  "query": {
      "ids" : {
          "type" : "my_type",
          "values" : ["1", "4", "100"]
      }
  }
}'

```
## 验证查询
```bash
GET /gb/tweet/_validate/query
{
 "query": {
    "tweet" : {
       "match" : "really powerful"
    }
 }
}
```
## 是否存在
```bash
curl -i -XHEAD http://localhost:9200/website/blog/123

```
## Delete By Query
```bash
curl -XPOST http://localhost:9200/_delete_by_query -d 
'{ "query":{ "match":{ "message":"some message" }}'
```

## 查询调试查询调试
```bash
# 当 explain 选项加到某一文档上时， explain api 会帮助你理解为何这个文档会被匹配，更重要的是，一个文档为何没有被匹配。
# "failure to match filter: cache(user_id:[2 TO 2])"
GET /us/tweet/12/_explain
{
   "query" : {
      "bool" : {
         "filter" : { "term" :  { "user_id" : 2           }},
         "must" :  { "match" : { "tweet" :   "honeymoon" }}
      }
   }
}

```
## 分词查看
```bash
POST /my_index/_analyze
{
  "field":"name",
  "analyzer":"pinyin",
  "text":"明天"
  
}
```