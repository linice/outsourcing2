<?php
include_once 'BaseController.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


class Test_DbController extends BaseController
{
    /**
     * db: execute add sql
     */
    public function exeaddsqlAction() {
    	$sql = " insert test2(name, age)
			select name, age from test2 where id = 4 ";
    	$result = BaseService::exeAddSql($sql);
    	exit($result);
    }
    
    
    /**
     * db: 更新
     */
    public function updateAction() {
    	$cond = " name = 'los 27' and age = 0 ";
    	$result = BaseService::updateByCond('test2', array('name' => 'linice'), $cond);
    	exit($result);
    }
    
    
    
    /**
     * using db mytest
     */
    public function mytestAction() {
//    	$test2s = BaseService2::getAll('DB_MYTEST', 'test2');
    	$test2s = BaseService2::getAll('DB', 'test2');
    	var_dump($test2s);
    	exit;
    }
    
    

    
	
	
	
} //End: class Test_TableController