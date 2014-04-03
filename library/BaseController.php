<?php
include_once 'AbstractController.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


class BaseController extends AbstractController {
	public function init() {
		//初始化变量
		$this->layout = Zend_Registry::get('LAYOUT');
		$this->auth = new Zend_Session_Namespace('AUTH');
		$this->tr = Zend_Registry::get('TRANSLATE');
		$this->db = Zend_Registry::get('DB');
		$this->now = date('Y-m-d H:i:s');
		
		//如果是AJAX请求，则禁用layout和禁止跳转
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
		} else {
			$this->view->usr = $this->auth->usr;
		}
		
		//统计在线访问者
		$this->statOlVisitors();
		
		//检查是否有权限访问资源
		$this->ckPerm();
    }
    
    
    /**
     * 组合法人的员工案件的检索条件
     * @param string $range
     * @param string $sValue
     */
    protected function genLpEmpCaseOption($range, $sValue) {
    	$option = array();
		if(empty($sValue)) return $option;
		switch ($range) {
			case 'emp_code': array_push($option, "r.talent_code like '%$sValue%'");break;
			case 'emp_name': array_push($option, "r.fullname like '%$sValue%'");break;
			case 'case_code': array_push($option, "cs.code like '%$sValue%'");break;
			case 'case_name': array_push($option, "cs.name like '%$sValue%'");break;
			case 'CASE_NAME': array_push($option, "cs.name like '%$sValue%'");break;
			case 'resume_code': array_push($option, "r.code = '$sValue'");break;
		}
		return $option;
    }

    
    /**
     * 操作成功提示页面
     * @param string $opUrl
     * @param string $opName
     * @param string $opResultName
     * @param string $opResultImgUrl
     */
	protected function showSuccessMsg($opUrl=null, $opName=null, $opResultName=null, $opResultImgUrl=null) {
		$title = $this->tr->translate('op_success');
		$title = htmlentities(urlencode($title));
		$opUrl = empty($opUrl) ? '/lp_lp/lp' : $opUrl;
		$opUrl = htmlentities(urlencode($opUrl));
		$opName = empty($opName) ? $this->tr->translate('lp_center') : $this->tr->translate($opName);
		$opName = htmlentities(urlencode($opName));
		$opResultName = empty($opResultName) ? $this->tr->translate('op_success') : $this->tr->translate($opResultName);
		$opResultName = htmlentities(urlencode($opResultName));
		$opResultImgUrl = empty($opResultImgUrl) ? '' : $opResultImgUrl;
		$opResultImgUrl = htmlentities(urlencode($opResultImgUrl));
		$this->_redirect("/msg/msg/?title=$title&opUrl=$opUrl&opName=$opName&opResultName=$opResultName&opResultImgUrl=$opResultImgUrl");
	}
	
	
	/**
     * 操作失败提示页面
     * @param string $opUrl
     * @param string $opName
     * @param string $opResultName
     * @param string $opResultImgUrl
     */
	protected function showErrorMsg($opUrl=null, $opName=null, $opResultName=null, $opResultImgUrl = '') {
		$title = $this->tr->translate('op_error');
		$title = htmlentities(urlencode($title));
		$opUrl = empty($opUrl) ? '/lp_lp/lp' : $opUrl;
		$opUrl = htmlentities(urlencode($opUrl));
		$opName = empty($opName) ? $this->tr->translate('lp_center') : $this->tr->translate($opName);
		$opName = htmlentities(urlencode($opName));
		$opResultName = empty($opResultName) ? $this->tr->translate('op_error') : $this->tr->translate($opResultName);
		$opResultName = htmlentities(urlencode($opResultName));
		$this->_redirect("/msg/msg/?title=$title&opUrl=$opUrl&opName=$opName&opResultName=$opResultName&opResultImgUrl=$opResultImgUrl");
	}
	
	
	/**
	 * 转向操作成功提示页面
	 * @param string $title: 网页标题
	 * @param string $opUrl：当前操作页面的URL
	 * @param string $opName：当前操作页面的名字
	 * @param string $opResultName：操作结果的名字
	 * @param string $opResultImgUrl：操作结果的图片链接
	 */
	protected function redirectSucc($title, $opUrl, $opName, $opResultName, $opResultImgUrl) {
		if (empty($title)) {
			$title = $this->tr->translate('op_success');
		}
		$title = htmlentities(urlencode($title));
		$opUrl = htmlentities(urlencode($opUrl));
		$opName = htmlentities(urlencode($opName));
		$opResultName = htmlentities(urlencode($opResultName));
		$opResultImgUrl = htmlentities(urlencode($opResultImgUrl));
		$this->_redirect("/msg/msg/?title=$title&opUrl=$opUrl&opName=$opName&opResultName=$opResultName&opResultImgUrl=$opResultImgUrl");
	}
	
	
	/**
	 * 转向操作失败提示页面
	 * @param string $title: 网页标题
	 * @param string $opUrl：当前操作页面的URL
	 * @param string $opName：当前操作页面的名字
	 * @param string $opResultName：操作结果的名字
	 * @param string $opResultImgUrl：操作结果的图片链接
	 */
	protected function redirectErr($title, $opUrl, $opName, $opResultName, $opResultImgUrl) {
		if (empty($title)) {
			$title = $this->tr->translate('op_error');
		}
		$title = htmlentities(urlencode($title));
		$opUrl = htmlentities(urlencode($opUrl));
		$opName = htmlentities(urlencode($opName));
		$opResultName = htmlentities(urlencode($opResultName));
		$opResultImgUrl = htmlentities(urlencode($opResultImgUrl));
		$this->_redirect("/msg/msg/?title=$title&opUrl=$opUrl&opName=$opName&opResultName=$opResultName&opResultImgUrl=$opResultImgUrl");
	}
	
	
	
	
} //End: class BaseController