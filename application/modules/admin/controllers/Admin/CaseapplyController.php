<?php
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'EtcService.php';
include_once 'BaseAdminController.php';
include_once 'ResumeService.php';
include_once 'CaseApplyService.php';

class Admin_Admin_CaseapplyController extends BaseAdminController {

	/**
	 * 人才应聘case
	 */
	public function talentapplycaseAction() {
		$this->layout->headTitle($this->tr->translate('talent_apply_case'));
		$crumb = array('uri' => '/admin/admin_caseapply/talentapplycase', 'name' => $this->tr->translate('talent_apply_case'));
		$this->view->layout()->crumbs = array($crumb);
		
		$caseCode = $this->_getParam('caseCode'); 
		$resumeCodes = $this->_getParam('resumes');
		
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
	 * 应聘案件时，人才检索
	 */
	public function talentsearchAction() {
		$this->layout->headTitle($this->tr->translate('talent_search'));
		$crumb = array('uri' => '/admin/admin_caseapply/talentsearch', 'name' => $this->tr->translate('talent_search'));
		$this->view->layout()->crumbs = array($crumb);
		
		$this->view->caseCode = $this->_getParam('caseCode');
	}
	
	/**
     * 查找当前用户的募集中案件关注列表
     */
    public function searchtalentsearchAction() {
		$this->getPagination();
		$resumes = array();
		$case_code = $this->_getParam('case_code');
		$searchFields = $this->_getParam('searchFields');
		$dbresumes = ResumeService::findAllUsrForCaseApply($this->genFieldsSearch($searchFields), $this->pagination);
		if (!empty($dbresumes["Rows"])) {
			foreach ($dbresumes["Rows"] as $resume) {
				array_push($resumes, $this->resumeToView($resume));
			}
		}
		exit(json_encode($this->genPagination($dbresumes["Total"], $resumes)));
    }
    
    /**
     * 管理员应聘员工
     */
    public function applycaseforadminAction() {
    	$empCodeList = $this->_getParam('resumeCodes', "");
		$caseCode = $this->_getParam('caseCode', "");
		if (!isset($this->auth->usr)) {
			$this->showErrorMsg("/", 'apply_case', "");
		} else {
			$usr_code = $this->auth->usr['code'];
			//$retVal = CaseApplyService::valCaseApplyForLp($caseCode, $usr_code);
			$retVal = TRUE;
    		if ($retVal == FALSE) {
    			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
    			exit(json_encode($ret));
    		} elseif (is_string($retVal)) {
    			$ret = array('err' => 1, 'msg' => $this->tr->translate($retVal));
    			exit(json_encode($ret));
    		} else 
				$ret = CaseApplyService::saveCaseApplyForAdmin($caseCode, $empCodeList, $usr_code);		
			if ($ret!==FALSE) {
				$ret = array('err' => 0, 'msg' => $this->tr->translate('apply_case_success'));
    			exit(json_encode($ret));
			} else {
				$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
    			exit(json_encode($ret));
			}
		}
    }

	/**
	 * 应聘取消
	 */
	public function applycancelAction() {
		$this->layout->headTitle($this->tr->translate('apply_cancel'));
		$crumb = array('uri' => '/admin/admin_caseapply/applycancel', 'name' => $this->tr->translate('apply_cancel'));
		$this->view->layout()->crumbs = array($crumb);
	}

	/**
	 * case应聘取消
	 */
	public function caseapplycancelAction() {
		$this->layout->headTitle($this->tr->translate('case_apply_cancel'));
		$crumb = array('uri' => '/admin/admin_caseapply/caseapplycancel', 'name' => $this->tr->translate('case_apply_cancel'));
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
				$usrList[$i]['cancel_lackReason'] = $usrInfoList[$i]['lackReason'];
				$usrList[$i]['cancel_remark'] = $usrInfoList[$i]['remark'];
			}
			$this->view->usrInfoList = $this->caseapplyListToView($usrList);
		}
	}

	/**
	 * case应聘取消确认
	 */
	public function caseapplycancelconfirmAction() {
		$this->layout->headTitle($this->tr->translate('case_apply_cancel_confirm'));
		$crumb = array('uri' => '/admin/admin_caseapply/caseapplycancelconfirm', 'name' => $this->tr->translate('case_apply_cancel_confirm'));
		$this->view->layout()->crumbs = array($crumb);
		
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
			
			$applyId = $usr["applyId"];
			$r = CaseApplyService::updateCaseApply($applyId, $caseapply, $this->auth->usr['code']);
			if ($r == FALSE) {
				$err = TRUE;
				break;
			};
		}
		if ($err) {
			$db->rollback();
			$this->showErrorMsg("/admin/admin_applymgt/applymgt", "apply_cancel");
		} else {
			$db->commit();
			$this->showSuccessMsg("/admin/admin_applymgt/applymgt", "apply_cancel");
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