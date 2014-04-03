<?php
include_once 'LogService.php';

/**
 * 消息通知：针对普通用户或法人
 * @author los
 *
 */
class MsgSendService {
	/**
	 * msg_send: 新增单条记录
	 * @param array(field => value) $row
	 */
	public static function addMsgSend($row = array()) {
		if (!is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		try {
			$db->insert('msg_send', $row);
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
	 * msg_send: 删除多条记录，根据自定义条件cond
	 * @param string $cond
	 */
	public static function delMsgSendsByCond($cond) {
		if (!is_string($cond)) {
			return FALSE;
		}
		$sql = "delete from msg_send where $cond";
		$db = Zend_Registry::get('DB');
		try {
			$db->query($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * msg_send: 更新单条记录，根据id
	 * @param int $id
	 * @param array(field => value) $row
	 */
	public static function updateMsById($id, $row = array()) {
		if (!is_numeric($id) || !is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		$cond = $db->quoteInto('id = ?', $id);
		try {
			$db->update('msg_send', $row, $cond);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => $e->getMessage(), 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * msg_send: 更新单条/多条记录，根据自定义条件cond
	 * @param string $cond
	 * @param array(field => value) $row
	 */
	public static function updateMsgSendsByCond($cond, $row = array()) {
		if (!is_string($cond) || !is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		try {
			$db->update('msg_send', $row, $cond);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => $e->getMessage(), 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * msg_send: 查询，根据id
	 * @param int $id
	 * @param array(string) $fields
	 */
	public static function getMsgSendById($id, $fields = array('*')) {
		if (!is_numeric($id) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$sql = "select $strFields from msg_send where id = '$id' limit 1";
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
	 * msg_send：查询,根据自定义cond
	 * @param string $cond
	 * @param array(string) $fields
	 */
	public static function getMsgSendsByCond($cond, $fields = array('*')) {
		$strFields = implode(',', $fields);
		$sql = "select $strFields from msg_send where $cond";
		//log
		$log = array('level' => 1, 'msg' => "sql: $sql", 'class' => __CLASS__, 'func' => __FUNCTION__);
		LogService::saveLog($log);
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
	 * msg_send: 查询，分页&自定义条件
	 * @param int $page
	 * @param int $pageSize
	 * @param string $cond
	 * @param array(string) $fields
	 */
	public static function getMsgSendsByPageAndCond($page, $pageSize, $cond, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($cond) || !is_array($fields)) {
			return false;
		}
		$offset = ($page - 1) * $pageSize;
		$strFields = implode(',', $fields);
		$sql = "select $strFields from msg_send 
			where $cond
			limit $offset, $pageSize";
		//log
		$log = array('level' => 1, 'msg' => "sql: $sql", 'class' => __CLASS__, 'func' => __FUNCTION__);
		LogService::saveLog($log);
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
	
	
	
} //End: class MsgSendService