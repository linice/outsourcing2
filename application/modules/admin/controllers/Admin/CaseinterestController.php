<?php
include_once 'BaseAdminController.php';

class Admin_Admin_CaseinterestController extends BaseAdminController {
    /**
     * 感兴趣case列表
     */
    public function interestcaselistAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('interest_case_list'));
		$crumb = array('uri' => '/admin/admin_caseinterest/interestcaselist', 'name' => $this->tr->translate('interest_case_list'));
		$this->view->layout()->crumbs = array($crumb);
    }

    /**
     * 感兴趣case列表（展开）
     */
    public function interestcaselistexAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('interest_case_list_expand'));
		$crumb = array('uri' => '/admin/admin_caseinterest/interestcaselistex', 'name' => $this->tr->translate('interest_case_list_expand'));
		$this->view->layout()->crumbs = array($crumb);
    }
}