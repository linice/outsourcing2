<?php
include_once 'BaseController.php';
include_once 'CaseService.php';
class Lp_CaseunpublishController extends BaseController {
    /**
     * 未发布案件列表
     */
    public function unpublishcaselistAction() {
		$this->layout->headTitle($this->tr->translate('unpublish_case_list'));
		$crumb = array('uri' => '/lp_caseunpublish/unpublishcaselist', 'name' => $this->tr->translate('unpublish_case_list'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    /**
     * 查找未发布案件列表
     */
    public function searchunpublishcaselistAction() {
		$this->getPagination();
		$range = $this->_getParam("range");
		$casename = $this->_getParam("casename");
		$cases = array();
		$dbcases = CaseService::searchDraftCasesList($this->auth->usr["code"], $this->createCaseSearch($range, $casename), $this->pagination); 
		//var_dump($dbcases);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
    }
}