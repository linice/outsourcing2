<?php
include_once 'Msg/MsgService.php';
include_once 'Msg/MsgSendService.php';
include_once 'UsrService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


class Admin_MailController extends Zend_Controller_Action {
	private $layout = null;
	private $auth = null;
	private $tr = null;
	
	
    public function init()
    {
        /* Initialize action controller here */
//    	include_once 'Acl/permission.php';
		$this->layout = Zend_Registry::get('LAYOUT');
		$this->auth = new Zend_Session_Namespace('AUTH');
		$this->tr = Zend_Registry::get('TRANSLATE');
		
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
		}
    }
    
    
	/**
	 * 自动发送邮件：管理员发给法人、个人、或营业员的消息
	 */
    public function timeadminsendmailAction() {
    	//设置环境
    	ini_set('memory_limit', '-1');
		set_time_limit(0);
    	
		//查询已发送，但未发送邮件的消息。
		//条件：一周内（虽然每天都会自动运行，但为了避免因为哪天出错而导致发送不成功，所以选择一周的已发送消息）
		$cond = "is_sent = 'Y' and send_time > DATE_SUB(now(),INTERVAL 1 week) and is_send_mail = 'N'";
    	$msgSends = BaseService::getAllByCond('msg_send', array('id', 'title', 'content', 'recver'), $cond);
//    	var_dump($msgSends);
//    	exit;
    	
    	//发送邮件
    	$succSendMailCnt = 0;
    	foreach ($msgSends as $msgSend) {
    		$recvers = json_decode($msgSend['recver'], TRUE);
			$strRecvers = implode("','", $recvers);
			$strRecvers = "'" . $strRecvers . "'";
			$cond = " role_code in ($strRecvers) ";
			$usrs = UsrService::getUsrsByCond($cond, array('email_consignee', 'fullname'));
//			var_dump($usrs);
//    		exit;
//			$tos = array();
//			foreach ($usrs as $usr) {
//				$tos[] = array($usr['fullname'] => $usr['email_consignee']);
//			}
    		//发送邮件
	    	$fromEmail = 'admin@jinzai-anken.com';
	    	$fromName = '';
	    	$subject = $msgSend['title'];
	    	$body = $msgSend['content'];
			$mail = new Zend_Mail('utf-8');
	    	$mail->setFrom($fromEmail, $fromName);
			$mail->setSubject($subject);
			$mail->setBodyHtml($body);
//			$mail->addTo($tos);
    		foreach ($usrs as $usr) {
//				$tos[] = array($usr['fullname'] => $usr['email_consignee']);
				$mail->addTo($usr['email_consignee'], $usr['fullname']);
				$result = $mail->send();
			}
//			if ($result) {
				$result2 = BaseService::updateById('msg_send', array('is_send_mail' => 'Y'), $msgSend['id']);
				if ($result2) {
					$succSendMailCnt++;
				}
//			}
    	}
    	
		//返回：成功
		if ($succSendMailCnt == count($msgSends)) {
			$ret = array('err' => 0, 'msg' => 'Success');
			exit(json_encode($ret));
		}
		
		//返回：失败
		$ret = array('err' => 1, 'msg' => 'Error');
		exit(json_encode($ret));
    }
    
    
    
} //End: class Admin_MailController