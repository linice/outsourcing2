<?php
include_once 'BaseController.php';
include_once 'BaseService2.php';
include_once 'UsrService.php';
include_once 'ResumeService.php';
include_once 'ActiveEtcService.php';
include_once 'EtcService.php';
include_once 'BaseService.php';


class Front_TalentController extends BaseController
{
    /**
     * 人才高级检索页面
     */
    public function talentAction() {
		//导航
    	$this->layout->headTitle($this->tr->translate('talent_search'));
		$crumb = array('uri' => '/front_talent', 'name' => $this->tr->translate('talent_search'));
		$this->view->layout()->crumbs = array($crumb);
		
		//获取参数：检索条件
		$jsAdvancedSearchParms = $this->_getParam('advancedSearchParms');
//		var_dump($advancedSearchParms);
//		exit;
		
		//检索杂项表
		$types = array('OS', 'FRAMEWORK', 'BIZ', 'EXP_WORKPLACE_JP', 'EXP_WORKPLACE_OVERSEA', 'EXPERIENCE_YEARS');
		$etcs = EtcService::getEtcsByTypes($types, array('type', 'code', 'value'));
    	foreach ($etcs as $etc) {
			switch ($etc['type']) {
				case 'OS': //操作系统
					$oss[] = $etc['code'];
					break;
				case 'FRAMEWORK': //语言&框架
					$fws[] = $etc['code'];
					break;
				case 'BIZ': //业务
					$bizs[] = $etc['code'];
					break;
				case 'EXP_WORKPLACE_JP': //期望工作地点：日本
					$expWps_jp[] = $etc['code'];
					break;
				case 'EXP_WORKPLACE_OVERSEA': //期望工作地点：日本
					$expWps_os[] = $etc['code'];
					break;
				case 'EXPERIENCE_YEARS': //经验年数
					$experienceYears[] = $etc['value'];
					break;
			}
		}
		
		//检索动态杂项表
		$aes = ActiveEtcService::getActiveEtcsByType('STATS', array('code', 'value'));
		$allTalentsCnt = 0;$newTalentsCnt=0;$recmdTalentsCnt=0;
    	foreach ($aes as $ae) {
			switch ($ae['code']) {
				case 'ALL_VALID_TALENTS_CNT': //所有有效人才数量
					$allTalentsCnt = $ae['value'];
					break;
				case 'NEW_VALID_TALENTS_CNT': //新增有效人才数量
					$newTalentsCnt = $ae['value'];
					break;
				case 'RECMD_VALID_TALENTS_CNT': //推荐有效人才数量
					$recmdTalentsCnt = $ae['value'];
					break;
			}
		}
		
		//view
		$this->view->oss = $oss;
		$this->view->fws = $fws;
		$this->view->bizs = $bizs;
		$this->view->expWps_jp = $expWps_jp;
		$this->view->expWps_os = $expWps_os;
		$this->view->experienceYears = $experienceYears;
		$this->view->jsAdvancedSearchParms = $jsAdvancedSearchParms;
		$this->view->allTalentsCnt = $allTalentsCnt;
		$this->view->newTalentsCnt = $newTalentsCnt;
		$this->view->recmdTalentsCnt = $recmdTalentsCnt;
    }
    
    
    /**
     * 人才高级检索结果
     */
    public function talentsearchresultAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('talent_search_result'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_talent', 'name' => $this->tr->translate('talent_search'));
		$crumbs[] = array('uri' => '/front_talent/talentsearchresult', 'name' => $this->tr->translate('talent_search_result'));
		$this->view->layout()->crumbs = $crumbs;
		
		//检索杂项表
		$types = array('FRAMEWORK', 'BIZ');
		$etcs = EtcService::getEtcsByTypes($types, array('type', 'code', 'name'));
    	foreach ($etcs as $etc) {
			switch ($etc['type']) {
				case 'FRAMEWORK': //语言&框架
					$fws[] = $etc['code'];
					break;
				case 'BIZ': //业务
					$bizs[] = $etc['code'];
					break;
			}
		}
		
		//获取参数：OS, FRAMEWORK, BIZ, EXP_WORKPLACE_JP, EXP_WORKPLACE_OVERSEA,
		//sex, fullageMin, fullageMax, jaAbility, ableWorkDateChoice, 
		//ableWorkDateBegin, ableWorkDateEnd, salaryMin
		
		//人才类型
		$talentType = trim($this->_getParam('talentType'));
		
		//现居国家
		$actual_residence_cntry = trim($this->_getParam('actual_residence_cntry'));
		
		//OS
		$oss = $this->_getParam('oss');
		
		//fws
		$fwAges = array();
		foreach ($fws as $fw) {
			$fwAges[$fw . '_fw_age'] = trim($this->_getParam($fw . '_fw_age'));
		}
		//bizs
		$bizAges = array();
		foreach ($bizs as $biz) {
			$bizAges[$biz . '_biz_age'] = trim($this->_getParam($biz . '_biz_age'));
		}
		
		//勤务地
		$expWps_jp = $this->_getParam('expWps_jp');
		$expWps_os = $this->_getParam('expWps_os');

		//other
		$sex = trim($this->_getParam('sex'));
		$fullageMin = trim($this->_getParam('fullageMin'));
		$fullageMax = trim($this->_getParam('fullageMax'));
		$jaAbility = trim($this->_getParam('jaAbility'));
		$ableWorkDateChoice = trim($this->_getParam('ableWorkDateChoice'));
		$ableWorkDateBegin = trim($this->_getParam('ableWorkDateBegin'));
		$ableWorkDateEnd = trim($this->_getParam('ableWorkDateEnd'));
		$salaryMin = trim($this->_getParam('salaryMin'));
		
		//把参数组合成一个数组
		$advancedSearchParms = array('talentType' => $talentType, 'oss' => $oss, 
			'expWps_jp' => $expWps_jp, 'expWps_os' => $expWps_os, 
			'sex' => $sex, 'fullageMin' => $fullageMin, 'fullageMax' => $fullageMax, 'jaAbility' => $jaAbility, 
			'ableWorkDateChoice' => $ableWorkDateChoice, 'ableWorkDateBegin' => $ableWorkDateBegin,
			'ableWorkDateEnd' => $ableWorkDateEnd, 'salaryMin' => $salaryMin
		);
		if (!empty($actual_residence_cntry)) {
			$advancedSearchParms['actual_residence_cntry'] = $actual_residence_cntry;
		}
		foreach ($fwAges as $fwAgeKey => $fwAge) {
			$advancedSearchParms[$fwAgeKey] = $fwAge;
		}
		foreach ($bizAges as $bizAgeKey => $bizAge) {
			$advancedSearchParms[$bizAgeKey] = $bizAge;
		}
		
		//人才高级检索的键
		foreach ($advancedSearchParms as $k => $v) {
			$advancedSearchParmsKeys[] = $k;
		}
		
		//高级检索view
		$this->view->advancedSearchParmsKeys = $advancedSearchParmsKeys;
		$this->view->advancedSearchParms = $advancedSearchParms;
		$this->view->fws = $fws;
		$this->view->bizs = $bizs;
    }
    
    
	/**
     * 人才检索
     */
    public function gettalentlistAction() {
    	//获取参数：分页
		$this->getPagination();
		
    	$fsRsm = array('code', 'talent_code', 'fullname', 'sex', 'birthday', 'tel', 'update_time');
		//获取参数：高级人才查询组合条件
		$parm = trim($this->_getParam('parm'));
		$parm = json_decode($parm, TRUE);
		
		//检索人才条件：OS, FRAMEWORK, BIZ, EXP_WORKPLACE_JP, EXP_WORKPLACE_OVERSEA,
		//sex, fullageMin, fullageMax, jaAbility, ableWorkDateChoice, 
		//ableWorkDateBegin, ableWorkDateEnd, salaryMin
		
		//根据检索条件生成sql
		$sfsRsm = implode(',', $fsRsm);
		$isSpecifyDate = FALSE;
		$cond = " is_open = 'Y' and enabled = 'Y' and ok_base = 'Y' and ok_biz = 'Y' and ok_prj = 'Y' and ok_other = 'Y' ";
		foreach ($parm as $k => $v) {
			if (!empty($v)) {
				if ($k == 'talentType') {
					if ($v == 'NEW') {
						$cond .= " and create_time > DATE_SUB(now(),INTERVAL 1 month) ";
					} else if ($v == 'RECMD') {
						$cond .= " and bid_points > 0 ";
					}
				} else if ($k == 'actual_residence_cntry') {
						$cond .= " and actual_residence_cntry = '$v' ";
				} else if ($k == 'expWps_jp') {
					foreach ($v as $expWp) {
						$expWp = addslashes($expWp);
						$cond .= " and exp_workplace like '%$expWp%' ";
					}
				} else if ($k == 'expWps_os') {
					foreach ($v as $expWp) {
						$expWp = addslashes($expWp);
						$cond .= " and exp_workplace like '%$expWp%' ";
					}
				} else if ($k == 'sex') {
					$v = addslashes($v);
					if (in_array($v, array('M', 'F'))) {
						$cond .= " and sex = '$v' ";
					}
				} else if ($k == 'fullageMin') {
					$v = addslashes($v);
					$birthday = (date('Y') - $v) . '-' . date('m') . '-' . date('d');
					$cond .= " and birthday <= '$birthday' ";
				} else if ($k == 'fullageMax') {
					$v = addslashes($v);
					$birthday = (date('Y') - $v) . '-' . date('m') . '-' . date('d');
					$cond .= " and birthday >= '$birthday' ";
				} else if ($k == 'jaAbility') {
					$v = addslashes($v);
					$cond .= " and ja_ability <= '$v' ";
				} else if ($k == 'ableWorkDateChoice') {
					$v = addslashes($v);
					if ($v == 'FROM_NOW_ON') {
						$today = date('Y-m-d');
						$cond .= " and able_work_date <= '$today' ";
					} else if ($v == 'SPECIFY_DATE') {
						$isSpecifyDate = TRUE;
					}
				} else if ($k == 'ableWorkDateBegin') {
					if ($isSpecifyDate === TRUE) {
						$v = addslashes($v);
						$cond .= " and able_work_date >= '$v' ";
					}
				} else if ($k == 'ableWorkDateEnd') {
					if ($isSpecifyDate === TRUE) {
						$v = addslashes($v);
						$cond .= " and able_work_date <= '$v' ";
					}
				} else if ($k == 'salaryMin') {
					$v = addslashes($v);
					$cond .= " and salary_min >= '$v' ";
				} else if ($k == 'oss') {
					foreach ($v as $os) {
						$os = addslashes($os);
						$cond .= " and exists (select * from resume_biz as b where a.code = b.resume_code and b.type = 'OS' and b.biz = '$os') ";
					}
				} else if (substr($k, -7) == '_fw_age') {
					$fw = substr($k, 0, -7);
					$v = addslashes($v);
					$cond .= " and exists (select * from resume_biz as b where a.code = b.resume_code and b.type = 'FRAMEWORK' and b.biz = '$fw' and b.age >= '$v') ";
				} else if (substr($k, -8) == '_biz_age') {
					$biz = substr($k, 0, -8);
					$v = addslashes($v);
					$cond .= " and exists (select * from resume_biz as b where a.code = b.resume_code and b.type = 'BIZ' and b.biz = '$biz' and b.age >= '$v') ";
				}
			}
		}
		$sql = "select $sfsRsm from resume as a where $cond order by a.bid_points desc, a.create_time desc ";
//		var_dump($sql);
//		exit;
		$pgRsms = BaseService::getByPageWithSql($sql, $this->pagination);
		
		//整理$resumes
		$this->rsmsToView($pgRsms['Rows']);
		
		//返回
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $pgRsms['Total'], 'Rows' => $pgRsms['Rows']);
		exit(json_encode($ret));
    }
    
    
    /**
     * 人才高级检索
     */
    public function talentsearchAction() {
    	$this->layout->headTitle($this->tr->translate('talent_search_advanced'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_talent', 'name' => $this->tr->translate('talent_search'));
		$crumbs[] = array('uri' => '/front_talent/talentsearchresult', 'name' => $this->tr->translate('talent_search_advanced'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    /**
     * 人才提案
     */
    public function talentproposeAction() {
    	$this->layout->headTitle($this->tr->translate('talent_propose'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_talent', 'name' => $this->tr->translate('talent_search'));
		$crumbs[] = array('uri' => '/front_talent/talentpropose', 'name' => $this->tr->translate('talent_propose'));
		$this->view->layout()->crumbs = $crumbs;
    }
    
    
    
    
    
    
    
    
    
} //End: class Front_TalentController