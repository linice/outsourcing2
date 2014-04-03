<?php
include_once 'BaseController.php';
include_once 'CaseApplyService.php';
include_once 'EtcService.php';
class Usr_CaseapplyController extends BaseController {
    
    /**
     * 取消应聘案件
     */
    public function cancelapplycaseAction() {
		$this->layout->headTitle($this->tr->translate('cancel_apply_case'));
		$crumb = array('uri' => '/usr_applycase/cancelapplycase', 'name' => $this->tr->translate('cancel_apply_case'));
		$this->view->layout()->crumbs = array($crumb);
		
		$etcs = EtcService::getEtcsByType('CASE_APPLY_REASON');
		$cancelReasons = array();
		foreach ($etcs as $reason) 
			$cancelReasons[$reason['code']] = $this->getText($reason['code']);
		$this->view->cancelReasons = $cancelReasons;
		
		$ids = $this->_getParam('ids');
		$applyList = CaseApplyService::findCaseListByApplyIds($ids);
		
		$applyInfoList = $this->_getParam('applyList');
		if (empty($applyInfoList)) {
			$this->view->applyList = $this->caseapplyListToView($applyList, FALSE);
		} else {
			$applyInfoList = json_decode($applyInfoList, true);
			for ($i = 0; $i < count($applyInfoList); $i++) {
				$applyList[$i]['cancel_reason'] = $applyInfoList[$i]['lackReason'];
				$applyList[$i]['cancel_other'] = $applyInfoList[$i]['remark'];
			}
			$this->view->applyList = $this->caseapplyListToView($applyList, FALSE);
		}
		$this->view->ids = $ids;
    }
    
    /**
     * 检查选中的应聘纪录是否可以进行应聘取消操作
     * 1、已经被取消了或者是有结果的应聘纪录不能被取消
     */
    public function valcancelapplycaseAction() {
    	$ids = $this->_getParam('ids');
    	$retVal = CaseApplyService::valCancelCaseApply($ids);
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
     * 取消应聘案件确认
     */
    public function  cancelapplycaseconfirmAction() {
		$this->layout->headTitle($this->tr->translate('cancel_apply_case_confirm'));
		$crumb = array('uri' => '/usr_applycase/cancelapplycaseconfirm', 'name' => $this->tr->translate('cancel_apply_case_confirm'));
		$this->view->layout()->crumbs = array($crumb);
		
		$applyList = $this->_getParam('applyList');
		
		$this->view->applyList = $applyList;
		$this->view->ids = $this->_getParam('ids');
    }
    
	/**
	 * 个人主动取消案件应聘
     * 应聘取消信息保存
     */
    public function savecasecancelAction() {
    	$ids = $this->_getParam('ids');
    	$retVal = CaseApplyService::valCancelCaseApply($ids);
    	if ($retVal !== FALSE && is_string($retVal)) {
    		$this->showErrorMsg('/', 'apply_cancel', $retVal);
    	}
    	
    	$role_code = $this->auth->usr['role_code'];
		$applyList = $this->_getParam('applyList');
		$applyList = (array)json_decode(stripslashes($applyList), true);
		
		$db = $this->db;
		$db->beginTransaction();
		$err = FALSE;
			
		foreach ($applyList as $ca) {
			$caseapply = array();
			$caseapply['remark'] = $ca['remark'];
			$caseapply['reason'] = $ca['lackReason'];
			$caseapply['status'] = 'APPLY_CANCEL';
			$caseapply['cancel_body'] = $role_code == 'LP' ? 'caseapple_cancel_emp' : 'caseapple_cancel_usr';
			
			$applyId = $ca['applyId'];
			
			$r = CaseApplyService::updateCaseApply($applyId, $caseapply, $this->auth->usr['code']);
	    	if ($r == FALSE) {
	    		$err = TRUE;
	    		break;
	    	}
		}
		if ($err) {
			$db->rollback();
			$this->showErrorMsg('/', 'apply_cancel');
		} else {
			$db->commit();
			$this->showSuccessMsg('/', 'apply_cancel');
		}
    }
    
    /**
     * 应聘案件列表
     */
    public function applycaselistAction() {
		$this->layout->headTitle($this->tr->translate('apply_case_list'));
		$crumb = array('uri' => '/usr_usr/applycaselist', 'name' => $this->tr->translate('apply_case_list'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    /**
     * 应聘案件列表-查询应聘案件列表
     */
    public function searchapplycaselistAction() {
    	$this->getPagination();
		$cases = array();
    	$usr_code = isset($this->auth->usr) ? $this->auth->usr['code'] : NULL;
		$dbcases = CaseApplyService::findCaseListByUsrCode($usr_code, NULL, $this->pagination);
		if (!empty($dbcases['Rows'])) {
			foreach ($dbcases['Rows'] as $case) {
				if ((empty($usr_code) || $usr_code == 'MEMBER') && ($case['apply_status'] == 'NO_RECOMMEND' || $case['apply_status'] == 'RECOMMEND')) {
					$case['applyStatusValue'] = $this->getText('NO_VOTE');
					$case['applyReasonValue'] = '';
				} else {
					$case['applyStatusValue'] = $this->getText($case['apply_status']);
					$case['applyReasonValue'] = $this->getText($case['apply_reason']);
				}
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases['Total']['count'], $cases)));
    }
    
    /**
     * 应聘案件历史一览
     */
	public function historyapplycaselistAction() {
		$this->layout->headTitle($this->tr->translate('apply_case_list_history'));
		$crumb = array('uri' => '/usr_usr/applycaselisthistory', 'name' => $this->tr->translate('apply_case_list_history'));
		$this->view->layout()->crumbs = array($crumb);
    }

    /**
     * 应聘案件列表-查询应聘案件列表
     */
    public function searchhistoryapplycaselistAction() {
    	$this->getPagination();
		$cases = array();
    	$usr_code = isset($this->auth->usr) ? $this->auth->usr['code'] : NULL;
		$dbcases = CaseApplyService::findHistoryCaseListByUsrCode($usr_code, $this->pagination);
		if (!empty($dbcases['Rows'])) {
			foreach ($dbcases['Rows'] as $case) {
				$case['applyStatusValue'] = $this->getText($case['apply_status']);
				$case['applyReasonValue'] = $this->getText($case['apply_reason']);
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases['Total'], $cases)));
    }
}