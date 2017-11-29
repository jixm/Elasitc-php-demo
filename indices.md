# 索引相关例子

## create
```bash
curl -XPUT 'localhost:9200/twitter?pretty' -H 'Content-Type: application/json' -d'
{
    "settings" : {
        "index" : {
            "number_of_shards" : 3,
            "number_of_replicas" : 2
        }
    }
}
'

# 假设只开启两个节点,wait_for_active_shards设置为3,那么活动分片数只有2个,，所以是不够的，会等待新的活动节点的到来
# 这是插入文档操作,会处于等待状态,不会马上返回,开启第三个节点后文档的存储马上返回
#
# 当分片副本不足时会怎样？Elasticsearch会等待更多的分片出现。默认等待一分钟。如果需要，你可以设置timeout参数让它终止的更早：100表示100毫秒，30s表示3#0秒。

PUT test
{
    "settings": {
        "index.write.wait_for_active_shards": "2"
    }
}
```
