<?php
include_once 'BaseController.php';


class Lp_EmpapplyController extends BaseController
{
    /**
     * 法人员工应聘
     */
    public function empapplyAction() {
		$this->layout->headTitle($this->tr->translate('emp_apply'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_empapply/empapply', 'name' => $this->tr->translate('emp_apply'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    /**
     * 法人员工列表
     */
    public function emplistAction() {
		$this->layout->headTitle($this->tr->translate('emp_list'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_empapply/emplist', 'name' => $this->tr->translate('emp_list'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    /**
     * 应聘取消
     */
    public function empapplycancelAction() {
		$this->layout->headTitle($this->tr->translate('apply_cancel'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_empapply/empapplycancel', 'name' => $this->tr->translate('apply_cancel'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    /**
     * 应聘取消确认 
     */
    public function empapplycancelconfirmAction() {
		$this->layout->headTitle($this->tr->translate('apply_cancel_confirm'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_empapply/empapplycancelconfirm', 'name' => $this->tr->translate('apply_cancel_confirm'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    
    
    
    
    
    
} //End: class Lp_EmpapplyController