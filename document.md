## 目录
- [bulk](#bulk)
- [update](#update)
- [取回多个文档](#取回多个文档)

## bulk
```bash
POST _bulk
{ "create": { "_index": "my_index", "_type": "my_type", "_id": 1 } }
{ "title": "周星驰最新电影" }
{ "update" : {"_id" : "1", "_type" : "_doc", "_index" : "index1", "retry_on_conflict" : 3} }
{ "doc" : {"field" : "value"} }
{ "delete" : { "_index" : "test", "_type" : "_doc", "_id" : "2" } }
```

## update
**simple update**

```bash
PUT test/_doc/1
{
    "counter" : 1,
    "tags" : ["red"]
}
```
**bulk**

```bash
POST _bulk
{ "update" : {"_id" : "1", "_type" : "_doc", "_index" : "index1", "retry_on_conflict" : 3} }
{ "doc" : {"field" : "value"} }
{ "update" : { "_id" : "0", "_type" : "_doc", "_index" : "index1", "retry_on_conflict" : 3} }
{ "script" : { "source": "ctx._source.counter += params.param1", "lang" : "painless", "params" : {"param1" : 1}}, "upsert" : {"counter" : 1}}
{ "update" : {"_id" : "2", "_type" : "_doc", "_index" : "index1", "retry_on_conflict" : 3} }
{ "doc" : {"field" : "value"}, "doc_as_upsert" : true }
{ "update" : {"_id" : "3", "_type" : "_doc", "_index" : "index1", "_source" : true} }
{ "doc" : {"field" : "value"} }
{ "update" : {"_id" : "4", "_type" : "_doc", "_index" : "index1"} }
{ "doc" : {"field" : "value"}, "_source": true}
```
**scripted updates**

```bash
POST test/_doc/1/_update
{
    "script" : {
        "source": "ctx._source.counter += params.count",
        "lang": "painless",
        "params" : {
            "count" : 4
        }
    }
}
```
**添加新字段**

```bash
POST test/_doc/1/_update
{
    "script" : "ctx._source.new_field = 'value_of_new_field'"
}
```
**删除字段**

```bash
POST test/_doc/1/_update
{
    "script" : "ctx._source.remove('new_field')"
}
```
**带条件**

```bash
# 如果包含green删除,否则不做操作
POST test/_doc/1/_update
{
    "script" : {
        "source": "if (ctx._source.tags.contains(params.tag)) { ctx.op = 'delete' } else { ctx.op = 'none' }",
        "lang": "painless",
        "params" : {
            "tag" : "green"
        }
    }
}
```
**部分更新**

```bash
POST test/_doc/1/_update
{
    "doc" : {
        "name" : "new_name"
    }
}
```
**Detecting noop updates**
> 如果字段没有更新,返回noop
```bash
POST test/_doc/1/_update
{
    "doc" : {
        "name" : "new_name"
    }
}

## 禁用
POST test/_doc/1/_update
{
    "doc" : {
        "name" : "new_name"
    },
    "detect_noop": false
}

```
**Upserts**
```bash
POST test/_doc/1/_update
{
    "script" : {
        "source": "ctx._source.counter += params.count",
        "lang": "painless",
        "params" : {
            "count" : 4
        }
    },
    "upsert" : {
        "counter" : 1
    }
}
```
contents of doc as the upsert value
```bash
POST test/_doc/1/_update
{
    "script" : {
        "source": "ctx._source.counter += params.count",
        "lang": "painless",
        "params" : {
            "count" : 4
        }
    },
    "upsert" : {
        "counter" : 1
    }
}
```
**Update By Query Api**
[https://www.elastic.co/guide/en/elasticsearch/reference/6.2/docs-update-by-query.html]
```bash
POST twitter/_update_by_query
{
  "script": {
    "source": "ctx._source.likes++",
    "lang": "painless"
  },
  "query": {
    "term": {
      "user": "kimchy"
    }
  }
}
```

## 取回多个文档 _mget

```bash
GET /_mget
{
   "docs" : [
      {
         "_index" : "website",
         "_type" :  "blog",
         "_id" :    2
      },
      {
         "_index" : "website",
         "_type" :  "pageviews",
         "_id" :    1,
         "_source": "views"
      }
   ]
}

# index,type相同
GET /website/blog/_mget
{
   "ids" : [ "2", "1" ]
}
```
