<?php
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'ResumeService.php';
include_once 'BaseController.php';


class Test_PluploadController extends Zend_Controller_Action
{
	private $layout = null;
	private $auth = null;
	private $tr = null;
	
    public function init()
    {
        /* Initialize action controller here */
//    	include_once 'Acl/permission.php';
		$this->layout = Zend_Registry::get('LAYOUT');
		$this->auth = new Zend_Session_Namespace('AUTH');
		$this->tr = Zend_Registry::get('TRANSLATE');

//		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
//    		&& $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
//	    	$this->_helper->layout()->disableLayout();
//			$this->_helper->viewRenderer->setNoRender(TRUE);
//    	}
	    if ($this->_request->isXmlHttpRequest()) {
//			$fc = Zend_Controller_Front::getInstance();
//			$fc->setParam('noViewRenderer', true);
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
		}
    }
    
    
    /**
     * plupload文件上传主页
     */
    public function pluploadAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('TEST'));
	    $crumb = array('uri'=>'/test_test/tmp', 'name'=>$this->tr->translate('TEST'));
		$this->view->layout()->crumbs = array($crumb);
		
		
    }
    
    
    /**
     * 处理文件上传 
     */
    public function handlepluploadAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('TEST'));
	    $crumb = array('uri'=>'/test_test/test', 'name'=>$this->tr->translate('TEST'));
		$this->view->layout()->crumbs = array($crumb);
		
		//获取参数
		$param = $this->_getAllParams();
		var_dump($param);
		
		exit('Success');
    }
    
	
	
} //End: class Test_TestController