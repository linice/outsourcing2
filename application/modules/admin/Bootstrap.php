<?php
include_once 'LayoutLoader.php';


class Admin_Bootstrap extends Zend_Application_Module_Bootstrap 
{
	/**
	 * 设置布局layout
	 */
   	protected function _initLayoutHelper() 
	{
		$this->bootstrap('frontController'); 
		$layout = Zend_Controller_Action_HelperBroker::addHelper(new LayoutLoader()); 
	}
	
	
	
	
	
}
