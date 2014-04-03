<?php
include_once 'BaseAdminController.php';
include_once 'ResumeService.php';
include_once 'UtilService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


class Admin_Admin_UsrlistController extends BaseAdminController
{
    /**
     * 新增普通用户一览
     */
    public function newusrlistAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('new_usr_list'));
		$crumb = array('uri' => '/admin/admin_usr/newusrlist', 'name' => $this->tr->translate('new_usr_list'));
		$this->view->layout()->crumbs = array($crumb);
    }

    
    /**
     * 查询新增普通用户
     */
    public function getnewmemberlistAction() {
    	//获取参数：分页，查询变量及其值
		$this->getPagination();
		
		$searchKey = trim($this->_getParam('searchKey'));
		$searchVal = trim($this->_getParam('searchVal'));
    	
    	//查询新增的普通用户
    	$fields = array('code', 'talent_code', 'fullname', 'sex', 'birthday', 'tel', 'update_time');
    	if (!empty($searchKey) && !empty($searchVal)) { //有查询条件
    		$cond = " $searchKey like '%$searchVal%' 
    			and create_time >= DATE_SUB(now(),INTERVAL 1 month) 
    			and enabled = 'Y' ";
    	} else { //无查询条件
    		$cond = " create_time >= DATE_SUB(now(),INTERVAL 1 month) and enabled = 'Y' ";
    	}
		$pgRsms = BaseService::getByPageWithCond('resume', $fields, $cond, $this->pagination);
		
		//整理$pgRsms
//		$sex = array('' => '', 'M' => $this->tr->translate('M'), 'F' => $this->tr->translate('F'));
//		foreach ($pgRsms['Rows'] as &$resume) {
//			$resume['sex'] = $sex[$resume['sex']];
//			$age = UtilService::calcFullAge($resume['birthday']);
//			$resume['age'] =  ($age != false) ? $age : 0;
//			$resume['update_date'] = substr($resume['update_time'], 0, 10);
//		}
		$this->rsmsToView($pgRsms['Rows']);
		
		//返回
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $pgRsms['Total'], 'Rows' => $pgRsms['Rows']);
		exit(json_encode($ret));
    }

    
	/**
     * 删除人才（停止服务）
     */
    public function deltalentAction() {
    	//获取参数：resumeCodes
    	$resumeCode = trim($this->_getParam('resumeCode'));
    	
    	//删除人才：更新简历enabled = 'N'
    	$result = ResumeService::updateResumeByCodes(array($resumeCode), array('enabled' => 'N'));
    	
    	//返回
    	if ($result === true) {
    		$ret = array('err' => 0, 'msg' => 'Success');
			exit(json_encode($ret));
    	}
    	$ret = array('err' => 1, 'msg' => $this->tr->translate('STOP_SERVICE_ERR'));
		exit(json_encode($ret));
    }
    
} //End: class Admin_Admin_UsrlistController