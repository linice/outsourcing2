<?php
include_once 'BaseController.php';
include_once 'LpService.php';
include_once 'UsrService.php';
include_once 'UtilService.php';
include_once 'CaseService.php';
include_once 'BaseService2.php';
include_once 'Msg/MsgService.php';
include_once 'BaseService.php';


/**
 * 法人中心，法人修改界面
 * @author GONGM
 */
class Lp_LpController extends BaseController {
	private $baseinfo = array('companyName'=>null, 'companyNum'=>null, 'website'=>null, 'id'=>null,
						'telephone'=>null, 'address'=>null, /*'email'=>null,*/ 'linkman'=>null, 
						'passwd'=>null, 'passwd2'=>null);
	
	private $rows = array();
	
	
	/**
	 * 法人中心主页面
	 */
	public function lpAction() {
		$this->layout->headTitle($this->tr->translate('lp_center'));
		$crumb = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$this->view->layout()->crumbs = array($crumb);
		
		//统计
		$lpCode = $this->auth->usr['code'];
		$caseCount = CaseService::countEffectiveCases($lpCode);
		$resumeCount = CaseService::countEffectiveEmp($lpCode);
		$empCount = CaseService::countAllEmp($lpCode);
		$unabledResumeCount = $empCount - $resumeCount;
		//未读消息数
		$msgsCnt = MsgService::cntUnreadMsgsByRecverCode($lpCode);
		$dbLpSetting = LpService::findAllSettingByLpCode($lpCode);
		$lpSetting = array();
		if (!empty($dbLpSetting)) {
			foreach ($dbLpSetting as $setting) {
				$lpSetting[$setting['setting_code']] = $setting;
			}
		}
		$this->view->lpSetting = $lpSetting;
		$this->view->caseCount = $caseCount;
		$this->view->empCount = $resumeCount;
		$this->view->unabledResumeCount = $unabledResumeCount;
		$this->view->msgsCnt = $msgsCnt;
	}
	
	
	/**
	 * 进入修改法人信息界面
	 */
	public function baseinfoAction() {
		$this->layout->headTitle($this->tr->translate('base_info'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_lp/baseinfo', 'name' => $this->tr->translate('base_info'));
		$this->view->layout()->crumbs = $crumbs;
		
		$jsRegisterInfo = $this->_getParam('registerInfo');
		if (!empty($jsRegisterInfo)) {
			$lp = (array)json_decode($jsRegisterInfo);
		} else {
			$lp = UsrService::getUsrByCode($this->auth->usr['code']);
		}
		$this->view->lp = $lp;
	}
	
	
	private function fetchFromEntity($lp) {
		foreach (array_keys($this->baseinfo) as $value) {
			$this->baseinfo[$value] = $lp[$value];
		}
	}
	
	
	private function fetchFromParam() {
		foreach (array_keys($this->baseinfo) as $value) {
			$this->baseinfo[$value] = trim($this->_getParam($value));
		}
	}
	
	
	/**
	 * JSON验证邮箱
	 */
	public function valemailAction() {
   		$email = trim($this->_getParam('email'));
   		$id = trim($this->_getParam('id'));
		if (empty($email)) {
			exit();
		} else {
			$lp = UsrService::getUsrByEmail($email);
			if ($lp !== FALSE && $lp['id'] != $id) {
				$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_ALREADY_EXIST'), 'errField' => 'email');
				exit(json_encode($ret));
			} else {
				$ret = array('err' => 0, 'msg' => 'OK');
				exit(json_encode($ret));
			}
		}
	}
	
   	
	/**
	 * 修改法人信息
	 */
	public function modlpAction() {
		//设置页面标题和导航
		$this->layout->headTitle($this->tr->translate('lp_reg_succ'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_lpreg', 'name' => $this->tr->translate('lp_register'));
		$crumbs[] = array('uri' => '/front_lpreg/regsucc', 'name' => $this->tr->translate('lp_reg_succ'));
		$this->view->layout()->crumbs = $crumbs;
		
		//获取参数
		$this->fetchFromParam();
		if (empty($this->baseinfo['companyName'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('companyName_can_not_be_empty'), 'errField' => 'companyName');
			exit(json_encode($ret));
		} else if (empty($this->baseinfo['linkman'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('linkman_can_not_be_empty'), 'errField' => 'linkman');
			exit(json_encode($ret));
		} else if (empty($this->baseinfo['telephone'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('telephone_can_not_be_empty'), 'errField' => 'telephone');
			exit(json_encode($ret));
		} else if (!UtilService::isTel($this->baseinfo['telephone'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('telephone_style_error'), 'errField' => 'telephone');
			exit(json_encode($ret));;
		} else if (empty($this->baseinfo['id'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('lp_not_exists'));
			exit(json_encode($ret));
		} else if ($this->baseinfo['passwd'] != $this->baseinfo['passwd2']) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('passwords_are_not_equal'), 'errField' => 'passwd2');
			exit(json_encode($ret));
		}
		
		//法人用户记录信息
		$lp = array('lp_linkman' => $this->baseinfo['linkman'], 
					'fullname' => $this->baseinfo['companyName'], 'nickname' => $this->baseinfo['companyName'],
					'website' => $this->baseinfo['website'], 'tel' =>$this->baseinfo['telephone'],
					'lp_address' =>$this->baseinfo['address']);
		if (!empty($this->baseinfo['passwd'])) {
			$lp['passwd'] = md5($this->baseinfo['passwd']);
		}
		$result = LpService::updateLp($this->baseinfo['id'], $lp);
		if ($result) {
			$this->copyProperty($lp, $this->auth->usr);
//			$this->showSuccessMsg('/lp_lp/lp', 'base_info', 'lp_mod_succ');
//			return;
			$ret = array('err' => 0, 'msg' => $this->tr->translate('MODIFY') . $this->tr->translate('SUCCESS'));
			exit(json_encode($ret));
		}
//		$this->showErrorMsg('/lp_lp/lp', 'base_info', 'error_and_contact_admin');
		$ret = array('err' => 1, 'msg' => $this->tr->translate('MODIFY') . $this->tr->translate('SUCCESS'));
		exit(json_encode($ret));
	}
	
	
	/**
	 * 邮件模板设定：内容
	 */
	public function modifyemailtempAction() {
		//获取参数
		$tpl = $this->_getParam('temp');
		
		//验证参数
		if (empty($tpl)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('please_select_a_email_temp'));
			exit(json_encode($ret));
		}
		$retVal = LpService::modifyEmailTemp($this->auth->usr['code'], $tpl);
		if ($retVal) {
			$this->auth->usr['lp_propose_temp'] = $tpl;
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
		exit(json_encode($ret));
	}
	
	
	/**
	 * 邮件模板设定：邮件头和邮件尾
	 */
	public function modifyemailoptionAction() {
		//获取参数
		$tpl = $this->_getParam('layout');
		$header = $this->_getParam('layoutHeader');
		$footer = $this->_getParam('layoutFooter');
		
		//验证参数
		if (empty($header) && empty($footer)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('please_input_the_info'));
			exit(json_encode($ret));
		}
		if (empty($tpl)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('please_select_a_email_temp'));
			exit(json_encode($ret));
		}
		
		$retVal = LpService::modifyEmailOption($this->auth->usr['code'], $tpl, $header, $footer);
		if ($retVal) {
			$this->auth->usr['lp_propose_temp'] = $tpl;
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
		exit(json_encode($ret));
	}
	
	
	/**
	 * 查找当前法人的邮件头尾设置
	 */
	public function findemailoptionAction() {
		$rows = LpService::findEmailOption($this->auth->usr['code']);
		$options = array();
		if (!empty($rows) && count($rows) > 0) {
			$temp1 = array();
			$temp2 = array(); 
			foreach ($rows as $option) {
				if ($option['option_code'] == 'Ls8') {
					$temp1['header'] = $option['description'];
				} else if ($option['option_code'] == 'Ls9') {
					$temp2['header'] = $option['description'];
				} else if ($option['option_code'] == 'Ls10') {
					$temp1['footer'] = $option['description'];
				} else if ($option['option_code'] == 'Ls11') {
					$temp2['footer'] = $option['description'];
				}
			}
			$options['temp1'] = $temp1;
			$options['temp2'] = $temp2;
		}
		exit(json_encode($options));
	}
	
	
	/**
	 * 对法人设定
	 */
	public function modifylpsettingAction() {
		$ls5OnOrOff = $this->_getParam('ls5OnOrOff');
		$ls5Rete = $this->_getParam('ls5Rete');
		$ls6OnOrOff = $this->_getParam('ls6OnOrOff');
		$ls6Rete = $this->_getParam('ls6Rete');
		$ls7OnOrOff = $this->_getParam('ls7OnOrOff');
		$ls7Rete = $this->_getParam('ls7Rete');
		
		$code = $this->auth->usr['code'];
		$settings = array();
		$settings['Ls5'] = array('setting_code'=>'Ls5', 'lp_code'=>$code, 'on_or_off'=>$ls5OnOrOff, 'rete'=>$ls5Rete);
		$settings['Ls6'] = array('setting_code'=>'Ls6', 'lp_code'=>$code, 'on_or_off'=>$ls6OnOrOff, 'rete'=>$ls6Rete);
		$settings['Ls7'] = array('setting_code'=>'Ls7', 'lp_code'=>$code, 'on_or_off'=>$ls7OnOrOff, 'rete'=>$ls7Rete);
		$retVal = LpService::modifyLpSetting($code, $settings);
		
		if ($retVal !== FALSE) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
			exit(json_encode($ret));
		} else {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
			exit(json_encode($ret));
		}
	}
	
	
	/**
     * 退会
     */
	public function unsubscribeAction() {
    	//给usr赋值
    	$usr = array();
    	$usr['enabled'] = 'N';
		
		//保存用户修改的注册信息
		$result = UsrService::updateUsrByCode($this->auth->usr['code'], $usr);
		if ($result) {
			Zend_Session::destroy();
			$ret = array('err' => 0, 'msg' => $this->tr->translate('UNSUBSCRIBE_SUCC'));
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('UNSUBSCRIBE_ERR'));
		exit(json_encode($ret));
    }
    
    
} //End: class Lp_LpController