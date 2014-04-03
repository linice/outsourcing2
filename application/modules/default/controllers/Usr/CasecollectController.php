<?php
include_once 'BaseController.php';
include_once 'CaseAttentionService.php';
include_once 'InviteService.php';
include_once 'EtcService.php';
class Usr_CasecollectController extends BaseController {
	
	/**
	 * 感兴趣案件
	 */
	public function incollectcaselistAction() {
		$this->layout->headTitle($this->tr->translate('interest_case_list'));
		$crumb = array('uri' => '/usr_usr/incollectcaselist', 'name' => $this->tr->translate('in_collect_case_list'));
		$this->view->layout()->crumbs = array($crumb);
	}
	
	/**
	 * 感兴趣案件-募集中案件一览
	 */
	public function searchincollectcaselistAction() {
		$this->getPagination();
		$cases = array();
		$range = $this->_getParam("range");
		$casename = $this->_getParam("casename");
		$dbcases = CaseAttentionService::findReleaseAttentionCaseListForApplyByUsrCode($this->auth->usr["code"], null, $this->pagination);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
	}
	
	 /**
	 * 感兴趣案件-募集终了案件一览
	 */
	public function searchendcollectcaselistAction() {
		$this->getPagination();
		$cases = array();
		$range = $this->_getParam("range");
		$casename = $this->_getParam("casename");
		$dbcases = CaseAttentionService::findClosedAttentionCaseListForApplyByUsrCode($this->auth->usr["code"], null, $this->pagination);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
	}
	
	/**
	 * 检查是否可以对指定的案件进行募集拒绝
	 */
	public function valrefusecaseAction() {
		$ids = $this->_getParam("ids");
    	$retVal = InviteService::valRefuseCase($ids);
    	if ($retVal !== FALSE && is_string($retVal)) {
	    	$ret = array('err' => 1, 'msg' => $this->tr->translate($retVal));
    	} else if ($retVal === TRUE) {
	    	$ret = array('err' => 0);
    	} else {
	    	$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
    	}
    	exit(json_encode($ret));
	}
	
	/**
	 * 拒绝案件募集
	 */
	public function refusecollectcaseAction() {
		$this->layout->headTitle($this->tr->translate('refuse_case_collect'));
		$crumb = array('uri' => '/usr_collectcase/refusecasecollect', 'name' => $this->tr->translate('refuse_case_collect'));
		$this->view->layout()->crumbs = array($crumb);
		
		$etcs = EtcService::getEtcsByType("CASE_APPLY_REASON");
		$cancelReasons = array();
		foreach ($etcs as $reason) 
			$cancelReasons[$reason["code"]] = $this->getText($reason["code"]);
		$this->view->cancelReasons = $cancelReasons;
		
		$ids = $this->_getParam("ids");
		$cases = array();
		$dbcases = InviteService::findInviteCasesByInviteIds($ids);
		if (!empty($dbcases)) {
			foreach ($dbcases as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		$this->view->cases = $cases;
	}
	
	/**
	 * 公司募集案件
	 */
	public function comcollectcaselistAction() {
		$this->layout->headTitle($this->tr->translate('com_collect_case_list'));
		$crumb = array('uri' => '/usr_usr/comcollectcaselist', 'name' => $this->tr->translate('com_collect_case_list'));
		$this->view->layout()->crumbs = array($crumb);
	}
	
	/**
	 * 公司募集案件-查找公司募集案件
	 */
	public function searchcomcollectcaselistAction() {
		$this->getPagination();
		$cases = array();
		$range = $this->_getParam("range");
		$casename = $this->_getParam("casename");
		$dbcases = InviteService::findInviteCaseListByUsrCode($this->auth->usr["code"], null, $this->pagination);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
	}
	
	/**
	 * 募集拒绝
	 */
	public function submitrefusecollectcaseAction() {
		$refuseList = $this->_getParam("refuseList");
		$refuseList = (array)json_decode(stripslashes($refuseList), true);
		
		$db = $this->db;
		$db->beginTransaction();
		$err = FALSE;
			
		foreach ($refuseList as $ca) {
			$refuse = array();
			$refuse["refuse_remark"] = $ca["remark"];
			$refuse["refuse_reason"] = $ca["lackReason"];
			$refuse["refuse_person"] = $this->auth->usr['code'];
			
			$usr_code = $this->auth->usr["code"];
			$inviteId = $ca["inviteId"];
			$where = "id=$inviteId";
			
			$r = InviteService::refuseCase($where, $refuse);
	    	if ($r == FALSE) {
	    		$err = TRUE;
	    		break;
	    	};
		}
		if ($err) {
			$db->rollback();
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
		} else {
			$db->commit();
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
		}
		exit(json_encode($ret));
	}
}