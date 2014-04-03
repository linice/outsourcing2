<?php
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'ResumeService.php';
include_once 'BaseController.php';


class Test_ZfController extends BaseController 
{
	function indexAction() {
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		var_dump($this->_request->getBaseUrl());
	}
	
	
	function handleindexAction() {
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		$val = $this->_getParam('text_test');
		var_dump($val);
	}
}