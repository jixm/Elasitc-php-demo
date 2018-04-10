## 目录
- [slowlog](#slowlog)
- [设置字段总数](#设置字段总数)
- [设置offset](#设置offset)
- [refresh_interval](#refresh_interval)
- [translog](#translog)
- [段合并速率](#段合并速率)
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

## refresh_interval
```bash
# 禁用
PUT /my_logs/_settings
{ "refresh_interval": -1 }
# 1s
PUT /my_logs/_settings
{ "refresh_interval": "1s" }

```
## translog
```bash
PUT /my_index/_settings
{
    "index.translog.durability": "async",
    "index.translog.sync_interval": "5s"
}
```

## 段合并速率段合并速率
```bash
PUT /_cluster/settings
{
    "persistent" : {
        "indices.store.throttle.max_bytes_per_sec" : "100mb"
    }
}

# 在做批量导入，完全不在意搜索，你可以彻底关掉合并限流。这样让你的索引速度跑到你磁盘允许的极限：
PUT /_cluster/settings
{
    "transient" : {
        "indices.store.throttle.type" : "none" 
    }
}

# 这个设置允许 max_thread_count + 2 个线程同时进行磁盘操作
index.merge.scheduler.max_thread_count: 1


# 这可以在一次清空触发的时候在事务日志里积累出更大的段,减少磁盘IO
index.translog.flush_threshold_size
```