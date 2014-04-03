<?php
include_once 'BaseController.php';
include_once 'UsrService.php';
include_once 'UsrSkillService.php';
include_once 'EtcService.php';
include_once 'ActiveEtcService.php';
include_once 'UtilService.php';
include_once 'ResumeService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


class Usr_ResumeController extends BaseController
{
	//简历信息
	private $roleCode = NULL; //当管理员帮其它人创建简历时，该值用于保存普通用户、法人或管理员的角色
	private $prjCnt = NULL; //项目数目

    /**
     * 修改简历
     */
    public function  modifyresumeAction() {
    	//禁止布局和页面跳转
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
			
		//接收参数：resume_code, anchor(baseinfo, biz, prj, other)
		$talentCode = trim($this->_getParam('talentCode'));
		$resumeCode = trim($this->_getParam('resumeCode'));
		$anchor = trim($this->_getParam('anchor'));
		
		//检验参数
		//如果是以talent_code传递进来，修改简历，则根据talent_code查询resume code
		if (!empty($talentCode)) {
			$resumeCode = BaseService::getOneByCond('resume', 'code', "talent_code = '$talentCode'");
		}

    	//如果简历编号为空，转向错误页面；
		//只有以下情况才有权查看修改简历：普通用户自己，法人自己的员工，管理员和录入人员
		if (empty($resumeCode)) {
			$err = 1;
		} else {
			$resume = ResumeService::getResumeByCode($resumeCode, array('talent_code'));
			if (!($this->auth->usr['code'] == $resume['talent_code']
				|| UsrService::isEmpOfLP($resume['talent_code'], $this->auth->usr['code'])
				|| in_array($this->auth->usr['role_code'], array('ADMIN', 'EDITOR')))) {
				$err = 1;
			}
		}
		if ($err) {
    		//转向失败通知页面
			$title = $this->tr->translate('MODIFY') . $this->tr->translate('RESUME') . $this->tr->translate('ERROR');
			$opUrl = '/usr_resume/modifyresume';
			$opName = $this->tr->translate('MODIFY') . $this->tr->translate('RESUME');
			$opResultName = $this->tr->translate('MODIFY') . $this->tr->translate('RESUME') . $this->tr->translate('ERROR');
			$opResultImgUrl = '';
			$this->redirectErr($title, $opUrl, $opName, $opResultName, $opResultImgUrl);
    	}
    	
		//修改简历共用创建简历，因此直接跳转到创建简历
		$this->_redirect('/usr_resume/createresume/resumeCode/' . $resumeCode . '/modify/Y/#' . $anchor);
    }
        
    
    /**
     * 创建简历
     */
    public function  createresumeAction() {
    	//根据角色设定layout
    	if (in_array($this->auth->usr['role_code'], array('ADMIN', 'ASSIST', 'EDITOR'))) {
    		$this->_helper->layout->setLayout('admin');
    	}
    	//获取参数：
    	$modify = trim($this->_getParam('modify', 'N')); //modify（是否是modify resume）
    	$resumeCode = trim($this->_getParam('resumeCode'));
    	//导航
    	if ($modify == 'Y') {
			$this->layout->headTitle($this->tr->translate('modify_resume'));
			$crumb = array('uri' => '/usr_resume/createresume', 'name' => $this->tr->translate('modify_resume'));
    	} else {
			$this->layout->headTitle($this->tr->translate('create_resume'));
			$crumb = array('uri' => '/usr_resume/createresume', 'name' => $this->tr->translate('create_resume'));
    	}
		$this->view->layout()->crumbs = array($crumb);
		
    	//检查session里是否有resumeCode，若无，则生成用户简历编码
    	if (empty($resumeCode)) {
			$resumeCode = ActiveEtcService::genCode('RESUME_CODE');
    	}
    	
    	//查询杂项，Web app变量
    	$osOthers = array();
    	$fwOthers = array();
    	$bizOthers = array();
    	$types = array('COUNTRY', 'PROVINCE', 'COUNTY', 'EXP_WORKPLACE_JP', 'EXP_WORKPLACE_OVERSEA', 'ABLE_WORK_DATE_CHOICE', 
    		'VISA_FORM', 'EXP_PRJ_FORM', 'EXPERIENCE_YEARS', 'OS', 'DB', 'FRAMEWORK', 'BIZ', 'BIZ_LEVEL', 
    		'OS_OTHER', 'FW_OTHER', 'BIZ_OTHER', 'INTERVIEW_WAIT', 'INSURANCE');
		$etcs = EtcService::getEtcsByTypes($types, array('type', 'code', 'name', 'value'));
		foreach ($etcs as $etc) {
			switch ($etc['type']) {
				case 'COUNTRY': //国家：日本，中国
					$countries[$etc['code']] = $this->tr->translate($etc['code']);
					break;
				case 'PROVINCE': //中国省
					$provinces[$etc['code']] = $this->tr->translate($etc['code']);
					break;
				case 'COUNTY': //日本县
					$counties[$etc['code']] = $this->tr->translate($etc['code']);
					break;
				case 'EXP_WORKPLACE_JP': //期望工作地点——日本
					$expWorkplaces_jp[] = $etc['code'];
					break;
				case 'EXP_WORKPLACE_OVERSEA': //期望工作地点——海外
					$expWorkplaces_oversea[] = $etc['code'];
					break;
				case 'ABLE_WORK_DATE_CHOICE': //可工作日选项
					$ableWorkDateChoices[] = $etc['code'];
					break;
				case 'VISA_FORM': //签证形态
					$visaForms[] = $etc['code'];
					break;
				case 'EXP_PRJ_FORM': //期望工作形态：长期，中期，短期
					$expPrjForms[] = $etc['code'];
					break;
				case 'EXPERIENCE_YEARS': //经验年数
					$experienceYears[] = $etc['value'];
					break;
				case 'OS': //操作系统
					$oss[] = $etc['code'];
					break;
				case 'DB': //数据库
					$dbs[] = $etc['code'];
					break;
				case 'FRAMEWORK': //语言&框架
					$frameworks[] = $etc['code'];
					break;
				case 'BIZ': //业务
					$bizs[] = $etc['code'];
					break;
				case 'BIZ_LEVEL': //业务能力级别
					$bizLevels[] = $etc['code'];
					break;
				case 'OS_OTHER': //OS其它
					$osOthers[] = $etc['name'];
					break;
				case 'FW_OTHER': //FW其它
					$fwOthers[] = $etc['name'];
					break;
				case 'BIZ_OTHER': //BIZ其它
					$bizOthers[] = $etc['name'];
					break;
				case 'INTERVIEW_WAIT': //面试等待
					$interviewWaits[] = $etc['value'];
					break;
				case 'INSURANCE': //各种保险
					$insurances[] = $etc['code'];
					break;
			}
		}
		//创建简历，刷新页面时，通过session保存的resume_code来初始化简历信息
		$resume = ResumeService::getResumeByCode($resumeCode);
		$resumeBizs = ResumeService::getResumeBizsByResumeCode($resumeCode);
//		$resumePrjs = ResumeService::getResumePrjsByResumeCode($resumeCode);
		$rsmPrjSql = "select * from resume_prj 
			where resume_code = '$resumeCode' 
			order by date_begin";
		$resumePrjs = BaseService::getAllBySql($rsmPrjSql);
		//修饰简历信息
		if ($resume['birthday'] == '0000-00-00') {
			$resume['birthday'] = '';
		}
		if ($resume['date_graduate'] == '0000-00-00') {
			$resume['date_graduate'] = '';
		}
		if ($resume['date_arrive_jp'] == '0000-00-00') {
			$resume['date_arrive_jp'] = '';
		}
		//los test
//		var_dump($resume);
		//输出到view
		$this->view->countries = $countries;
		$this->view->provinces = $provinces;
		$this->view->counties = $counties;
		$this->view->resumeCode = $resumeCode;
		$this->view->experienceYears = $experienceYears;
		$this->view->expWorkplaces_jp = $expWorkplaces_jp;
		$this->view->expWorkplaces_oversea = $expWorkplaces_oversea;
		$this->view->ableWorkDateChoices = $ableWorkDateChoices;
		$this->view->expPrjForms = $expPrjForms;
		$this->view->visaForms = $visaForms;
		$this->view->resume = $resume;
		$this->view->resumeBizs = $resumeBizs;
//		$this->view->bizResumeBizs = $bizResumeBizs;
		$this->view->resumePrjs = $resumePrjs;
		$this->view->oss = $oss;
		$this->view->dbs = $dbs;
		$this->view->frameworks = $frameworks;
		$this->view->bizs = $bizs;
		$this->view->bizLevels = $bizLevels;
		$this->view->osOthers = $osOthers;
		$this->view->fwOthers = $fwOthers;
		$this->view->bizOthers = $bizOthers;
		$this->view->interviewWaits = $interviewWaits;
		$this->view->insurances = $insurances;
    }
    

