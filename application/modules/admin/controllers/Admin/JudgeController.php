<?php
include_once 'BaseAdminController.php';
include_once 'EtcService.php';
include_once 'CaseApplyService.php';

class Admin_Admin_JudgeController extends BaseAdminController {

	/**
	 * 管理员判定
	 */
	public function judgeAction() {
		$this->layout->headTitle($this->tr->translate('judge'));
		$crumb = array('uri' => '/admin/admin_judge/judge', 'name' => $this->tr->translate('judge'));
		$this->view->layout()->crumbs = array($crumb);
		
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
				$usrList[$i]['judgeResult'] = $usrInfoList[$i]['judgeResult'];
				$usrList[$i]['judgeReason'] = $usrInfoList[$i]['lackReason'];
				$usrList[$i]['judgeRemark'] = $usrInfoList[$i]['remark'];
			}
			$this->view->usrInfoList = $this->caseapplyListToView($usrList);
		}
	}
	
	/**
	 * 管理员判定确定
	 */
	public function judgeconfirmAction() {
		$this->layout->headTitle($this->tr->translate('judge_confirm'));
		$crumb = array('uri' => '/admin/admin_judge/judgeconfirm', 'name' => $this->tr->translate('judge_confirm'));
		$this->view->layout()->crumbs = array($crumb);
		
		$case = $this->_getParam("case");
		
		$this->view->case = (array)json_decode($case);
		$this->view->usrInfoList = $this->_getParam("usrInfoList");
		$this->view->usrList = $this->_getParam("usrList");
	}
	
	/**
	 * 保存管理员判定确定
	 */
	public function savecasejudgeAction() {
		$case = $this->_getParam("case");
		$usrInfoList = $this->_getParam("usrInfoList");
		
		$ids = $this->_getParam("usrList");
		$usrList = CaseApplyService::findUsrListByApplyIds($ids);
		$retVal = $this->valjudge($usrList, $case);
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
			$caseapply["remark"] = $usr['remark'];
			$caseapply["reason"] = $usr['lackReason'];
			$caseapply["status"] = $usr['judgeResult'];
			
			$applyId = $usr["applyId"];
			
			$r = CaseApplyService::updateCaseApply($applyId, $caseapply, $this->auth->usr['code']);
			if ($r == FALSE) {
				$err = TRUE;
				break;
			};
		}
		if ($err) {
			$db->rollback();
			$this->showErrorMsg("/admin/admin_applymgt/applymgt", "judge");
		} else {
			$db->commit();
			$this->showSuccessMsg("/admin/admin_applymgt/applymgt", "judge");
		}
	}
	
	/**
	 * 对管理员判定操作进行验证
	 * @param unknown_type $applyUsrList
	 * @param unknown_type $case
	 */
	private function valjudge($applyUsrList, $case) {
		foreach ($applyUsrList as $usr) {
			if ($usr['apply_status'] != 'NO_VOTE' && $usr['apply_status'] != 'NO_RECOMMEND') {
				return $this->getText('judge_talent_status_error');
			}
		}
		return TRUE;
	}
}