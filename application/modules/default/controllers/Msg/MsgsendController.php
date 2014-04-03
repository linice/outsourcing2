<?php
include_once 'Msg/MsgSendService.php';
include_once 'BaseController.php';


class Msg_MsgsendController extends BaseController {
	/**
	 * 消息一览
	 */
    public function msgsendAction() {
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
    	
		//查询消息
		$usrCode = $this->auth->usr['code']; 
		$cond = " sender_code = '$usrCode' ";
		$msgs = MsgSendService::getMsgSendsByCond($cond, array('count(*) as cnt'));
		$msgsCnt = $msgs[0]['cnt'];
		$pageMsgs = MsgSendService::getMsgSendsByPageAndCond($page, $pageSize, $cond, array('id', 'title', 'content', 'send_time', 'is_sent'));
    	
		//return
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $msgsCnt, 'Rows' => $pageMsgs);
		exit(json_encode($ret));
    }
    
    
	/**
	 * 发送消息
	 */
    public function sendmsgAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('send_msg'));
		$crumb = array('uri' => '/msg_msgsend/sendmsg', 'name' => $this->tr->translate('send_msg'));
		$this->view->layout()->crumbs = array($crumb);
		
    }
    
    
    
} //End: class Msg_MsgsendController