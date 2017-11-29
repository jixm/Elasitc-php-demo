<?php
/**
 * elastic服务连接
 * @author ji.xiaoming  
 * @date(2017-11-20)
 */
namespace Module;
require dirname(APP_PATH).'/src/vendor/autoload.php';
use Elasticsearch\ClientBuilder;
use Config\Elastic;
class Connection {

    protected static $client = null;

    public static function getClient( $config = array()) {
        if( is_null( self::$client ) ) {
            if( !$config ) {
               
                $config = Elastic::$search;
            }
            self::$client = ClientBuilder::create()
                -> setHosts($config)
                -> build();
        }
        return self::$client;
    }

}