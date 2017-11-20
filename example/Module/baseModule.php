<?php
namespace Module;
use Module\Connection;
class BaseModule {
    protected $_client;
    public function __construct() {
        $this->_client = Connection::getClient();
    }
}