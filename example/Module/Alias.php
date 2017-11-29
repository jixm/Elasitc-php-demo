<?php
namespace Module;
/**
 * 别名操作
 * 
 */
class Alias extends \Module\BaseModule{

    /**
     * add alias
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2017-11-20
     * @param    [type] $index [description]
     * @param    [type] $alias [description]
     * @return   [type] [description]
     */
    public function  create($index,$alias) {
        return $this->_client
            -> indices()
            -> putAlias([
                    'index' => $index,
                    'alias' => $alias
                ]);
    }

    /**
     * delete alias
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2017-11-20
     * @param    [type] $index [description]
     * @param    [type] $alias [description]
     * @return   [type] [description]
     */
    public function delete($index,$alias) {
        return $this->_client
            -> indices()
            -> deleteAlias([
                    'index' => $index,
                    'name'  => $alias
                ]);
    }

    /**
     * get alias
     * @Author   ji.xiaoming@scimall.org.cn
     * @DateTime 2017-11-20
     * @param    [type] $index [description]
     * @return   [type] [description]
     */
    public function get($index) {
        return $this->_client
            -> indices()
            -> getAlias([
                    'index' => $index,
                ]); 
    }
    /*
    $params['body'] = array(
    'actions' => array(
        array(
            'add' => array(
                'index' => 'myindex',
                'alias' => 'myalias'
            )
        )
    )
);
$client->indices()->updateAliases($params);
     */
}