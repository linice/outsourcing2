<?php
include_once 'BaseAdminController.php';
include_once 'CaseBaseService.php';
include_once 'LpService.php';
include_once 'BaseService.php';
include_once 'BaseService2.php';
include_once 'UsrService.php';
include_once 'ActiveEtcService.php';


/**
 * 管理员中心
 * @author GONGM
 */
class Admin_Admin_IndexController extends BaseAdminController {
    public function indexAction() {
        //导航
		$this->layout->headTitle($this->tr->translate('ADMIN_CENTER'));
		$crumb = array('uri' => '/admin/admin_index/index', 'name' => $this->tr->translate('ADMIN_CENTER'));
		$this->view->layout()->crumbs = array($crumb);
		
		//查询动态杂项表，获取人才，案件等数量
		$aes = ActiveEtcService::getActiveEtcsByType('STATS', array('CODE', 'value'));
		foreach ($aes as $ae) {
			switch ($ae['CODE']) {
				case 'ALL_VALID_MBRS_CNT': //所有有效普通用户数量
					$this->view->mbrsCnt = $ae['value'];
					break;
				case 'ALL_VALID_LPS_CNT': //所有有效法人数量
					$this->view->lpsCnt = $ae['value'];
					break;
				case 'ALL_VALID_EDITORS_CNT': //所有有效录入员数量
					$this->view->editorsCnt = $ae['value'];
					break;
				case 'ALL_VALID_ASSISTS_CNT': //所有有效营业员数量
					$this->view->assistsCnt = $ae['value'];
					break;
				case 'ALL_VALID_EVENTS_CNT': //所有有效活动数量
					$this->view->eventsCnt = $ae['value'];
					break;
				case 'ALL_VALID_CASES_CNT': //所有有效案件数量
					$this->view->casesCnt = $ae['value'];
					break;
			}
		}
		
		//在线法人数
		$likeLpRole = 's:9:"role_code";s:2:"LP";';
		$sql = "select count(*) from session where data like '%$likeLpRole%'";
		$this->view->onlineLpsCnt = BaseService::getOneBySql($sql);
		
		//在线普通用户数
		$likeMbrRole = 's:9:"role_code";s:6:"MEMBER";';
		$sql = "select count(*) from session where data like '%$likeMbrRole%'";
		$this->view->onlineMbrsCnt = BaseService::getOneBySql($sql);
		
		//在线游客数
		$sql = "select count(*) from visitor where update_time > DATE_SUB(now(),INTERVAL 30 minute)";
		$olVisitorsCnt = BaseService::getOneBySql($sql);
		$sql = "select count(*) from session where modified > unix_timestamp(DATE_SUB(now(),INTERVAL 30 minute))";
		$olUsrsCnt = BaseService::getOneBySql($sql);
		$this->view->onlineGuestsCnt = $olVisitorsCnt - $olUsrsCnt;
		
		
		//新增案件列表
		$pg = array('start' => 0, 'limit' => 5);
		$option = array('casetype'=>'cases_new');
		$casesList = array();
		$dbcases = CaseService::findReleaseCasesFront($option, null, $pg, $this->auth->usr['role_code']);
		if (!empty($dbcases["Rows"])) {
			foreach ($dbcases["Rows"] as $case) {
				array_push($casesList, $this->caseToView($case));
			}
		}
		
		//未承认法人列表
		$unAckLpsSql = "select * from usr where role_code = 'LP' and enabled = ''";
		$pgUnAckLps = BaseService::getByPageWithSql($unAckLpsSql, $pg);
		
		//新增普通用户
		$newMbrsSql = "select a.code, a.talent_code, a.fullname, a.sex, a.birthday, 
				get_rsm_biz(a.code) as rsm_biz,
				a.date_graduate, a.date_arrive_jp, a.able_work_date_choice, a.able_work_date, 
				a.ja_ability, a.update_time, b.last_login_time
			from resume as a left join usr as b
			on a.talent_code = b.code
			where a.create_time > DATE_SUB(now(),INTERVAL 1 month)
				and a.enabled = 'Y'
			order by a.create_time desc";
		$pgNewMbrs = BaseService::getByPageWithSql($newMbrsSql, $pg);
		//los2 test
		$this->rsmsWithBizToView($pgNewMbrs['Rows']);
//		var_dump($pgNewMbrs['Rows']);
//		exit;
//		$fields = array('type', 'biz', 'biz_name', 'age');
//		foreach ($pgNewMbrs['Rows'] as &$newMbr) {
//			$cond = "resume_code = '{$newMbr['code']}' and type = 'FRAMEWORK'";
//			$rsmBizs = BaseService::getAllByCond('resume_biz', $fields, $cond);
//			$fw = '';
//			if (!empty($rsmBizs)) {
//				foreach ($rsmBizs as $rsmBiz) {
//					if (substr($rsmBiz['biz'], 0, 10) == 'framework_') {
//		    			$fw .= $rsmBiz['biz_name'] . '/' . $rsmBiz['age'] . $this->tr->translate('YEAR') . ', ';
//	    			} else {
//		    			$fw .= $this->tr->translate($rsmBiz['biz']) . '/' . $rsmBiz['age'] . $this->tr->translate('YEAR') . ', ';
//	    			}
//				}
//			}
//			$newMbr['fw'] = trim($fw, ', ');
//			$newMbr['age'] = UtilService::calcFullAge($newMbr['birthday']);
//			$newMbr['experience_age'] = UtilService::calcFullAge($newMbr['date_graduate']);
//	    	if (strtotime($newMbr['date_arrive_jp']) > strtotime($newMbr['date_graduate'])) {
//		    	$newMbr['ja_experience_age'] = UtilService::calcFullAge($newMbr['date_arrive_jp']);
//	    	} else {
//	    		$newMbr['ja_experience_age'] = $newMbr['experience_age'];
//	    	}
//			if ($newMbr['able_work_date_choice'] == 'SPECIFY_DATE') {
//	    		$newMbr['adjust_able_work_date'] = $newMbr['able_work_date'];
//	    	} else {
//		    	$newMbr['adjust_able_work_date'] = $this->tr->translate($newMbr['able_work_date_choice']);
//	    	}
//		}
		
		//view
		$this->view->addCaseNum = $dbcases["Total"];
		$this->view->casesList = $casesList;
		$this->view->unAckLpsCnt = $pgUnAckLps['Total'];
		$this->view->unAckLps = $pgUnAckLps['Rows'];
		$this->view->newMbrsCnt = $pgNewMbrs['Total'];
		$this->view->newMbrs = $pgNewMbrs['Rows'];
	}
	
	
	/**
	 * 
	 */
	public function lpeditAction(){
		$id = $this->_getParam('id');
		$row=array('enabled'=>'Y');
		$result = BaseService::updateById('usr', $row, $id);
		if($result) {
			$ret = array('err' => 0, 'msg' => '更新成功');
		} else {
			$ret = array('err' => 1, 'msg' => '更新失败');
		}
		exit(json_encode($ret));
	}

} //End: class Admin_IndexController