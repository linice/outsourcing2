<?php
include_once 'UtilService.php';
include_once 'BaseController.php';


class Test_MailController extends BaseController
{
    /**
     * Zend Mail：通过网络邮件服务器（如：smtp.163.com）发送邮件
     */
    public function sendemailAction() {
//    	set_time_limit(0);
//		ini_set('memory_limit', '-1');
		//连接邮箱，并发送邮件
		$config = array('auth' => 'login',
			'username' => 'linice_2006',
			'password' => 'SunShine10'
//			'ssl' => 'ssl'
		);
		$transport = new Zend_Mail_Transport_Smtp('smtp.163.com', $config);
		$mail = new Zend_Mail('UTF-8');
		$mail->setBodyHtml('Test mail.');
		$mail->setFrom('linice_2006@163.com', null);
		$mail->addTo('linice01@163.com', null);
		$mail->setSubject('Test mail');
//		$file = file_get_contents('D:/CurrentWork/js.js');
//		$mail->createAttachment($file, 'application/octet-stream', 
//			Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64,
//			'by createAttachment js.js');
		$result = $mail->send($transport);
		//若邮件发送成功，则把随机生成的密码写入数据库
		if ($result) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('EMAIL_SEND_SUCCESS'));
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_SEND_FAILED'));
		exit(json_encode($ret));
    }
    
    
    /**
     * postfix: send mail
     */
    public function sendmailAction() {
//    	$to      = '51220269@qq.com';
    	$to      = 'linice01@163.com';
		$subject = 'the subject test';
		$message = '<div style="color: red;">Red Hello World!</div>';
//		$headers = 'From: los@os2.com.dev' . "\r\n" .
//		'Reply-To:  los@os2.com.dev' . "\r\n" .
//		'X-Mailer: PHP/' . phpversion();
		$headers = 'From: los2@os2.com.dev' . "\r\n"; //会在收件人里显示成：los2 <los2@os2.com.dev>
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$result = mail($to, $subject, $message, $headers);
//		$result = mail($to, $subject, $message);
		var_dump($result);
		
		exit;
    }
    
    
    /**
     * postfix: send mail by Zend_Mail
     */
    public function zendmailAction() {
    	$fromEmail = 'admin@jinzai-anken.com';
    	$fromName = 'admin';
    	$toEmail = 'linice01@163.com';
    	$toName = 'linice01';
    	$ccEmail = '51220269@qq.com';
    	$ccName = 'qq51220269';
    	$bcc = 'linice_2006@163.com';
    	$subject = 'zend mail';
    	$body = '<div style="color: red;">Red Hello World!</div>';
    	
    	$mail = new Zend_Mail('utf-8');
    	$mail->setFrom($fromEmail, $fromName);
		$mail->addTo($toEmail, $toName);
		$mail->addCc($ccEmail, $ccName);
		$mail->addBcc($bcc);
		$mail->setSubject($subject);
		$mail->setBodyHtml($body); //HTML格式，被解析成红色的：Red Hello World!
//		$mail->setBodyText($body); //TEXT格式，保持原样：<div style="color: red;">Red Hello World!</div>
    	$fileContent = file_get_contents('/tmp/att/index.html');
//		$at = new Zend_Mime_Part($fileContent);
//		$at->type        = 'application/octet-stream';
//		$at->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
//		$at->encoding    = Zend_Mime::ENCODING_BASE64;
//		$at->filename    = 'index.html';
//		$mail->addAttachment($at);
		$mail->createAttachment($fileContent, 'application/octet-stream', 
			Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64,
			'by createAttachment index.html');
		$result = $mail->send();
		var_dump($result);
		exit;
    }
    
    
    
    
    
	/**
     * 临时测试 
     */
    public function testAction() {
    	echo phpversion();
    	
    	exit;
    }
	
	
	
} //End: class Test_MailController