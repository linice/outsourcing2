<?php
include_once 'LogService.php';

/**
 * order_prod: 订单行表（即订单对应的产品表）
 * @author los
 */
class OrderProdService {
	/**
	 * order_prod: 新增单条记录
	 * @param array(field => value) $row
	 */
	public static function addOrderProd($row = array()) {
		if (!is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		try {
			$db->insert('order_prod', $row);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => $e->getMessage(), 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * order_prod: 新增多条记录
	 * @param array(array(field => value)) $rows
	 */
	public static function addOrderProds($rows = array()) {
		if (!is_array($rows)) {
			return FALSE;
		}
		$fields = array();
		foreach ($rows as $row) {
			foreach ($row as $field => $value) {
				$fields[] = addslashes($field);
			}
			break;
		}
		$strFields = implode(',', $fields);
		$sql = "insert order_prod($strFields) values ";
		foreach ($rows as $r) {
			foreach ($r as &$v) {
				$v = addslashes($v);
			}
			$sql .= "('" . implode("','", $r) . "'),";
		}
		$sql = rtrim($sql, ', ');
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
	 * order_prod: 删除单条记录，根据id
	 * @param int $id
	 */
	public static function delOrderProdById($id) {
		if (!is_numeric($id)) {
			return FALSE;
		}
		$sql = "delete from order_prod where id = $id";
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
	 * order_prod: 删除单条记录，根据order_code
	 * @param string $orderCode
	 */
	public static function delOrderProdsByOrderCode($orderCode) {
		if (!is_string($orderCode)) {
			return FALSE;
		}
		$orderCode = addslashes($orderCode);
		$sql = "delete from order_prod where order_code = '$orderCode'";
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
	 * order_prod: 更新单条记录，根据id
	 * @param int $id
	 * @param array(field => value) $row
	 */
	public static function updateOrderProdById($id, $row = array()) {
		if (!is_numeric($id) || !is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		$cond = $db->quoteInto('id = ?', $id);
		try {
			$db->update('order_prod', $row, $cond);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => $e->getMessage(), 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * order_prod: 更新单条记录，根据order_code
	 * @param string $orderCode
	 * @param array(field => value) $row
	 */
	public static function updateOrderProdByOrderCode($orderCode, $row = array()) {
		if (!is_string($orderCode) || !is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		$cond = $db->quoteInto('order_code = ?', $orderCode);
		try {
			$db->update('order_prod', $row, $cond);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => $e->getMessage(), 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * order_prod: 更新单条/多条记录，根据自定义条件cond
	 * @param string $cond
	 * @param array(field => value) $row
	 */
	public static function updateOrderProdsByCond($cond, $row = array()) {
		if (!is_string($cond) || !is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		try {
			$db->update('order_prod', $row, $cond);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => $e->getMessage(), 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * order_prod: 查询，根据id
	 * @param int $id
	 * @param array(string) $fields
	 */
	public static function getOrderProdById($id, $fields = array('*')) {
		if (!is_numeric($id) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$sql = "select $strFields from order_prod where id = '$id' limit 1";
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
	 * order_prod: 查询，根据order_code
	 * @param string $orderCode
	 * @param array(string) $fields
	 */
	public static function getOrderProdsByOrderCode($orderCode, $fields = array('*')) {
		if (!is_string($orderCode) || !is_array($fields)) {
			return false;
		}
		$orderCode = addslashes($orderCode);
		$strFields = implode(',', $fields);
		$sql = "select $strFields from order_prod where order_code = '$orderCode'";
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
	 * order_prod: 查询，根据自定义条件cond
	 * @param string $cond
	 * @param array(string) $fields
	 */
	public static function getOrderProdsByCond($cond, $fields = array('*')) {
		if (!is_string($cond) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$sql = "select $strFields from order_prod where $cond";
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
	 * order_prod: 查询，分页
	 * @param int $page
	 * @param int $pageSize
	 * @param array(string) $fields
	 */
	public static function getOrderProdsByPage($page, $pageSize, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_array($fields)) {
			return false;
		}
		$offset = ($page - 1) * $pageSize;
		$strFields = implode(',', $fields);
		$sql = "select $strFields from order_prod limit $offset, $pageSize";
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
	 * order_prod: 查询，分页&自定义条件
	 * @param int $page
	 * @param int $pageSize
	 * @param string $cond
	 * @param array(string) $fields
	 */
	public static function getOrderProdsByPageAndCond($page, $pageSize, $cond, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($cond) || !is_array($fields)) {
			return false;
		}
		$offset = ($page - 1) * $pageSize;
		$strFields = implode(',', $fields);
		$sql = "select $strFields from order_prod 
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
	
	
	
	
} //End: class OrderProdService