<?php
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'BaseAdminController.php';
include_once 'EtcService.php';
include_once 'ActiveEtcService.php';
include_once 'CaseService.php';
class Admin_Admin_CaseController extends BaseAdminController {
	
	private $case = array('caseId'=>null, 'careers'=>array(), 'languages'=>array(), 'industries'=>array(),
						'caseName'=> null, 'businessReq'=> null, 'technicalReq'=> null, 'startDateCurrent'=>null,
						'jpl'=> null, 'caseRange1'=> null, 'caseRange2'=> null, 'lp_code'=>null,
						'startDate'=> null, 'endRange'=> null, 'endDate'=> null,'type'=>null,
						'delay'=> null, 'workingPlace'=> null, 'workingPlaceDetail'=> null,
						'unitPrice'=>null, 'unitPriceDetail1'=>null, 'unitPriceDetail2'=> null,'unitPriceUnit'=>NULL,
						'overtimePay'=>null, 'overtimePayDetail'=>null, 'interviewer'=>null,
						'ageReq'=>null, 'ageReqDetail1'=>null, 'ageReqDetail2'=>null, 'akb'=>null,
						'countryReq'=>null, 'timeliness'=>null, 'visibility'=>null, 'remark'=>null); 

	private $careers = array();
	private $languages = array();
	private $industries = array();
	private $jpl = array();
	private $caseRange = array();
	private $endRange = array();
	private $overtimePay = array();
	private $caseDelay = array();
	private $unitPrice = array();
	private $workingPlace = array();
	private $interviewer = array();
	private $ageReq = array();
	private $countrieReq = array();
	private $visibility = array();
	private $unitPriceUnit = array();
	
