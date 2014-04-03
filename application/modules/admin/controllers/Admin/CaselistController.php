<?php
include_once 'UtilService.php';
include_once 'UsrService.php';


class Admin_Admin_CaselistController extends Zend_Controller_Action
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
     * case列表
     */
    public function caselistAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('case_list'));
		$crumb = array('uri' => '/admin/admin_caselist/caselist', 'name' => $this->tr->translate('case_list'));
		$this->view->layout()->crumbs = array($crumb);
    }


    /**
     * 应聘case列表
     */
    public function applycaselistAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('apply_case_list'));
		$crumb = array('uri' => '/admin/admin_caselist/applycaselist', 'name' => $this->tr->translate('apply_case_list'));
		$this->view->layout()->crumbs = array($crumb);
    }


    /**
     * 募集case列表
     */
    public function collectcaselistAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('collect_case_list'));
		$crumb = array('uri' => '/admin/admin_caselist/collectcaselist', 'name' => $this->tr->translate('collect_case_list'));
		$this->view->layout()->crumbs = array($crumb);
    }


    /**
     * 历史应聘case列表
     */
    public function historyapplycaselistAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('history_apply_case_list'));
		$crumb = array('uri' => '/admin/admin_caselist/historyapplycaselist', 'name' => $this->tr->translate('history_apply_case_list'));
		$this->view->layout()->crumbs = array($crumb);
    }


    /**
     * 新增case列表
     */
    public function newcaselistAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('new_case_list'));
		$crumb = array('uri' => '/admin/admin_caselist/newcaselist', 'name' => $this->tr->translate('new_case_list'));
		$this->view->layout()->crumbs = array($crumb);


    }


    /**
     * 新增case列表（扩展）
     */
    public function newcaselistexAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('new_case_list_expand'));
		$crumb = array('uri' => '/admin/admin_caselist/newcaselistex', 'name' => $this->tr->translate('new_case_list_expand'));
		$this->view->layout()->crumbs = array($crumb);

    }






} //End: class Admin_Admin_CaseapplyController