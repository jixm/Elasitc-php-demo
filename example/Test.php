<?php

include './Autoload/Autoload.php';
use Module\Mapping;
use Module\Index;
use Module\Alias;
$response = (new Mapping)->create('test123');
$response = (new Mapping)->update('test123','test');
$response = (new Mapping)->getMapping(array('index'=>'test123'));

$response = (new Index)->index(
        'test123',
        'test',
        ['title'=>'test','description'=>'test','content'=>'content']
    );

$id = 'AV_YlmfX4wDUniPcUFQm';
$response = (new Index)->update(
    'test123',
    'test',
    $id,
    ['title'=>'test1','description'=>'test1','content'=>'content1']
    );


$response = (new Alias) ->create('test123','test');
var_dump($response);