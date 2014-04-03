<?php
include_once 'BaseController.php';
include_once 'CaseService.php';
/**
 * 法人中心-募集中，募集终了案件列表
 * @author GONGM
 */
class Lp_CasecollectController extends BaseController {
    /**
     * 进入募集中案件列表界面
     */
    public function incollectcaselistAction() {
		$this->layout->headTitle($this->tr->translate('cases_view'));
		$crumb = array('uri' => '/lp_casecollect/incollectcaselist', 'name' => $this->tr->translate('cases_view'));
		$this->view->layout()->crumbs = array($crumb);
    }
 
    /**
     * 查找募集终了案件--历史案件
     */
    public function searchhistorycasesAction() {
		$this->getPagination();
		$range = $this->_getParam("range");
		$casename = $this->_getParam("casename");
		$cases = array();
		$dbcases = CaseService::searchHistoryCaseList($this->auth->usr["code"], $this->createCaseSearch($range, $casename), $this->pagination);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
    }
 
    /**
     * 查找有效案件列表
     */
    public function searcheffectivecasesAction() {
		$this->getPagination();
		$range = $this->_getParam("range");
		$casename = $this->_getParam("casename");
		$cases = array();
		$dbcases = CaseService::searchEffectiveCaseList($this->auth->usr["code"], $this->createCaseSearch($range, $casename), $this->pagination);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
    }

    /**
     * 查找已发布案件列表
     */
    public function searchreleasecasesAction() {
		$this->getPagination();
		$range = $this->_getParam("range");
		$casename = $this->_getParam("casename");
		$cases = array();
		$dbcases = CaseService::searchReleaseCaseList($this->auth->usr["code"], $this->createCaseSearch($range, $casename), $this->pagination);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
    }
}