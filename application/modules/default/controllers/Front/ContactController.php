<?php
include_once 'UtilService.php';
include_once 'BaseService2.php';
include_once 'BaseController.php';
include_once 'BaseService.php';


class Front_ContactController extends BaseController
{
    /**
     * 联系我们主页面
     */
    public function contactAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('contact_us'));
		$crumb = array('uri' => '/front_contact/contact', 'name' => $this->tr->translate('contact_us'));
		$this->view->layout()->crumbs = array($crumb);
		
		//获取参数：确认页面返回时，传回来的信息:是否同意隐私政策，姓名，电话，Email，内容
		$jsContactInfo = trim($this->_getParam('contactInfo'));
    	$contactInfo = json_decode($jsContactInfo, TRUE);
//		var_dump($contactInfo);

    	//view
    	$this->view->contactInfo = $contactInfo;
    }
    
    
    /**
     * 验证联系我们的参数
     */
 	public function contactverifyAction() {
		//获取参数: 是否同意隐私政策，姓名，电话，Email，内容
		$isAgree = trim($this->_getParam('isAgree'));
		$fullname = trim($this->_getParam('fullname'));
		$tel = trim($this->_getParam('tel'));
		$email = trim($this->_getParam('email'));
		$content = trim($this->_getParam('content'));
		
		//验证参数
		if (empty($isAgree)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('YOU_SHOULD_AGREE_WITH_PRIVACY_POLICY'));
			exit(json_encode($ret));
		}
		if (empty($fullname)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER_YOUR_NAME'));
			exit(json_encode($ret));
		}
		if (!empty($tel) && !UtilService::isTel($tel)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER_LEGAL_TEL'));
			exit(json_encode($ret));
		}
 		if (empty($email)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER_EMAIL_ADDR'));
			exit(json_encode($ret));
		} else if (!UtilService::isEmail($email)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER_LEGAL_EMAIL_ADDR'));
			exit(json_encode($ret));
		}
 		if (empty($content)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER_CONTENT'));
			exit(json_encode($ret));
		}
		
		//邮件信息合法，组合此信息，并输出（在js里将此信息转向确认邮件信息页面）
		$contactInfo = array('isAgree' => $isAgree, 'fullname' => $fullname, 'tel' => $tel,
			'email' => $email, 'content' => $content	
		);
		$ret = array('err' => 0, 'msg' => 'Success', 'jsContactInfo' => json_encode($contactInfo));
    	exit(json_encode($ret));
    }
    
    
    /**
     * 联系我们确认页面
     */
    public function contactconfirmAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('contact_info_confirm'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_contact', 'name' => $this->tr->translate('contact_us'));
		$crumbs[] = array('uri' => '/front_contact/contactconfirm', 'name' => $this->tr->translate('contact_info_confirm'));
		$this->view->layout()->crumbs = $crumbs;
		
		//获取参数: 是否同意隐私政策，姓名，电话，Email，内容
		$jsContactInfo = trim($this->_getParam('contactInfo'));
    	$contactInfo = json_decode($jsContactInfo, TRUE);
		
    	//view
    	$this->view->contactInfo = $contactInfo;
    }
    
    
    /**
     * 联系我们最后处理函数
     */
    public function contacthandleAction() {
		//获取参数: 是否同意隐私政策，姓名，电话，Email，内容
		$jsContactInfo = trim($this->_getParam('contactInfo'));
    	$contactInfo = json_decode($jsContactInfo, TRUE);
    	
    	//查询管理员的邮箱
    	$usrs = BaseService::getAllByCond('usr', array('email_consignee', 'fullname'), "role_code = 'ADMIN'");
    	
    	//发送邮件
    	$fromEmail = $contactInfo['email'];
    	$fromName = $contactInfo['fullname'];
    	$bcc = 'linice01@163.com';
    	$subject = 'Contact';
    	$body = $contactInfo['content'];
    	$mail = new Zend_Mail('utf-8');
    	$mail->setFrom($fromEmail, $fromName);
		foreach ($usrs as $usr) {
			$mail->addTo($usr['email_consignee'], $usr['fullname']);
		}
		$mail->addBcc($bcc);
		$mail->setSubject($subject);
		$mail->setBodyHtml($body);
		$result = $mail->send();
    	
		//转向邮件发送成功或失败页面
		if ($result) { //success
			$title = $this->tr->translate('EMAIL_SEND_SUCCESS');
			$opUrl = '/front_contact/contact';
			$opName = $this->tr->translate('contact_us');
			$opResultName = $this->tr->translate('EMAIL_SEND_SUCCESS');
			$opResultImgUrl = '/img/default/front/<?=$auth->locale?>/msg_send_success.gif';
			$this->redirectSucc($title, $opUrl, $opName, $opResultName, $opResultImgUrl);
		} else { //error
			$title = $this->tr->translate('EMAIL_SEND_FAILED');
			$opUrl = '/front_contact/contact';
			$opName = $this->tr->translate('contact_us');
			$opResultName = $this->tr->translate('EMAIL_SEND_FAILED');
			$opResultImgUrl = '';
			$this->redirectErr($title, $opUrl, $opName, $opResultName, $opResultImgUrl);
		}
    }
    
    
    
    
    
    
} //End: class Front_ContactController