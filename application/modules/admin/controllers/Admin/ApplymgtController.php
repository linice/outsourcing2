<?php
include_once 'BaseAdminController.php';
include_once 'CaseService.php';
include_once 'CaseApplyService.php';
class Admin_Admin_ApplymgtController extends BaseAdminController {
    
    /**
     * 应聘管理
     */
    public function applymgtAction() {
		$this->layout->headTitle($this->tr->translate('apply_mgt'));
		$crumb = array('uri' => '/admin/admin_applymgt/applymgt', 'name' => $this->tr->translate('apply_mgt'));
		$this->view->layout()->crumbs = array($crumb);
    
		$caseCode = $this->_getParam("caseCode");

		if (!empty($caseCode)) {
			$case = CaseService::findCaseByCode($caseCode);
			if (empty($case)) {
				$this->showErrorMsg(NULL, NULL, $this->getText('data_not_exist'));
			}
			$this->view->tab = $this->_getParam('t');
			$usrList = CaseApplyService::findUsrListByCaseCode($caseCode);
			$this->view->usr = $this->auth->usr;
			$this->view->case = $this->caseToView($case);
			$this->view->usrList = $this->caseapplyListToView($usrList);
		} else {
			$this->showErrorMsg($this->getText('data_not_exist'));
		}
    }
}