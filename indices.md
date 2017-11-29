# 索引相关例子

## create index
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
# 也可以创建索引时指定
curl -XPUT 'localhost:9200/test?wait_for_active_shards=2&pretty'

```

## delete index

```bash
curl -XDELETE 'localhost:9200/twitter?pretty'

# 别名不能用来删除一个索引 通配符表达式仅解析为匹配具体的索引。
# 通过使用_all或*作为索引 删除索引
# 删除多个索引，使用逗号分隔列表

# 禁止通配符和_all 删除所有 ,这个设置也可以通过update setting api设置
action.destructive_requires_name = true。

```

## get index

```bash
# 多个索引用逗号隔开
curl -XGET 'localhost:9200/twitter?pretty'

```
output:

```bash
{
  "test" : {
    "aliases" : { },
    "mappings" : {
      "test" : {
        "properties" : {
          "end_time" : {
            "type" : "date",
            "store" : true,
            "format" : "yyyy-MM-dd HH:mm:ss"
          },
          "id" : {
            "type" : "integer",
            "store" : true
          },
          "operate_time" : {
            "type" : "long"
          },
          "status" : {
            "type" : "integer",
            "store" : true
          },
          "title" : {
            "type" : "text",
            "boost" : 10.0,
            "store" : true,
            "term_vector" : "with_positions_offsets",
            "analyzer" : "ik_max_word"
          },
          "uid" : {
            "type" : "integer",
            "store" : true
          }
        }
      }
    },
    "settings" : {
      "index" : {
        "number_of_shards" : "1",
        "provided_name" : "meeting",
        "creation_date" : "1504858942959",
        "analysis" : {
          "analyzer" : {
            "ik" : {
              "tokenizer" : "ik_max_word"
            }
          }
        },
        "number_of_replicas" : "0",
        "uuid" : "HikmBXBLTZW7tIOoK2RyvA",
        "version" : {
          "created" : "5050199"
        }
      }
    }
  }
}

```

## indices exists

```bash
# return code 404 or 200
curl -XHEAD '127.0.0.1:9200/twitter?pretty'


```
## open/close index
关闭后的索引几乎没有开销(除了维护它的元数据)
```bash
curl -XPOST 'localhost:9200/my_index/_close?pretty'
curl -XPOST 'localhost:9200/my_index/_open?pretty'

# 可以通过集群设置API禁用关闭索引。默认值是true。
cluster.indices.close.enable=false

```

## <font style="background:green>shrink index </font>
> elasticsearch索引的shard数是固定的，设置好了之后不能修改，只能在创建索引的时候设置好，并且数据进来了之后就不能进行修改，如果要修改，只能重建索引。
> Shrink接口，它可将分片数进行收缩成它的因数，如之前你是15个分片，你可以收缩成5个或者3个又或者1个，那么可以在写入压力非常大阶段，设置足够多的索引，充分利用shard的并行写能力，索引写完之后收缩成更少的shard，提高查询性能。

```bash
curl -XPUT 'localhost:9200/my_source_index/_settings?pretty' -H 'Content-Type: application/json' -d'
{
  "settings": {
    "index.routing.allocation.require._name": "shrink_node_name", 
    "index.blocks.write": true # 防止对此索引写入
    "index.codec":"best_compression"
  }
}
'
# 该索引在所有分片中总共不得包含超过2,147,483,519个文档，这些文档将被缩减为目标索引中的一个分片，因为这是可以放入单个分片的最大文档数量。 
POST my_source_index/_shrink/my_target_index


curl -XPOST 'localhost:9200/my_source_index/_shrink/my_target_index?pretty' -H 'Content-Type: application/json' -d'
{
  "settings": {
    "index.number_of_replicas": 1,
    "index.number_of_shards": 1, 
    "index.codec": "best_compression" 
  },
  "aliases": {
    "my_search_indices": {}
  }
}
'# index.codec 默认值使用LZ4压缩压缩存储的数据，但是这可以设置为best_compression，使用DEFLATE获得更高的压缩比，代价是存储的字段性能较慢。如果您正在更新压缩类型，则会在合并片段之后应用新的压缩类型。可以使用强制合并来强制段合并。

```
Shrinking works as follows:
* First, it creates a new target index with the same definition as the source index, but with a smaller number of primary shards.
* Then it hard-links segments from the source index into the target index. (If the file system doesn’t support hard-linking, then all segments are copied into the new index, which is a much more time consuming process.)
* Finally, it recovers the target index as though it were a closed index which had just been re-opened.


## <font style="background:green>Rollover index </font>
















