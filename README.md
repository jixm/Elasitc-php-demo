## Some simple uses of ElasticSearch


```php
<?php

include './Autoload/Autoload.php';
use Module\Mapping;
use Module\Index;
use Module\Alias;

$index = 'test_v1';
$index_alias = 'test';
$type  = 'test';
$doc   =  array(
    'title'=>'test',
    'description'=>'test',
    'content'=>'content'
);

//创建索引
$response = (new Mapping)
    -> create($index);

//更新索引
$response = (new Mapping)
    -> update($index,$type);
//get mapping
$response = (new Mapping)
    -> getMapping(['index'=>$index]);

//索引数据
$response = (new Index)
    -> index(
        $index,
        $type,
        $doc
    );

//update index data
$id = 'AV_YlmfX4wDUniPcUFQm';
$response = (new Index)
    -> update(
        $index,
        $type
        $id,
        $doc
    );

// create alias for index
$response = (new Alias) 
    -> create($index,$type);

var_dump($response);

```
> 更多操作请参考example下代码