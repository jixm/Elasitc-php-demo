### 1.nfs安装
> server 192.168.33.14
> client 192.168.33.11

1. server install nfs
```bash
yum install nfs-utils rpcbind

systemctl status rpcbind.service

systemctl restart rpcbind.service

systemctl start nfs.service

exportfs -arv

sudo  systemctl stop firewalld
```
设置开机启动
```bash
chkconfig rpcbind on
chkconfig nfs on
chkconfig --list rpcbind
chkconfig --list nfs
```
配置文件
vim /etc/exports
```bash
#/backup/ 192.168.33.0/24(rw,sync,all_squash,anonuid=501,anongid=501)
/backup/ 192.168.33.0/24(rw,sync,all_squash)
```
> rw ：读写；
> ro ：只读；
> sync ：同步模式，内存中数据时时写入磁盘；
> async ：不同步，把内存中数据定期写入磁盘中；
> no_root_squash ：加上这个选项后，root用户就会对共享的目录拥有至高的权限控制，就像是对本机的目录操作一样。不安全，不建议使用；
> root_squash：和上面的选项对应，root用户对共享目录的权限不高，只有普通用户的权限，即限制了root；
> all_squash：不管使用NFS的用户是谁，他的身份都会被限定成为一个指定的普通用户身份；
> anonuid/anongid ：要和root_squash 以及all_squash一同使用，用于指定使用NFS的用户限定后的uid和gid，前提是本机的/etc/passwd中存在这个uid和gid。

2.客户端

```bash
yum install -y nfs-utils
```
查看服务端共享目录
```bash
showmount -e 192.168.137.14
#Export list for 192.168.33.14:
#/backup 192.168.33.0/24
```

挂载

```bash
mount -t nfs 192.168.137.10:/home/ /mnt/

# 不加锁
# 
mount -t nfs -o nolock 192.168.137.10:/tmp/ /test/
# -a ：全部挂载或者卸载；

# -r ：重新挂载；

# -u ：卸载某一个目录；

# -v ：显示共享的目录；
```
```bash
df -h
```

我们还可以把要挂载的nfs目录写到client上的/etc/fstab文件中，挂载时只需要执行 mount -a 即可。在 /etc/fstab里加一行:
```bash
192.168.137.14:/tmp/            /test        nfs       nolock  0 0
```
使用mount -a 即可挂载 卸载

umount  /test/


### 2.备份
** 仓库设置 **
```bash
curl -XPUT '192.168.33.11:9200/_snapshot/bak0920?pretty' -d '{
    "type": "fs",
    "settings": {
        "location": "/backup",
        "compress": true,

        #当快照数据进入仓库时，这个参数控制这个过程的限流情况。默认是每秒 20mb 
        "max_snapshot_bytes_per_sec" : "50mb", 

        #当从仓库恢复数据时，这个参数控制什么时候恢复过程会被限流以保障你的网络不会被占满。默认是每秒 `20mb`
        "max_restore_bytes_per_sec" : "50mb"
    }
}'
```
** 备份全部 **
```bash
curl -XPUT "192.168.33.11:9200/_snapshot/bak0920/snapshot_all?wait_for_completion=true&pretty"
```
** 备份指定索引 **
```bash
curl -XPUT "192.168.33.11:9200/_snapshot/bak0920/bank?wait_for_completion=true&pretty" -d '{
    "indices": ["bank"],
    "ignore_unavailable": true,
    "include_global_state": false
}'
```
> 这个会阻塞调用直到快照完成。注意大型快照会花很长时间才返回。

** 查看快照信息 **
```bash
curl -XGET "192.168.33.11:9200/_snapshot/bak0920/bank?pretty"
curl -XGET "192.168.33.11:9200/_snapshot/bak0920/_all?pretty"
```

** 快照删除 **

```bash
#delete
curl -XDELETE "192.168.33.11:9200/_snapshot/bak0920/bank?pretty"

#delete all
curl -XDELETE "192.168.33.11:9200/_snapshot/bak0920?pretty"
```

## 3.恢复

首先要关闭要恢复的索引

```bash
 curl -XPOST "192.168.33.11:9200/bank/_close"
```

```bash
POST _snapshot/bak0920/bank/_restore
```

```bash
curl -XPOST "192.168.33.11:9200/_snapshot/bak0920/snapshot_all/_restore?pretty" -d '{
    "indices": "bank",
    "ignore_unavailable": true,
    "include_global_state": false,

    #查找所提供的模式能匹配上的正在恢复的索引。
    "rename_pattern": "bank",

    #把它们重命名成替代的模式。
    "rename_replacement": "bank"

}'
```
监控恢复状态
```bash
//recovery
curl -XGET http://192.168.33.11:9200/restored_bank/_recovery?pretty=true
//get status
curl -XGET 'localhost:9200/_snapshot/_status?pretty'
```












3.删除索引在恢复