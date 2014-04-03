<?php
include_once 'BaseController.php';
include_once 'UtilService.php';
include_once 'BaseService2.php';
include_once 'CaseBaseService.php';
include_once 'CaseService.php';
include_once 'EventService.php';
include_once 'BaseService.php';
include_once 'UsrService.php';


class IndexController extends BaseController {

	/**
	 * 首页
	 */
	public function indexAction() {
		$this->layout->headTitle($this->tr->translate('HOMEPAGE'));
		$this->view->layout()->crumbs = array();
		
		//设置密码访问/?pwd=10
//		$pwd = trim($this->_getParam('pwd'));
//		if ($pwd != '10') {
//			$this->_redirect('/error/error');
//		}
		
		$pg = array("limit"=>5, "start"=>0);
		$usr_code = isset($this->auth->usr) ? $this->auth->usr['code'] : '';
		$usr_role = isset($this->auth->usr) ? $this->auth->usr['role_code'] : '';
		$dbNewCases = CaseService::findReleaseCasesFront(array('casetype'=>'cases_new'), $usr_code, $pg, $usr_role);
		$dbRecommendCases = CaseService::findReleaseCasesFront(array('casetype'=>'cases_recommend'), $usr_code, $pg, $usr_role);
		
		$newCases = array();
		if (!empty($dbNewCases['Rows'])) {
			foreach ($dbNewCases['Rows'] as $case) {
				array_push($newCases, $this->caseToView($case));
			}
		}
		$recommendCases = array();
		if (!empty($dbRecommendCases['Rows'])) {
			foreach ($dbRecommendCases['Rows'] as $case) {
				array_push($recommendCases, $this->caseToView($case));
			}
		}
		$this->view->newCases = $newCases;
		$this->view->newCasesCnt = $dbNewCases['Total'];
		$this->view->recommendCases = $recommendCases;
		$this->view->recommendCasesCnt = $dbRecommendCases['Total'];
		$this->view->totalCaseCnt = CaseBaseService::countCases(array());
		
		//人才总数
		$talentsCnt = BaseService::getOneBySql('select count(*) from resume');
		$pubTalentsCnt = BaseService::getOneBySql("select count(*) from resume where is_open = 'Y'");
		$unpubTalentsCnt = $talentsCnt - $pubTalentsCnt;
		
		//查询推荐用户
		$fsRsm = array('code', 'date_graduate', 'date_arrive_jp', 'ja_ability', 'able_work_date_choice', 'able_work_date', 
    		'salary_min', 'salary_exp', 'salary_min_display', 'salary_exp_display',
			'remark_lp', 'update_time', 'talent_code', 'fullname', 'sex', 'birthday', 'tel');
		$pg = array('start' => 0, 'limit' => 5);
		$pgRecmdTalents = ResumeService::getRecmdTalentsByPg($fsRsm, $pg);
		$this->rsmsWithBizToView($pgRecmdTalents['Rows']);
		
		//查询新用户
		$pg = array('start' => 0, 'limit' => 3);
		$pgNewTalents = ResumeService::getNewTalentsByPg($fsRsm, $pg);
		$this->rsmsWithBizToView($pgNewTalents['Rows']);
		
		$page = array('start' => 0, 'limit' => 5, 'sortname' => 'update_time', 'sortorder' => 'desc');
		$eventList = EventService::searchEventList(null, $page);
		
		//view
		$this->view->talentsCnt = $talentsCnt;
		$this->view->pubTalentsCnt = $pubTalentsCnt;
		$this->view->unpubTalentsCnt = $unpubTalentsCnt;
		$this->view->recmdMbrsCnt = $pgRecmdTalents['Total'];
		$this->view->recmdMbrs = $pgRecmdTalents['Rows'];
		$this->view->newMbrsCnt = $pgNewTalents['Total'];
		$this->view->newMbrs = $pgNewTalents['Rows'];
		$this->view->eventList = $eventList['Rows'];
		$this->initcasepage();
	}
	
	
	/**
	 * 初始化案件检索页面各种列表
	 */
	private function initcasepage() {
    	//查询杂项，Web app变量
		$etcs = EtcService::getEtcs();
		foreach ($etcs as $etc) {
			if ($etc['type'] == 'CAREER') { //职种
				$this->careers[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'INDUSTRY') { //业务领域
				$this->industries[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'LANGUAGE') { //程序设计语言
				$this->languages[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'WORKING_PLACE_JP') { //日语要求
				$this->japan[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'WORKING_PLACE_OTHER') { //担当范围
				$this->oversea[$etc['code']] = $this->tr->translate($etc['code']);
			}
		}
		$this->view->industries = $this->industries;
		$this->view->careers = $this->careers;
		$this->view->languages = $this->languages;
		$this->view->japan = $this->japan;
		$this->view->oversea = $this->oversea;
    }
	
	
	/**
	 * 设置语言，locale: 日语为ja_JP，中文为zh_CN
	 */
	public function setlangAction() {
		//获取参数
		$locale = trim($this->_getParam('locale'));
		
		//设置session里的locale变量
		$this->auth->locale = $locale;
		
		//Ret
		$ret = array('err' => 0, 'msg' => $this->tr->translate('set_lang_success'));
		exit(json_encode($ret));
	}
	
	
	/**
	 * 发送推荐邮件
	 */
	public function sendemailAction() {
		//接收参数
		$email = trim($this->_getParam('email'));
		
		//验证参数 
		if (empty($email)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('email_can_not_be_empty'));
			exit(json_encode($ret));
		} else if (!UtilService::isEmail($email)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('email_is_not_real'));
			exit(json_encode($ret));
		} else if (UsrService::existEmail($email)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_ALREADY_EXIST'));
			exit(json_encode($ret));
		}
		
		//验证推荐人是否登陆，没有则提示未登陆，有则继续
		if (empty($this->auth->usr)) {
			$ret = array('err' => -1, 'msg' => $this->tr->translate('you_have_not_logon'));
			exit(json_encode($ret));
		}
		$fromEmail = $this->auth->usr['email_consignee'];
		$fromName = $this->auth->usr['fullname'];
		$toEmail = $email;
		$toName = '';
		$ccEmail = '';
		$ccName = '';
		$bcc = 'linice01@163.com';
		$subject = 'recommand';
		$url = "http://jinzai-anken.com/front_register/register/email/{$this->auth->usr['email']}/passwd/{$this->auth->usr['passwd']}";
		$body = '<div style="color: red;"><a href="' . $url . '" target="_blank">人才-案件</a></div>';
		
		$mail = new Zend_Mail('utf-8');
		$mail->setFrom($fromEmail, $fromName);
		$mail->addTo($toEmail, $toName);
		$mail->addCc($ccEmail, $ccName);
		$mail->addBcc($bcc);
		$mail->setSubject($subject);
		$mail->setBodyHtml($body); //HTML格式，被解析成红色的：Red Hello World!
//		$mail->setBodyText($body); //TEXT格式，保持原样：<div style="color: red;">Red Hello World!</div>
		$result = $mail->send();

		$ret = array('err' => 0, 'msg' => $this->tr->translate('EMAIL_SEND_SUCCESS'));
		exit(json_encode($ret));
	}

} //End: class IndexController