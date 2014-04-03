<?php
include_once 'BaseService.php';
include_once 'UsrService.php';
include_once 'EtcService.php';
include_once 'UtilService.php';
include_once 'AKBService.php';
include_once 'CaseBaseService.php';

class EditorService {
	/**
	 * 注册一个新法人
	 * 从etc中根据类型以及名称取得注册法人时奖励的AK币，直接保存到用户属性中
	 * @param unknown_type $usr
	 */
	public static function regNewEditor($usr, $reg=FALSE) {
		if (!is_array($usr)) return FALSE;
		$usr['create_time'] = UtilService::getCurrentTime();
		$usr['last_login_time'] = UtilService::getCurrentTime();
		$usr['enabled'] = "Y";
		$ret = UsrService::addUsr($usr);
    	return $ret;
	}
	
	
	/**
	 * 根据公司名称查找法人
	 * @param string $name
	 * @param array(string) $fields
	 */
	public static function getEditorByName($name, $fields = array('*')) {
		if (!is_string($name) || !is_array($fields)) return false;
		$strFields = implode(',', $fields);
		$sql = "select $strFields from usr where role_code='EDITOR' and fullname = '$name'";
		return BaseService::fetchRowBySql($sql);
	}
	
	
	/**
	 * 根据主键修改法人信息
	 * @param unknown_type $id
	 * @param unknown_type $row
	 */
	public static function updateEditor($where, $row = array()) {
		if (!is_array($row)) return false;
		return BaseService::update('usr', $where, $row);
	}
	
	
	/**
	 * 删除录入人员
	 * @param unknown_type $codes
	 */
	public static function deleteEditor($codes) {
		$row = array('enabled' => 'N');
		$db = BaseService::getDb();
		$cond = $db->quoteInto("role_code='EDITOR' and code in (?)", $codes);
		return BaseService::updateByCond('usr', $row, $cond);
	}
	
	
	/**
	 * 关于法人的综合查询方法
	 * @param unknown_type $option
	 * @param unknown_type $field
	 * @param unknown_type $page
	 */
	public static function findEditorByOption($option, $fields=array('*'), $page=NULL, $reg=TRUE) {
		if (empty($option)) $option = array();
		if (!is_array($option)) return FALSE;
		if ($reg === TRUE) {
			array_push($option, "enabled='Y'");
		} else if ($reg !== FALSE) {
			array_push($option, "enabled='".$option['enabled']."'");
		}
		array_push($option, "role_code='EDITOR'");
		$strFields = implode(',', $fields);
		$sql = "select $strFields from usr where ";
		$whereOption = implode(' and ', $option);
		$sql = $sql.$whereOption;
		//var_dump($sql);
		if (empty($page)) {
			return BaseService::fetchAllBySql($sql);
		} else {
			return BaseService::findByPaginationWithSQL($sql, $page);
		}
	}
	
	
} //End: class EditorService