<?php
require dirname(APP_PATH).'/src/vendor/autoload.php';
use Elasticsearch\ClientBuilder;
class Connection {

    protected static $client = null;

    public static function getClient( $config = array()) {
        if( is_null( self::$client ) ) {
            if( !$config ) {
                include './Config.php';
                $config = \Config::$search;
            }
            self::$client = ClientBuilder::create()
                -> setHosts($config)
                -> build();
        }
        return self::$client;
    }

}