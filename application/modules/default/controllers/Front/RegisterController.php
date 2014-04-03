<?php
include_once 'BaseController.php';
include_once 'UsrService.php';
include_once 'UsrSkillService.php';
include_once 'EtcService.php';
include_once 'ActiveEtcService.php';
include_once 'UtilService.php';
include_once 'BaseService2.php';
include_once 'ResumeService.php';
include_once 'BaseService.php';


class Front_RegisterController extends BaseController
{
	//用户注册信息
	private $usrCode = NULL;
	private $email = NULL;
	private $passwd = null;
	private $passwd2 = null;
	private $nickname = null;
	private $lastname_p = null;
	private $firstname_p = null;
	private $lastname = null;
	private $firstname = null;
	private $tel = null;
	private $birthday = null;
	private $sex = null;
	private $country = null;
	private $province = null;
	private $oss = array();
	private $ossYears = array();
	private $bizs = array();
	private $bizsYears = array();
	private $fws = array();
	private $fwsYears = array();
	private $isReceiveNews = null;
	private $referee = null;

	
    /**
     * 用户注册页面
     */
    public function registerAction()
    {
    	//导航
		$this->layout->headTitle($this->tr->translate('user_register'));
		$crumb = array('uri' => '/front_register/register', 'name' => $this->tr->translate('user_register'));
		$this->view->crumbs = array($crumb);
		
		//获取推荐人参数：推荐用户email及passwd
		$refereeEmail = trim($this->_getParam('email'));
		$refereePasswd = trim($this->_getParam('passwd'));
		
		//验证推荐人参数：推荐用户email及passwd
		if (!empty($refereeEmail)) {
			$referee = UsrService::getUsrByEmailAndPasswd($refereeEmail, $refereePasswd);
			if (empty($referee)) {
				$this->_redirect('/?pwd=10');
			}
		}
		
		//查询杂项，Web app变量
		$types = array('COUNTRY', 'PROVINCE', 'COUNTY', 'OS', 'FRAMEWORK', 'BIZ', 'EXPERIENCE_YEARS');
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
				case 'OS': //OS
					$oss[$etc['code']] = $this->tr->translate($etc['code']);
					break;
				case 'BIZ': //行业
					$bizs[$etc['code']] = $this->tr->translate($etc['code']);
					break;
				case 'FRAMEWORK': //程序设计语言
					$fws[$etc['code']] = $this->tr->translate($etc['code']);
					break;
				case 'EXPERIENCE_YEARS': //经验年数
					$experienceYears[$etc['code']] = $etc['value'];
					break;
			}
		}
		
		//获取确认页面返回的数据
		$jsRegInfo = $this->_getParam('registerInfo', json_encode(new stdClass()));
		
		//view
		$this->view->countries = $countries;
		$this->view->provinces = $provinces;
		$this->view->counties = $counties;
		$this->view->oss = $oss;
		$this->view->bizs = $bizs;
		$this->view->fws = $fws;
		$this->view->experienceYears = $experienceYears;
		$this->view->refereeEmail = $refereeEmail;
		$this->view->refereePasswd = $refereePasswd;
		$this->view->jsRegInfo = $jsRegInfo;
    }
    

    /**
     * 获取用户注册信息 
     */
    private function getUsrRegisterInfo() {
		$this->email = trim($this->_getParam('email'));
		$this->passwd = trim($this->_getParam('passwd'));
		$this->passwd2 = trim($this->_getParam('passwd2'));
		$this->nickname = trim($this->_getParam('nickname'));
		$this->lastname_p = trim($this->_getParam('lastname_p'));
		$this->firstname_p = trim($this->_getParam('firstname_p'));
		$this->lastname = trim($this->_getParam('lastname'));
		$this->firstname = trim($this->_getParam('firstname'));
		$this->tel = trim($this->_getParam('tel'));
		$this->birthday = trim($this->_getParam('birthday'));
		$this->sex = trim($this->_getParam('sex'));
		$this->country = trim($this->_getParam('country'));
		$this->province = trim($this->_getParam('province'));
		$this->oss = $this->_getParam('oss');
		$this->ossYears = $this->_getParam('ossYears');
		$this->bizs = $this->_getParam('bizs');
		$this->bizsYears = $this->_getParam('bizsYears');
		$this->fws = $this->_getParam('fws');
		$this->fwsYears = $this->_getParam('fwsYears');
		$this->isReceiveNews = trim($this->_getParam('isReceiveNews', 'N'));
		$this->referee = trim($this->_getParam('referee'));
//		var_dump($this->oss);
//		exit;
    }
    
    
    /**
     * 验证用户注册信息，若信息合法，则转向注册确认页面
     */
    public function registerverifyAction() {
    	//获取用户注册信息 
    	$this->getUsrRegisterInfo();
    	
	    //验证注册信息：邮箱，密码
		if (empty($this->email)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('email_can_not_be_empty'));
			exit(json_encode($ret));
		} else if (!UtilService::isEmail($this->email)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('email_is_not_real'));
			exit(json_encode($ret));
		}
		if (empty($this->passwd)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('password_can_not_be_empty'));
			exit(json_encode($ret));
		} else if (!UtilService::isPasswd($this->passwd)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PASSWORD') . $this->tr->translate('SHOULD_BE') . $this->tr->translate('REG_PASSWD_ILLUSTRATION_1'));
			exit(json_encode($ret));
		}
		if ($this->passwd != $this->passwd2) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('passwords_are_not_equal'));
			exit(json_encode($ret));
		}
		
    	//验证职种和技术及其年份
    	//删除空的行
