<?php
include_once 'BaseController.php';


class Lp_AccountController extends BaseController
{
    /**
     * 入金查询 
     */
    public function depositAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('deposit_search'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_account/deposit', 'name' => $this->tr->translate('deposit_search'));
		$this->view->layout()->crumbs = $crumbs;
		
		//
    }
    
    
    /**
     * 交易查询 
     */
    public function transAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('trans_search'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_account/trans', 'name' => $this->tr->translate('trans_search'));
		$this->view->layout()->crumbs = $crumbs;
		
		//
    }
    
    
    
    
    
    
} //End: class Lp_AccountController