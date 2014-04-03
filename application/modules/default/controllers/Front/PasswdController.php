<?php
include_once 'Front/PasswdService.php';
include_once 'UsrService.php';
include_once 'BaseController.php';


class Front_PasswdController extends BaseController
{
    /**
     * 找回密码主页面
     */
    public function passwdAction() {
		$this->layout->headTitle($this->tr->translate('get_back_passwd'));
		$crumb = array('uri' => '/front_passwd', 'name' => $this->tr->translate('get_back_passwd'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
    /**
     * 为找回密码，发送邮件
     */
    public function emailpasswdAction() {
//    	set_time_limit(0);
//		ini_set('memory_limit', '-1');
		//获取参数：用户邮箱
		$toEmail = trim($this->_getParam('email'));
		//验证邮箱是否有效，若无效，则提示；若有效，则发送邮件
		$usr = UsrService::getUsrByEmail($toEmail, array('1'));
		if (empty($usr)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('email_not_exists'));
			exit(json_encode($ret));
		}
		//动态生成新密码字母与数字的组合
		$passwd = PasswdService::genPasswd();
		
		//连接Admin邮箱，并将新密码发送到用户邮箱
		$fromEmail = 'admin@jinzai-anken.com';
    	$fromName = 'admin';
    	$toName = $toEmail;
    	$bcc = 'linice01@163.com';
    	$subject = 'www.jinzai-anken.com' . $this->tr->translate('reset_passwd');
    	$body = 'Your new password in site www.jinzai-anken.com is: ' . $passwd;
    	
    	$mail = new Zend_Mail('utf-8');
    	$mail->setFrom($fromEmail, $fromName);
		$mail->addTo($toEmail, $toName);
		$mail->addBcc($bcc);
		$mail->setSubject($subject);
		$mail->setBodyHtml($body);
		$result = $mail->send();
		//若邮件发送成功，则把随机生成的密码写入数据库
		if ($result) {
			UsrService::updateUsrByEmail($toEmail, array('passwd' => md5($passwd)));
			$ret = array('err' => 0, 'msg' => $this->tr->translate('EMAIL_SEND_SUCCESS'));
			exit(json_encode($ret));
		} else {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_SEND_FAILED'));
			exit(json_encode($ret));
		}
    }
    
    
    
    
} //End: class Front_PasswdController