//    	var_dump($this->oss);
//    	var_dump($this->bizs);
//    	var_dump($this->fws);
//    	exit;
		$ossCnt = count($this->oss);
    	for ($i = 0; $i < $ossCnt; $i++) {
    		if (empty($this->oss[$i])) {
    			unset($this->oss[$i]);
    			unset($this->ossYears[$i]);
    		} else if (empty($this->ossYears[$i])) {
	    		$ret = array('err' => 1, 'msg' => $this->tr->translate('OSS_AND_YEARS_DO_NOT_MATCH'));
    			exit(json_encode($ret));
	    	}
    	}
    	$bizsCnt = count($this->bizs);
    	for ($i = 0; $i < $bizsCnt; $i++) {
    		if (empty($this->bizs[$i])) {
    			unset($this->bizs[$i]);
    			unset($this->bizsYears[$i]);
    		} else if (empty($this->bizsYears[$i])) {
    			$ret = array('err' => 1, 'msg' => $this->tr->translate('BIZS_AND_YEARS_DO_NOT_MATCH'));
    			exit(json_encode($ret));
    		}
    	}
    	$fwsCnt = count($this->fws);
    	for ($i = 0; $i < $fwsCnt; $i++) {
    		if (empty($this->fws[$i])) {
    			unset($this->fws[$i]);
    			unset($this->fwsYears[$i]);
    		} else if (empty($this->fwsYears[$i])) {
    			$ret = array('err' => 1, 'msg' => $this->tr->translate('FWS_AND_YEARS_DO_NOT_MATCH'));
    			exit(json_encode($ret));
    		}
    	}
    	//检查是否有重复的os，biz，fw；