    /**
     * 简历预览
     */
    public function previewresumeAction() {
    	$caseCode = $this->_getParam('caseCode', '');
    	if (empty($caseCode)) {
	    	$this->_helper->layout()->disableLayout();
    	} else {
			$this->layout->headTitle($this->tr->translate('resume_preview'));
			$crumb = array('uri' => '/usr_resume/previewresume', 'name' => $this->tr->translate('resume_preview'));
			$this->view->layout()->crumbs = array($crumb);
			$this->view->caseCode = $caseCode;
    	}
		
		//接收参数：resume code
		$resumeCode = trim($this->_getParam('resumeCode'));
		
    	//如果简历编号为空，转向错误页面；
		//只有以下情况才有权查看修改简历：普通用户自己，法人，管理员和录入人员
		$err = 0;
		if (empty($resumeCode)) {
			$err = 1;
		} else {
			$resume = ResumeService::getResumeByCode($resumeCode);
			if (!($this->auth->usr['code'] == $resume['talent_code']
				|| in_array($this->auth->usr['role_code'], array('LP', 'ADMIN', 'EDITOR')))) {
				$err = 1;
			} 
			//如果是法人，但不是法人自己的员工，则隐藏部分信息：カタカナ, 氏　名, 現住所, E-Mail, 電　話
			else if (in_array($this->auth->usr['role_code'], array('LP'))
				&& !UsrService::isEmpOfLP($resume['talent_code'], $this->auth->usr['code'])) {
				$resume['fullname_p'] = '******';
				$resume['fullname'] = '******';
				$resume['actual_residence_province'] = '';
				$resume['actual_residence'] = '******';
				$resume['email'] = '******';
				$resume['tel'] = '******';
			}
		}
		if ($err) {
    		//转向失败通知页面
			$title = $this->tr->translate('preview') . $this->tr->translate('RESUME') . $this->tr->translate('ERROR');
			$opUrl = '/usr_resume/previewresume';
			$opName = $this->tr->translate('preview') . $this->tr->translate('RESUME');
			$opResultName = $this->tr->translate('preview') . $this->tr->translate('RESUME') . $this->tr->translate('ERROR');
			$opResultImgUrl = '';
			$this->redirectErr($title, $opUrl, $opName, $opResultName, $opResultImgUrl);
    	}
    	
    	//查询杂项，Web app变量
    	$types = array('COUNTRY', 'PROVINCE', 'COUNTY', 'EXP_WORKPLACE_JP', 'EXP_WORKPLACE_OVERSEA', 'ABLE_WORK_DATE_CHOICE', 
    		'VISA_FORM', 'EXP_PRJ_FORM', 'EXPERIENCE_YEARS', 'OS', 'DB', 'FRAMEWORK', 'BIZ');
		$etcs = EtcService::getEtcsByTypes($types, array('type', 'code', 'value'));
		foreach ($etcs as $etc) {
			switch ($etc['type']) {
				case 'COUNTRY': //国家：日本，中国
					$countries[$etc['code']] = $this->tr->translate($etc['code']);
					break;
				case 'PROVINCE': //中国省
					$provinces[$etc['code']] = $this->tr->translate($etc['code']);
					break;
				case 'COUNTY': //日本县
					$counties[$etc['code']] = $this->tr->translate($etc['code']);
					break;
				case 'EXP_WORKPLACE_JP': //期望工作地点——日本
					$expWorkplaces_jp[] = $etc['code'];
					break;
				case 'EXP_WORKPLACE_OVERSEA': //期望工作地点——海外
					$expWorkplaces_oversea[] = $etc['code'];
					break;
				case 'ABLE_WORK_DATE_CHOICE': //可工作日选项
					$ableWorkDateChoices[] = $etc['code'];
					break;
				case 'VISA_FORM': //签证形态
					$visaForms[] = $etc['code'];
					break;
				case 'EXP_PRJ_FORM': //期望工作形态：长期，中期，短期
					$expPrjForms[] = $etc['code'];
					break;
				case 'EXPERIENCE_YEARS': //经验年数
					$experienceYears[$etc['code']] = $etc['value'];
					break;
				case 'OS': //操作系统
					$oss[] = $etc['code'];
					break;
				case 'DB': //数据库
					$dbs[] = $etc['code'];
					break;
				case 'FRAMEWORK': //语言&框架
					$frameworks[] = $etc['code'];
					break;
				case 'BIZ': //业务
					$bizs[] = $etc['code'];
					break;
			}
		}
		
		//查询简历信息
		$resumeBizs = ResumeService::getResumeBizsByResumeCode($resumeCode);
		$resumePrjs = ResumeService::getResumePrjsByResumeCode($resumeCode);

		//根据业务biz字段来重写resumeBizs
		$bizResumeBizs = array();
		foreach ($resumeBizs as $resumeBiz) {
			$bizResumeBizs[$resumeBiz['biz']] = $resumeBiz;
		}
		
		//view
		$this->view->resume = $resume;
		$this->view->resumeBizs = $resumeBizs;
		$this->view->bizResumeBizs = $bizResumeBizs;
		$this->view->resumePrjs = $resumePrjs;
		$this->view->oss = $oss;
		$this->view->dbs = $dbs;
		$this->view->frameworks = $frameworks;
		$this->view->bizs = $bizs;
    }
    
    
    /**
     * 保存简历：基本信息
     */
    public function saveresumebaseinfoAction() {
		//获取简历基本信息
		$resumeCode = trim($this->_getParam('resumeCode'));
		$usr_code = trim($this->_getParam('usr_code', '')); //被代理的用户编号
		$this->roleCode = trim($this->_getParam('roleCode', '')); //被代理的角色
		
		//View里用数据库字段名来代替输入框ID和Name后，新的参数接受方法
		//设置简历基本信息
		//普通用户自己创建简历时，设置简历基本信息如下，
		//而，如果是法人或管理员创建简历时，再修改人才编号talent_code
		$rsmBaseInfo = array('fullname' => null, 'birthplace' => null, 
			'actual_residence_cntry' => null, 'actual_residence' => null,
			'actual_residence_province' => null, 'nearest_station' => null, 'final_degree' => null, 
			'sex' => null, 'birthday' => null, 'marital' => null, 'nationality' => null, 
			'major' => null, 'ja_ability' => null, 'en_ability' => null, 'ja_qualification' => null, 
			'en_qualification' => null, 'tel' => null, 'email' => null, 'date_arrive_jp' => null, 
			'date_graduate' => null, 'skill' => null, 'pr' => null, 'fullname_p' => null,
		);
		foreach ($rsmBaseInfo as $k => &$v) {
			$v = trim($this->_getParam($k));
		}
		$rsmBaseInfo['code'] = $resumeCode;
		$rsmBaseInfo['update_usr_code'] = $this->auth->usr['code'];
		$rsmBaseInfo['ok_base'] = 'Y';
		$rsmBaseInfo['enabled'] = 'Y';
		
    	//验证简历基本信息：生年月日, 氏名, 电话, 現住所, E-Mail, 国籍
    	$labels = array();
		$msgs = array();
		if (in_array($this->auth->usr['role_code'], array('MEMBER'))) {
	    	if (empty($rsmBaseInfo['email']) || !UtilService::isEmail($rsmBaseInfo['email']) || $rsmBaseInfo['email'] != $this->auth->usr['email']) {
	    		$labels[] = 'label_email';
	    		$msgs[] = $this->tr->translate('email_should_be_the_same_with_register_email');
			}
		} else {
	    	if (empty($rsmBaseInfo['email']) || !UtilService::isEmail($rsmBaseInfo['email'])) {
	    		$labels[] = 'label_email';
	    		$msgs[] = $this->tr->translate('email_can_not_be_empty');
			}
		}
		
    	if (empty($rsmBaseInfo['fullname_p'])) {
    		$labels[] = 'label_pFullname';
    		$msgs[] = $this->tr->translate('please_input_fullname') . $this->tr->translate('PINYIN');
		}
		
    	if (empty($rsmBaseInfo['fullname'])) {
    		$labels[] = 'label_fullname';
    		$msgs[] = $this->tr->translate('please_input_fullname');
		}
		
    	if (empty($rsmBaseInfo['birthday'])) {
    		$labels[] = 'label_birthday';
    		$msgs[] = $this->tr->translate('PLEASE') . $this->tr->translate('INPUT') . $this->tr->translate('BIRTHDAY');
		}
		
    	if (empty($rsmBaseInfo['ja_ability'])) {
    		$labels[] = 'label_jaAbility';
    		$msgs[] = $this->tr->translate('please_select_ja_ability');
		}
		
		if (empty($rsmBaseInfo['tel'])) {
			$labels[] = 'label_tel';
    		$msgs[] = $this->tr->translate('please_input_tel');
		} else if (!UtilService::isTel($rsmBaseInfo['tel'])) {
			$labels[] = 'label_tel';
			$msgs[] = $this->tr->translate('tel_is_not_real');
		}
		
		if (empty($rsmBaseInfo['nationality'])) {
			$labels[] = 'label_nationality';
    		$msgs[] = $this->tr->translate('please_select_nationality');
		}
		
		if (empty($rsmBaseInfo['date_graduate'])) {
			$labels[] = 'label_dateGraduate';
    		$msgs[] = $this->tr->translate('PLEASE') . $this->tr->translate('ENTER') . $this->tr->translate('GRADUATE_YEAR');
		}
		
		$labels = array_unique($labels); //去除重复的label
		if (!empty($labels)) {
			$ret = array('err' => 2, 'msg' => json_encode($msgs), 'labels' => json_encode($labels));
			exit(json_encode($ret));
		}
		
		//如果是法人或管理员帮助法人添加新员工，或管理员添加自己的员工，设置员工注册信息
		//区别员工与普通用户的条件为，看usr表email与email_consignee是否一致，一致则为普通用户，不一致则为员工，
		//最开始的想法是，用EMP12001等来做员工的code，
		//后来，放弃，原因如下：1. 后续系统升级，员工可能转变为普通用户；2. 创建简历时，因为可以创建简历的角色多样，难以判断是否为员工
		if ($this->auth->usr['role_code'] == 'LP'
			|| ($this->auth->usr['role_code'] == 'ADMIN' && in_array($this->roleCode, array('LP', 'ADMIN', ''))) //当role_code为空时，表示给当前登陆的管理员自己添加新员工
		) {
			$register = array(); //员工（属于管理员自己或法人）用户注册信息
			$resume = ResumeService::getResumeByCode($resumeCode, array('talent_code'));
			//如果简历还没有被保存，即还没有创建员工注册信息
			if (empty($resume)) {
		    	$rsmBaseInfo['talent_code'] = ActiveEtcService::genCode('USR_CODE'); //生成员工用户唯一编号
		    	//如果是法人添加新员工，设置法人编号
		    	if ($this->auth->usr['role_code'] == 'LP') {
					$rsmBaseInfo['lp_code'] = $this->auth->usr['code'];
		    	} 
		    	//如果是管理员帮助法人添加新员工，设置法人编号
		    	else if ($this->auth->usr['role_code'] == 'ADMIN' && (in_array($this->roleCode, array('LP')))) {
					$rsmBaseInfo['lp_code'] = $usr_code;
		    	} 
		    	//管理员添加自己的员工，设置管理员编号
		    	else if($this->auth->usr['role_code'] == 'ADMIN') {
		    		//如果管理员代理其他管理员添加新员工
		    		if (in_array($this->roleCode, array('ADMIN'))) {
						$rsmBaseInfo['admin_code'] = $usr_code;
		    		} else { //管理员添加自己的新员工
						$rsmBaseInfo['admin_code'] = $this->auth->usr['code'];
		    		}
		    	}
				$register['code'] = $rsmBaseInfo['talent_code'];
		    	$register['email'] = $rsmBaseInfo['email'] . '.' . date('YmdHis');
			//如果简历保存后，再修改，则调用原来的员工编号;
			//或者，修改法人修改自己员工的简历；
			//或者，管理员修改人才的简历
			} else {
				$rsmBaseInfo['talent_code'] = $resume['talent_code'];
				$register['code'] = $resume['talent_code'];
			}
			$register['role_code'] = 'EMP';
	    	$register['email_consignee'] = $rsmBaseInfo['email'];
			$register['passwd'] = '';
			$register['nickname'] = $rsmBaseInfo['fullname'];
			$register['fullname_p'] = $rsmBaseInfo['fullname_p'];
			$register['fullname'] = $rsmBaseInfo['fullname'];
			$register['tel'] = $rsmBaseInfo['tel'];
			$register['birthday'] = $rsmBaseInfo['birthday'];
			$register['sex'] = $rsmBaseInfo['sex'];
			$register['country'] = $rsmBaseInfo['actual_residence_cntry'];
			$register['province'] = $rsmBaseInfo['actual_residence_province'];
			$register['is_receive_news'] = 'Y';
			$register['referee'] = '';
			
			//保存员工用户注册信息
			//los2 test
			UsrService::saveUsrByCode($register['code'], $register);
		}
		
		//如果是管理员修改普通用户简历，为什么是修改，是因为，普通用户在注册时，即会自动创建简历(不完整)
		else if ($this->auth->usr['role_code'] == 'ADMIN' && in_array($this->roleCode, array('MEMBER'))) {
			$rsmBaseInfo['admin_code'] = '';
		}
		
		//los2 test
//		var_dump($register['code']);
//		var_dump($register);
//		var_dump($row);
//		exit;
		//保存用户简历基本信息
		$result = ResumeService::saveResumeByCode($resumeCode, $rsmBaseInfo);
		if ($result) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('save_resume_success'));
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('save_resume_error'));
		exit(json_encode($ret));
    }
    
    
    /**
     * 保存简历：业务
     */
    public function saveresumebizAction() {
    	//简历编号
    	$resumeCode = trim($this->_getParam('resumeCode'));
    	
    	//验证简历——基本信息是否已经保存，若是，则继续，否则，退出提示先保存简历——基本信息
		$resume = ResumeService::getResumeByCode($resumeCode, array('ok_base'));
		if ($resume['ok_base'] != 'Y') {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('please_save_resume_base_info_firstly'));
			exit(json_encode($ret));
		}
		
		//查询杂项表
    	$types = array('OS', 'DB', 'FRAMEWORK', 'BIZ', 'OS_OTHER', 'DB_OTHER', 'FW_OTHER', 'BIZ_OTHER');
		$etcs = EtcService::getEtcsByTypes($types, array('type', 'code', 'name'));
		//OS, DB, FW, BIZ的其它项
		$keyOsOthers = array('os_other1', 'os_other2', 'os_other3');
		$keyDbOthers = array('db_other');
		$keyFwOthers = array('framework_other1', 'framework_other2', 'framework_other3');
		$keyBizOthers = array('biz_other1', 'biz_other2', 'biz_other3');
		//获取参数
		//简历业务其它
		$osOthers = array(); //OS其它参数的名称，及键值对
		$dbOthers = array(); //DB其它参数的名称，及键值对
		$fwOthers = array(); //FW其它参数的名称，及键值对
		$bizOthers = array(); //BIZ其它参数的名称，及键值对
		foreach ($keyOsOthers as $v) {
			$osOthers[$v] = trim($this->_getParam($v));
			$key_level = $v . '_level';
			$key_age = $v . '_age';
			$osOthers[$key_level] = trim($this->_getParam($key_level));
			$osOthers[$key_age] = trim($this->_getParam($key_age));
		}
		foreach ($keyDbOthers as $v) {
			$dbOthers[$v] = trim($this->_getParam($v));
			$key_level = $v . '_level';
			$key_age = $v . '_age';
			$dbOthers[$key_level] = trim($this->_getParam($key_level));
			$dbOthers[$key_age] = trim($this->_getParam($key_age));
		}
		foreach ($keyFwOthers as $v) {
			$fwOthers[$v] = trim($this->_getParam($v));
			$key_level = $v . '_level';
			$key_age = $v . '_age';
			$fwOthers[$key_level] = trim($this->_getParam($key_level));
			$fwOthers[$key_age] = trim($this->_getParam($key_age));
		}
		foreach ($keyBizOthers as $v) {
			$bizOthers[$v] = trim($this->_getParam($v));
			$key_level = $v . '_level';
			$key_age = $v . '_age';
			$bizOthers[$key_level] = trim($this->_getParam($key_level));
			$bizOthers[$key_age] = trim($this->_getParam($key_age));
		}
    	//如果简历业务有重复的，在这里只有手动选择与输入的存在重复的可能性，则提示用户更改, dup: duplicate
		$dupBizs = array('os' => false, 'db' => false, 'framework' => false, 'biz' => false);
		if (count(array_filter($osOthers)) != count(array_unique(array_filter($osOthers)))) {
			$dupBizs['os'] = true;
		}
		if (count(array_filter($fwOthers)) != count(array_unique(array_filter($fwOthers)))) {
			$dupBizs['framework'] = true;
		}
		if (count(array_filter($bizOthers)) != count(array_unique(array_filter($bizOthers)))) {
			$dupBizs['biz'] = true;
		}
		//标识重复的简历业务——OS，DB，FW，BIZ
    	$labels = array();
		foreach ($dupBizs as $bizCode => $dupBiz) {
			if ($dupBiz) {
				$labels[] = 'label_' . $bizCode;
			}
		}
		if (!empty($labels)) {
			$ret = array('err' => 2, 'msg' => $this->tr->translate('BIZ_CAN_NOT_BE_DUPLICATE'), 'labels' => json_encode($labels));
			exit(json_encode($ret));
		}
		
		//简历业务核心
		$keyOss = array(); //OS参数的键
		$keyDbs = array(); //DB参数的键
		$keyFws = array(); //FW参数的键
		$keyBizs = array(); //BIZ参数的键
		$oss = array(); //OS参数的键值对
		$dbs = array(); //DB参数的键值对
		$fws = array(); //FW参数的键值对
		$bizs = array(); //BIZ参数的键值对
		$dbOsOthers = array();
		$dbDbOthers = array();
		$dbFwOthers = array();
		$dbBizOthers = array();
		foreach ($etcs as $etc) {
			switch ($etc['type']) {
				case 'OS': //操作系统
					$keyOss[] = $etc['code'];
					break;
				case 'DB': //数据库
					$keyDbs[] = $etc['code'];
					break;
				case 'FRAMEWORK': //语言&框架
					$keyFws[] = $etc['code'];
					break;
				case 'BIZ': //业务
					$keyBizs[] = $etc['code'];
					break;
				case 'OS_OTHER':
					$dbOsOthers[] = $etc['name'];
					break;
				case 'DB_OTHER':
					$dbDbOthers[] = $etc['name'];
					break;
				case 'FW_OTHER':
					$dbFwOthers[] = $etc['name'];
					break;
				case 'BIZ_OTHER':
					$dbBizOthers[] = $etc['name'];
					break;
			}
		}
		
		foreach ($keyOss as $v) {
			$key_level = $v . '_level';
			$key_age = $v . '_age';
			$oss[$key_level] = trim($this->_getParam($key_level));
			$oss[$key_age] = trim($this->_getParam($key_age));
		}
		foreach ($keyDbs as $v) {
			$key_level = $v . '_level';
			$key_age = $v . '_age';
			$dbs[$key_level] = trim($this->_getParam($key_level));
			$dbs[$key_age] = trim($this->_getParam($key_age));
		}
		foreach ($keyFws as $v) {
			$key_level = $v . '_level';
			$key_age = $v . '_age';
			$fws[$key_level] = trim($this->_getParam($key_level));
			$fws[$key_age] = trim($this->_getParam($key_age));
		}
		foreach ($keyBizs as $v) {
			$key_level = $v . '_level';
			$key_age = $v . '_age';
			$bizs[$key_level] = trim($this->_getParam($key_level));
			$bizs[$key_age] = trim($this->_getParam($key_age));
		}
		
		//赋值给简历业务记录
		$rows = array(); //resume_biz记录
		$keyOssAll = array_merge($keyOss, $keyOsOthers);
		$keyDbsAll = array_merge($keyDbs, $keyDbOthers);
		$keyFwsAll = array_merge($keyFws, $keyFwOthers);
		$keyBizsAll = array_merge($keyBizs, $keyBizOthers);
		
		foreach ($keyOssAll as $v) {
			if (in_array($v, $keyOsOthers)) {
				$bizName = $osOthers[$v];
				$level = $osOthers[$v . '_level'];
				$age = $osOthers[$v . '_age'];
			} else {
				$bizName = '';
				$level = $oss[$v . '_level'];
				$age = $oss[$v . '_age'];
			}
			$rows[] = array('resume_code' => $resumeCode, 'type' => 'OS', 'biz' => $v, 'biz_name' => $bizName, 
				'level' => $level, 'age' => $age);
		}
		foreach ($keyDbsAll as $v) {
			if (in_array($v, $keyDbOthers)) {
				$bizName = $dbOthers[$v];
				$level = $dbOthers[$v . '_level'];
				$age = $dbOthers[$v . '_age'];
			} else {
				$bizName = '';
				$level = $dbs[$v . '_level'];
				$age = $dbs[$v . '_age'];
			}
			$rows[] = array('resume_code' => $resumeCode, 'type' => 'DB', 'biz' => $v, 'biz_name' => $bizName, 
				'level' => $level, 'age' => $age);
		}
		foreach ($keyFwsAll as $v) {
			if (in_array($v, $keyFwOthers)) {
				$bizName = $fwOthers[$v];
				$level = $fwOthers[$v . '_level'];
				$age = $fwOthers[$v . '_age'];
			} else {
				$bizName = '';
				$level = $fws[$v . '_level'];
				$age = $fws[$v . '_age'];
			}
			$rows[] = array('resume_code' => $resumeCode, 'type' => 'FRAMEWORK', 'biz' => $v, 'biz_name' => $bizName, 
				'level' => $level, 'age' => $age);
		}
		foreach ($keyBizsAll as $v) {
			if (in_array($v, $keyBizOthers)) {
				$bizName = $bizOthers[$v];
				$level = $bizOthers[$v . '_level'];
				$age = $bizOthers[$v . '_age'];
			} else {
				$bizName = '';
				$level = $bizs[$v . '_level'];
				$age = $bizs[$v . '_age'];
			}
			$rows[] = array('resume_code' => $resumeCode, 'type' => 'BIZ', 'biz' => $v, 'biz_name' => $bizName, 
				'level' => $level, 'age' => $age);
		}
		
		//验证简历业务是否合法：如果年份不为0，则要求级别不为空，且业务名不为空（有些业务名是自己输入或选择的），否则，提示输入有错
		//后来想想，改为，只有业务名、熟练程度和使用年限都不为空，才保存，否则，删除
		foreach ($rows as $key => $row) {
			//如果是OS, DB, FRAMEWORK, BIZ中的other
			if (strpos($row['biz'], 'other') !== false) {
				if (empty($row['biz_name']) || empty($row['level']) || empty($row['age'])) {
					unset($rows[$key]);
				}
			} else {
				if (empty($row['level']) || empty($row['age'])) {
					unset($rows[$key]);
				}
			}
		}
		//los test
