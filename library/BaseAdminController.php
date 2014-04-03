<?php
include_once 'AbstractController.php';
class BaseAdminController extends AbstractController {
	public function init() {
		//初始化变量
		$this->layout = Zend_Registry::get('LAYOUT');
		$this->auth = new Zend_Session_Namespace('AUTH');
		$this->tr = Zend_Registry::get('TRANSLATE');
		$this->db = Zend_Registry::get('DB');
		
		//如果是AJAX请求，则禁用layout和禁止跳转
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
		}
		
		//统计在线访问者
		$this->statOlVisitors();
		
		//检查是否有权限访问资源
		$this->ckPerm();
	}
	
	
	/**
	 * 组装法人的查询条件
	 * @param unknown_type $range
	 * @param unknown_type $value
	 */
	protected function genLpOption($range, $value) {
		$option = array();
		if(empty($value)) return $option;
		switch ($range) {
			case 'lp_code': array_push($option, "usr.code like '%$value%'");break;
			case 'lp_name': array_push($option, "usr.fullname like '%$value%'");break;
		}
		return $option;
	}
	
	
	/**
	 * 组装法人的查询条件
	 * @param unknown_type $range
	 * @param unknown_type $value
	 */
	protected function genResumeOption($range, $value) {
		$option = array();
		if(empty($value)) return $option;
		switch ($range) {
			case 'lp_code': array_push($option, "u.talent_code like '%$value%'");break;
			case 'lp_name': array_push($option, "u.fullname like '%$value%'");break;
		}
		return $option;
	}
	

	/**
	 * 转向操作成功提示页面
	 * @param string $opUrl
	 * @param string $opName
	 * @param string $opResultName
	 * @param string $opResultImgUrl
	 */
	protected function showSuccessMsg($opUrl, $opName, $opResultName, $opResultImgUrl='') {
		$title = $this->tr->translate('op_success');
		$opUrl = empty($opUrl) ? '/admin/admin_index/index' : $opUrl;
		$opName = empty($opName) ? $this->tr->translate('ADMIN_CENTER') : $this->tr->translate($opName);
		$opResultName = empty($opResultName) ? $this->tr->translate('op_success') : $this->tr->translate($opResultName);
		$opResultImgUrl = empty($opResultImgUrl) ? '' : $opResultImgUrl;
		$this->_redirect("/msg/msg/?title=$title&opUrl=$opUrl&opName=$opName&opResultName=$opResultName&opResultImgUrl=$opResultImgUrl");
	}
	
	/**
	 * 转向操作失败提示页面
	 * @param string $opUrl
	 * @param string $opName
	 * @param string $opResultName
	 * @param string $opResultImgUrl
	 */
	protected function showErrorMsg($opUrl, $opName, $opResultName, $opResultImgUrl = '') {
		$title = $this->tr->translate('op_error');
		$opUrl = empty($opUrl) ? '/admin/admin_index/index' : $opUrl;
		$opName = empty($opName) ? $this->tr->translate('ADMIN_CENTER') : $this->tr->translate($opName);
		$opResultName = empty($opResultName) ? $this->tr->translate('op_error') : $this->tr->translate($opResultName);
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
	
	
	/**
	 * 检索人才时对查询条件进行解释，在页面进行条件组织，后台再解释
	 * @param unknown_type $searchFields
	 */
	protected function genFieldsSearch($searchFields) {
		if (empty($searchFields)) return NULL;
		return json_decode($searchFields, true);
	}
}

