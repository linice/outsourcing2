<?php
include_once 'BaseController.php';


class Lp_TalentapplyController extends BaseController
{
    /**
     * 人才应聘取消
     */
    public function talentapplycancelAction() {
		$this->layout->headTitle($this->tr->translate('apply_cancel'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_caseapply/talentapplycancel', 'name' => $this->tr->translate('apply_cancel'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    /**
     * 用户应聘取消确认
     */
    public function talentapplycancelconfirmAction() {
		$this->layout->headTitle($this->tr->translate('apply_cancel_confirm'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_caseapply/talentapplycancelconfirm', 'name' => $this->tr->translate('apply_cancel_confirm'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
} //End: class Lp_TalentapplyController