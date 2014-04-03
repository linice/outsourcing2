<?php
include_once 'BaseController.php';
include_once 'UsrService.php';
include_once 'ResumeService.php';
include_once 'BaseService2.php';
include_once 'Msg/MsgService.php';
include_once 'BaseService.php';
include_once 'UtilService.php';


class Usr_UsrController extends BaseController
{
    /**
     * 普通用户中心主页面
     */
    public function usrAction() {
		//导航
    	$this->layout->headTitle($this->tr->translate('usr_center'));
		$crumb = array('uri' => '/usr_usr/usr', 'name' => $this->tr->translate('usr_center'));
		$this->view->layout()->crumbs = array($crumb);
		
		//变量
		$mbrCode = $this->auth->usr['code'];
		
		//查询简历
		$fields = array('id', 'code', 'talent_code', 'fullname', 'able_work_date_choice', 'able_work_date_choice', 
			'is_apply_prj', 'salary_exp', 'salary_min', 'exp_workplace', 'exp_prj_form', 'is_open', 'ok_base', 'ok_biz', 'ok_prj', 'ok_other');
		$resume = array();
		$resumes = ResumeService::getResumesByTalentCode($mbrCode, $fields);
		if (!empty($resumes)) {
			$resume = $resumes[0];
		}
		
		//统计未读消息数
		$msgsCnt = MsgService::cntUnreadMsgsByRecverCode($mbrCode);
		
		//输出到view
		$this->view->usr = $this->auth->usr;
		$this->view->resume = $resume;
		$this->view->msgsCnt = $msgsCnt;
    }
    
    
    /**
     * 修改登陆邮箱
     */
    public function chgemailAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('MODIFY') . $this->tr->translate('LOGIN_EMAIL'));
		$crumb = array('uri' => '/usr_usr/chgemail', 'name' => $this->tr->translate('MODIFY') . $this->tr->translate('LOGIN_EMAIL'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
    /**
     * 处理修改登陆邮箱
     */
    public function handlechgemailAction() {
    	//获取参数
    	$email = trim($this->_getParam('email'));
    	$email2 = trim($this->_getParam('email2'));
    	$oldEmail = $this->auth->usr['email'];
    	$now = date('Y-m-d H:i:s');
    	
	    //验证注册信息：邮箱，密码
		if (empty($email)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER_NEW_EMAIL'));
			exit(json_encode($ret));
		} 
		if (empty($email2)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_CONFIRM_NEW_EMAIL'));
			exit(json_encode($ret));
		}
		if (!UtilService::isEmail($email)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER_REAL_EMAIL'));
			exit(json_encode($ret));
		}
		if ($email != $email2) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAILS_ENTERED_TWICE_ARE_NOT_THE_SAME'));
			exit(json_encode($ret));
		}
		//邮箱是否已经存在
		$usr = BaseService::getOneByCond('usr', 1, "email = '$email'");
		if ($usr) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_ALREADY_EXIST'));
			exit(json_encode($ret));
		}
//		var_dump($usr);
//		exit;
		
		$db = Zend_Registry::get('DB');
		$db->beginTransaction();
		//修改用户的最后更新时间，用于验证和真正修改用户邮箱
		$result = BaseService::updateByCond('usr', array('update_time' => $now), "email = '$oldEmail'");

    	//发送邮件：包含参数：旧邮箱，新邮箱，旧邮箱md5值
    	//其中，旧邮箱md5值，用于验证修改邮箱
		$fromEmail = 'admin@jinzai-anken.com';
    	$fromName = 'admin';
    	$toEmail = $email;
    	$toName = $this->auth->usr['fullname'];
    	$subject = $this->tr->translate('MODIFY') . $this->tr->translate('EMAIL');
    	$md5Email = md5($this->auth->usr['email']);
    	$url = "http://www.jinzai-anken.com/usr_usr/validateemail/?email={$this->auth->usr['email']}&newEmail=$email&passwd=$md5Email";
    	$hrefName = $this->tr->translate('CLICK_HERE_TO_VALIDATE');
    	$body = "<a href='$url' target='_blank'>$hrefName</a>" 
    		. $this->tr->translate('OR_COPY_THIS_URL') . ': ' . $url;
    	$mail = new Zend_Mail('utf-8');
    	$mail->setFrom($fromEmail, $fromName);
		$mail->addTo($toEmail, $toName);
		$mail->setSubject($subject);
		$mail->setBodyHtml($body);
		
    	try {
			$result = $mail->send();
		} catch (Exception $e) {
			$db->rollback();
			$log = array('err' => 1, 'msg' => "{$e->getMessage()}");
			exit(json_encode($log));
		}
    	$db->commit();
    	//返回
    	if ($result) {
	    	$ret = array('err' => 0, 'msg' => $this->tr->translate('MOD_EMAIL_ADDR_SUCC__PLEASE_ACTIVATE_NEW_EMAIL_ADDR_FROM_EMAIL'));
			exit(json_encode($ret));
    	}
    	$ret = array('err' => 1, 'msg' => $this->tr->translate('MOD_EMAIL_ADDR_ERR__PLEASE_TRY_AGAIN'));
		exit(json_encode($ret));
    }
    
    
    /**
     * 验证并真正的修改用户登陆邮箱
     */
    public function validateemailAction() {
    	//获取参数
    	$email = trim($this->_getParam('email'));
    	$newEmail = trim($this->_getParam('newEmail'));
    	$passwd = trim($this->_getParam('passwd'));
    	
    	//验证参数
    	//判断密码是否为：旧的email的md5值
    	if ($passwd == md5($email)) {
    		//判断旧的邮箱存在，且最后更新时间在24小时之内
			$cond = "email = '{$email}' and enabled = 'Y' and update_time > DATE_SUB(now(),INTERVAL 1 day)";
			$usr = BaseService::getRowByCond('usr', $cond, array('*'));
			if (!empty($usr)) {
				//修改表usr及resume的邮箱
				$result = UsrService::changeEmail($email, $newEmail);
				if ($result) {
					//用户登陆
					$usr['email'] = $newEmail;
					$usr['email_consignee'] = $newEmail;
					$usr['update_time'] = date('Y-m-d H:i:s');
					$this->auth->usr = $usr;
					$this->view->tip = $this->tr->translate('VALIDATE_SUCC');
					return;
				}
			} else {
				$cond = "email = '{$email}' and enabled = 'Y'";
				$usr = BaseService::getRowByCond('usr', $cond, array(1));
				if (!empty($usr)) { //邮箱存在，表明过期
					$this->view->tip = $this->tr->translate('VALIDATE_HAVE_EXPIRED');
					return;
				}
			}
		}
		$this->view->tip = $this->tr->translate('VALIDATE_INFO_ERR');
    }
    
    
    /**
     * 登陆信息变更
     */
    public function baseinfoAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('login_info_change'));
		$crumb = array('uri' => '/usr_usr/logininfochange', 'name' => $this->tr->translate('login_info_change'));
		$this->view->layout()->crumbs = array($crumb);
		
		//view
		$this->view->usr = $this->auth->usr;
    }
    
    
    /**
     * 获取普通用户、法人或管理员列表
     */
    public function getusrsAction() {
    	//获取参数：分页, 角色, 检索条件：用户名、法人公司名或管理员姓名
		$page = trim($this->_getParam('page', 1));
		$pageSize = trim($this->_getParam('pagesize', 20));
		$roleCode = trim($this->_getParam('roleCode'));
		$fullname = trim($this->_getParam('fullname'));
		
		//如果是检索
		if (!empty($fullname)) {
			$usrsCnt = UsrService::getUsrsCntByRoleCodeAndLikeFullname($roleCode, $fullname);
	    	$usrs = UsrService::getUsrsByPageAndRoleCodeAndLikeFullname($page, $pageSize, $roleCode, $fullname, array('code as usr_code', 'email', 'fullname'));
		} else {
			$usrsCnt = UsrService::getUsrsCntByRoleCode($roleCode);
	    	$usrs = UsrService::getUsrsByPageAndRoleCode($page, $pageSize, $roleCode, array('code as usr_code', 'email', 'fullname', 'tel'));
		}
    	
    	$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $usrsCnt, 'Rows' => $usrs);
		exit(json_encode($ret));
    }
    

    /**
     * 修改注册信息
     */
    public function modifyreginfoAction() {
    	//获取参数：普通用户注册信息
		$passwd = trim($this->_getParam('passwd'));
		$passwd2 = trim($this->_getParam('passwd2', ''));
		$nickname = trim($this->_getParam('nickname'));
		$lastname_p = trim($this->_getParam('lastname_p'));
		$firstname_p = trim($this->_getParam('firstname_p'));
		$fullname = trim($this->_getParam('fullname'));
		$sex = trim($this->_getParam('sex'));
		$usrCode = $this->auth->usr['code'];
		
		//验证参数
		$labels = array();
		$msgs = array();
		if (empty($lastname_p)) {
			$labels[] = 'label_lastname_p';
			$msgs[] = $this->tr->translate('LASTNAME') . $this->tr->translate('PRONOUNCE') . $this->tr->translate('CAN_NOT_BE') . $this->tr->translate('EMPTY');
		}
		if (empty($firstname_p)) {
			$labels[] = 'label_firstname_p';
			$msgs[] = $this->tr->translate('FIRSTNAME') . $this->tr->translate('PRONOUNCE') . $this->tr->translate('CAN_NOT_BE') . $this->tr->translate('EMPTY');
		}
		if (empty($fullname)) {
			$labels[] = 'label_fullname';
			$msgs[] = $this->tr->translate('FULLNAME') . $this->tr->translate('CAN_NOT_BE') . $this->tr->translate('EMPTY');
		}
		if (empty($sex)) {
			$labels[] = 'label_sex';
			$msgs[] = $this->tr->translate('SEX') . $this->tr->translate('CAN_NOT_BE') . $this->tr->translate('EMPTY');
		}
		
		if (!empty($passwd)) {
			if ($passwd != $passwd2) {
				$labels[] = 'label_passwd';
				$msgs[] = $this->tr->translate('passwords_are_not_equal');
			} else if (!UtilService::isPasswd($passwd)) {
				$labels[] = 'label_passwd';
				$msgs[] = $this->tr->translate('PASSWORD') . $this->tr->translate('SHOULD_BE') . $this->tr->translate('REG_PASSWD_ILLUSTRATION_1');
			}
		}
		if (!empty($labels)) {
			$ret = array('err' => 2, 'msg' => json_encode($msgs), 'labels' => json_encode($labels));
			exit(json_encode($ret));
		}
		
    	//如果邮箱改变，查验邮箱是否被注册
//    	if ($email != $this->auth->usr['email']) {
//	    	$dbUsr = UsrService::getUsrByEmail($email, array('1'));
//	    	if ($dbUsr !== FALSE) { //返回值不为false，说明此邮箱已被注册
//	    		$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_ALREADY_EXIST'));
//	    		exit(json_encode($ret));
//	    	}
//    	}
    	
    	//给usr赋值
    	$usr = array();
//    	$usr['email'] = $email;
//    	$usr['email_consignee'] = $usr['email'];
		$usr['nickname'] = $nickname;
		$usr['fullname_p'] = $lastname_p . ' ' . $firstname_p;
		$usr['fullname'] = $fullname;
		$usr['sex'] = $sex;
		if (!empty($passwd)) {
			$usr['passwd'] = md5($passwd);
		}
		
		//给resume赋值
		$resume = array();
//		$resume['email'] = $email;
		$resume['fullname_p'] = $lastname_p . ' ' . $firstname_p;
		$resume['fullname'] = $fullname;
		$resume['sex'] = $sex;
		
		//保存用户修改的注册信息
		$db = Zend_Registry::get('DB');
		$db->beginTransaction();
//		$result = UsrService::updateUsrByCode($this->auth->usr['code'], $usr);
		$cond = "code = '$usrCode'";
		$result = BaseService::updateByCond('usr', $usr, $cond);
//		$result2 = ResumeService::updateResumeByTalentCode($this->auth->usr['code'], $resume);
		$cond = "talent_code = '$usrCode'";
		$result2 = BaseService::updateByCond('resume', $resume, $cond);
		if ($result && $result2) {
			$db->commit();
			//更新session里的usr信息
			$this->auth->usr = UsrService::getUsrByEmail($this->auth->usr['email']);
			$ret = array('err' => 0, 'msg' => $this->tr->translate('UPDATE_REG_INFO_SUCCESS'));
			exit(json_encode($ret));
		} else {
			$db->rollback();
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('UPDATE_REG_INFO_ERR'));
		exit(json_encode($ret));
    }
    
    
    /**
     * 邮件通知设定
     */
	public function modifyisreceivenewsAction() {
    	//获取参数：是否接收信息
    	$isReceiveNews = trim($this->_getParam('isReceiveNews'));
		
    	//验证注册信息：是否接收信息
    	if (empty($isReceiveNews)) {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_SELECT_IS_RECEIVE_NEWS'));
			exit(json_encode($ret));
    	}
		
    	//给usr赋值
    	$usr = array();
    	$usr['is_receive_news'] = $isReceiveNews;
		
		//保存用户修改的注册信息
		$result = UsrService::updateUsrByCode($this->auth->usr['code'], $usr);
		if ($result === TRUE) {
			//更新session里的usr信息
			$this->auth->usr = UsrService::getUsrByCode($this->auth->usr['code']);
			$ret = array('err' => 0, 'msg' => $this->tr->translate('EMAIL_NOTIFY_SET_SUCC'));
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_NOTIFY_SET_ERR'));
		exit(json_encode($ret));
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
		if ($result === TRUE) {
			Zend_Session::destroy();
			$ret = array('err' => 0, 'msg' => $this->tr->translate('UNSUBSCRIBE_SUCC'));
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('UNSUBSCRIBE_ERR'));
		exit(json_encode($ret));
    }
    
    
    
    
    
    
    
} //End: class Usr_UsrController