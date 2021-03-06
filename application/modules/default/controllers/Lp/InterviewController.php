<?php
include_once 'BaseController.php';
include_once 'CaseApplyService.php';
include_once 'UsrService.php';
include_once 'EtcService.php';
/**
 * 法人中心-面试调整
 * @author GONGM
 */
class Lp_InterviewController extends BaseController {
	
	/**
	 * 点击面试调整进行验证操作
	 */
	public function valinterviewAction() {
		$case = $this->_getParam("case");
		$case = (array)json_decode($case);
		$ids = $this->_getParam("usrList");
		$usrList = CaseApplyService::findUsrListByApplyIds($ids);
		$retVal = $this->valinterview($usrList, $case);
		if ($retVal !== TRUE) {
			$ret = array('err' => 1, 'msg' => $retVal);
		} else {
			$ret = array('err' => 0);
		}
		exit(json_encode($ret));
	}
	
	/**
	 * 面试调整
	 */
	public function interviewAction() {
		$this->layout->headTitle($this->tr->translate('interview_change'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_interview/interviewchange', 'name' => $this->tr->translate('interview_change'));
		$this->view->layout()->crumbs = $crumbs;
		
		$case = $this->_getParam("case");
		$case = (array)json_decode($case);
		$ids = $this->_getParam("usrList");
		$usrList = CaseApplyService::findUsrListByApplyIds($ids);
		
		$this->view->case = $case;
		
		$this->view->usrList = $ids;
		
		$interview = $this->_getParam('interview');
		if (empty($interview))
			$this->view->usr = UsrService::getUsrByCode($this->auth->usr['code']);
		else {
			$interview = json_decode($interview, true);
			$this->view->usr = array('lp_linkman'=>$interview['linkman'], 'tel'=>$interview['telephone'], 'lp_address'=>$interview['interviewPlace']);
		}
		$usrInfoList = $this->_getParam("usrInfoList");
		if (empty($usrInfoList)) {
			$this->view->usrInfoList = $this->caseapplyListToView($usrList);
		} else {
			$usrInfoList = json_decode($usrInfoList, true);
			for ($i = 0; $i < count($usrList); $i++) {
				$usrList[$i]['interviewTime1'] = $usrInfoList[$i]['interviewTime1'];
				$usrList[$i]['interviewTime2'] = $usrInfoList[$i]['interviewTime2'];
				$usrList[$i]['interviewTime3'] = $usrInfoList[$i]['interviewTime3'];
			}
			$this->view->usrInfoList = $this->caseapplyListToView($usrList);
		}
	}

	/**
	 * 面试调整确认
	 */
	public function interviewchangeconfirmAction() {
		$this->layout->headTitle($this->tr->translate('interview_change_confirm'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_interview/interviewchangeconfirm', 'name' => $this->tr->translate('interview_change_confirm'));
		$this->view->layout()->crumbs = $crumbs;
		
		$case = $this->_getParam("case");
		$interview = array("linkman"=>$this->_getParam("linkman"), "telephone"=>$this->_getParam("telephone"),
							"interviewPlace"=>$this->_getParam("interviewPlace"));
		
		$this->view->case = (array)json_decode($case);
		$this->view->usrInfoList = $this->_getParam("usrInfoList");
		$this->view->usrList = $this->_getParam("usrList");
		$this->view->interview = $interview;
	}
	
	/**
	 * 面试结果通知
	 */
	public function interviewresultinformAction() {
		$this->layout->headTitle($this->tr->translate('interview_inform'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_interview/interviewinform', 'name' => $this->tr->translate('interview_inform'));
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
				$usrList[$i]['admission_result'] = $usrInfoList[$i]['result'];
				$usrList[$i]['admission_reason'] = $usrInfoList[$i]['reason'];
				$usrList[$i]['admission_date'] = $usrInfoList[$i]['admission_date'];
				$usrList[$i]['admission_place'] = $usrInfoList[$i]['admission_place'];
				$usrList[$i]['admission_linkman'] = $usrInfoList[$i]['admission_linkman'];
				$usrList[$i]['admission_telephone'] = $usrInfoList[$i]['admission_telephone'];
			}
			$this->view->usrInfoList = $this->caseapplyListToView($usrList);
		}
	}
	
	/**
	 * 面试结果通知确认
	 */
	public function interviewresultinformconfirmAction() {
		$this->layout->headTitle($this->tr->translate('interview_inform_confirm'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_interview/interviewinformconfirm', 'name' => $this->tr->translate('interview_inform_confirm'));
		$this->view->layout()->crumbs = $crumbs;
		
		$case = $this->_getParam("case");
		
		$this->view->case = (array)json_decode($case);
		$this->view->usrList = $this->_getParam("usrList");
		$this->view->usrInfoList = $this->_getParam("usrInfoList");
	}
	
	/**
	 * 保存面试调整信息
	 */
	public function saveinterviewAction() {
		$case = $this->_getParam("case");
		$usrInfoList = $this->_getParam("usrInfoList");
		$interview = $this->_getParam("interview");
		
		$ids = $this->_getParam("usrList");
		$usrList = CaseApplyService::findUsrListByApplyIds($ids);
		$retVal = $this->valinterview($usrList, $case);
		if ($retVal !== TRUE) {
			$this->showErrorMsg(NULL, NULL, $retVal);
		}
		
		$interview = (array)json_decode($interview);
		$case = (array)json_decode($case);
		$usrInfoList = (array)json_decode(stripslashes($usrInfoList), true);
		
		$db = $this->db;
		$db->beginTransaction();
		$err = FALSE;
			
		foreach ($usrInfoList as $usr) {
			$caseapply = array();
			$caseapply["remark"] = "";
			$caseapply["reason"] = "";
			$caseapply["status"] = 'INTERVIEW_ADJUST';
			
			$applyId = $usr["applyId"];
			
			$r = CaseApplyService::updateCaseApply($applyId, $caseapply, $this->auth->usr['code']);
			var_dump($r);
			if ($r != FALSE) {
				$interviewRows = array();
				$interviewRows["case_apply_id"] = $applyId;
				$interviewRows["expect_interview_date1"] = $usr["interviewTime1"];
				$interviewRows["expect_interview_date2"] = $usr["interviewTime2"];
				$interviewRows["expect_interview_date3"] = $usr["interviewTime3"];
				$interviewRows["interview_linkman"] = $interview["linkman"];
				$interviewRows["interview_telephone"] = $interview["telephone"];
				$interviewRows["interview_place"] = $interview["interviewPlace"];
				$r = CaseApplyService::saveInterview($interviewRows);
				if ($r == FALSE) {
					$err = TRUE;
					break;
				}
			} else {
				$err = TRUE;
				break;
			};
		}
		if ($err) {
			$db->rollback();
			$this->showErrorMsg("/lp_caseapply/caseapplymgt", "interview_change");
		} else {
			$db->commit();
			$this->showSuccessMsg("/lp_caseapply/caseapplymgt", "interview_change");
		}
	}
	
	/**
	 * 保存面试通知结果信息
	 */
	public function saveinterviewresultAction() {
		$case = $this->_getParam("case");
		$usrInfoList = $this->_getParam("usrInfoList");
		
		$ids = $this->_getParam("usrList");
		$usrList = CaseApplyService::findUsrListByApplyIds($ids);
		$retVal = $this->valinterviewresultinform($usrList, $case);
		if ($retVal !== TRUE) {
			$this->showErrorMsg(NULL, NULL, $retVal);
		}
		
		$usrInfoList = (array)json_decode(stripslashes($usrInfoList), true);
		$case = (array)json_decode($case);
		
		$db = $this->db;
		$db->beginTransaction();
		$err = FALSE;
		
		foreach ($usrInfoList as $usr) {
			$caseapply = array();
			if ($usr["result"] == 'OK') {
				$caseapply["remark"] = $usr["reason"];
				$caseapply["reason"] = "";
				$caseapply["status"] = 'INTERVIEW_OK';
			} else {
				$caseapply["remark"] = "";
				$caseapply["reason"] = $usr["reason"];
				$caseapply["status"] = 'INTERVIEW_LOSE';
			}
			
			$applyId = $usr["applyId"];
			
			$r = CaseApplyService::updateCaseApply($applyId, $caseapply, $this->auth->usr['code']);
			if ($r != FALSE) {
				$interviewRows = array();
				$interviewRows["case_apply_id"] = $applyId;
				$interviewRows["result"] = $usr["result"];
				$interviewRows["reason"] = $usr["reason"];
				$interviewRows["admission_place"] = $usr["admission_place"];
				$interviewRows["admission_linkman"] = $usr["admission_linkman"];
				$interviewRows["admission_telephone"] = $usr["admission_telephone"];
				$interviewRows["admission_date"] = $usr["admission_date"];
				$r = CaseApplyService::saveInterviewResult($interviewRows);
				if ($r == FALSE) {
					$err = TRUE;
					break;
				}
			} else {
				$err = TRUE;
				break;
			};
		}
		if ($err) {
			$db->rollback();
			$this->showErrorMsg("/lp_caseapply/caseapplymgt", "interview_inform");
		} else {
			$db->commit();
			$this->showSuccessMsg("/lp_caseapply/caseapplymgt", "interview_inform");
		}
	}
	
	/**
	 * 面试信息编辑
	 */
	public function interviewadjustAction() {
		$this->layout->headTitle($this->tr->translate('interview_info_adjust'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_interview/interviewadjust', 'name' => $this->tr->translate('interview_info_adjust'));
		$this->view->layout()->crumbs = $crumbs;
	
		$case = $this->_getParam("case");
		$case = (array)json_decode($case);
		$ids = $this->_getParam("usrList");
		$usrList = CaseApplyService::findInterviewAdjustByApplyIds($ids, $case["code"]);
		
		$this->view->case = $case;
		$this->view->usrList = $ids;
		
		$interview = $this->_getParam('interview');
		if (empty($interview))
			$this->view->usr = UsrService::getUsrByCode($case['lp_code']);
		else {
			$interview = json_decode($interview, true);
			$this->view->usr = array('lp_linkman'=>$interview['linkman'], 'tel'=>$interview['telephone'], 'lp_address'=>$interview['interviewPlace']);
		}
		$usrInfoList = $this->_getParam("usrInfoList");
		if (empty($usrInfoList)) {
			$this->view->usrInfoList = $this->caseapplyListToView($usrList);
		} else {
			$usrInfoList = json_decode($usrInfoList, true);
			for ($i = 0; $i < count($usrList); $i++) {
				$usrList[$i]['interviewTime'] = $usrInfoList[$i]['interviewTime'];
			}
			$this->view->usrInfoList = $this->caseapplyListToView($usrList);
		}
	}
	
	/**
	 * 面试信息编辑确认
	 */
	public function interviewadjustconfirmAction() {
		$this->layout->headTitle($this->tr->translate('interview_info_adjust_confirm'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_interview/interviewinformconfirm', 'name' => $this->tr->translate('interview_info_adjust_confirm'));
		$this->view->layout()->crumbs = $crumbs;
		
		$case = $this->_getParam("case");
		$usrList = $this->_getParam("usrList");
		$interview = array("linkman"=>$this->_getParam("linkman"), "telephone"=>$this->_getParam("telephone"),
							"interviewPlace"=>$this->_getParam("interviewPlace"));
		
		$this->view->case = (array)json_decode($case);
		$this->view->usrInfoList = $this->_getParam("usrInfoList");
		$this->view->usrList = $this->_getParam("usrList");
		$this->view->interview = $interview;
	}
	

	/**
	 * 保存面试信息编辑
	 */
	public function saveinterviewadjustAction() {
		$case = $this->_getParam("case");
		$usrList = $this->_getParam("usrInfoList");
		$interview = $this->_getParam("interview");
		
		$interview = (array)json_decode($interview);
		$case = (array)json_decode($case);
		$usrList = (array)json_decode(stripslashes($usrList), true);
		
		$db = $this->db;
		$db->beginTransaction();
		$err = FALSE;
			
		foreach ($usrList as $usr) {
			$caseapply = array();
			//$caseapply["status"] = 'INTERVIEW_OK';
			
			$applyId = $usr["applyId"];
			
			$r = TRUE;
			//$r = CaseApplyService::updateCaseApply($applyId, $caseapply);
			if ($r != FALSE) {
				$interviewRows = array();
				$interviewRows["real_interview_date"] = $usr["interviewTime"];
				if (!empty($interview["linkman"]) && trim($interview["linkman"]) != "") 
					$interviewRows["interview_linkman"] = $interview["linkman"];
				if (!empty($interview["interview_telephone"]) && trim($interview["interview_telephone"]) != "") 
					$interviewRows["interview_telephone"] = $interview["telephone"];
				if (!empty($interview["interview_place"]) && trim($interview["interview_place"]) != "") 
					$interviewRows["interview_place"] = $interview["interviewPlace"];
				
				$r = CaseApplyService::updateInterview($usr["interviewId"], $interviewRows);
				if ($r == FALSE) {
					$err = TRUE;
					break;
				}
			} else {
				$err = TRUE;
				break;
			};
		}
		if ($err) {
			$db->rollback();
			$this->showErrorMsg("/lp_caseapply/caseapplymgt", "interview_info_adjust");
		} else {
			$db->commit();
			$this->showSuccessMsg("/lp_caseapply/caseapplymgt", "interview_info_adjust");
		}
	}

	/**
	 * 验证面试调整
	 * @param unknown_type $applyUsrList
	 * @param unknown_type $case
	 */
	private function valinterview($applyUsrList, $case) {
		foreach ($applyUsrList as $usr) {
			if ($usr['apply_status'] != 'NO_VOTE' && $usr['apply_status'] != "RECOMMEND" && $usr['apply_status'] != "NO_RECOMMEND") {
				return $this->getText('interview_talent_status_error');
			}
		}
		return TRUE;
	}

	/**
	 * 验证面试结果通知
	 */
	private function valinterviewresultinform($applyUsrList, $case) {
		foreach ($applyUsrList as $usr) {
			if ($usr['apply_status'] != 'INTERVIEW_ADJUST') {
				return $this->getText('interviewresult_talent_status_error');
			}
		}
		return TRUE;
	}
}