//    	var_dump($this->oss);
//    	var_dump($this->bizs);
//    	var_dump($this->fws);
//    	exit;
		$oss = $this->oss;
		$bizs = $this->bizs;
		$fws = $this->fws;
    	sort($oss);
		sort($bizs);
		sort($fws);
		$oss_u = array_unique($this->oss);
		$bizs_u = array_unique($this->bizs);
		$fws_u = array_unique($this->fws);
    	sort($oss_u);
    	sort($bizs_u);
    	sort($fws_u);
    	if ($oss != $oss_u) {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('CAN_NOT_REPEATEDLY_SELECT_THE_SAME_OS'));
    		exit(json_encode($ret));
    	}
    	if ($bizs != $bizs_u) {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('CAN_NOT_REPEATEDLY_SELECT_THE_SAME_INDUSTRY'));
    		exit(json_encode($ret));
    	}
    	if ($fws != $fws_u) {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('CAN_NOT_REPEATEDLY_SELECT_THE_SAME_LANG'));
    		exit(json_encode($ret));
    	}
		
    	//将OS, BIZ, FW的下标重新从0排序，用于后续保存数据时，技能与年份的对应关系
    	$oss = array();
    	$bizs = array();
    	$fws = array();
    	$ossYears = array();
    	$bizsYears = array();
    	$fwsYears = array();
    	foreach ($this->oss as $v) {
    		$oss[] = $v;
    	}
    	foreach ($this->bizs as $v) {
    		$bizs[] = $v;
    	}
    	foreach ($this->fws as $v) {
    		$fws[] = $v;
    	}
    	foreach ($this->ossYears as $v) {
    		$ossYears[] = $v;
    	}
    	foreach ($this->bizsYears as $v) {
    		$bizsYears[] = $v;
    	}
    	foreach ($this->fwsYears as $v) {
    		$fwsYears[] = $v;
    	}
    	$this->oss = $oss;
    	$this->bizs = $bizs;
    	$this->fws = $fws;
    	$this->ossYears = $ossYears;
    	$this->bizsYears = $bizsYears;
    	$this->fwsYears = $fwsYears;
		
		//查验邮箱是否被注册
    	$dbUsr = UsrService::getUsrByEmail($this->email, array('1'));
    	if ($dbUsr !== FALSE) { //返回值不为false，说明此邮箱已被注册
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_ALREADY_EXIST'));
    		exit(json_encode($ret));
    	}
    	
    	//用户信息通过验证，返回真，并返回用户注册信息
    	$registerInfo = array('email' => $this->email, 'passwd' => $this->passwd, 'passwd2' => $this->passwd2, 
    		'nickname' => $this->nickname, 'lastname_p' => $this->lastname_p, 'firstname_p' => $this->firstname_p, 
    		'lastname' => $this->lastname, 'firstname' => $this->firstname, 'tel' => $this->tel, 'birthday' => $this->birthday, 
    		'sex' => $this->sex, 'country' => $this->country, 'province' => $this->province, 
    		'oss' => $this->oss, 'ossYears' => $this->ossYears, 
    		'bizs' => $this->bizs, 'bizsYears' => $this->bizsYears, 
    		'fws' => $this->fws, 'fwsYears' => $this->fwsYears, 
    		'isReceiveNews' => $this->isReceiveNews, 'referee' => $this->referee);
		$ret = array('err' => 0, 'msg' => $this->tr->translate('register_information_is_verified_success'), 
			'jsRegInfo' => json_encode($registerInfo));
    	exit(json_encode($ret));
    }
    
    
    /**
     * 用户注册确认页面 
     */
    public function registerconfirmAction() {
    	//设置页面标题和导航
    	$this->layout->headTitle($this->tr->translate('user_register_confirm'));
    	$crumbs = array();
		$crumbs[] = array('uri' => '/front_register/register', 'name' => $this->tr->translate('user_register'));
		$crumbs[] = array('uri' => '/front_register/registerconfirm', 'name' => $this->tr->translate('user_register_confirm'));
		$this->view->layout()->crumbs = $crumbs;
		
    	//获取用户注册信息 
    	$jsRegisterInfo = $this->_getParam('registerInfo');
    	$registerInfo = json_decode($jsRegisterInfo, true);
    	
    	//view
    	$this->view->registerInfo = $registerInfo;
    }
    
    
    /**
     * 受理用户注册 
     */
    public function handleregisterAction() {
    	//获取用户注册信息 
    	$jsRegisterInfo = $this->_getParam('registerInfo');
    	$regInfo = json_decode($jsRegisterInfo, TRUE);
//    	var_dump($regInfo);
//    	exit;
    	
		//用户的基本信息
		$now = date('Y-m-d H:i:s');
    	$usr = array();
    	$usr['role_code'] = 'MEMBER';
    	$usr['email'] = $regInfo['email'];
    	$usr['email_consignee'] = $usr['email'];
		$usr['passwd'] = md5($regInfo['passwd']);
		$usr['nickname'] = $regInfo['nickname'];
		$usr['fullname_p'] = $regInfo['lastname_p'] . ' ' . $regInfo['firstname_p'];
		$usr['fullname'] = $regInfo['lastname'] . $regInfo['firstname'];
		$usr['tel'] = $regInfo['tel'];
		$usr['birthday'] = $regInfo['birthday'];
		$usr['sex'] = $regInfo['sex'];
		$usr['country'] = $regInfo['country'];
		$usr['province'] = $regInfo['province'];
		$usr['is_receive_news'] = $regInfo['isReceiveNews'];
		$usr['referee'] = $regInfo['referee'];
		$usr['enabled'] = 'Y';
		$usr['create_time'] = $now;
    	
    	//保存用户信息（基本信息+技能），并更新用户唯一编号
    	$this->db->beginTransaction();
    	//生成用户唯一编号
    	$this->usrCode = ActiveEtcService::genCode('USR_CODE');
    	$usr['code'] = $this->usrCode;
    	//生成简历编码
    	$rsmCode = ActiveEtcService::genCode('RESUME_CODE');
    	//简历基本信息
    	$rsm = array('code' => $rsmCode, 'talent_code' => $usr['code'], 
    		'fullname' => $usr['fullname'], 'fullname_p' => $usr['fullname_p'],
    		'actual_residence_cntry' => $usr['country'], 'actual_residence_province' => $usr['province'], 
    		'email' => $usr['email'], 'tel' => $usr['tel'], 'birthday' => $usr['birthday'], 'sex' => $usr['sex'],
    		'create_time' => $now, 
    	);
    	
    	//用户的职种和技术及其年份
    	$rsmBizs = array();
    	for ($i = 0; $i < count($regInfo['oss']); $i++) {
    		$rsmBizs[] = array('resume_code' => $rsmCode, 'type' => 'OS', 
    			'biz' => $regInfo['oss'][$i], 'level' => 'B', 'age' => $regInfo['ossYears'][$i]);
    	}
    	for ($i = 0; $i < count($regInfo['bizs']); $i++) {
    		$rsmBizs[] = array('resume_code' => $rsmCode, 'type' => 'BIZ', 
    			'biz' => $regInfo['bizs'][$i], 'level' => 'B', 'age' => $regInfo['bizsYears'][$i]);
    	}
    	for ($i = 0; $i < count($regInfo['fws']); $i++) {
    		$rsmBizs[] = array('resume_code' => $rsmCode, 'type' => 'FRAMEWORK', 
    			'biz' => $regInfo['fws'][$i], 'level' => 'B', 'age' => $regInfo['fwsYears'][$i]);
    	}
    	$result = UsrService::addUsr($usr);
    	$result2 = BaseService::addRow('resume', $rsm);
    	if (!empty($rsmBizs)) {
	    	$result3 = BaseService::addRows('resume_biz', $rsmBizs);
    	} else {
    		$result3 = true;
    	}
    	//保存用户基本信息成功
    	if ($result && $result2 && $result3) {
    		$this->db->commit();
    		//给管理员发消息
    		$content = $usr['code'] . ' | ' . $usr['email'] . ' | ' . $usr['fullname'] . ' | ' . $usr['nickname']  
    		. ' | ' . $usr['tel'] . ' | ' . $usr['birthday'] . ' | ' . $this->tr->translate($usr['sex']) . ' | ' 
    		. $this->tr->translate($usr['country']) . ' | ' . $this->tr->translate($usr['province'])
    		. ' | ' . $usr['create_time'];
    		$msg = array('recver_code' => 'Mgr12001', 'title' => $this->tr->translate('NEW_MEMBER_REG'), 
    			'content' => $content, 'recv_time' => $now, 'sender_code' => $usr['code']
    		);
    		BaseService::addRow('msg', $msg);
    		//给管理员发邮件
    		$fromEmail = $usr['email'];
	    	$fromName = $usr['fullname'];
	    	$toEmail = 'linice01@163.com';
	    	$toName = 'linice01';
	    	$subject = $this->tr->translate('NEW_MEMBER_REG');
	    	$body = $content;
	    	$mail = new Zend_Mail('utf-8');
	    	$mail->setFrom($fromEmail, $fromName);
			$mail->addTo($toEmail, $toName);
			$mail->setSubject($subject);
			$mail->setBodyHtml($body);
			$result = $mail->send();
			//发注册用户发激活邮件
			//los2 comment begin: 先屏蔽 
//			$fromEmail = 'admin@jinzai-anken.com';
//	    	$fromName = 'admin';
//	    	$toEmail = $usr['email'];
//	    	$toName = $usr['fullname'];
//	    	$subject = $this->tr->translate('NEW_MEMBER_REG');
//	    	$md5Email = md5($usr['email']);
//	    	$url = "http://www.jinzai-anken.com/login/validateusr/?email={$usr['email']}&passwd=$md5Email";
//	    	$hrefName = $this->tr->translate('CLICK_HERE_TO_VALIDATE');
//	    	$body = "<a href='$url' target='_blank'>$hrefName</a>" 
//	    		. $this->tr->translate('OR_COPY_THIS_URL') . ': ' . $url;
//	    	$mail = new Zend_Mail('utf-8');
//	    	$mail->setFrom($fromEmail, $fromName);
//			$mail->addTo($toEmail, $toName);
//			$mail->setSubject($subject);
//			$mail->setBodyHtml($body);
//			$result = $mail->send();
			//los2 comment end: 先屏蔽 
    		//转向成功通知页面
			$title = $this->tr->translate('user_register_success');
			$opUrl = '/front_register/register';
			$opName = $this->tr->translate('user_register');
			$opResultName = $this->tr->translate('user_register_success');
			$opResultImgUrl = '/img/default/front/<?=$auth->locale?>/register_successful_t.gif';
			$this->redirectSucc($title, $opUrl, $opName, $opResultName, $opResultImgUrl);
    		exit;
    	}
    	//保存用户信息失败
   		$this->db->rollback();
   		//转向失败通知页面
		$title = $this->tr->translate('user_register_error');
		$opUrl = '/front_register/register';
		$opName = $this->tr->translate('user_register');
		$opResultName = $this->tr->translate('user_register_error');
		$opResultImgUrl = '';
		$this->redirectErr($title, $opUrl, $opName, $opResultName, $opResultImgUrl);
		exit;
    }
    
} //End: class IndexController