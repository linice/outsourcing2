<?php
class Admin_Admin_PriceController extends Zend_Controller_Action
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
     * 人员、案件价格浮动设定
     */
    public function pricesetAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('price_set'));
		$crumb = array('uri' => '/admin/admin_price/priceset', 'name' => $this->tr->translate('price_set'));
		$this->view->layout()->crumbs = array($crumb);
    }

    


    
} //End: class Admin_Admin_PriceController