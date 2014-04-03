<?php
include_once 'BaseController.php';
include_once 'CaseAttentionService.php';

/**
 * 法人中心-感兴趣案件列表
 * @author GONGM
 */
class Lp_CaseinterestController extends BaseController {
    
    /**
     * 感兴趣案件列表-募集中
     */
    public function interestcaselistAction() {
		$this->layout->headTitle($this->tr->translate('interest_case_list'));
		$crumb = array('uri' => '/lp_caseinterest/interestcaselist', 'name' => $this->tr->translate('interest_case_list'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
	/**
     * 查找当前用户的募集中案件关注列表
     */
    public function searchattentioncaseAction() {
		$this->getPagination();
		$cases = array();
		$range = $this->_getParam("range");
		$casename = $this->_getParam("casename");
		$dbcases = CaseAttentionService::findReleaseAttentionCaseListForApplyByLpCode($this->auth->usr["code"], $this->createCaseSearch($range, $casename), $this->pagination);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
    }
 
    /**
     * 查找当前用户的募集终了案件关注列表
     */
    public function searchendattentioncaseAction() {
		$this->getPagination();
		$cases = array();
		$range = $this->_getParam("range");
		$casename = $this->_getParam("casename");
		$dbcases = CaseAttentionService::findClosedAttentionCaseListForApplyByLpCode($this->auth->usr["code"], $this->createCaseSearch($range, $casename), $this->pagination, $this->auth->usr["role_code"]);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($cases, $this->caseToView($case));
			}
		}
		exit(json_encode($this->genPagination($dbcases["Total"], $cases)));
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
    		$retVal = CaseAttentionService::attentionCases(explode(",", $ids), $this->auth->usr["code"]);
    		if($retVal === FALSE) {
    			$db->rollback();
	    		$ret = array('err' => 0, 'msg' => $this->tr->translate('op_error'));
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
}