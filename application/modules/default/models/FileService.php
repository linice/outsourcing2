<?php
include_once 'LogService.php';
include_once 'UtilService.php';

/**
 * 文件：主要是简历文件，如：张三.xslx
 * @author los
 *
 */
class FileService {
	/**
	 * file: 增加
	 * @param array(field => value) $row
	 */
	public static function addFile($row = array()) {
		if (!is_array($row)) {
			return false;
		}
		$db = Zend_Registry::get('DB');
		try {
			$db->insert('file', $row);
			$lastInsertId = $db->lastInsertId();
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "row: $jsRow, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $lastInsertId;
	}
	
	
	/**
	 * file: 删除
	 * @param int $id: file 的主键id
	 */
	public static function delFileById($id) {
		if (!is_numeric($id)) {
			return false;
		}
		$sql = "delete from file where id = $id";
		$db = Zend_Registry::get('DB');
		try {
			$db->query($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return true;
	}
	
	
	/**
	 * file: 删除
	 * @param int $ids: file 的主键id(s)
	 */
	public static function delFileByIds($ids) {
		if (!is_array($ids)) {
			return false;
		}
		$strIds = implode(',', $ids);
		$sql = "delete from file where id in ($strIds)";
		$db = Zend_Registry::get('DB');
		try {
			$db->query($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return true;
	}
	
	
	/**
	 * file: 删除
	 * @param int $ids: file 的主键id(s)
	 * @param array(field => value) $row
	 */
	public static function updateFileByIds($ids, $row = array()) {
		if (!is_array($ids) || !is_array($row)) {
			return false;
		}
		$strIds = implode(',', $ids);
		$sql = "update file set ";
		foreach ($row as $field => $value) {
			$field = addslashes($field);
			$value = addslashes($value);
			$sql .= " $field = '$value', ";
		}
		$sql = rtrim($sql, ', ');
		$sql .= " where id in ($strIds)";
		$db = Zend_Registry::get('DB');
		try {
			$db->query($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return true;
	}
	
	
	/**
	 * file: 查询，根据id
	 * @param int $id
	 * @param array(string) $fields
	 */
	public static function getFileById($id, $fields = array('*')) {
		if (!is_numeric($id)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$sql = "select $strFields
			FROM file
			where id = $id";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchRow($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	/**
	 * file: 查询，根据ids
	 * @param int $ids: file的主键id(s)
	 * @param array(string) $fields
	 */
	public static function getFileByIds($ids, $fields = array('*')) {
		if (!is_array($ids)) {
			return false;
		}
		$strIds = implode(',', $ids);
		$strFields = implode(',', $fields);
		$sql = "select $strFields
			FROM file
			where id in ($strIds)";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchAll($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	/**
	 * file: 查询数量，根据文件类型type
	 * @param string $type
	 */
	public static function getFilesCntByType($type) {
		if (!is_string($type)) {
			return FALSE;
		}
		$type = addslashes($type);
		$sql = "select count(*) as cnt
			FROM file
			where type = '$type'";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchRow($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result['cnt'];
	}
	
	
	/**
	 * file: 查询数量，根据文件类型type
	 * @param string $type
	 * @param string $status
	 */
	public static function getFilesCntByTypeAndStatus($type, $status) {
		if (!is_string($type) || !is_string($status)) {
			return FALSE;
		}
		$type = addslashes($type);
		$sql = "select count(*) as cnt
			FROM file
			where type = '$type'
				and status = '$status'";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchRow($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result['cnt'];
	}
	
	
	/**
	 * file: 查询数量，根据文件类型type
	 * @param string $type
	 * @param string $status
	 * @param string $basename
	 */
	public static function getFilesCntByTypeAndStatusAndLikeBasename($type, $status, $basename) {
		if (!is_string($type) || !is_string($status)|| !is_string($basename)) {
			return FALSE;
		}
		$type = addslashes($type);
		$sql = "select count(*) as cnt
			FROM file
			where type = '$type'
				and status = '$status'
				and basename_c like '%$basename%'";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchRow($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result['cnt'];
	}
	
	
	/**
	 * file: 查询数量，根据文件类型type
	 * @param string $type
	 * @param string $basename
	 */
	public static function getFilesCntByTypeAndLikeBasename($type, $basename) {
		if (!is_string($type) || !is_string($basename)) {
			return FALSE;
		}
		$type = addslashes($type);
		$sql = "select count(*) as cnt
			FROM file
			where type = '$type'
				and basename_c like '%$basename%'";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchRow($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result['cnt'];
	}
	
	
	/**
	 * file: 查询数量，根据人才编号talent_code，文件类型type
	 * @param string $talentCode
	 * @param string $type
	 */
	public static function getFilesCntByTalentCodeAndType($talentCode, $type) {
		if (!is_string($talentCode) || !is_string($type)) {
			return FALSE;
		}
		$sql = "select count(*) as cnt
			FROM file
			where talent_code = '$talentCode'
				and type = '$type'";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchRow($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result['cnt'];
	}
	
	
	/**
	 * file: 查询数量，根据法人编号lp_code，文件类型type
	 * @param string $lpCode
	 * @param string $type
	 */
	public static function getFilesCntByLpCodeAndType($lpCode, $type) {
		if (!is_string($lpCode) || !is_string($type)) {
			return FALSE;
		}
		$sql = "select count(*) as cnt
			FROM file
			where lp_code = '$lpCode'
				and type = '$type'";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchRow($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result['cnt'];
	}
	
	
	/**
	 * file: 查询，根据文件类型type
	 * @param int $page
	 * @param int $pageSize
	 * @param string $type
	 * @param array(string) $fields
	 */
	public static function getFilesByPageAndType($page, $pageSize, $type, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($type) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$offset = ($page - 1) * $pageSize;
		$sql = "select $strFields
			FROM file
			where type = '$type'
			limit $offset, $pageSize";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchAll($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	/**
	 * file: 查询，根据文件类型type
	 * @param int $page
	 * @param int $pageSize
	 * @param string $type
	 * @param string $status
	 * @param array(string) $fields
	 */
	public static function getFilesByPageAndTypeAndStatus($page, $pageSize, $type, $status, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($type) || !is_string($status) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$offset = ($page - 1) * $pageSize;
		$sql = "select $strFields
			FROM file
			where type = '$type'
				and status = '$status'
			limit $offset, $pageSize";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchAll($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	/**
	 * file: 查询，根据文件类型type
	 * @param int $page
	 * @param int $pageSize
	 * @param string $type
	 * @param string $status
	 * @param string $basename
	 * @param array(string) $fields
	 */
	public static function getFilesByPageAndTypeAndStatusAndLikeBasename($page, $pageSize, $type, $status, $basename, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($type) || !is_string($status) || !is_string($basename) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$offset = ($page - 1) * $pageSize;
		$sql = "select $strFields
			FROM file
			where type = '$type'
				and status = '$status'
				and basename_c like '%$basename%'
			limit $offset, $pageSize";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchAll($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	/**
	 * file: 查询，根据文件类型type
	 * @param int $page
	 * @param int $pageSize
	 * @param string $type
	 * @param string $basename
	 * @param array(string) $fields
	 */
	public static function getFilesByPageAndTypeAndLikeBasename($page, $pageSize, $type, $basename, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($type) || !is_string($basename) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$offset = ($page - 1) * $pageSize;
		$sql = "select $strFields
			FROM file
			where type = '$type'
				and basename_c like '%$basename%'
			limit $offset, $pageSize";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchAll($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	/**
	 * file: 查询，根据人才编号talent_code，文件类型type
	 * @param int $page
	 * @param int $pageSize
	 * @param string $talentCode
	 * @param string $type
	 * @param array(string) $fields
	 */
	public static function getFilesByPageAndTalentCodeAndType($page, $pageSize, $talentCode, $type, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($talentCode) || !is_string($type) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$offset = ($page - 1) * $pageSize;
		$sql = "select $strFields
			FROM file
			where talent_code = '$talentCode'
				and type = '$type'
			limit $offset, $pageSize";
		//log
//		$log = array('level' => 1, 'msg' => "sql: $sql", 'class' => __CLASS__, 'func' => __FUNCTION__);
//		LogService::saveLog($log);
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchAll($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	/**
	 * file: 查询，根据法人编号lp_code，文件类型type
	 * @param int $page
	 * @param int $pageSize
	 * @param string $lpCode
	 * @param string $type
	 * @param array(string) $fields
	 */
	public static function getFilesAndUsrsByPageAndLpCodeAndType($page, $pageSize, $lpCode, $type, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($lpCode) || !is_string($type) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',a.', $fields);
		$strFields = 'a.' . $strFields;
		$offset = ($page - 1) * $pageSize;
		//如果在表file里有法人的员工编号，则查询员工的姓名
//		$sql = "select $strFields, b.fullname
//			FROM file as a left join usr as b
//			on a.talent_code = b.code 
//			where a.lp_code = '$lpCode'
//				and a.type = '$type'
//			limit $offset, $pageSize";
		$sql = "select $strFields
			FROM file as a
			where a.lp_code = '$lpCode'
				and a.type = '$type'
			limit $offset, $pageSize";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchAll($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	
	
	
	
} //End: class FileService