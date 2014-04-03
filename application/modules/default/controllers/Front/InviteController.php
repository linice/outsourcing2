<?php
include_once 'BaseController.php';
include_once 'CaseApplyService.php';
include_once 'CaseService.php';
include_once 'ResumeService.php';
include_once 'InviteService.php';
include_once 'UtilService.php';
class Front_InviteController extends BaseController {
	
	/**
	 * 邀请人才主页面，因为人才的要求，要跟case关联，所以该页面不需要
	 */
	public function inviteAction() {
		$this->layout->headTitle($this->tr->translate('invite_talent'));
		$crumb = array('uri' => '/front_invite', 'name' => $this->tr->translate('invite_talent'));
		$this->view->layout()->crumbs = array($crumb);
		exit;
	}
	
	/**
	 * 通过人才检索，邀请人才
	 */
	public function invitetalentAction() {
		$this->layout->headTitle($this->tr->translate('invite_talent'));
		$crumb = array('uri' => '/front_invitetalent', 'name' => $this->tr->translate('invite_talent'));
		$this->view->layout()->crumbs = array($crumb);
	}
	
	/**
	 * 选择员工点击募集邀请，进入募集中案件列表进行案件选择
	 */
	public function chooseinvitecaseAction() {
		$this->layout->headTitle($this->tr->translate('in_collect_case_list'));
		$crumb = array('uri' => '/front_invite/chooseinvitecase', 'name' => $this->tr->translate('in_collect_case_list'));
		$this->view->layout()->crumbs = array($crumb);
		
		$this->view->resumeCodes = $this->_getParam('ids');
	}
	
	/**
	 * 选择员工点击募集邀请，进入募集中案件列表进行案件选择
	 */
	public function chooseinvitecaseconfirmAction() {
		$this->layout->headTitle($this->tr->translate('in_collect_case_list'));
		$crumb = array('uri' => '/front_invite/chooseinvitecase', 'name' => $this->tr->translate('in_collect_case_list'));
		$this->view->layout()->crumbs = array($crumb);
		
		$caseCode = $this->_getParam('caseCode');
		$resumeCodes = $this->_getParam('resumeCodes');
		
		$resumeList = array();
		$resumes = CaseApplyService::findResumeListByResumeCodesExpApply($resumeCodes, $caseCode);
		
		foreach ($resumes as $resume) {
			array_push($resumeList, $this->resumeToView($resume));
		}
		$case = CaseService::findCaseByCode($caseCode);
		
		$this->view->case = $this->caseToView($case);
		$this->view->resumeList = $resumeList;
	}
	
	/**
	 * 保存募集结果
	 */
	public function saveinvitetalentforcaseAction() {
		$caseCode = $this->_getParam('caseCode');
		$resumeCodes = $this->_getParam('resumeCodes');
		
		$db = $this->db;
		$db->beginTransaction();
		$again = InviteService::valInviteTalent($resumeCodes, $caseCode);
		if ($again === FALSE) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
			exit(json_encode($ret));
		} else if (is_string($again)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate($again));
			exit(json_encode($ret));
		}
		if (!empty($again))
		$retVal = TRUE;
		$resumeArray = explode(',', $resumeCodes);
		foreach ($resumeArray as $resumeCode) {
			$resume = ResumeService::getResumeByCode($resumeCode);
			if ($resume === FALSE) break;
			$invite = array('case_code'=>$caseCode, 
				'lp_code'=>new Zend_Db_Expr("(select lp_code from cases where code='$caseCode')"), 
				'usr_code'=>empty($resume['lp_code']) ? $resume['talent_code'] : $resume['lp_code'],
							'resume_code'=>$resumeCode, 'invite_date'=>UtilService::getCurrentTime());
			$retVal = InviteService::saveInvite($invite);
			if ($retVal === FALSE) break;
		}
		if ($retVal !== FALSE) {
			$db->commit();
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
		} else {
			$db->rollback();
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
		}
		exit(json_encode($ret));
	}
}