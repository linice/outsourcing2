<?php
class Admin_Admin_AdController extends Zend_Controller_Action
{
	private $layout = null;
	private $auth = null;
	private $tr = null;
	private $db = NULL;
	
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
     * 首页广告
     */
    public function adAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('homepage_ad'));
		$crumb = array('uri' => '/admin/admin_ad/ad', 'name' => $this->tr->translate('homepage_ad'));
		$this->view->layout()->crumbs = array($crumb);
    }

    
    /**
     * 创建广告
     */
    public function createadAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('create_ad'));
		$crumb = array('uri' => '/admin/admin_ad/createad', 'name' => $this->tr->translate('create_ad'));
		$this->view->layout()->crumbs = array($crumb);
    }

    
    /**
     * 创建广告分类
     */
    public function createadcatalogAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('create_ad_catalog'));
		$crumb = array('uri' => '/admin/admin_ad/createadcatalog', 'name' => $this->tr->translate('create_ad_catalog'));
		$this->view->layout()->crumbs = array($crumb);
    }

    
    /**
     * 广告分类列表
     */
    public function adcatalogAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('ad_catalog'));
		$crumb = array('uri' => '/admin/admin_ad/adcataloglist', 'name' => $this->tr->translate('ad_catalog'));
		$this->view->layout()->crumbs = array($crumb);
    }

    
    /**
     * 广告列表
     */
    public function adlistAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('ad_list'));
		$crumb = array('uri' => '/admin/admin_ad/adlist', 'name' => $this->tr->translate('ad_list'));
		$this->view->layout()->crumbs = array($crumb);
    }

    


    
} //End: class Admin_Admin_AdController