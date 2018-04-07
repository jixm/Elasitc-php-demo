## 目录
- [slowlog](#slowlog)
- [设置字段总数](#设置字段总数)
- [设置offset](#设置offset)
## slow log
```bash
# 索引
curl -XPUT 'localhost:9200/testindex-slowlogs/_settings' -H 'Content-Type: application/json' -d '{
     "index.indexing.slowlog.threshold.index.warn": "10s", #索引数据超过10秒产生一个warn日志
     "index.indexing.slowlog.threshold.index.info": "5s", #索引数据超过5秒产生一个info日志
     "index.indexing.slowlog.threshold.index.debug": "2s",#索引数据超过2秒产生一个ddebug日志
     "index.indexing.slowlog.threshold.index.trace": "500ms",#索引数据超过500毫秒产生一个trace日志
     "index.indexing.slowlog.level": "trace",
     "index.indexing.slowlog.source": "1000"
}'

# 查询
curl -XPUT 'localhost:9200/testindex-slowlogs/_settings' -H 'Content-Type: application/json' -d '{
    "index.search.slowlog.threshold.query.warn": "0ms",
    "index.search.slowlog.threshold.query.info": "0ms",
    "index.search.slowlog.threshold.query.debug": "0ms",
    "index.search.slowlog.threshold.query.trace": "0ms",
    "index.search.slowlog.threshold.fetch.warn": "0ms",
    "index.search.slowlog.threshold.fetch.info": "0ms",
    "index.search.slowlog.threshold.fetch.debug": "0ms",
    "index.search.slowlog.threshold.fetch.trace": "0ms"
}'

```

## 设置字段总数
```bash
PUT my_index/_settings
{
      "index.mapping.total_fields.limit": 2000
}
```

## 设置offset
```bash
curl -XPUT ' http://180.76.135.40:9200/_all/_settings?preserve_existing=true' -d '{
  "index.max_result_window" : "1000000"
}'
```