<?php
include_once 'BaseController.php';


class Front_CollectController extends BaseController
{
    /**
     * 新增人才主页面
     */
    public function collectAction() {
		$this->layout->headTitle($this->tr->translate('collect_talent'));
		$crumb = array('uri' => '/front_collect/collect', 'name' => $this->tr->translate('collect_talent'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
    
    
    
    
} //End: class Front_CollectController