<?php
include_once 'BaseController.php';
include_once 'Msg/MsgService.php';
include_once 'Msg/MsgRecvService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


class Msg_MsgController extends BaseController {
	/**
	 * 消息通知：针对普通用户或法人
	 */
    public function msgAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('msg_notify'));
		$crumb = array('uri' => '/msg_msg/msg', 'name' => $this->tr->translate('msg_notify'));
		$this->view->layout()->crumbs = array($crumb);
		
    }
    
    
    /**
     * 获取消息列表
     */
    public function getmsglistAction() {
    	//获取参数
		$this->getPagination();
    	$parm = trim($this->_getParam('parm'));
    	$parm = json_decode($parm, TRUE);
    	
		//查询消息
		$usrCode = $this->auth->usr['code'];
		if ($parm['msgType'] == 'PERSONAL') {
//			$cond = " recver_code = '$usrCode' ";
//			$msgs = MsgService::getMsgsByCond($cond, array('count(*) as cnt'));
//			$msgsCnt = $msgs[0]['cnt'];
//			$pageMsgs = MsgService::getMsgsByPageAndCond($page, $pageSize, $cond, array('id', 'title', 'content', 'recv_time', 'is_read'));
//			$fields = array('id', 'sender_code', 'title', 'content', 'recv_time', 'is_read');
			$sql = "select a.id, a.sender_code, a.title, a.content, a.recv_time, a.is_read, b.fullname as sender_name
				from msg as a left join usr as b
				on a.sender_code = b.code
				where a.recver_code = '$usrCode'";
			$statMsgs = BaseService::getByPageWithSql($sql, $this->pagination);
		} else if ($parm['msgType'] == 'SYS') {
//			$cond = " a.recver_code = '$usrCode' ";
//			$msgs = MsgRecvService::getMrsByCond($cond, array('count(*) as cnt'));
//			$msgsCnt = $msgs[0]['cnt'];
//			$pageMsgs = MsgRecvService::getMrsAndMssByPageAndCond($page, $pageSize, $cond, array('id', 'is_read'), array('title', 'content', 'send_time as recv_time'));
			
			$sql = "select a.id, a.is_read, b.title, b.content, b.send_time as recv_time
				from msg_recv as a left join msg_send as b
				on a.ms_id = b.id
				where a.recver_code = '$usrCode'";
			$statMsgs = BaseService::getByPageWithSql($sql, $this->pagination);
		}
    	
		//return
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $statMsgs['Total'], 'Rows' => $statMsgs['Rows']);
		exit(json_encode($ret));
    }
    
    
    /**
     * 标识消息已读
     */
    public function readmsgAction() {
    	//获取参数
    	$msgId = trim($this->_getParam('msgId'));
    	$msgType = trim($this->_getParam('msgType'));
    	
    	//标识消息已读
    	$result = false;
    	if ($msgType == 'PERSONAL') {
	    	$result = MsgService::updateMsgById($msgId, array('is_read' => 'Y'));
    	} else if ($msgType == 'SYS') {
    		$result = MsgRecvService::updateMrById($msgId, array('is_read' => 'Y'));
    	}
    	if ($result) {
    		$ret = array('err' => 0, 'msg' => 'Success');
			exit(json_encode($ret));
    	}
    	$ret = array('err' => 1, 'msg' => $this->tr->translate('UPDATE_MSG_ERR'));
		exit(json_encode($ret));
    }
    
    
    /**
     * 删除消息
     */
    public function delmsgAction() {
    	//获取参数
    	$msgIds = $this->_getParam('msgIds');
    	$msgType = trim($this->_getParam('msgType'));
    	
    	//删除消息
    	$result = false;
    	$strMsgIds = implode(',', $msgIds);
    	$cond = " id in ($strMsgIds) ";
    	if ($msgType == 'PERSONAL') {
	    	$result = MsgService::delMsgsByCond($cond);
    	} else if ($msgType == 'SYS') {
    		$result = MsgRecvService::delMrsByCond($cond);
    	}
    	if ($result === TRUE) {
    		$ret = array('err' => 0, 'msg' => 'Success');
			exit(json_encode($ret));
    	}
    	$ret = array('err' => 1, 'msg' => $this->tr->translate('DEL_MSG_ERR'));
		exit(json_encode($ret));
    }
    
    
   
    
    
    
} //End: class Msg_MsgController