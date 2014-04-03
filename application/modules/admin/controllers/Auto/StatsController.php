<?php
include_once 'BaseAdminController.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';
include_once 'ResumeService.php';


class Admin_Auto_StatsController extends BaseAdminController
{
	/**
	 * 统计人才数量：所有人才，新增人才，推荐人才
	 */
    public function stattalentAction()
    {
    	//统计所有有效人才数量
    	$allValidTalentsCnt = ResumeService::cntAllValidTalents();
    	//统计新增有效人才数量
    	$newValidTalentsCnt = BaseService::getOneByCond('resume', 'count(*)', "create_time > DATE_SUB(NOW(),INTERVAL 1 month) and enabled = 'Y'");
    	//统计推荐有效人才数量
    	$recmdValidTalentsCnt = BaseService::getOneByCond('resume', 'count(*)', "bid_points > 0 and enabled = 'Y'");
    	
    	//更新所有有效人才数量
    	$cond = "type = 'STATS' and code = 'ALL_VALID_TALENTS_CNT'";
    	BaseService::updateByCond('active_etc', array('value' => $allValidTalentsCnt), $cond);
    	//更新新增有效人才数量
    	$cond = "type = 'STATS' and code = 'NEW_VALID_TALENTS_CNT'";
    	BaseService::updateByCond('active_etc', array('value' => $newValidTalentsCnt), $cond);
    	//更新推荐有效人才数量
    	$cond = "type = 'STATS' and code = 'RECMD_VALID_TALENTS_CNT'";
    	BaseService::updateByCond('active_etc', array('value' => $recmdValidTalentsCnt), $cond);
    	
    	//RETURN
    	$ret = array('err' => 0, 'msg' => 'Success');
    	exit(json_encode($ret));
    }
    
    
    /**
     * 统计普通用户数量
     */
    public function statmbrAction()
    {
    	//统计所有有效普通用户数量
    	$sql = "select count(1) from usr where enabled = 'Y' and role_code = 'MEMBER'";
		$allValidMbrsCnt = BaseService::getOneBySql($sql);
    	
    	//更新所有有效普通用户数量
    	$cond = "type = 'STATS' and code = 'ALL_VALID_MBRS_CNT'";
    	BaseService::updateByCond('active_etc', array('value' => $allValidMbrsCnt), $cond);
    	
    	//RETURN
    	$ret = array('err' => 0, 'msg' => 'Success');
    	exit(json_encode($ret));
    }
    
    
    /**
     * 统计法人数量
     */
    public function statlpAction()
    {
    	//统计所有有效法人数量
    	$sql = "select count(1) from usr where enabled = 'Y' and role_code = 'LP'";
		$allValidLpsCnt = BaseService::getOneBySql($sql);
    	
    	//更新所有有效法人数量
    	$cond = "type = 'STATS' and code = 'ALL_VALID_LPS_CNT'";
    	BaseService::updateByCond('active_etc', array('value' => $allValidLpsCnt), $cond);
    	
    	//RETURN
    	$ret = array('err' => 0, 'msg' => 'Success');
    	exit(json_encode($ret));
    }
    
    
    /**
     * 统计录入员数量
     */
	public function stateditorAction()
    {
    	//统计所有有效录入员数量
    	$sql = "select count(1) from usr where enabled = 'Y' and role_code = 'EDITOR'";
		$allValidEditorsCnt = BaseService::getOneBySql($sql);
    	
    	//更新所有有效录入员数量
    	$cond = "type = 'STATS' and code = 'ALL_VALID_EDITORS_CNT'";
    	BaseService::updateByCond('active_etc', array('value' => $allValidEditorsCnt), $cond);
    	
    	//RETURN
    	$ret = array('err' => 0, 'msg' => 'Success');
    	exit(json_encode($ret));
    }
    
    
    /**
     * 统计营业员数量
     */
    public function statassistAction()
    {
    	//统计所有有效营业员数量
    	$sql = "select count(1) from usr where enabled = 'Y' and role_code = 'ASSIST'";
		$allValidAssistsCnt = BaseService::getOneBySql($sql);
    	
    	//更新所有有效营业员数量
    	$cond = "type = 'STATS' and code = 'ALL_VALID_ASSISTS_CNT'";
    	BaseService::updateByCond('active_etc', array('value' => $allValidAssistsCnt), $cond);
    	
    	//RETURN
    	$ret = array('err' => 0, 'msg' => 'Success');
    	exit(json_encode($ret));
    }
    
    
    /**
     * 统计活动数量
     */
    public function stateventAction()
    {
    	//统计所有有效活动数量
    	$sql = "select count(*) from event where enabled = 'Y'";
		$allValidEventsCnt = BaseService::getOneBySql($sql);
    	
    	//更新所有有效营业员数量
    	$cond = "type = 'STATS' and code = 'ALL_VALID_EVENTS_CNT'";
    	BaseService::updateByCond('active_etc', array('value' => $allValidEventsCnt), $cond);
    	
    	//RETURN
    	$ret = array('err' => 0, 'msg' => 'Success');
    	exit(json_encode($ret));
    }
    
    
    /**
     * 统计案件数量
     */
    public function statcaseAction()
    {
    	//统计所有有效活动数量
    	$sql = "select count(*) from cases where type in ('R', 'C')";
		$allValidCasesCnt = BaseService::getOneBySql($sql);
    	
    	//更新所有有效营业员数量
    	$cond = "type = 'STATS' and code = 'ALL_VALID_CASES_CNT'";
    	BaseService::updateByCond('active_etc', array('value' => $allValidCasesCnt), $cond);
    	
    	//RETURN
    	$ret = array('err' => 0, 'msg' => 'Success');
    	exit(json_encode($ret));
    }
    
    
    
    
    
    
    
    
    
    

} //End: class Admin_Auto_StatsController