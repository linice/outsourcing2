<?php
include_once 'BaseAdminController.php';
include_once 'UtilService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';



class Admin_Admin_LplistController extends BaseAdminController {
	//登陆信息
	private $email = NULL;
	private $passwd = NULL;
//	private $mgrType = NULL;


    /**
     * 法人列表
     */
    public function lplistAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('lp_list'));
		$crumb = array('uri' => '/admin/admin_lp/lplist', 'name' => $this->tr->translate('lp_list'));
		$this->view->layout()->crumbs = array($crumb);

    }


    /**
     * 未承认法人列表
     */
    public function unacklplistAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('unack_lp_list'));
		$crumb = array('uri' => '/admin/admin_lp/unacklplist', 'name' => $this->tr->translate('unack_lp_list'));
		$this->view->layout()->crumbs = array($crumb);

		//查询未承认法人
    }








} //End: class Admin_Admin_LplistController