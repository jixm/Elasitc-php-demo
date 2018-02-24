## 聚合 Aggs
```bash
POST /test/list/_search
{
    "query":{
        "bool":{
            "filter":[
                {
                    "terms":{
                        "uuid":[
                            "U1802779"
                        ]
                    }
                },
                {
                    "range":{
                        "month":{
                            "gte":"201710"
                        }
                    }
                }
            ]
        }
    },
    "size":0,
    "aggs":{
        "group":{
            "terms":{
                "field":"month"
            }
        }
    }
}
# output:##############
{
  "took": 0,
  "timed_out": false,
  "_shards": {
    "total": 1,
    "successful": 1,
    "failed": 0
  },
  "hits": {
    "total": 2,
    "max_score": 0,
    "hits": []
  },
  "aggregations": {
    "group": {
      "doc_count_error_upper_bound": 0,
      "sum_other_doc_count": 0,
      "buckets": [
        {
          "key": 201802,
          "doc_count": 2
        }
      ]
    }
  }
}
```
## Aggs top_hits
```bash
POST /test/list/_search
{
    "size":0,
    "aggs":{
        "group":{
            "terms":{
                "field":"uuid",
                "size":10,
                "order":{
                    "_count":"desc"
                }
            },
            "aggs":{
                "top_detail_hits":{
                    "top_hits":{
                        "_source":{
                            "includes":[
                                "create_time",
                                "id"
                            ]
                        },
                        "size":1
                    }
                }
            }
        }
    },
    "query":{
        "bool":{
            "filter":[
                {
                    "terms":{
                        "uuid":[
                            "U1802779"
                        ]
                    }
                },
                {
                    "range":{
                        "create_time":{
                            "gte":1234566
                            
                        }
                    }
                }
            ]
        }
    }
}
# output: #########################################
{
  "took": 0,
  "timed_out": false,
  "_shards": {
    "total": 1,
    "successful": 1,
    "failed": 0
  },
  "hits": {
    "total": 2,
    "max_score": 0,
    "hits": []
  },
  "aggregations": {
    "group": {
      "doc_count_error_upper_bound": 0,
      "sum_other_doc_count": 0,
      "buckets": [
        {
          "key": "U1802779",
          "doc_count": 2,
          "top_detail_hits": {
            "hits": {
              "total": 2,
              "max_score": 1.4e-45,
              "hits": [
                {
                  "_index": "test",
                  "_type": "list",
                  "_id": "AWFqXifBN8F409SxAeRA",
                  "_score": 0,
                  "_source": {
                    "create_time": 1517908010,
                    "id": 1435753
                  }
                }
              ]
            }
          }
        }
      ]
    }
  }
}
```
## collapse
```bash
POST /test/list/_search
{
    "query":{
        "bool":{
            "must":[],
            "should":[],
            "must_not":[],
            "filter":[
                
            ]
        }
    },
    "collapse":{
        "field":"type"
    }
}
# output:########################3
  "hits": {
    "total": 243,
    "max_score": null,
    "hits": [
      {
        "_index": "test",
        "_type": "list",
        "_id": "AWAMMZ7P4wDUniPcXxPw",
        "_score": 1,
        "_source": {
          "type":1,
          "name":"name"
          ...
        },
        "fields": {
          "type": [
                1
          ]
        }
      },
      ...
```

## collapse inner_hits
```bash
GET /test/list/_search
{
    "query":{
        "bool":{
            "must":[],
            "should":[],
            "must_not":[],
            "filter":[
                
            ]
        }
    },
    "size":"1",
    "_source":[
    
    ],
    "sort":{
        "msectime":{
            "order":"desc"
        }
    },
    "collapse":{
        "field":"type",
         "inner_hits":{
            "name":"top",
            "size": 2,
            "sort": {
                "msectime":"desc"
            }
         }
    }
}
# output:##################3
{
  "took": 1,
  "timed_out": false,
  "_shards": {
    "total": 3,
    "successful": 3,
    "failed": 0
  },
  "hits": {
    "total": 243,
    "max_score": null,
    "hits": [
      {
        "_index": "test",
        "_type": "list",
        "_id": "AWHGiER8N8F409SxGX2L",
        "_score": null,
        "_source": {
           "type":1,
            "name":"name"
           ...
        },
        "fields": {
          "type": [
                1
          ]
        },
        "sort": [
          "1519454274819.0"
        ],
        "inner_hits": {
          "top": {
            "hits": {
              "total": 7,
              "max_score": null,
              "hits": [
                {
                  "_index": "test",
                  "_type": "list",
                  "_id": "AWHGiER8N8F409SxGX2L",
                  "_score": null,
                  "_source": {
                      "type":1,
                      "name":"name"
                      ...
                  },
                  "sort": [
                    "1519454274819.0"
                  ]
                },
                {
                  "_index": "test",
                  "_type": "list",
                  "_id": "AWFPJ38CN8F409Sx-Zp3",
                  "_score": null,
                  "_source": {
                     "type":1,
                     "name":"name"
                     ...
                  },
                  "sort": [
                    "1517451443995.0"
                  ]
                }
              ]
            }
          }
        }
      }
    ]
  }
}
```
##  cardinality去重
```bash
{
    "query":{
        "bool":{
            "filter":[
                {
                    "terms":{
                        "uuid":[
                            "U1802779"
                        ]
                    }
                },
                {
                    "range":{
                        "month":{
                            "gte":"201710"
                        }
                    }
                }
            ]
        }
    },
    "size":0,
    "aggs":{
        "group":{
            "cardinality":{
                "field":"month"
            }
        }
    }
}
## output:
# 一共有几个月份有数据
"aggregations": {
    "group": {
      "value": 1
    }
}
# 如果不用cardinality
"aggs":{
    "group":{
        "cardinality":{
            "field":"month"
        }
    }
}
#output:每个月数据数据量
"aggregations": {
    "group": {
      "doc_count_error_upper_bound": 0,
      "sum_other_doc_count": 0,
      "buckets": [
        {
          "key": 201802,
          "doc_count": 2
        }
      ]
    }
}
````