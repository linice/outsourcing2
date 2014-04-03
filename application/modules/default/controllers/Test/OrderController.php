<?php
include_once 'UtilService.php';
include_once 'Test/OrderService.php';
include_once 'BaseController.php';


class Test_OrderController extends BaseController
{
    /**
     * 测试add
     */
    public function addorderAction() {
    	$order2 = array('code' => 'c2', 'buyer_code' => 'bc2');
    	$order3 = array('code' => 'c3', 'buyer_code' => 'bc3');
    	$orders = array($order2, $order3);
    	OrderService::addOrder($order3);
    	exit('Success');
    }
    
    
    /**
     * 测试update
     */
    public function updateorderAction() {
    	$cond = "fullname like '%i%'";
    	$row = array('age' => 27);
    	OrderService::updateTtsByCond($cond, $row);
    	
    	exit('Success');
    }
    
    
    
} //End: class Test_TtController