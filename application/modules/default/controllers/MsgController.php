<?php
include_once 'BaseController.php';



class MsgController extends BaseController {
	private $title = NULL; //页面标题
	private $opUrl = NULL; //操作url，用于导航
	private $opName = NULL; //操作name，用于导航
	private $opResultName = NULL; //操作结果name，用于导航，或可用于操作结果提示的图片不存在时的代替显示
	private $opResultImgUrl = NULL; //操作结果提示的图片url，用于页面内容提示
	
	
	/**
	 * 通过zf的_redirect方法，展示消息页面
	 */
    public function msgAction() {
    	//获取参数
    	$this->title = trim($this->_getParam('title'));
    	$this->opUrl = trim($this->_getParam('opUrl'));
    	$this->opName = trim($this->_getParam('opName'));
    	$this->opResultName = trim($this->_getParam('opResultName'));
    	$this->opResultImgUrl = trim($this->_getParam('opResultImgUrl'));
    	
    	//设置页面标题和导航
    	$this->layout->headTitle($this->title);
    	$crumbs = array();
		$crumbs[] = array('uri' => $this->opUrl, 'name' => $this->opName);
		$crumbs[] = array('uri' => '/msg/msg', 'name' => $this->opResultName);
		$this->view->layout()->crumbs = $crumbs;
		
		//view
		$this->view->opResultImgUrl = $this->opResultImgUrl;
		$this->view->opResultName = $this->opResultName;
    }
    
    
    
} //End: class MsgController extends BaseController