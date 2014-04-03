<?php
include_once 'BaseController.php';
include_once 'EtcService.php';
include_once 'CaseService.php';
include_once 'CaseApplyService.php';
include_once 'CaseAttentionService.php';
/**
 * 主页面案件检索页面
 * @author GONGM
 */
class Front_CaseController extends BaseController {
	private $industries = array();
	private $careers = array();
	private $languages = array();
    private $japan = array();
	private $oversea = array();
	
	private $searchFields = array("range"=>NULL, "casename"=>NULL, "casetype"=>NULL,
								"careers"=>array(), "languages"=>array(), "industries"=>array(),
								"workplace"=>array(), "radiobutton"=>null, "startDate"=>NULL,
								"endDate"=>NULL, "unitprice"=>null);
	
    /**
	 * 进入案件检索页面
	 */
	public function caseAction() {
		$this->layout->headTitle($this->tr->translate('case_search'));
		$crumb = array('uri' => '/front_case/case', 'name' => $this->tr->translate('case_search'));
		$this->view->layout()->crumbs = array($crumb);
		
		$jsSearchFields = $this->_getParam('searchFields');
		$searchFields = json_decode($jsSearchFields, true);
		
		$this->view->orgSearchFields = $searchFields;
		$this->initcasepage();
	}
	
	
	/**
	 * 初始化案件检索页面各种列表
	 */
	private function initcasepage() {
    	//查询杂项，Web app变量
		$etcs = EtcService::getEtcs();
		foreach ($etcs as $etc) {
			if ($etc['type'] == 'CAREER') { //职种
				$this->careers[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'INDUSTRY') { //业务领域
				$this->industries[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'LANGUAGE') { //程序设计语言
				$this->languages[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'WORKING_PLACE_JP') { //日语要求
				$this->japan[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'WORKING_PLACE_OTHER') { //担当范围
				$this->oversea[$etc['code']] = $this->tr->translate($etc['code']);
			}
		}
		$this->view->industries = $this->industries;
		$this->view->careers = $this->careers;
		$this->view->languages = $this->languages;
		$this->view->japan = $this->japan;
		$this->view->oversea = $this->oversea;
    }
	
    /**
	 * 案件检索结果
	 */
	public function casesearchresultAction() {
		$this->layout->headTitle($this->tr->translate('case_search_result'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_case/', 'name' => $this->tr->translate('case_search'));
		$crumbs[] = array('uri' => '/front_case/casesearchresult', 'name' => $this->tr->translate('case_search_result'));
		$this->view->layout()->crumbs = $crumbs;
		
		$jsSearchFields = $this->_getParam('searchFields');
		if (empty($jsSearchFields)) {
			$this->fetchFromParam();
			$searchFields = $this->searchFields;
		} else {
			$searchFields = json_decode($jsSearchFields, true);
		}
		$this->view->searchFields = $searchFields;
		//los2 log
		$jsSearchFields = json_encode($searchFields);
		$log = array('level' => 1, 'msg' => "searchFields: $jsSearchFields", 'class' => __CLASS__, 'func' => __FUNCTION__);
		LogService::saveLog($log);
	}
	
	/**
     * 案件检索结果-查找募集中案件
     */
    public function searchreleasecaseAction() {
		$this->getPagination();
		$this->fetchFromParam();
		$role = isset($this->auth->usr) ? $this->auth->usr["role_code"] : NULL;
		$usr_code = isset($this->auth->usr) ? $this->auth->usr["code"] : NULL;
		$cases = array();
		//los2: 没有用到，所有注释掉
//		$searchFields = $this->_getParam('searchFields');
//		$searchFields = json_encode($searchFields);
		//los2 log
		$jsSearchFields = json_encode($this->searchFields);
		$log = array('level' => 1, 'msg' => "searchFields: $jsSearchFields", 'class' => __CLASS__, 'func' => __FUNCTION__);
		LogService::saveLog($log);
		
		$dbcases = CaseService::findReleaseCasesFront($this->searchFields, $usr_code, $this->pagination, $role);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
    }

	/**
     * 案件检索结果-查找有效案件
     */
    public function searcheffectivecaseAction() {
		$this->getPagination();
		$this->fetchFromParam();
		$role = isset($this->auth->usr) ? $this->auth->usr["role_code"] : NULL;
		$usr_code = isset($this->auth->usr) ? $this->auth->usr["code"] : NULL;
		$cases = array();
		$searchFields = $this->_getParam('searchFields');
		$searchFields = json_encode($searchFields);
		
		$dbcases = CaseService::findEffectiveCasesFront($this->searchFields, $usr_code, $this->pagination, $role);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
    }
    
	/**
     * 案件检索结果-查找历史案件
     */
    public function searchhistorycaseAction() {
		$this->getPagination();
		$this->fetchFromParam();
		$role = isset($this->auth->usr) ? $this->auth->usr["role_code"] : NULL;
		$usr_code = isset($this->auth->usr) ? $this->auth->usr["code"] : NULL;
		$cases = array();
		$searchFields = $this->_getParam('searchFields');
		$searchFields = json_encode($searchFields);
		
		$dbcases = CaseService::findHistoryCasesFront($this->searchFields, $usr_code, $this->pagination, $role);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
    }
	
	/**
     * 从param中接收参数到对象ob中
     * @param unknown_type $ob
     */
    private function fetchFromParam() {
    	foreach (array_keys($this->searchFields) as $value) {
    		if (is_array($this->searchFields[$value])) 
    			$this->searchFields[$value] = $this->_getParam($value);
    		else 
    			$this->searchFields[$value] = trim($this->_getParam($value));
    	}
    }
	
	/**
	 * 案件详情
	 */
	public function casedetailAction() {
		$this->layout->headTitle($this->tr->translate('case_detail'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_case/case', 'name' => $this->tr->translate('case_search'));
		$crumbs[] = array('uri' => '/front_case/casedetail', 'name' => $this->tr->translate('case_detail'));
		$this->view->layout()->crumbs = $crumbs;
		
		$btnlist = $this->_getParam('btnlist');
		$case_code = $this->_getParam('caseCode');

		$usr_code = isset($this->auth->usr) ? $this->auth->usr['code'] : NULL;
		if (!empty($case_code)) {
			$btnList = explode(',', $btnlist);
			if (in_array('cancelApply', $btnList)) {
				$applyId = $this->_getParam('applyId');
				$case = CaseApplyService::findCaseListByApplyIds($applyId);
				if ($case == FALSE) {
					$this->showErrorMsg('/front_case/case', 'case_search', 'case_is_not_exists');
				}
				$case = $case[0];
			} else {
				$case = CaseService::findCaseByIdWithUsrAttention($case_code, $usr_code);
			}
			if ($case == FALSE) {
				$this->showErrorMsg('/front_case/case', 'case_search', 'case_is_not_exists');
			}
			if ((!isset($this->auth->usr) || $this->auth->usr['code'] != $case['lp_code']) && !in_array('submit', $btnList)) {
				$case['viewers'] = $case['viewers']+1;
				CaseService::addViewNumById($case_code);
			}
			$this->view->case = $this->caseToView($case);
			$this->view->orgCase = $case;
			$this->view->btnList = $btnList;
			$this->view->resumeCode = $this->_getParam('resumeCode', NULL);
		} else {
			$this->showErrorMsg('/front_case/case', 'case_search', 'case_is_not_exists');
		}
	}

    /**
     * 取消关注
     */
    public function cancelattentionAction() {
    	$ids = $this->_getParam("ids");
    	if (!empty($ids)) {
    		$retVal = CaseAttentionService::cancelAttentionCases(explode(",", $ids), $this->auth->usr["code"]);
    		if($retVal === FALSE) {
	    		$ret = array('err' => 0, 'msg' => $this->tr->translate('op_error'));
	    		exit(json_encode($ret));
    		} else {
	    		$ret = array('err' => 0, 'msg' => $this->tr->translate('cancel_attention_success'));
    			exit(json_encode($ret));
    		}
    	} else {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('cancel_attention_selected_null'));
    		exit(json_encode($ret));
    	}
    }

    /**
     * 关注
     */
    public function attentionAction() {
    	$ids = $this->_getParam("ids");
    	if (!empty($ids)) {
    		$db = $this->db;
			$db->beginTransaction();
	    	$retVal = CaseAttentionService::valAttentionCases(explode(",", $ids), $this->auth->usr["code"], $this->auth->usr["role_code"]);
	    	if ($retVal == FALSE || is_string($retVal)) {
	    		$retVal = $retVal == FALSE ? $this->getText('op_error') : $this->getText($retVal);
	    		$ret = array('err' => 1, 'msg' => $retVal);
	    		exit(json_encode($ret));
	    	}
    		$retVal = CaseAttentionService::attentionCases(explode(",", $ids), $this->auth->usr["code"]);
    		if($retVal === FALSE) {
    			$db->rollback();
	    		$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
	    		exit(json_encode($ret));
    		} else {
    			$db->commit();
	    		$ret = array('err' => 0, 'msg' => $this->tr->translate('attention_success'));
	    		exit(json_encode($ret));
    		}
    	} else {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('attention_selected_null'));
    		exit(json_encode($ret));
    	}
    }
    
    /**
     * 验证是否可以进行关注操作
     */
    private function valAttentionAction($case_codes) {
    	
    }
	
}