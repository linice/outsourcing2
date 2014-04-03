<?php
include_once 'LogService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


class ActiveEtcService {
	/**
	 * active_etc：查询
	 * @param array(string) $fields
	 */
	public static function getActiveEtcs($fields = array('*')) {
		$strFields = implode(',', $fields);
		$sql = "select $strFields from active_etc";
		return BaseService::getAllBySql($sql);
	}
	
	
	/**
	 * active_etc：查询
	 * @param string $type
	 * @param array(string) $fields
	 */
	public static function getActiveEtcsByType($type, $fields = array('*')) {
		$type = addslashes($type);
		$strFields = implode(',', $fields);
		$sql = "select $strFields from active_etc where type = '$type'";
		return BaseService::getAllBySql($sql);
	}
	
	
	/**
	 * active_etc：查询,根据type, code
	 * @param string $type
	 * @param string $code
	 * @param array(string) $fields
	 */
	public static function getActiveEtcByTypeAndCode($type, $code, $fields = array('*')) {
		$strFields = implode(',', $fields);
		$sql = "select $strFields from active_etc where type = '$type' and code = '$code'";
		return BaseService::getRowBySql($sql);
	}
	
	
	/**
	 * active_etc: 更新，根据type, code
	 * @param string $type
	 * @param string $code
	 * @param array(field=>value) $row
	 */
	public static function updateActiveEtcByTypeAndCode($type, $code, $row) {
		if (!is_array($row)) {
			return false;
		}
		$db = Zend_Registry::get('DB');
		$where[] = $db->quoteInto('type = ?', $type);
		$where[] = $db->quoteInto('code = ?', $code);
		try {
			$db->update('active_etc', $row, $where);
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "tyep = $type, code = $code, row = $jsRow, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return true;
	}
	
	
	/**
	 * 生成唯一编码，包括：人才编码，法人编码，编辑人员编码，管理员编码，案件编码
	 * @param string $code: 表active_etc的字段code
	 */
	public static function genCode($code) {
		$activeEtc = self::getActiveEtcByTypeAndCode('CODE', $code, array('value_prefix', 'value', 'increment', 'base_value'));
		if (!empty($activeEtc)) {
			$year = date('y');
			$codeVal = $activeEtc['value_prefix'] .$year. ($activeEtc['base_value'] + $activeEtc['value'] + $activeEtc['increment']);
			self::updateCode($code, $activeEtc['increment']);
			return $codeVal;
		}
		return FALSE;
	}
	
	
	/**
	 * 更新唯一编码，包括：人才编码，法人编码，编辑人员编码，管理员编码，案件编码
	 * @param string $code: 表active_etc的字段code
	 * @param int $extend
	 */
	public static function updateCode($code, $extend = 1) {
		$code = addslashes($code);
		$sql = "update active_etc
			set value = value + $extend
			where type = 'CODE'
				and code = '$code'";
		return BaseService::exeUpSql($sql);
	}
	
} //End: class ActiveEtcService