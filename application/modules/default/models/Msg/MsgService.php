<?php
include_once 'LogService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';

/**
 * 消息通知：针对普通用户或法人
 * @author los
 *
 */
class MsgService {
	/**********************************msg*******************************/
	/**
	 * msg: 删除多条记录，根据自定义条件cond
	 * @param string $cond
	 */
	public static function delMsgsByCond($cond) {
		if (!is_string($cond)) {
			return FALSE;
		}
		$sql = "delete from msg where $cond";
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
	 * msg: 更新单条/多条记录，根据自定义条件cond
	 * @param int $id
	 * @param array(field => value) $row
	 */
	public static function updateMsgById($id, $row = array()) {
		if (!is_numeric($id) || !is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		try {
			$db->update('msg', $row, $id);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => $e->getMessage(), 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * msg: 更新单条/多条记录，根据自定义条件cond
	 * @param string $cond
	 * @param array(field => value) $row
	 */
	public static function updateMsgsByCond($cond, $row = array()) {
		if (!is_string($cond) || !is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		try {
			$db->update('msg', $row, $cond);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => $e->getMessage(), 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * msg：查询,根据自定义cond
	 * @param string $cond
	 * @param array(string) $fields
	 */
	public static function getMsgsByCond($cond, $fields = array('*')) {
		$strFields = implode(',', $fields);
		$sql = "select $strFields from msg where $cond";
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
	 * msg: 查询，分页&自定义条件
	 * @param int $page
	 * @param int $pageSize
	 * @param string $cond
	 * @param array(string) $fields
	 */
	public static function getMsgsByPageAndCond($page, $pageSize, $cond, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($cond) || !is_array($fields)) {
			return false;
		}
		$offset = ($page - 1) * $pageSize;
		$strFields = implode(',', $fields);
		$sql = "select $strFields from msg 
			where $cond
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
	
	
	/**************************************msg&msg_recv******************************/
	/**
	 * 计算未读消息的数目：根据收件件编号
	 * @param string $recverCode
	 */
	public static function cntUnreadMsgsByRecverCode($recverCode) {
		$recverCode = addslashes($recverCode);
		$cond = "recver_code = '$recverCode' and is_read = 'N'";
    	$cnt = BaseService::getOneByCond('msg', 'count(*)', $cond);
    	$cnt += BaseService::getOneByCond('msg_recv', 'count(*)', $cond);
    	return $cnt;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
} //End: class MsgService