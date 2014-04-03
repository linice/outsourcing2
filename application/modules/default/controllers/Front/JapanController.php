<?php
include_once 'BaseController.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';
include_once 'ResumeService.php';


class Front_JapanController extends BaseController
{
    /**
     * 赴日直通车主页面
     */
    public function japanAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('go_to_japan'));
		$crumb = array('uri' => '/front_japan', 'name' => $this->tr->translate('go_to_japan'));
		$this->view->layout()->crumbs = array($crumb);
		
    	//查询新人才
    	$fsRsm = array('code', 'date_graduate', 'date_arrive_jp', 'ja_qualification', 'ja_ability', 'able_work_date_choice', 
    		'able_work_date', 'salary_min_display', 'salary_exp_display',
    		'salary_min', 'salary_exp', 'remark_lp', 'update_time', 'talent_code', 'fullname', 'sex', 'birthday', 'tel');
		$pg = array('start' => 0, 'limit' => 5);
//    	$sFsRsm = implode(',', $fsRsm);
//		$newTalentsSql = "select $sFsRsm
//			from resume as a
//			where a.create_time > DATE_SUB(now(),INTERVAL 1 month)
//			order by a.bid_points desc, a.create_time desc ";
//		$statNewTalents = BaseService::getByPageWithSql($newTalentsSql, $page);
//		$newTalents = $statNewTalents['Rows'];
//		$fsRsmBiz = array('type', 'biz', 'biz_name', 'age');
//		foreach ($newTalents as &$newTalent) {
//			$cond = "resume_code = '{$newTalent['code']}' and type = 'FRAMEWORK'";
//			$resumeBizs = BaseService::getAllByCond('resume_biz', $fsRsmBiz, $cond);
//			$this->rsmDetailToView($newTalent, $resumeBizs);
//		}
		
		$pgChinaNewTalents = ResumeService::getChinaNewTalentsByPg($fsRsm, $pg);
		$this->rsmsWithBizToView($pgChinaNewTalents['Rows']);
		
		//view
		$this->view->newTalentsCnt = $pgChinaNewTalents['Total'];
		$this->view->newTalents = $pgChinaNewTalents['Rows'];
    }
    
    
    
    
    
    
} //End: class Front_JapanController