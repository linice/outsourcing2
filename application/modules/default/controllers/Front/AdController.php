<?php
include_once 'BaseController.php';


class Front_AdController extends BaseController
{
    /**
     * 广告主页面
     */
    public function adAction() {
		$this->layout->headTitle($this->tr->translate('advertisement'));
		$crumb = array('uri' => '/front_ad/ad', 'name' => $this->tr->translate('advertisement'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
    /**
     * 广告列表页面
     */
    public function adlistAction() {
		$this->layout->headTitle($this->tr->translate('ad_list'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_ad', 'name' => $this->tr->translate('advertisement'));
		$crumbs[] = array('uri' => '/front_ad/adlist', 'name' => $this->tr->translate('ad_list'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    /**
     * 广告详情页面
     */
    public function addetailAction() {
		$this->layout->headTitle($this->tr->translate('ad_detail'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_ad', 'name' => $this->tr->translate('advertisement'));
		$crumbs[] = array('uri' => '/front_ad/adlist', 'name' => $this->tr->translate('ad_list'));
		$crumbs[] = array('uri' => '/front_ad/addetail', 'name' => $this->tr->translate('ad_detail'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
} //End: class Front_JapanController