//		var_dump($rows);
//		exit;
		
		//OS, DB, 语言&框架, 业务，这4类业务，每类必须设置一个
		$existBizs = array('os' => false, 'db' => false, 'framework' => false, 'biz' => false);
		foreach ($rows as $r) {
			if ($r['type'] == 'OS') {
				$existBizs['os'] = true;
			} else if ($r['type'] == 'DB') {
				$existBizs['db'] = true;
			} else if ($r['type'] == 'FRAMEWORK') {
				$existBizs['framework'] = true;
			} else if ($r['type'] == 'BIZ') {
				$existBizs['biz'] = true;
			}
		}
		
		//标识缺少的简历业务——OS，DB，FW，BIZ
		$labels = array();
		foreach ($existBizs as $bizCode => $existBiz) {
			if ($existBiz === false) {
				$labels[] = 'label_' . $bizCode;
			}
		}
		if (!empty($labels)) {
			$ret = array('err' => 2, 'msg' => $this->tr->translate('every_biz_type_should_have_at_lease_one_item'), 'labels' => json_encode($labels));
			exit(json_encode($ret));
		}
		
		//保存简历——业务
		$result = ResumeService::saveResumeBiz($rows);
		if ($result) {
			//保存简历-业务中，其它OS，DB，FW，BIZ
			if (!empty($osOthers['os_other3']) && !in_array($osOthers['os_other3'], $dbOsOthers)) {
				$newEtcs[] = array('type' => 'OS_OTHER', 'code' => $osOthers['os_other3'], 'name' => $osOthers['os_other3']);
			}
			if (!empty($dbOthers['db_other']) && !in_array($dbOthers['db_other'], $dbDbOthers)) {
				$newEtcs[] = array('type' => 'DB_OTHER', 'code' => $dbOthers['db_other'], 'name' => $dbOthers['db_other']);
			}
			if (!empty($fwOthers['framework_other3']) && !in_array($fwOthers['framework_other3'], $dbFwOthers)) {
				$newEtcs[] = array('type' => 'FW_OTHER', 'code' => $fwOthers['framework_other3'], 'name' => $fwOthers['framework_other3']);
			}
			if (!empty($bizOthers['biz_other3']) && !in_array($bizOthers['biz_other3'], $dbBizOthers)) {
				$newEtcs[] = array('type' => 'BIZ_OTHER', 'code' => $bizOthers['biz_other3'], 'name' => $bizOthers['biz_other3']);
			}
			if (!empty($newEtcs)) {
				BaseService::addRows('etc', $newEtcs);
			}
			$ret = array('err' => 0, 'msg' => $this->tr->translate('save_resume_biz_success'), 'code' => $resumeCode);
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('save_resume_biz_error'));
		exit(json_encode($ret));
    }
    
    
    /**
     * 保存简历——项目经验 
     */
    public function saveresumeprjAction() {
    	//获取参数：简历——项目经验
    	$resumeCode = trim($this->_getParam('resumeCode'));
		$this->dateBegins = $this->_getParam('dateBegins');
		$this->dateEnds = $this->_getParam('dateEnds');
		$this->prjContents = $this->_getParam('prjContents');
		$this->machine_oss = $this->_getParam('machine_oss');
		$this->lang_dbs = $this->_getParam('lang_dbs');
		$this->poss = $this->_getParam('poss');
		$this->seg_mgts = $this->_getParam('seg_mgts');
		$this->seg_bizAnalys = $this->_getParam('seg_bizAnalys');
		$this->seg_cmpntDefs = $this->_getParam('seg_cmpntDefs');
		$this->seg_preliminaryDesigns = $this->_getParam('seg_preliminaryDesigns');
		$this->seg_dbDesigns = $this->_getParam('seg_dbDesigns');
		$this->seg_detailDesigns = $this->_getParam('seg_detailDesigns');
		$this->seg_codings = $this->_getParam('seg_codings');
		$this->seg_unitTests = $this->_getParam('seg_unitTests');
		$this->seg_moduleTests = $this->_getParam('seg_moduleTests');
		$this->seg_integrationTests = $this->_getParam('seg_integrationTests');
		$this->seg_advisorys = $this->_getParam('seg_advisorys');
		$this->seg_maintainces = $this->_getParam('seg_maintainces');
    	$this->prjCnt = count($this->prjContents); //项目条数
    	
    	//验证简历——基本信息是否已经保存，若是，则继续，否则，退出提示先保存简历——基本信息
		$resume = ResumeService::getResumeByCode($resumeCode, array('ok_base'));
		if ($resume['ok_base'] != 'Y') {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('please_save_resume_base_info_firstly'));
			exit(json_encode($ret));
		}
    	
    	//整理项目开始时间与结束时间
    	foreach ($this->dateBegins as &$dateBegin) {
    		if (!empty($dateBegin)) {
    			$dateBegin .= '-01';
    		}
    	}
    	foreach ($this->dateEnds as &$dateEnd) {
    		if (!empty($dateEnd)) {
    			$dateEnd .= '-01';
    		}
    	}
    	//los2 test