	/**
	 * 初始化页面元素
	 */
	private function initcasepage() {
		//查询杂项，Web app变量
		$etcs = EtcService::getEtcs();
		foreach ($etcs as $etc) {
			if ($etc['type'] == 'COUNTRY_REQ') { //国家：日本，中国
				$this->countrieReq[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'WORKING_PLACE_JP') {
				$this->workingPlace[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'WORKING_PLACE_OTHER') {
				$this->workingPlace[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'CAREER') { //职种
				$this->careers[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'INDUSTRY') { //业务领域
				$this->industries[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'LANGUAGE') { //程序设计语言
				$this->languages[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'JPL') { //日语要求
				$this->jpl[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'CASE_RANGE') { //担当范围
				$this->caseRange[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'END_RANGE') { //项目结束日期范围
				$this->endRange[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'UNIT_PRICE') { //单价
				$this->unitPrice[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'INTERVIEWER_NUM') { //面试次数
				$this->interviewer[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'VISIBILITY') {
				$this->visibility[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'OVERTIME_PAY') {
				$this->overtimePay[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'CASE_DELAY') {
				$this->caseDelay[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'AGE_REQ') {
				$this->ageReq[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'UNIT_PRICE_UNIT') {
				$this->unitPriceUnit[$etc['code']] = $this->tr->translate($etc['code']);
			}
		}
		
		$this->view->careers = $this->careers;
		$this->view->languages = $this->languages;
		$this->view->industries = $this->industries;
		$this->view->jpl = $this->jpl;
		$this->view->caseRange = $this->caseRange;
		$this->view->endRange = $this->endRange;
		$this->view->overtimePay = $this->overtimePay;
		$this->view->caseDelay = $this->caseDelay;
		$this->view->workingPlace = $this->workingPlace;
		$this->view->unitPrice = $this->unitPrice;
		$this->view->interviewer = $this->interviewer;
		$this->view->ageReq = $this->ageReq;
		$this->view->countrieReq = $this->countrieReq;
		$this->view->visibility = $this->visibility;
		$this->view->unitPriceUnit = $this->unitPriceUnit;
	}
	
	/**
	 * 添加新案件
	 */
	public function createcaseAction() {
		$this->layout->headTitle($this->tr->translate('create_case'));
		$crumb = array('uri' => '/admin/admin_case/createcase', 'name' => $this->tr->translate('create_case'));
		$this->view->layout()->crumbs = array($crumb);
		
		$jsRegisterInfo = $this->_getParam('copyCase');
		if (!empty($jsRegisterInfo)) {
			$case = (array)json_decode($jsRegisterInfo);
			$case = $this->copyNewCase($case);
			//$lp = UsrService::getUsrByCode($case['lp_code'], array('balance'));
			//$this->view->currAkb = empty($lp) ? null : $lp['balance'];
			$this->view->currAkb = 0;
		} else {
			$jsRegisterInfo = $this->_getParam('case');
			$case = (array)json_decode($jsRegisterInfo);
			$caseId = $this->_getParam('caseId');
			$case['id'] = $caseId;
		}
		$this->view->orgCase = $case;
		
		$this->initcasepage();
	}
	
	/**
	 * 从param中接收参数到对象ob中
	 * @param unknown_type $ob
	 */
	private function fetchFromParam() {
		foreach (array_keys($this->case) as $value) {
			if (is_array($this->case[$value])) 
				$this->case[$value] = $this->_getParam($value);
			else 
				$this->case[$value] = trim($this->_getParam($value));
		}
	}
	
	/**
	 * 验证案件的信息
	 */
	private function valCaseinfo() {
		if (empty($this->case['lp_code'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('lp_can_not_be_empty'));
			exit(json_encode($ret));
		} if (empty($this->case['careers'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('careers_can_not_be_empty'));
			exit(json_encode($ret));
		} else if (empty($this->case['languages'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('languages_can_not_be_empty'));
			exit(json_encode($ret));
		} else if (empty($this->case['industries'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('industries_can_not_be_empty'));
			exit(json_encode($ret));
		} else if (empty($this->case['caseName'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('casename_can_not_be_empty'));
			exit(json_encode($ret));
		} else if (empty($this->case['jpl'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('jpl_can_not_be_empty'));
			exit(json_encode($ret));
		} else if ($this->case['startDateCurrent']!='Y' && empty($this->case['startDate'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('startdate_can_not_be_empty'));
			exit(json_encode($ret));
		} else if (empty($this->case['endRange']) && empty($this->case['endDate'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('enddate_can_not_be_empty'));
			exit(json_encode($ret));
		} else if (empty($this->case['workingPlace']) && empty($this->case['workingPlaceDetail'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('workingplace_can_not_be_empty'));
			exit(json_encode($ret));
		} else if (empty($this->case['timeliness'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('timeliness_can_not_be_empty'));
			exit(json_encode($ret));
		}
		return TRUE;
	}

	/**
	 * 从页面接收案件信息组装成可以保存进数据的数组对象，ID没有组装进来
	 * @param $confirm FALSE：保存/修改; TRUE: 提交 
	 * @param $save FALSE：只封闭对象，不保存到数据库; TRUE:  
	 */
	private function genCSaveCase() {
		$this->fetchFromParam();
		$ret = $this->valCaseinfo();
		$newCase = array();
		$newCase['name'] = $this->case['caseName'];
		//$newCase['lp_name'] = $this->case['lp_name'];
		$newCase['careers'] = join(";", $this->case['careers']);
		$newCase['languages'] = join(";", $this->case['languages']);
		$newCase['industries'] = join(";", $this->case['industries']);
		$newCase['age_req'] = $this->case['ageReq'];
		$newCase['age_req_begin'] = $this->case['ageReqDetail1'];
		$newCase['age_req_end'] = $this->case['ageReqDetail2'];
		$newCase['business_req'] = $this->case['businessReq'];
		$newCase['technical_req'] = $this->case['technicalReq'];
		$newCase['case_range_start'] = $this->case['caseRange1'];
		$newCase['case_range_end'] = $this->case['caseRange2'];
		$newCase['country_req'] = $this->case['countryReq'];
		$newCase['delay'] = $this->case['delay'];
		$newCase['end_date'] = $this->case['endDate'];
		$newCase['end_range'] = $this->case['endRange'];
		$newCase['interviewer'] = $this->case['interviewer'];
		$newCase['jpl'] = $this->case['jpl'];
		$newCase['overtime_pay'] = $this->case['overtimePay'];
		$newCase['overtime_pay_detail'] = $this->case['overtimePayDetail'];
		$newCase['remark'] = $this->case['remark'];
		$newCase['start_date_current'] = $this->case['startDateCurrent'];
		$newCase['start_date'] = $this->case['startDate'];
		$newCase['timeliness'] = $this->case['timeliness'];
		$newCase['unit_price'] = $this->case['unitPrice'];
		$newCase['unit_price_low'] = $this->case['unitPriceDetail1'];
		$newCase['unit_price_high'] = $this->case['unitPriceDetail2'];
		$newCase['unit_price_low_view'] = '';
		$newCase['unit_price_high_view'] = '';
		$newCase['unit_price_unit'] = $this->case['unitPriceUnit'];;
		$newCase['working_place'] = $this->case['workingPlace'];
		$newCase['visibility'] = $this->case['visibility'];
		$newCase['working_place_detail'] = $this->case['workingPlaceDetail'];
		$newCase['lp_code'] = $this->case['lp_code'];
		$newCase['akb'] = $this->case['akb'];
		$newCase['type'] = $this->case['type'];
		return $newCase;
	}
	
	/**
	 * 以草稿的形式保存案件
	 */
	public function savecaseindraftAction() {
		$case = $this->genCSaveCase();
		$is_edit = $this->_getParam('editflag');
		
		$db = $this->db;
		$db->beginTransaction();
		if (empty($this->case['caseId'])) {
			$id = CaseService::createDraftCase($case);
			if ($id != FALSE) {
				$db->commit();
				$ret = array('err' => 0, 'msg' => $this->tr->translate('case_create_success'), 'id' => $id, 'type' => 'U');
				exit(json_encode($ret));
			}
		} else {
			$ret = CaseService::updateCase($this->case['caseId'], $case);
			if ($ret != FALSE) {
				$db->commit();
				if (!empty($is_edit)) {
					$ret = array('err' => 0, 'msg' => $this->tr->translate('case_edit_success'));
				} else {
					$ret = array('err' => 0, 'msg' => $this->tr->translate('case_create_success'));
				}
				exit(json_encode($ret));
			}
		}
		$db->rollback();
		$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
		exit(json_encode($ret));
	}
	
	/**
	 * 添加新案件确认
	 */
	public function createcaseconfirmAction() {
		$this->layout->headTitle($this->tr->translate('create_case'));
		$crumb = array('uri' => '/admin/admin_case/createcase', 'name' => $this->tr->translate('create_case'));
		$this->view->layout()->crumbs = array($crumb);
		
		$case = $this->genCSaveCase();

		//向页面传递信息
		$this->view->case = $this->caseToView($case);
		$this->view->saveCase = $case;
		$this->view->orgCase = $this->case;
		$this->view->caseId = $this->case['caseId'];
	}
 
	/**
	 * 案件发布
	 */
	public function publishcaseAction() {
		$this->layout->headTitle($this->tr->translate('create_case_confirm'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_case/publishcase', 'name' => $this->tr->translate('case_publish'));
		$this->view->layout()->crumbs = $crumbs;
		
		$jsRegisterInfo = $this->_getParam('case');
		//$case = (array)json_decode($jsRegisterInfo);
		//$caseId = $this->_getParam('caseId');
		$case = $this->genCSaveCase();
		$caseId = $this->case['caseId'];
		
		$db = $this->db;
		$db->beginTransaction();
		$ret = CaseService::publishCase($caseId, $case, $case['lp_code']);
		if ($ret !== FALSE) {
			$db->commit();
			$this->showSuccessMsg("/lp_lp/lp", 'create_case');
		} else {
			$db->rollback();
			$this->showErrorMsg("/lp_lp/lp", 'create_case');
		}
	}

	/**
	 * 编辑案件
	 */
	public function editcaseAction() {
		$this->layout->headTitle($this->tr->translate('edit_case'));
		$crumb = array('uri' => '/admin/admin_case/editcase', 'name' => $this->tr->translate('edit_case'));
		$this->view->layout()->crumbs = array($crumb);
		
		$jsRegisterInfo = $this->_getParam('case');
		$case = null;
		if (empty($jsRegisterInfo)) {
			$caseCode = $this->_getParam('caseCode');
			if (!empty($caseCode)) {
				$case = CaseService::findCaseByCode($caseCode);
				if ($case == FALSE) {
					$this->showErrorMsg($this->getText('case_is_not_exists'));
				}
				$this->view->case = $case;
			} else {
				$this->showErrorMsg($this->getText('case_is_not_exists'));
			}
		} else {
			$case = (array)json_decode($jsRegisterInfo);
			$case['id'] = $this->_getParam('caseId');
			$this->view->case = $case;
		}
		
		$this->initcasepage();
		$this->view->lp = UsrService::getUsrByCode($case['lp_code'], array('balance'));
	}
	
	/**
	 * 编辑case确认
	 */
	public function editcaseconfirmAction() {
		$this->layout->headTitle($this->tr->translate('edit_case_confirm'));
		$crumb = array('uri' => '/admin/admin_case/editcaseconfirm', 'name' => $this->tr->translate('edit_case_confirm'));
		$this->view->layout()->crumbs = array($crumb);
		
		$case = $this->genCSaveCase();

		//向页面传递信息
		$this->view->case = $this->caseToView($case);
		$this->view->saveCase = $case;
		$this->view->caseId = $this->case['caseId'];
	}
	
	/**
	 * 修改Case确认的一进行保存操作，保存后直接转向到募集中案件管理
	 */
	public function savemodifycaseAction() {
		$jsRegisterInfo = $this->_getParam('case');
		$case = (array)json_decode($jsRegisterInfo);
		$caseId = $this->_getParam('caseId');
		$publish = $this->_getParam('publish');

		$db = $this->db;
		$db->beginTransaction();
		if ($publish == 'Y')
			$ret = CaseService::publishCase($caseId, $case, $this->auth->usr['code']);
		else 
   			$ret = CaseService::updateCase($caseId, $case);
		if ($ret !== FALSE) {
			$db->commit();
			$this->showSuccessMsg("/lp_lp/lp", 'edit_case');
		} else {
			$db->rollback();
			$this->showErrorMsg("/lp_lp/lp", 'edit_case');
		}
	}

	/**
	 * 案件详情
	 */
	public function casedetailAction() {
		$this->layout->headTitle($this->tr->translate('case_detail'));
		$crumb = array('uri' => '/admin/admin_case/casedetail', 'name' => $this->tr->translate('case_detail'));
		$this->view->layout()->crumbs = array($crumb);

		$btnlist = $this->_getParam('btnlist');
		$caseCode = $this->_getParam('caseCode');

		if (!empty($caseCode)) {
			$btnList = explode(',', $btnlist);
			$case = CaseService::findCaseByCode($caseCode);
			if ($case == FALSE) {
				$this->showErrorMsg('/admin/admin_casesearch/casesearch', 'case_search', 'case_is_not_exists');
			}
			$this->view->case = $this->caseToView($case);
			$this->view->orgCase = $case;
			$this->view->btnList = $btnList;
		} else {
			$this->showErrorMsg('/admin/admin_casesearch/casesearch', 'case_search', 'case_is_not_exists');
		}
	}
	
	public function searchcasecreatorAction() {
		$this->getPagination();
		$cases = array();
		$range = $this->_getParam("range");
		$sValue = $this->_getParam("sValue");
		$lpList = CaseService::searchCaseCreatorList($this->genLpOption($range, $sValue), array('*'), $this->pagination, $reg=FALSE);
		exit(json_encode($lpList));
	}
	
}