<?php
include_once 'BaseController.php';
include_once 'ResumeService.php';
include_once 'UtilService.php';
include_once 'BaseService2.php';
include_once 'LpService.php';
include_once 'BaseService.php';


/**
 * 法人员工
 * @author GONGM
 */
class Lp_EmpController extends BaseController {
    /**
     * 法人的员工列表
     */
    public function emplistAction() {
    	$crumbs = array(); //用于导航
	    //根据角色设定layout
    	if (in_array($this->auth->usr['role_code'], array('ADMIN', 'ASSIST', 'EDITOR'))) {
    		$this->_helper->layout->setLayout('admin');
    		$crumbs[] = array('uri' => '/admin/admin/index', 'name' => $this->tr->translate('ADMIN_CENTER'));
    	} else {
    		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
    	}
    	
    	//导航
		$this->layout->headTitle($this->tr->translate('emp_list'));
		$crumbs[] = array('uri' => '/lp_emp/emplist', 'name' => $this->tr->translate('emp_list'));
		$this->view->layout()->crumbs = $crumbs;
		
		//获取参数：法人编号
		$lpCode = trim($this->_getParam('lpCode'));
		
		//View
		$this->view->lpCode = $lpCode;
    }
    
    
    /**
     * 查询法人员工
     */
    public function getemplistAction() {
    	//获取参数：法人编号，分页，检索键，检索值，是否应聘有
    	$lpCode = trim($this->_getParam('lpCode'));
    	$this->getPagination();
		$searchKey = trim($this->_getParam('searchKey'));
		$searchVal = trim($this->_getParam('searchVal'));
		$hasApply = trim($this->_getParam('hasApply'));
		
		//校验参数
		if (empty($lpCode)) {
			$lpCode = $this->auth->usr['code'];
		}

		//los2 change begin
		$fields = array('code', 'talent_code', 'fullname', 'sex', 'birthday', 'tel', 'update_time');
		$strFields = implode(',a.', $fields);
		$strFields = 'a.' . $strFields; 
		if ($hasApply == 'Y') {
			$sql = "select $strFields
				from resume as a
				left join case_apply as b
				on a.talent_code = b.emp_code
				where b.status in ('NO_VOTE', 'RECOMMEND', 'NO_RECOMMEND', 'INTERVIEW_ADJUST', 'INTERVIEW_INFORM', 'INTERVIEW_DECIDE') 
					and a.lp_code='$lpCode' ";
		} else {
	    	$sql = "select $strFields from resume as a where a.lp_code='$lpCode'";
		}
		$pgRsms = BaseService::getByPageWithSql($sql, $this->pagination);
		
		//整理$resumes
		$this->rsmsToView($pgRsms['Rows']);
		
		//返回
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $pgRsms['Total'], 'Rows' => $pgRsms['Rows']);
		exit(json_encode($ret));
    }
    
    
    /**
     * 员工应聘案件状况管理
     */
    public function empapplystatusAction() {
		$this->layout->headTitle($this->tr->translate('emp_apply_case_mgt'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_emp/empapplystatus', 'name' => $this->tr->translate('emp_apply_case_mgt'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    /**
     * 员工应聘案件状况管理-查询
     */
    public function searchempapplystatusAction() {
    	$this->getPagination();
		$range = $this->_getParam('range');
		$casename = $this->_getParam('casename');
		$emps = array();
		$dbemps = LpService::findEmpListWithApplyInfoForLpByLpCode($this->auth->usr['code'], $this->createCaseSearch($range, $casename), $this->pagination);
		if (!empty($dbemps['Rows'])) {
			foreach ($dbemps['Rows'] as &$emp) {
				array_push($emps, $this->resumeToView($emp));
			}
		}
		exit(json_encode($this->genPagination($dbemps['Total'], $emps)));
    }
    
    
    /**
     * 员工编辑
     */
    public function empeditAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('emp_edit'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_emp/empedit', 'name' => $this->tr->translate('emp_edit'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    /**
     * 删除员工
     */
    public function delempAction() {
    	//获取参数：resumeCodes
    	$rsmCodes = $this->_getParam('resumeCodes');
    	//查询简历是否用于应聘案件，有应聘则无法删除员工，无应聘则可以删除员工
    	$sRsmCodes = implode("','", $rsmCodes);
    	$sRsmCodes = "'" . $sRsmCodes . "'";
    	$cond = "resume_code in ($sRsmCodes)";
    	$caseApplys = BaseService::getAllByCond('case_apply', array('distinct emp_code'), $cond);
//    	var_dump($caseApplys);
//    	exit;
    	if (empty($caseApplys)) {
	    	//删除员工：更新简历enabled = 'N'
	    	$result = ResumeService::updateResumeByCodes($rsmCodes, array('enabled' => 'N'));
	    	
	    	//返回
	    	if ($result === true) {
	    		$ret = array('err' => 0, 'msg' => 'Success');
				exit(json_encode($ret));
	    	}
    	} else { //员工有应聘案件，无法删除
    		$empCodes = array();
    		foreach ($caseApplys as $ca) {
    			$empCodes[] = $ca['emp_code'];
    		}
    		$sEmpCodes = implode(',', $empCodes);
	    	$ret = array('err' => 1, 'msg' => $this->tr->translate('THE_FOLLOWING_EMPS_HAVE_APPLIED_CASES__SO_CAN_NOT_BE_DELETE') . ': ' . $sEmpCodes);
			exit(json_encode($ret));
    	}
    }
    
    
    /**
     * 检索员工 
     */
    public function empsearchAction() {
    	$this->layout->headTitle($this->tr->translate('emp_search'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_emp/empsearch', 'name' => $this->tr->translate('emp_search'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    /**
     * 检索员工结果
     */
    public function empsearchresultAction() {
    	$this->layout->headTitle($this->tr->translate('emp_search_result'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_emp/empsearchresult', 'name' => $this->tr->translate('emp_search_result'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
} //End: class Lp_EmpController