//		var_dump($this->dateBegins);
//		var_dump($this->dateEnds);
//		exit;
		
		//验证项目经验是否合法：当项目内容不为空时，要求输入开始与结束时间、機種ＯＳ、言語ＤＢ、役割
		//否则，删除该条项目经验
		$labelsEmpty = array(); //信息不完善的项目
		$labelsIllegal = array(); //信息不合法的项目：要求：结束时间 > 开始时间
		for ($i = 0; $i < $this->prjCnt; $i++) {
			if (!empty($this->prjContents[$i])) {
				if (empty($this->dateBegins[$i])
					|| empty($this->dateEnds[$i])
					|| empty($this->machine_oss[$i])
					|| empty($this->lang_dbs[$i])
					|| empty($this->poss[$i])
				) {
//					$labelsEmpty[] = 'label_prj_' . ($i + 1);
					$labelsEmpty[] = $i;
				} else if ($this->dateBegins[$i] >= $this->dateEnds[$i]) {
//					$labelsIllegal[] = 'label_prj_' . ($i + 1);
					$labelsIllegal[] = $i;
				}
			} else {
				unset($this->dateBegins[$i]);
				unset($this->dateEnds[$i]);
				unset($this->prjContents[$i]);
				unset($this->machine_oss[$i]);
				unset($this->lang_dbs[$i]);
				unset($this->poss[$i]);
				unset($this->seg_mgts[$i]);
				unset($this->seg_bizAnalys[$i]);
				unset($this->seg_cmpntDefs[$i]);
				unset($this->seg_preliminaryDesigns[$i]);
				unset($this->seg_dbDesigns[$i]);
				unset($this->seg_detailDesigns[$i]);
				unset($this->seg_codings[$i]);
				unset($this->seg_unitTests[$i]);
				unset($this->seg_moduleTests[$i]);
				unset($this->seg_integrationTests[$i]);
				unset($this->seg_advisorys[$i]);
				unset($this->seg_maintainces[$i]);
				$this->prjCnt--;
			}
		} //End: for ($i = 0; $i < $this->prjCnt; $i++)
		
		if (!empty($labelsEmpty)) {
			$ret = array('err' => 2, 'msg' => $this->tr->translate('please_fullfill_prj_info'), 'labels' => json_encode($labelsEmpty));
			exit(json_encode($ret));
		} else if (!empty($labelsIllegal)) {
			$ret = array('err' => 2, 'msg' => $this->tr->translate('END_DATE_SHOULD_BE_LATER_THAN_BEGIN_DATE'), 'labels' => json_encode($labelsIllegal));
			exit(json_encode($ret));
		}
		
		//对验证后的项目经验排序，用于根据下标赋值
		$this->dateBegins = array_values($this->dateBegins);
		$this->dateEnds = array_values($this->dateEnds);
		$this->prjContents = array_values($this->prjContents);
		$this->machine_oss = array_values($this->machine_oss);
		$this->lang_dbs = array_values($this->lang_dbs);
		$this->poss = array_values($this->poss);
		$this->seg_mgts = array_values($this->seg_mgts);
		$this->seg_bizAnalys = array_values($this->seg_bizAnalys);
		$this->seg_cmpntDefs = array_values($this->seg_cmpntDefs);
		$this->seg_preliminaryDesigns = array_values($this->seg_preliminaryDesigns);
		$this->seg_dbDesigns = array_values($this->seg_dbDesigns);
		$this->seg_detailDesigns = array_values($this->seg_detailDesigns);
		$this->seg_codings = array_values($this->seg_codings);
		$this->seg_unitTests = array_values($this->seg_unitTests);
		$this->seg_moduleTests = array_values($this->seg_moduleTests);
		$this->seg_integrationTests = array_values($this->seg_integrationTests);
		$this->seg_advisorys = array_values($this->seg_advisorys);
		$this->seg_maintainces = array_values($this->seg_maintainces);
		
		//赋值给项目经验记录
		$rows = array();
		for ($i = 0; $i < $this->prjCnt; $i++) {
			$rows[$i]['resume_code'] = $resumeCode;
			$rows[$i]['prj_num'] = $i + 1;
			$rows[$i]['date_begin'] = $this->dateBegins[$i];
			$rows[$i]['date_end'] = $this->dateEnds[$i];
			$rows[$i]['content'] = $this->prjContents[$i];
			$rows[$i]['machine_os'] = $this->machine_oss[$i];
			$rows[$i]['lang_db'] = $this->lang_dbs[$i];
			$rows[$i]['pos'] = $this->poss[$i];
			$rows[$i]['seg_mgt'] = $this->seg_mgts[$i];
			$rows[$i]['seg_biz_analy'] = $this->seg_bizAnalys[$i];
			$rows[$i]['seg_cmpnt_def'] = $this->seg_cmpntDefs[$i];
			$rows[$i]['seg_preliminary_design'] = $this->seg_preliminaryDesigns[$i];
			$rows[$i]['seg_db_design'] = $this->seg_dbDesigns[$i];
			$rows[$i]['seg_detail_design'] = $this->seg_detailDesigns[$i];
			$rows[$i]['seg_coding'] = $this->seg_codings[$i];
			$rows[$i]['seg_unit_test'] = $this->seg_unitTests[$i];
			$rows[$i]['seg_module_test'] = $this->seg_moduleTests[$i];
			$rows[$i]['seg_integration_test'] = $this->seg_integrationTests[$i];
			$rows[$i]['seg_advisory'] = $this->seg_advisorys[$i];
			$rows[$i]['seg_maintaince'] = $this->seg_maintainces[$i];
		}
		
		//保存简历——项目经验
		$result = ResumeService::saveResumePrj($rows);
		if ($result == TRUE) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('save_resume_prj_success'), 'code' => $resumeCode);
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('save_resume_prj_error'));
		exit(json_encode($ret));
    }
    
    
    /**
     * 保存简历——其它
     */
    public function saveresumeotherAction() {
		//获取参数：其它
		$resumeCode = trim($this->_getParam('resumeCode'));
		
    	//验证简历——基本信息是否已经保存，若是，则继续，否则，退出提示先保存简历——基本信息
		$resume = ResumeService::getResumeByCode($resumeCode, array('ok_base', 'ok_biz', 'ok_prj', 'ok_other', 'bid_date_end'));
		if ($resume['ok_base'] != 'Y') {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('please_save_resume_base_info_firstly'));
			exit(json_encode($ret));
		}
		
		//法人添加员工简历其它信息
		$remark_lp = trim($this->_getParam('remark_lp', ''));
		//管理员添加人才简历其它信息
		$salary_exp_display = trim($this->_getParam('salary_exp_display', 0));
		$salary_min_display = trim($this->_getParam('salary_min_display', 0));
		$remark_admin = trim($this->_getParam('remark_admin', ''));
		//法人与管理员创建人材简历的其它信息
		$bid_points = $this->_getParam('bid_points', 0);
		$bid_date_end = trim($this->_getParam('bid_date_end', date('Y-m-d', strtotime('+1 week', time()))));
		$today = date('Y-m-d');
		
		$row = array('able_work_date_choice' => null, 'able_work_date' => null, 'is_apply_prj' => null, 
			'interview_wait' => null, 'interview_result_wait' => null, 'salary_exp' => null, 
			'salary_min' => null, 
			'car_fee' => null, 'insurance' => array(), 'exp_workplace' => array(), 
			'exp_prj_form' => null, 'visa_form' => null, 'unseen_coms' => array(),
			'is_open' => null
		);
		//代理保存简历其它时，获取简历其它信息，如果不为空，则表示有输入，则根据需要验证再保存
		$row_4proxy = array('remark_lp' => null, 'salary_exp_display' => null, 'salary_exp_display' => null, 
			'remark_admin' => null, 'bid_points' => null, 'bid_date_end' => null
		);
		foreach ($row as $k => &$v) {
			if (is_array($v)) {
				$v = $this->_getParam($k, array());
			} else {
				$v = trim($this->_getParam($k));
			}
		}
		foreach ($row_4proxy as $k2 => &$v2) {
			$v2 = trim($this->_getParam($k2));
		}
		$row['ok_other'] = 'Y';
		
		//验证简历——其它
		$labels = array();
    	$msgs = array();
    	//可工作日
    	if (empty($row['able_work_date_choice'])) {
    		$labels[] = 'label_ableWorkDateChoice';
    		$msgs[] = $this->tr->translate('please_select_able_work_date');
		} else if ($row['able_work_date_choice'] == 'SPECIFY_DATE') {
			if (empty($row['able_work_date']) || $row['able_work_date'] == '0000-00-00') {
	    		$labels[] = 'label_ableWorkDateChoice';
	    		$msgs[] = $this->tr->translate('please_input_able_work_date');
			}
		}
		if ($row['is_apply_prj'] == 'Y') {
			if (empty($row['interview_wait']) || empty($row['interview_result_wait'])) {
				$labels[] = 'label_isApplyPrj';
	    		$msgs[] = $this->tr->translate('HAS_APPLIED_PRJ__SHOULD_INPUT') . $this->tr->translate('INTERVIEW_WAIT') . $this->tr->translate('AND') . $this->tr->translate('INTERVIEW_RESULT_WAIT');
			}
		}
		
		//期望单价
    	if (empty($row['salary_exp'])) {
    		$labels[] = 'label_salaryExp';
    		$msgs[] = $this->tr->translate('please_input_salary_exp');
		} else if (!(is_numeric($row['salary_exp']) && strpos($row['salary_exp'], '.') === false)) {
			$labels[] = 'label_salaryExp';
			$msgs[] = $this->tr->translate('EXP') . $this->tr->translate('UNIT_PRICE') . $this->tr->translate('SHOULD_BE') . $this->tr->translate('INT');
		}
		
		//最低提案单价
    	if (empty($row['salary_min'])) {
    		$labels[] = 'label_salaryMin';
    		$msgs[] = $this->tr->translate('please_input_salary_min');
		} else if (!(is_numeric($row['salary_min']) && strpos($row['salary_min'], '.') === false)) {
			$labels[] = 'label_salaryMin';
			$msgs[] = $this->tr->translate('MIN') . $this->tr->translate('PROPOSE') . $this->tr->translate('UNIT_PRICE') . $this->tr->translate('SHOULD_BE') . $this->tr->translate('INT');
		}
		
		//期望单价 >= 最低提案单价
		if ($row['salary_exp'] < $row['salary_min']) {
			$labels[] = 'label_salaryExp';
			$labels[] = 'label_salaryMin';
    		$msgs[] = $this->tr->translate('salary_exp_should_be_larger_than_salary_min');
		}
		
		if ($this->auth->usr['role_code'] == 'ADMIN') {
			//显示期望单价
	    	if (empty($row['salary_exp_display'])) {
	    		$labels[] = 'label_salaryExp_display';
	    		$msgs[] = $this->tr->translate('please_input_salary_exp_display');
			} else if (!is_numeric($row['salary_exp_display'])) {
				$labels[] = 'label_salaryExp_display';
				$msgs[] = $this->tr->translate('salary_exp_display_is_not_legal');
			}
			//显示最低提案单价
	    	if (empty($row['salary_min_display'])) {
	    		$labels[] = 'label_salaryMin_display';
	    		$msgs[] = $this->tr->translate('please_input_salary_min_display');
			} else if (!is_numeric($row['salary_min_display'])) {
				$labels[] = 'label_salaryMin_display';
				$msgs[] = $this->tr->translate('salary_min_display_is_not_legal');
			}
			//显示期望单价 >= 显示最低提案单价
			if ($row['salary_exp_display'] < $row['salary_min_display']) {
				$labels[] = 'label_salaryExp_display';
				$labels[] = 'label_salaryMin_display';
	    		$msgs[] = $this->tr->translate('salary_exp_display_should_be_larger_than_salary_min_display');
			}
		}
		
    	$labels = array_unique($labels); //去除重复的label
		if (!empty($labels)) {
			$ret = array('err' => 2, 'msg' => json_encode($msgs), 'labels' => json_encode($labels));
			exit(json_encode($ret));
		}
		
		//不可见公司：因为是input text，所以，要删除空字符串项
		foreach ($row['unseen_coms'] as $k => $unseenCom) {
			if (empty($unseenCom)) {
				unset($row['unseenComs[$k]']);
			}
		}
		//将unseen_coms, exp_workplace, insurance转换为json_encode字符串
		//$jsFs: json_encode fields
		$jsFs = array('unseen_coms', 'exp_workplace', 'insurance');
		foreach ($jsFs as $f) {
			$row[$f] = json_encode($row[$f]);
		}
		//将代理创建的信息合并
		foreach ($row_4proxy as $k3 => $v3) {
			if (!empty($v3)) {
				$row[$k3] = $v3;
			}
		}
		//如果是法人给员工竞价，则设定期限为一周
		if (in_array($this->auth->usr['role_code'], array('LP')) && !empty($row['bid_points'])) {
			$row['bid_date_end'] = date('Y-m-d', strtotime('+1 week', time()));
		}
		//los2 test
