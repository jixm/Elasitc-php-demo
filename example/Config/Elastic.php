<?php
namespace Config;
/**
 * Elastic Config
 * 
 */
class Elastic
{
    // 搜索配置.
    public static $search = array(
        [
            'host' => '10.10.10.44',
            'port' => '9200',
            'scheme' => 'http',
            'user' => 'elastic', // elastic   scimall
            'pass' => 'elastic',
        ]

    );
}