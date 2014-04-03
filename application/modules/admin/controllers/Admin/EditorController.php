<?php
include_once 'BaseAdminController.php';
include_once 'ActiveEtcService.php';
include_once 'UsrService.php';
include_once 'EditorService.php';
class Admin_Admin_EditorController extends BaseAdminController {
	
	/**
	 * 新增录入人员
	 */
	public function addeditorAction() {
		//获取参数
		$modify = trim($this->_getParam('modify'));
		$adt = array();
		
		//如果是修改Editor
		if ($modify == 'Y') {
			$title = $this->tr->translate('MODIFY') . $this->tr->translate('EDITOR');
			$edtCode = trim($this->_getParam('edtCode'));
			if (empty($edtCode)) {
				$this->showErrorMsg('/admin/admin_editor/editorlist', $this->tr->translate('EDITOR') . $this->tr->translate('GENERAL_VIEW'), $this->tr->translate('EDITOR') . $this->tr->translate('NOT_EXISTS'));
			}
			$edt = BaseService::getRowByCode('usr', $edtCode, array('code', 'nickname'));
		} else { //新增Editor
			$title = $this->tr->translate('add_editor');
		}
		
		//导航
		$this->layout->headTitle($title);
		$crumb = array('uri' => '/admin/admin_editor/addeditor', 'name' => $title);
		$this->view->layout()->crumbs = array($crumb);
		
		//View
		$this->view->title = $title;
		$this->view->jsEdt = json_encode($edt);
	}
	
	
	/**
	 * 保存EIDTOR
	 */
	public function saveeditorAction() {
		//获取参数
		$code = trim($this->_getParam('code'));
		$nickname = trim($this->_getParam('nickname'));
		$passwd = trim($this->_getParam('passwd'));
		
		//验证参数
		if (empty($nickname)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('editor_name_cannot_be_null'), 'errField' => 'nickname');
			exit(json_encode($ret));
		} 
		//如果是修改Editor
		if (!empty($code)) {
			$usr = array();
			$resultUsr = BaseService::getRowByCode('usr', $code, array('1'));
			if (empty($resultUsr)) {
				$ret = array('err' => 1, 'msg' => $this->tr->translate('EDITOR') . $this->tr->translate('NOT_EXISTS'));
				exit(json_encode($ret));
			}
			if (!empty($passwd)) {
				$usr['passwd'] = md5($passwd);
			}
			$usr['nickname'] = $nickname;
			$result = BaseService::updateByCode('usr', $usr, $code);
		} else { //新增Editor
			if (empty($passwd)) {
				$ret = array('err' => 1, 'msg' => $this->tr->translate('editor_password_cannot_be_null'), 'errField' => 'passwd');
				exit(json_encode($ret));
			}
			//赋值usr
			$usr = array();
			$usr['role_code'] = 'EDITOR';
			$usr['email'] = date('YmdHis') . rand(0, 10000) . '@jinzai-anken.com';
			$usr['email_consignee'] = $usr['email'];
			$usr['nickname'] = $nickname;
			$usr['passwd'] = md5($passwd);
			
			$this->db->beginTransaction();
			//查询数据库获取用户唯一编号
			$usr['code'] = ActiveEtcService::genCode('EDT_CODE');
			$result = EditorService::regNewEditor($usr);
			if ($result) {
				$this->db->commit();
			} else {
				$this->db->rollback();
			}
		}
		
		if ($result) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('SAVE') . $this->tr->translate('EDITOR') . $this->tr->translate('SUCCESS'));
			exit(json_encode($ret));
		} else {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('SAVE') . $this->tr->translate('EDITOR') . $this->tr->translate('FAILED'));
			exit(json_encode($ret));
		}
	}
	
	
	/**
	 * 录入人员列表
	 */
	public function editorlistAction() {
		//导航
		$this->layout->headTitle($this->tr->translate('editor_list'));
		$crumb = array('uri' => '/admin/admin_editor/editorlist', 'name' => $this->tr->translate('editor_list'));
		$this->view->layout()->crumbs = array($crumb);
	}
	

	/**
	 * 录入人员列表 --查询
	 */
	public function searcheditorlistAction() {
		//获取参数
		$this->getPagination();
		$range = $this->_getParam("range");
		$sValue = $this->_getParam("sValue");
		
		//查找所有的法人，包括失效以及未审核的
		$lpList = EditorService::findEditorByOption($this->genLpOption($range, $sValue), array('code', 'nickname'), $this->pagination);
		exit(json_encode($lpList));
	}
	
	
	/**
	 * 删除录入人员
	 */
	public function deleditorAction() {
		$edtCodes = $this->_getParam('edtCodes');
		if (empty($edtCodes)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_SELECT') . $this->tr->translate('EDITOR'));
			exit(json_encode($ret));
		}
		$result = EditorService::deleteEditor($edtCodes);
		if ($result) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('DEL') . $this->tr->translate('EDITOR') . $this->tr->translate('FAILED'));
		exit(json_encode($ret));
	}
	
	
} //End: class Admin_Admin_EditorController