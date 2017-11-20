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
            'host' => '192.168.33.11',
            'port' => '9200',
            'scheme' => 'http',
            'user' => 'elastic', // elastic   scimall
            'pass' => 'elastic',
        ]

    );
}