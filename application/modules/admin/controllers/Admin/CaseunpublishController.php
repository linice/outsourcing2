<?php
include_once 'BaseAdminController.php';
include_once 'CaseService.php';
class Admin_Admin_CaseunpublishController extends BaseAdminController {
    
    /**
     * 未发布案件列表
     */
    public function unpublishcaselistAction() {
		$this->layout->headTitle($this->tr->translate('unpublish_case_list'));
		$crumb = array('uri' => '/admin/admin_caseunpublish/unpublishcaselist', 'name' => $this->tr->translate('unpublish_case_list'));
		$this->view->layout()->crumbs = array($crumb);
    }

    public function searchunpublishcaselistAction() {
    	$this->getPagination();
		$range = $this->_getParam("range");
		$casename = $this->_getParam("casename");
		$cases = array();
		$dbcases = CaseService::searchDraftCasesList(null, $this->createCaseSearch($range, $casename), $this->pagination); 
		
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
    }
}