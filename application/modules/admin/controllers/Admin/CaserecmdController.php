<?php
include_once 'UtilService.php';
include_once 'UsrService.php';

class Admin_Admin_CaserecmdController extends Zend_Controller_Action
{
	private $layout = null;
	private $auth = null;
	private $tr = null;
	private $db = NULL;
	
	//登陆信息
	private $email = NULL;
	private $passwd = NULL;
//	private $mgrType = NULL;
	
    public function init()
    {
        /* Initialize action controller here */
//    	include_once 'Acl/permission.php';
		$this->layout = Zend_Registry::get('LAYOUT');
		$this->auth = new Zend_Session_Namespace('AUTH');
		$this->tr = Zend_Registry::get('TRANSLATE');
		$this->db = Zend_Registry::get('DB');

	    if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
		}
    }
    
    
    /**
     * case推荐
     */
    public function caserecmdAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('case_recommend'));
		$crumb = array('uri' => '/admin/admin_caserecmd/caserecmd', 'name' => $this->tr->translate('case_recommend'));
		$this->view->layout()->crumbs = array($crumb);
    }

    


    
} //End: class Admin_Admin_CaserecmdController