<?php
include_once 'BaseAdminController.php';
include_once 'UsrService.php';
include_once 'ResumeService.php';
include_once 'EtcService.php';
include_once 'CaseApplyService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


class Admin_Admin_UsrController extends BaseAdminController {
    
    /**
     * 会员基本情报
     */
    public function usrAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('MEMBER_BASE_INFO'));
		$crumb = array('uri' => '/admin/admin_usr/usr', 'name' => $this->tr->translate('MEMBER_BASE_INFO'));
		$this->view->layout()->crumbs = array($crumb);
		
		//获取参数
		//管理员查看员工信息时，传递过来的员工简历编号
		$resumeCode = trim($this->_getParam('resumeCode'));
		
		//查询人才信息
		$resume = array();
		$lp = array();
		if (!empty($resumeCode)) {
			$resume = ResumeService::getResumeByCode($resumeCode);
			$lp = UsrService::getUsrByCode($resume['lp_code'], array('fullname'));
		}
		//los2 test
//		var_dump($resume);
//		var_dump($lp);
//		exit;
		
		//输出到view
		$this->view->lp = $lp;
		$this->view->resume = $resume;
    }
    
    
    /**
     * 检索会员
     */
    public function searchusrAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('search_usr'));
		$crumb = array('uri' => '/admin/admin_usr/searchusr', 'name' => $this->tr->translate('search_usr'));
		$this->view->layout()->crumbs = array($crumb);
		
		//获取参数：检索条件
		$jsAdvancedSearchParms = $this->_getParam('advancedSearchParms');
		
		//检索OS, FRAMEWORK, BIZ, EXP_WORKPLACE_JP, EXP_WORKPLACE_OVERSEA
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
		$this->view->oss=$oss;
		$this->view->fws=$fws;
		$this->view->bizs=$bizs;
		$this->view->expWps_jp=$expWps_jp;
		$this->view->expWps_os=$expWps_os;
		$this->view->experienceYears = $experienceYears;
		$this->view->jsAdvancedSearchParms = $jsAdvancedSearchParms;
		$this->view->allTalentsCnt = $allTalentsCnt;
		$this->view->newTalentsCnt = $newTalentsCnt;
		$this->view->recmdTalentsCnt = $recmdTalentsCnt;
    }

    
    /**
     * 检索会员结果
     */
    public function searchusrresultAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('search_usr_result'));
		$crumb = array('uri' => '/admin/admin_usr/searchusrresult', 'name' => $this->tr->translate('search_usr_result'));
		$this->view->layout()->crumbs = array($crumb);
		
		//获取参数：检索类型（简单或高级）
		$searchType = trim($this->_getParam('searchType'));
		$advancedSearchParmsKeys = array();
		$advancedSearchParms = array();
		
		if ($searchType == 'SIMPLE') { //简单检索
			//获取参数：简单检索键与值
			$searchKey = trim($this->_getParam('searchKey'));
			$searchVal = trim($this->_getParam('searchVal'));
			
			//view
			$this->view->searchKey = $searchKey;
			$this->view->searchVal = $searchVal;
		} else { //高级检索：$searchType == 'ADVANCED'
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
			$rsmQfd = $this->_getParam('rsmQfd'); //简历是否合格
			
			//把参数组合成一个数组
			$advancedSearchParms = array('talentType' => $talentType, 'oss' => $oss, 
				'expWps_jp' => $expWps_jp, 'expWps_os' => $expWps_os, 
				'sex' => $sex, 'fullageMin' => $fullageMin, 'fullageMax' => $fullageMax, 'jaAbility' => $jaAbility, 
				'ableWorkDateChoice' => $ableWorkDateChoice, 'ableWorkDateBegin' => $ableWorkDateBegin,
				'ableWorkDateEnd' => $ableWorkDateEnd, 'salaryMin' => $salaryMin, 'rsmQfd' => $rsmQfd
			);
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
			//los2 test
//			var_dump($rsmQfd);
//			exit;
			//高级检索view
			$this->view->advancedSearchParmsKeys = $advancedSearchParmsKeys;
			$this->view->advancedSearchParms = $advancedSearchParms;
			$this->view->fws = $fws;
			$this->view->bizs = $bizs;
		}
		
		//简单检索与高级检索共有的view
		$this->view->searchType = $searchType;
    }
    
    
    /**
     * 人才检索
     */
    public function gettalentlistAction() {
    	//获取参数：分页，检索类型
		$this->getPagination();
		$searchType = trim($this->_getParam('searchType'));
		
    	$fields = array('code', 'talent_code', 'fullname', 'sex', 'birthday', 'tel', 'update_time');
    	$sFs = implode(',a.', $fields);
    	$sFs = 'a.' . $sFs;
    	
		//人才检索：简单
		if ($searchType == 'SIMPLE') {
			//获取参数：简单检索键与值
			$searchKey = trim($this->_getParam('searchKey'));
			$searchVal = trim($this->_getParam('searchVal'));
	    	//查询人才
	    	if (!empty($searchKey) && !empty($searchVal)) { //有查询条件
	    		$cond = " a.$searchKey like '%$searchVal%' and a.enabled = 'Y' and ok_base = 'Y' and ok_biz = 'Y' and ok_prj = 'Y' and ok_other = 'Y' ";
	    	} else { //无查询条件
	    		$cond = " a.enabled = 'Y' and ok_base = 'Y' and ok_biz = 'Y' and ok_prj = 'Y' and ok_other = 'Y' ";
	    	}
	    	$sql = "select $sFs
	    		from resume as a
	    		where $cond";
		} else { //人才检索：高级
			//获取参数：高级人才查询组合条件
			$parm = trim($this->_getParam('parm'));
			$parm = json_decode($parm, true);
			
			//检索人才条件：OS, FRAMEWORK, BIZ, EXP_WORKPLACE_JP, EXP_WORKPLACE_OVERSEA,
			//sex, fullageMin, fullageMax, jaAbility, ableWorkDateChoice, 
			//ableWorkDateBegin, ableWorkDateEnd, salaryMin, rsmQfd
			
			//根据检索条件生成sql
			$isSpecifyDate = FALSE;
			$cond = " a.is_open = 'Y' and a.enabled = 'Y' ";
			foreach ($parm as $k => $v) {
				if (!empty($v)) {
					if ($k == 'expWps_jp') {
						foreach ($v as $expWp) {
							$expWp = addslashes($expWp);
							$cond .= " and a.exp_workplace like '%$expWp%' ";
						}
					} else if ($k == 'expWps_os') {
						foreach ($v as $expWp) {
							$expWp = addslashes($expWp);
							$cond .= " and a.exp_workplace like '%$expWp%' ";
						}
					} else if ($k == 'sex') {
						$v = addslashes($v);
						if (in_array($v, array('M', 'F'))) {
							$cond .= " and a.sex = '$v' ";
						}
					} else if ($k == 'fullageMin') {
						$v = addslashes($v);
						$birthday = (date('Y') - $v) . '-' . date('m') . '-' . date('d');
						$cond .= " and a.birthday <= '$birthday' ";
					} else if ($k == 'fullageMax') {
						$v = addslashes($v);
						$birthday = (date('Y') - $v) . '-' . date('m') . '-' . date('d');
						$cond .= " and a.birthday >= '$birthday' ";
					} else if ($k == 'jaAbility') {
						$v = addslashes($v);
						$cond .= " and a.ja_ability <= '$v' ";
					} else if ($k == 'ableWorkDateChoice') {
						$v = addslashes($v);
						if ($v == 'FROM_NOW_ON') {
							$today = date('Y-m-d');
							$cond .= " and a.able_work_date <= '$today' ";
						} else if ($v == 'SPECIFY_DATE') {
							$isSpecifyDate = TRUE;
						}
					} else if ($k == 'ableWorkDateBegin') {
						if ($isSpecifyDate === TRUE) {
							$v = addslashes($v);
							$cond .= " and a.able_work_date >= '$v' ";
						}
					} else if ($k == 'ableWorkDateEnd') {
						if ($isSpecifyDate === TRUE) {
							$v = addslashes($v);
							$cond .= " and a.able_work_date <= '$v' ";
						}
					} else if ($k == 'salaryMin') {
						$v = addslashes($v);
						$cond .= " and a.salary_min >= '$v' ";
					} else if ($k == 'rsmQfd') {
						if ($v == array('Y')) {
							$cond .= " and a.ok_base = 'Y' and a.ok_biz = 'Y' and a.ok_prj = 'Y' and a.ok_other = 'Y' ";
						}
					} else if ($k == 'oss') {
						foreach ($v as $os) {
							$os = addslashes($os);
							$cond .= " and exists (select * from resume_biz as h where a.code = h.resume_code and h.type = 'OS' and h.biz = '$os') ";
						}
					} else if (substr($k, -7) == '_fw_age') {
						$fw = substr($k, 0, -7);
						$v = addslashes($v);
						$cond .= " and exists (select * from resume_biz as h where a.code = h.resume_code and h.type = 'FRAMEWORK' and h.biz = '$fw' and h.age >= '$v') ";
					} else if (substr($k, -8) == '_biz_age') {
						$biz = substr($k, 0, -8);
						$v = addslashes($v);
						$cond .= " and exists (select * from resume_biz as h where a.code = h.resume_code and h.type = 'BIZ' and h.biz = '$biz' and h.age >= '$v') ";
					}
				}
			}
			$sql = "select $sFs
				from resume as a 
				where $cond";
		}
		$pgRsms = BaseService::getByPageWithSql($sql, $this->pagination);
		
		//整理$resumes
		$this->rsmsToView($pgRsms['Rows']);
		
		//返回
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $pgRsms['Total'], 'Rows' => $pgRsms['Rows']);
		exit(json_encode($ret));
    }
    
    
	/**
     * 停止人才服务
     */
    public function stoptalentAction() {
    	//获取参数：resumeCodes
    	$resumeCodes = $this->_getParam('resumeCodes');
    	
    	//停止人才服务：更新简历enabled = 'N'
    	$result = ResumeService::updateResumeByCodes($resumeCodes, array('enabled' => 'N'));
    	
    	//返回
    	if ($result) {
    		$ret = array('err' => 0, 'msg' => 'Success');
			exit(json_encode($ret));
    	}
    	$ret = array('err' => 1, 'msg' => $this->tr->translate('STOP_TALENT_SERVICE_ERR'));
		exit(json_encode($ret));
    }

    
    /**
     * 应聘案件管理
     */
    public function applycasemgtAction() {
		$this->layout->headTitle($this->tr->translate('emp_apply_case_mgt'));
		$crumb = array('uri' => '/admin/admin_usr/applycasehistory', 'name' => $this->tr->translate('emp_apply_case_mgt'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
    /**
     * 应聘案件管理--查询
     */
    public function searchapplycasemgtAction() {
    	$this->getPagination();
		$emps = array();
		$range = $this->_getParam("range");
		$sValue = $this->_getParam("sValue");
		$dbemps = CaseApplyService::findAllEmpOrUsrListForApplyInfo($this->genResumeOption($range, $sValue), $this->pagination);
		if (!empty($dbemps["Rows"])) {
			foreach ($dbemps["Rows"] as $emp) {
				array_push($emps, $this->resumeToView($emp));
			}
		}
		exit(json_encode($this->genPagination($dbemps["Total"], $emps)));
    }
    
    
    /**
     * 用户推荐
     */
    public function usrrecmdAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('usr_recmd'));
		$crumb = array('uri' => '/admin/admin_usr/usrrecmd', 'name' => $this->tr->translate('usr_recmd'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
    
} //End: class Admin_Admin_UsrController