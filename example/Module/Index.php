<?php
/**
 * 索引数据
 * @author ji.xiaoming  
 * @date(2017-11-20)
 */
namespace Module;

use Module\Connection;
class Index {

    private $_client;

    public function __construct() {
        $this->_client = Connection::getClient();
    }

    public function index() {

    }

    public function update() {

    }

    public function delete() {

    }

    public function updatebyQuery() {

    }
    
}

