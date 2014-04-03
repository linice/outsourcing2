<?php
include_once 'BaseController.php';


class Front_AdvantageController extends BaseController
{
    /**
     * 赴日直通车主页面
     */
    public function advantageAction() {
		$this->layout->headTitle($this->tr->translate('our_advantage'));
		$crumb = array('uri' => '/front_advantage/advantage', 'name' => $this->tr->translate('our_advantage'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
    
    
    
    
} //End: class Front_AdvantageController