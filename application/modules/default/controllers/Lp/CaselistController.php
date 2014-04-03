<?php
include_once 'BaseController.php';
include_once 'CaseApplyService.php';
include_once 'ResumeService.php';

class Lp_CaselistController extends BaseController {
	
	/**
	 * 法人案件列表
	 */
	public function collectcaselistAction() {
		$this->layout->headTitle($this->tr->translate('collect_case_list'));
		$crumb = array('uri' => '/admin/admin_caselist/collectcaselist', 'name' => $this->tr->translate('collect_case_list'));
		$this->view->layout()->crumbs = array($crumb);
		
		$code = $this->_getParam('code');
		if (!empty($code)) {
			$resume = ResumeService::getResumeByCode($code);
			if ($resume !== FALSE) {
				$this->view->emp = $resume;
			}
		}
	}
	
	/**
	 * 法人员工应聘案件列表-查询
	 */
	public function searchapplycaselistAction() {
		$this->getPagination();
		$range = $this->_getParam('range');
		$sValue = $this->_getParam('sValue');
		$cases = array();
    	$usr_code = isset($this->auth->usr) ? $this->auth->usr["code"] : NULL;
		$dbcases = CaseApplyService::findCaseListByUsrCode($usr_code, $this->genLpEmpCaseOption($range, $sValue), $this->pagination);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				$case['applyStatusValue'] = $this->getText($case['apply_status']);
				$case['applyReasonValue'] = $this->getText($case['apply_reason']);
				$case['applyLackValue'] = $case['apply_status'] == 'APPLY_CANCEL' ? $this->getText($case['apply_cancel_body']).$this->getText('CANCEL').' ' : '';
				$case['applyLackValue'] = $case['applyLackValue'].$case['applyReasonValue']/*.' '.$case['apply_remark']*/;
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"]["count"], $cases)));
	}

	/**
	 * 募集case列表
	 */
	public function searchcollectcaselistAction() {
		$this->layout->headTitle($this->tr->translate('collect_case_list'));
		$crumb = array('uri' => '/admin/admin_caselist/collectcaselist', 'name' => $this->tr->translate('collect_case_list'));
		$this->view->layout()->crumbs = array($crumb);
	}

	/**
	 * 历史应聘case列表
	 */
	public function historyapplycaselistAction() {
		$this->layout->headTitle($this->tr->translate('history_apply_case_list'));
		$crumb = array('uri' => '/admin/admin_caselist/historyapplycaselist', 'name' => $this->tr->translate('history_apply_case_list'));
		$this->view->layout()->crumbs = array($crumb);
	}
	
	/**
	 * 法人员工直接应聘
	 */
	public function applycasewithinviteinfoAction() {
		$ids = $this->_getParam('ids');
		
		$invites = InviteService::findInviteCasesByInviteIds($ids);
		if ($invites !== FALSE) {
			//$db = $this->db;
			//$db->beginTransaction();
			$err = TRUE;
			foreach ($invites as $invite) {
				$err = CaseApplyService::saveCaseApplyForLp($invite['code'], $this->auth->usr['code'], $invite['resume_code']);
				if ($err === FALSE) {
		    		break;
		    	}
			}
			if ($err === FALSE) {
				//$db->rollback();
				$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
				exit(json_encode($ret));
			} else {
				//$db->commit();
				$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
				exit(json_encode($ret));
			}
		} else {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('data_not_exist'));
			exit(json_encode($ret));
		}
	}
}