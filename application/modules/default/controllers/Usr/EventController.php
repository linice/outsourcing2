<?php
include_once 'BaseController.php';


class Usr_EventController extends BaseController
{
    /**
     * 发起活动
     */
	public function initiateeventAction() {
		$this->layout->headTitle($this->tr->translate('initiate_event'));
		$crumb = array('uri' => '/usr_usr/initiateevent', 'name' => $this->tr->translate('initiate_event'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
    /**
     * 参与活动一览
     */
	public function joineventlistAction() {
		$this->layout->headTitle($this->tr->translate('join_event_list'));
		$crumb = array('uri' => '/usr_usr/joineventlist', 'name' => $this->tr->translate('join_event_list'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
    
    
    
} //End: class Usr_EventController