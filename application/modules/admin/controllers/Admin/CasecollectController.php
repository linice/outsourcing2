<?php
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'BaseAdminController.php';
include_once 'CaseApplyService.php';

class Admin_Admin_CasecollectController extends BaseAdminController {
    
    /**
     * 募集中case列表
     */
    public function incollectcaselistAction() {
		$this->layout->headTitle($this->tr->translate('ADMIN_CASE_MANAGER'));
		$crumb = array('uri' => '/admin/admin_casecollect/incollectcaselist', 'name' => $this->tr->translate('ADMIN_CASE_MANAGER'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
	/**
	 * 查找需要进行应聘管理的案件
	 */
	public function searcheffectivecasemgtAction() {
		$this->getPagination();
		$cases = array();
		$range = $this->_getParam('range');
		$casename = $this->_getParam('casename');
		if (empty($this->pagination['sortname'])) {
			$this->pagination['sortname'] = 'count_total';
			$this->pagination['sortorder'] = 'desc';
		}
		$dbcases = CaseService::searchEffectiveApplyMgtCaseList(NULL, $this->createCaseSearch($range, $casename), $this->pagination, $this->orderBy);
		if (!empty($dbcases['Rows'])) {
			foreach ($dbcases['Rows'] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases['Total'], $cases)));
	}
	
	/**
	 * 查找需要进行应聘管理的案件
	 */
	public function searchhistorycasemgtAction() {
		$this->getPagination();
		$cases = array();
		$range = $this->_getParam('range');
		$casename = $this->_getParam('casename');
		if (empty($this->pagination['sortname'])) {
			$this->pagination['sortname'] = 'count_total';
			$this->pagination['sortorder'] = 'desc';
		}
		$dbcases = CaseService::searchHistoryApplyMgtCaseList(NULL, $this->createCaseSearch($range, $casename), $this->pagination);
		if (!empty($dbcases['Rows'])) {
			foreach ($dbcases['Rows'] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases['Total'], $cases)));
	}
    
    /**
     * 募集终了case列表
     */
    public function endcollectcaselistAction() {
		$this->layout->headTitle($this->tr->translate('end_collect_case_list'));
		$crumb = array('uri' => '/admin/admin_casecollect/endcollectcaselist', 'name' => $this->tr->translate('end_collect_case_list'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    /**
     * 管理员募集-案件搜索
     */
    public function collectcasesearchAction() {
		$this->layout->headTitle($this->tr->translate('collect_case_search'));
		$crumb = array('uri' => '/admin/admin_casecollect/collectcasesearch', 'name' => $this->tr->translate('collect_case_search'));
		$this->view->layout()->crumbs = array($crumb);
		
		$this->view->resumeCodes = $this->_getParam('ids');
    }
    
    /**
     * 管理员募集-确认
     */
    public function collectcaseconfirmAction() {
		$this->layout->headTitle($this->tr->translate('collect_case_confirm'));
		$crumb = array('uri' => '/admin/admin_casecollect/collectcaseconfirm', 'name' => $this->tr->translate('collect_case_confirm'));
		$this->view->layout()->crumbs = array($crumb);
		
		$caseCode = $this->_getParam('caseCode');
		$resumeCodes = $this->_getParam('resumeCodes');
		
		$resumeList = array();
    	$resumes = CaseApplyService::findResumeListByResumeCodesExpApply($resumeCodes, $caseCode);
    	foreach ($resumes as $resume) {
    		array_push($resumeList, $this->resumeToView($resume));
    	}
    	$case = CaseService::findCaseByCode($caseCode);
    	
    	$this->view->case = $this->caseToView($case);
    	$this->view->resumeList = $resumeList;
    }
}