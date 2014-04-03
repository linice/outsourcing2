<?php
include_once 'CaseApplyService.php';
include_once 'BaseController.php';
include_once 'CaseService.php';
include_once 'EtcService.php';
/**
 * 法人案件应聘管理
 * @author GONGM
 */
class Lp_CaseapplyController extends BaseController {
	
	/**
	 * 案件应聘管理
	 */
	public function caseapplymgtAction() {
		$this->layout->headTitle($this->tr->translate('apply_mgt'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_caseapply/caseapplymgt', 'name' => $this->tr->translate('apply_mgt'));
		$this->view->layout()->crumbs = $crumbs;

		$caseCode = $this->_getParam('caseCode');

		if (!empty($caseCode)) {
			$case = CaseService::findCaseByCode($caseCode);
			if (empty($case)) {
				$this->showErrorMsg(NULL, NULL, $this->getText('data_not_exist'));
			} else if ($case['lp_code'] !== $this->auth->usr['code']) {
				$this->showErrorMsg(NULL, NULL, $this->getText('data_not_exist'));
			};
			$this->view->tab = $this->_getParam('t');
			$userList = CaseApplyService::findUsrListByCaseCode($caseCode);
			$this->view->usr = $this->auth->usr;
			$this->view->case = $this->caseToView($case);
			$this->view->userList = $this->caseapplyListToView($userList);
		} else {
			$this->showErrorMsg(NULL, NULL, $this->getText('data_not_exist'));
		}
	}
	
	/**
	 * 应聘案件
	 * @deprecated
	 */
	public function applycaseAction() {
		$this->layout->headTitle($this->tr->translate('apply_case'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_caseapply/applycase', 'name' => $this->tr->translate('apply_case'));
		$this->view->layout()->crumbs = $crumbs;
	}
	
	/**
	 * 应聘取消
	 */
	public function caseapplycancelAction() {
		$this->layout->headTitle($this->tr->translate('apply_cancel'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_caseapply/caseapplycancel', 'name' => $this->tr->translate('apply_cancel'));
		$this->view->layout()->crumbs = $crumbs;
		
		$etcs = EtcService::getEtcsByType("CASE_APPLY_REASON");
		$cancelReasons = array();
		foreach ($etcs as $reason) 
			$cancelReasons[$reason["code"]] = $this->getText($reason["code"]);
		$this->view->cancelReasons = $cancelReasons;
		
		$case = $this->_getParam("case");
		$case = (array)json_decode($case);
		$ids = $this->_getParam("usrList");
		$usrList = CaseApplyService::findUsrListByApplyIds($ids);
		
		$this->view->case = $case;
		
		$this->view->usrList = $ids;
		
   	 	$usrInfoList = $this->_getParam("usrInfoList");
		if (empty($usrInfoList)) {
			$this->view->usrInfoList = $this->caseapplyListToView($usrList);
		} else {
			$usrInfoList = json_decode($usrInfoList, true);
			for ($i = 0; $i < count($usrList); $i++) {
				$usrList[$i]['cancel_lackReason'] = $usrInfoList[$i]['lackReason'];
				$usrList[$i]['cancel_remark'] = $usrInfoList[$i]['remark'];
			}
			$this->view->usrInfoList = $this->caseapplyListToView($usrList);
		}
	}
	
	/**
	 * 应聘取消确认
	 */
	public function caseapplycancelconfirmAction() {
		$this->layout->headTitle($this->tr->translate('apply_cancel_confirm'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_caseapply/caseapplycancelconfirm', 'name' => $this->tr->translate('apply_cancel_confirm'));
		$this->view->layout()->crumbs = $crumbs;
		
		$case = $this->_getParam("case");
		
		$this->view->case = (array)json_decode($case);
		$this->view->usrInfoList = $this->_getParam("usrInfoList");;
		$this->view->usrList = $this->_getParam("usrList");;
	}
	
	/**
	 * 应聘取消信息保存
	 */
	public function savecasecancelAction() {
		$case = $this->_getParam("case");
		$usrInfoList = $this->_getParam("usrInfoList");
		
		$ids = $this->_getParam("usrList");
		$usrList = CaseApplyService::findUsrListByApplyIds($ids);
		$retVal = $this->valcasecancel($usrList, $case);
		if ($retVal !== TRUE) {
			$this->showErrorMsg(NULL, NULL, $retVal);
		}
		
		$case = (array)json_decode($case);
		$usrInfoList = (array)json_decode(stripslashes($usrInfoList), true);
		
		$db = $this->db;
		$db->beginTransaction();
		$err = FALSE;
			
		foreach ($usrInfoList as $usr) {
			$caseapply = array();
			$caseapply["remark"] = $usr["remark"];
			$caseapply["reason"] = $usr["lackReason"];
			$caseapply["status"] = 'APPLY_CANCEL';
			$caseapply["cancel_body"] = 'caseapple_cancel_lp';
			
			$applyId = $usr["applyId"];
			
			$r = CaseApplyService::updateCaseApply($applyId, $caseapply, $this->auth->usr['code']);
			if ($r == FALSE) {
				$err = TRUE;
				break;
			};
		}
		if ($err) {
			$db->rollback();
			$this->showErrorMsg("/lp_caseapply/caseapplymgt", "apply_cancel");
		} else {
			$db->commit();
			$this->showSuccessMsg("/lp_caseapply/caseapplymgt", "apply_cancel");
		}
	}
	
	/**
	 * 对应聘取消操作进行验证
	 * @param unknown_type $applyUsrList
	 * @param unknown_type $case
	 */
	private function valcasecancel($applyUsrList, $case) {
		foreach ($applyUsrList as $usr) {
			if ($usr['apply_status'] == 'INTERVIEW_LOSE' || $usr['apply_status'] == 'APPLY_CANCEL') {
				return $this->getText('cancel_talent_has_result_error');
			} else if ($usr["apply_status"] == 'INTERVIEW_ADJUST' || $usr["apply_status"] == 'INTERVIEW_INFORM') {
				return $this->getText('cancel_talent_is_proccess_error');
			}
		}
		return TRUE;
	}
}