//		var_dump($row['unseen_coms']);
//		exit;
		
		//如果当前日期>竞价结束日期（此条件对无竞价也适用），
		//则，判断是否有新的竞价；否则，即还在竞价周期之内，不对竞价作处理
		if ($today > $resume['bid_date_end']) {
			//如果有竞价，则要求简历完整，即先保存基本信息，业务经历，项目经验
			if (!empty($row['bid_points']) && $row['bid_points'] > 0) {
				if ($resume['ok_base'] == 'Y' && $resume['ok_biz'] == 'Y' && $resume['ok_prj'] == 'Y') {
					//竞价点数（即推荐度）， 竞价结束日期
					$row['bid_points'] = $row['bid_points'];
					$row['bid_date_end'] = $row['bid_date_end'];
				} else {
					$ret = array('err' => 1, 'msg' => $this->tr->translate('please_save_resume_base_info_biz_prj_firstly'));
					exit(json_encode($ret));
				}
			}
		}
		
		//保存到数据库表resume
		$result = ResumeService::updateResumeByCode($resumeCode, $row);
		if ($result) {
			//Success
			$ret = array('err' => 0, 'msg' => $this->tr->translate('save_resume_other_success'));
			exit(json_encode($ret));
		}
		//Error
		$ret = array('err' => 1, 'msg' => $this->tr->translate('save_resume_other_error'));
		exit(json_encode($ret));
    }
    
    
    /**
     * 查询竞价人才
     */
    public function getbidtalentAction() {
		//获取参数：分页
		$this->getPagination();
		
		//查询竞价人才
		$fields = array('code as resume_code', 'talent_code', 'sex', 'birthday', 'pr', 'able_work_date_choice', 'able_work_date', 'salary_min', 'salary_exp', 'bid_points');
		$strFields = implode(',a.', $fields);
		$strFields = 'a.' . $strFields;
		$sql = "select $strFields
			FROM resume as a
			where a.bid_date_end >= curdate()
				and a.bid_points > 0
				and a.bid_points < 90000000
			order by a.bid_points desc";
		$pgBidTalents = BaseService::getByPageWithSql($sql, $this->pagination);
		
		//return
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $pgBidTalents['Total'], 'Rows' => $pgBidTalents['Rows']);
		exit(json_encode($ret));
    }
    
    
	/**
     * 普通用户自己或管理员设定：是否简历公开
     */
	public function modifyisopenresumeAction() {
    	//获取参数：是否接收信息
    	$resumeCode = trim($this->_getParam('resumeCode'));
    	$isOpenResume = trim($this->_getParam('isOpenResume'));
    	
    	//通过$resumeCode是否为空来判定是普通用户自己还是管理员
    	if (empty($resumeCode)) {
    		$rsm = ResumeService::getRsmByTalentCode($this->auth->usr['code'], array('code'));
    		$resumeCode = $rsm['code'];
    	}
		
    	//验证注册信息：是否接收信息
    	if (empty($isOpenResume)) {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_SELECT_IS_OPEN_RESUME'));
			exit(json_encode($ret));
    	}
		
    	//给resume赋值
    	$resume = array();
    	$resume['is_open'] = $isOpenResume;
		
		//保存用户修改的注册信息
		$result = ResumeService::updateResumeByCode($resumeCode, $resume);
		if ($result === TRUE) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('RESUME_IS_OPEN_SET_SUCC'));
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('RESUME_IS_OPEN_SET_ERR'));
		exit(json_encode($ret));
    }
    
    
    /**
     * 获取人才详情
     */
    public function gettalentdetailAction() {
    	//获取参数：resume_code
    	$resumeCode = trim($this->_getParam('resumeCode'));
    	
    	//查询员工详情 ，涉及：resume
    	$fsRsm = array('code', 'date_graduate', 'date_arrive_jp', 'ja_ability', 'able_work_date_choice', 'able_work_date', 
    		'salary_min', 'salary_exp', 'salary_min_display', 'salary_exp_display', 
    		'remark_lp', 'update_time', 'talent_code', 'fullname', 'sex', 'birthday', 'tel');
    	$fsRsmBiz = array('type', 'biz', 'biz_name', 'age');
    	$resume = ResumeService::getResumeByCode($resumeCode, $fsRsm);
    	$resumeBizs = ResumeService::getRsmBizsByRsmCodeForTalentDetail($resumeCode, $fsRsmBiz);

    	//重组员工信息
    	$this->rsmDetailToView($resume, $resumeBizs);
    	
    	//返回
    	$ret = array('err' => 0, 'msg' => 'Success', 'resume' => $resume);
		exit(json_encode($ret));
    }
    
    
    /**
     * 获取人才详情for admin
     */
    public function gettalentdetail4adminAction() {
    	//获取参数：resume_code
    	$resumeCode = trim($this->_getParam('resumeCode'));
    	
    	//查询员工详情 ，涉及：resume
    	$fsRsm = array('code', 'lp_code', 'admin_code', 'nearest_station', 'date_graduate', 'date_arrive_jp', 'ja_ability', 'able_work_date_choice', 'able_work_date', 
    		'salary_min', 'salary_exp', 'salary_min_display', 'salary_exp_display', 
    		'remark_lp', 'update_time', 'talent_code', 'fullname', 'sex', 'birthday', 'tel');
    	$fsRsmBiz = array('type', 'biz', 'biz_name', 'age');
//    	$resume = ResumeService::getResumeByCode($resumeCode, $fsRsm);
    	$sFsRsm = implode(',a.', $fsRsm);
    	$sFsRsm = 'a.' . $sFsRsm;
    	$sqlRsm = "select $sFsRsm, b.last_login_time, c.fullname as lp_fullname
	    		from resume as a
				join usr as b
				on a.talent_code = b.code
				left join usr as c
				on a.lp_code = c.code
	    		where a.code = '$resumeCode'";
    	$resume = BaseService::getRowBySql($sqlRsm);
    	$resumeBizs = ResumeService::getRsmBizsByRsmCodeForTalentDetail($resumeCode, $fsRsmBiz);

    	//重组员工信息
    	$this->rsmDetailToView($resume, $resumeBizs);
    	
    	//返回
    	$ret = array('err' => 0, 'msg' => 'Success', 'resume' => $resume);
		exit(json_encode($ret));
    }
    
    
    /**
     * 查询法人的公司名或管理员的名称
     */
    public function getcomAction() {
    	//获取参数
    	$usrCode = trim($this->_getParam('usrCode')); //法人或管理员的编号
    	
    	//验证参数
    	if (empty($usrCode)) {
    		$ret = array('err' => 1, 'msg' => 'Error');
			exit(json_encode($ret));
    	}
    	
    	//查询法人的公司名或管理员的名称
    	$usr = BaseService::getRowByCode('usr', $usrCode, array('role_code', 'nickname', 'fullname'));
    	if ($usr['role_code'] == 'ADMIN') {
    		$username = $usr['nickname'];
    	} else if ($usr['role_code'] == 'LP') {
    		$username = $usr['fullname'];
    	}
    	$ret = array('err' => 0, 'msg' => 'Success', 'username' => $username);
		exit(json_encode($ret));
    }
    
    
} //End: class Usr_ResumeController