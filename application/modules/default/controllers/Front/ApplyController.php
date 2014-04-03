<?php
include_once 'BaseController.php';
include_once 'CaseApplyService.php';
include_once 'ResumeService.php';
include_once 'UtilService.php';
include_once 'LpService.php';
class Front_ApplyController extends BaseController {
	
	/**
	 * 应聘主页面（应聘是个过程，主页面用于根据用户信息是否满足应聘要求而进行相应的跳转）.
	 * 用户（在案件列表里点击“应聘”按钮）应聘案件.
	 */
	public function applyAction() {
		$this->layout->headTitle($this->tr->translate('apply_case'));
		$crumb = array('uri' => '/front_apply/apply', 'name' => $this->tr->translate('apply_case'));
		$this->view->layout()->crumbs = array($crumb);
		
		$case_code = $this->_getParam('case_code');
	}
	
	/**
	 * 用户稼动状态是稼动不可
	 * @deprecated
	 */
	public function notworkstatusAction() {
		$this->layout->headTitle($this->tr->translate('apply_case'));
		$crumb = array('uri' => '/front_apply/notworkstatus', 'name' => $this->tr->translate('apply_case'));
		$this->view->layout()->crumbs = array($crumb);
	}
	
	/**
	 * 简历验证
	 */
	public function applycheckAction() {
		$this->layout->headTitle($this->tr->translate('APPLY_CHECK'));
		$crumb = array('uri' => '/front_case/case', 'name' => $this->tr->translate('APPLY_CHECK'));
		$this->view->layout()->crumbs = array($crumb);
		
		$caseCode = $this->_getParam('caseCode', "");
		if (!isset($this->auth->usr)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('you_have_not_logon'));
		} else if ($this->auth->usr['role_code'] == 'MEMBER') {
			$usrCode = $this->auth->usr['code'];
			//普通人员应聘验证
			$resume = ResumeService::getResumesByTalentCode($usrCode);
			$retVal = CaseApplyService::valCaseApplyForUsr($caseCode, $usrCode, $resume);
			if ($retVal===FALSE) {
				$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
				exit(json_encode($ret));
			} else if ($retVal!==TRUE) {
				$this->view->errs = $retVal;
				$this->view->searchFields = (array)json_decode($this->_getParam('searchFields'), true);
				if (!empty($resume)) {
					$this->view->resume = $resume[0];
				}
			} else {
				$this->_forward('previewresume', 'usr_resume', 'default', array('resumeCode'=>$resume[0]['code'], 'caseCode'=>$caseCode));
			}
		} else if ($this->auth->usr['role_code'] == 'LP') {
			$usrCode = $this->auth->usr['code'];
			$retVal = CaseApplyService::valCaseApplyForLp($caseCode, $usrCode);
			if ($retVal === FALSE) {
				$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
				exit(json_encode($ret));
			} else if ($retVal !== TRUE) {
				$ret = array('err' => 1, 'msg' => $this->tr->translate($retVal));
				exit(json_encode($ret));
			}
			$ret = array('err' => 0);
			exit(json_encode($ret));
		}
	}
	
	/**
	 * 个人应聘
	 * 获取应聘信息-验证数据-保存结果-返回提示信息
	 */
	public function applycaseforusrAction() {
		$caseCode = $this->_getParam('caseCode');
		$usrCode = $this->auth->usr['code'];
		$resume = ResumeService::getResumesByTalentCode($usrCode);
		$retVal = CaseApplyService::valCaseApplyForUsr($caseCode, $usrCode, $resume);
		if ($retVal !== TRUE) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
		} else {
			$caseApply = array('case_code'=>$caseCode, 'usr_code'=>$usrCode, 'resume_code'=>$resume[0]['code']);
			$retVal = CaseApplyService::saveCaseApply($caseApply);
			if ($retVal !== FALSE) {
				$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
			}
		}
		exit(json_encode($ret));
	}
	
	/**
	 * 法人员工应聘
	 * 验证
	 *   如果已经应聘过，不会报错，但也不会对其再次应聘，而是在后台直接筛选掉
	 *   对法人下的员工，应聘案件是否是法人自己的案件进行验证
	 * 保存
	 */
	public function applycaseforlpAction() {
		$empCodeList = $this->_getParam('resumeCodes', "");
		$caseCode = $this->_getParam('caseCode', "");
		if (!isset($this->auth->usr)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('you_have_not_logon'));
			exit(json_encode($ret));
		} else {
			$usr_code = $this->auth->usr['code'];
			$retVal = CaseApplyService::valCaseApplyForLp($caseCode, $usr_code);
			if ($retVal == FALSE) {
				$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
				exit(json_encode($ret));
			} elseif (is_string($retVal)) {
				$ret = array('err' => 1, 'msg' => $this->tr->translate($retVal));
				exit(json_encode($ret));
			} else 
				$ret = CaseApplyService::saveCaseApplyForLp($caseCode, $usr_code, $empCodeList);		
			
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
	 * 对案件点击应聘，需要选择员工进行应聘
	 */
	public function chooseempAction() {
		$this->layout->headTitle($this->tr->translate('choose_emp'));
		$crumb = array('uri' => '/front_apply/chooseEmp', 'name' => $this->tr->translate('choose_emp'));
		$this->view->layout()->crumbs = array($crumb);
		
		$caseCode = $this->_getParam('caseCode', "");
		$this->view->caseCode = $caseCode;
	}
	
	
	/**
	 * 查找当前法人旗下的可应聘选中案件的员工列表
	 */
	public function findemplistAction() {
		$this->getPagination();
		$caseCode = $this->_getParam('caseCode', "");
		$range = $this->_getParam('range');
		$sValue = $this->_getParam('sValue');
		$dbemps = LpService::findEmpListWithForLpByLpCode($this->auth->usr["code"], $this->createCaseSearch($range, $sValue), $this->pagination);
		if (!empty($dbemps['Rows'])) {
			$fsRsmBiz = array('type', 'biz', 'biz_name', 'age');
			foreach ($dbemps['Rows'] as &$emp) {
				$cond = "resume_code = '{$emp['code']}' and type = 'FRAMEWORK'";
				$rsmBizs = BaseService::getAllByCond('resume_biz', $fsRsmBiz, $cond);
				$this->rsmDetailToView($emp, $rsmBizs);
			}
		}
		exit(json_encode($this->genPagination($dbemps['Total']['count'], $dbemps['Rows'])));
	}
	
	
	/**
	 * 法人应聘案件确认
	 */
	public function applycaseconfirmAction() {
		//导航
		$this->layout->headTitle($this->tr->translate('apply_case_confirm'));
		$crumb = array('uri' => '/front_invite/chooseinvitecase', 'name' => $this->tr->translate('apply_case_confirm'));
		$this->view->layout()->crumbs = array($crumb);
		
		$caseCode = $this->_getParam('caseCode'); 
		$resumeCodes = $this->_getParam('resumes');
		
		$resumeList = array();
		$resumes = CaseApplyService::findResumeListByResumeCodesForLpApply($resumeCodes, $caseCode);
		$fsRsmBiz = array('type', 'biz', 'biz_name', 'age');
		foreach ($resumes as &$resume) {
			array_push($resumeList, $this->resumeToView($resume));
//			$cond = "resume_code = '{$resume['code']}' and type = 'FRAMEWORK'";
//			$rsmBizs = BaseService::getAllByCond('resume_biz', $fsRsmBiz, $cond);
//			$this->rsmDetailToView($resume, $rsmBizs);
		}
		$case = CaseService::findCaseByCode($caseCode);
		
		$this->view->case = $this->caseToView($case);
		$this->view->resumeList = $resumeList;
	}
	
	
	
} //End: class Front_ApplyController