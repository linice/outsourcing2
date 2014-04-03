<?php
include_once 'Msg/MsgService.php';
include_once 'Msg/MsgSendService.php';
include_once 'UsrService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


class Admin_MsgController extends Zend_Controller_Action {
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
	 * 发送的消息一览
	 */
    public function msglistAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('msg_list'));
		$crumb = array('uri' => '/msg_msgsend/msgsend', 'name' => $this->tr->translate('msg_list'));
		$this->view->layout()->crumbs = array($crumb);
		
    }
    
    
	/**
     * 获取消息列表
     */
    public function getmsgsendlistAction() {
    	//获取参数
    	$page = trim($this->_getParam('page', 1));
		$pageSize = trim($this->_getParam('pagesize', 20));
    	$parm = trim($this->_getParam('parm'));
    	$parm = json_decode($parm, true);
    	$isSent = $parm['isSent'];
    	$pg = array('start' => ($page - 1) * $pageSize, 'limit' => $pageSize);
    	
		//查询消息
		$usrCode = $this->auth->usr['code']; 
		$cond = " sender_code = '$usrCode' and is_sent = '$isSent' ";
		$statMsgSends = BaseService::getByPageWithCond('msg_send', array('*'), $cond, $pg);
		
		//return
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $statMsgSends['Total'], 'Rows' => $statMsgSends['Rows']);
		exit(json_encode($ret));
    }
    
    
	/**
	 * 发送消息页面
	 */
    public function sendmsgAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('send_msg'));
		$crumb = array('uri' => '/msg_msgsend/sendmsg', 'name' => $this->tr->translate('send_msg'));
		$this->view->layout()->crumbs = array($crumb);
		
		//获取参数：如果是修改草稿，可获取消息ID
		$msId = trim($this->_getParam('msId'));
		$jsMs = array();
		$ms = array();
		if (!empty($msId)) {
			$ms = MsgSendService::getMsgSendById($msId);
			$ms['recver'] = json_decode($ms['recver']);
		}
		$this->view->jsMs = json_encode($ms);
    }
    
    
	/**
	 * 处理存草稿消息或发送消息
	 */
    public function handlesendmsgAction() {
		//获取参数：收件人，标题，内容
		$msId = trim($this->_getParam('msId'));
		$recvers = $this->_getParam('recvers');
		$title = trim($this->_getParam('title'));
		$content = trim($this->_getParam('content'));
		$isSent = trim($this->_getParam('isSent'));
		//los2 test
//		var_dump($content);
//		exit;
		
		//验证参数：收件人，标题，内容
		if (empty($recvers)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_SELECT_SEND_OBJECT'));
			exit(json_encode($ret));
		}
		if (empty($title)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER_TITLE'));
			exit(json_encode($ret));
		}
		if (empty($content)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER_CONTENT'));
			exit(json_encode($ret));
		}
		
		//发送消息，即保存到数据库，同时，设定关联表
		$msgSend = array('title' => $title, 'content' => $content, 'sender_code' => $this->auth->usr['code'], 
			'recver' => json_encode($recvers), 'is_sent' => $isSent, 'send_time' => date('Y-m-d H:i:s')
		);
		$db = Zend_Registry::get('DB');
		$db->beginTransaction();
		if (empty($msId)) { //新的消息
			$msId = MsgSendService::addMsgSend($msgSend);
		} else { //修改的草稿消息
			$resultUpdate = MsgSendService::updateMsById($msId, $msgSend);
			if (!$resultUpdate) {
				$msId = 0;
			}
		}
		if ($msId) {
			if ($isSent == 'Y') { //发送消息
				$strRecvers = implode("','", $recvers);
				$strRecvers = "'" . $strRecvers . "'";
				$cond = " role_code in ($strRecvers) ";
				$usrs = UsrService::getUsrsByCond($cond, array('code'));
				$msgRecvs = array();
				foreach ($usrs as $usr) {
					$msgRecvs[] = array('ms_id' => $msId, 'recver_code' => $usr['code']);
				}
				$result = BaseService::addRows('msg_recv', $msgRecvs);
				if ($result) {
					$db->commit();
					//成功
					$ret = array('err' => 0, 'msg' => 'Success');
					exit(json_encode($ret));
				}
			} else { //将消息存草稿
				$db->commit();
				$ret = array('err' => 0, 'msg' => 'Success');
				exit(json_encode($ret));
			}
		}
		$db->rollback();
		
		//返回
		$msg = $this->tr->translate('SAVE_DRAFT_ERR');
		if ($isSent == 'Y') {
			$msg = $this->tr->translate('SEND_MSG_ERR');
		}
		$ret = array('err' => 1, 'msg' => 'Error');
		exit(json_encode($ret));
    }
    
    
    /**
     * 删除管理员发送的消息 
     */
    public function delmsgsendAction() {
    	//获取参数
    	$msgIds = $this->_getParam('msgIds');
    	
    	//删除消息
    	$result = BaseService::delByIds('msg_send', $msgIds);
    	if ($result) {
	    	$sMsgIds = implode(',', $msgIds);
	    	$cond = " ms_id in ($sMsgIds) ";
	    	$result2 = BaseService::delByCond('msg_recv', $cond);
	    	if ($result2) {
	    		$ret = array('err' => 0, 'msg' => 'Success');
				exit(json_encode($ret));
	    	}
    	}
    	$ret = array('err' => 1, 'msg' => 'Error');
		exit(json_encode($ret));
    }
    
    
    
    
} //End: class Admin_MsgController