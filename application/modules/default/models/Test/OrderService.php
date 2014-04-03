<?php
include_once 'LogService.php';

/**
 * orders: 订单表
 * @author los
 */
class OrderService {
	/**
	 * orders: 新增单条记录
	 * @param array(field => value) $row
	 */
	public static function addOrder($row = array()) {
		if (!is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		try {
			$db->insert('orders', $row);
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
	 * orders: 新增多条记录
	 * @param array(array(field => value)) $rows
	 */
	public static function addOrders($rows = array()) {
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
		$sql = "insert orders($strFields) values ";
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
	 * orders: 删除单条记录，根据id
	 * @param int $id
	 */
	public static function delOrderById($id) {
		if (!is_numeric($id)) {
			return FALSE;
		}
		$sql = "delete from orders where id = $id";
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
	 * orders: 删除单条记录，根据code
	 * @param string $code
	 */
	public static function delOrderByCode($code) {
		if (!is_string($code)) {
			return FALSE;
		}
		$code = addslashes($code);
		$sql = "delete from orders where code = '$code'";
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
	 * orders: 删除多条记录，根据自定义条件cond
	 * @param string $cond
	 */
	public static function delOrdersByCond($cond) {
		if (!is_string($cond)) {
			return FALSE;
		}
		$sql = "delete from orders where $cond";
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
	 * orders: 更新单条记录，根据id
	 * @param int $id
	 * @param array(field => value) $row
	 */
	public static function updateOrderById($id, $row = array()) {
		if (!is_numeric($id) || !is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		$cond = $db->quoteInto('id = ?', $id);
		try {
			$db->update('orders', $row, $cond);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => $e->getMessage(), 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * orders: 更新单条记录，根据code
	 * @param string $code
	 * @param array(field => value) $row
	 */
	public static function updateOrderByCode($code, $row = array()) {
		if (!is_string($code) || !is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		$cond = $db->quoteInto('code = ?', $code);
		try {
			$db->update('orders', $row, $cond);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => $e->getMessage(), 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * orders: 更新单条/多条记录，根据自定义条件cond
	 * @param string $cond
	 * @param array(field => value) $row
	 */
	public static function updateOrdersByCond($cond, $row = array()) {
		if (!is_string($cond) || !is_array($row)) {
			return FALSE;
		}
		$db = Zend_Registry::get('DB');
		try {
			$db->update('orders', $row, $cond);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => $e->getMessage(), 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * orders: 查询，根据id
	 * @param int $id
	 * @param array(string) $fields
	 */
	public static function getOrderById($id, $fields = array('*')) {
		if (!is_numeric($id) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$sql = "select $strFields from orders where id = '$id' limit 1";
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
	 * orders: 查询，根据code
	 * @param string $code
	 * @param array(string) $fields
	 */
	public static function getOrderByCode($code, $fields = array('*')) {
		if (!is_string($code) || !is_array($fields)) {
			return false;
		}
		$code = addslashes($code);
		$strFields = implode(',', $fields);
		$sql = "select $strFields from orders where code = '$code' limit 1";
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
	 * orders: 查询，根据自定义条件cond
	 * @param string $cond
	 * @param array(string) $fields
	 */
	public static function getOrdersByCond($cond, $fields = array('*')) {
		if (!is_string($cond) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$sql = "select $strFields from orders where $cond";
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
	 * orders: 查询，分页
	 * @param int $page
	 * @param int $pageSize
	 * @param array(string) $fields
	 */
	public static function getOrdersByPage($page, $pageSize, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_array($fields)) {
			return false;
		}
		$offset = ($page - 1) * $pageSize;
		$strFields = implode(',', $fields);
		$sql = "select $strFields from orders limit $offset, $pageSize";
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
	 * orders: 查询，分页&自定义条件
	 * @param int $page
	 * @param int $pageSize
	 * @param string $cond
	 * @param array(string) $fields
	 */
	public static function getOrdersByPageAndCond($page, $pageSize, $cond, $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($cond) || !is_array($fields)) {
			return false;
		}
		$offset = ($page - 1) * $pageSize;
		$strFields = implode(',', $fields);
		$sql = "select $strFields from orders 
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
	
	
	/**
	 * orders, usr: 查询，根据orders的code
	 * @param string $orderCode
	 * @param array(string) $fields
	 */
	public static function getOrderAndBuyerByOrderCode($orderCode, $fsOrder = array('*'), $fsUsr = array('*')) {
		if (!is_string($orderCode) || !is_array($fsOrder) || !is_array($fsUsr)) {
			return false;
		}
		$orderCode = addslashes($orderCode);
		$sFsOrder = implode(',a.', $fsOrder);
		$sFsOrder = 'a.' . $sFsOrder;
		$sFsUsr = implode(',b.', $fsUsr);
		$sFsUsr = 'b.' . $sFsUsr;
		$sql = "select $sFsOrder, $sFsUsr
			from orders as a left join usr as b
			on a.buyer_code = b.code
			where a.code = '$orderCode'
			limit 1";
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
	 * orders, usr: 查询，根据自定义条件
	 * @param string $cond
	 * @param array(string) $fsOrder
	 * @param array(string) $fsUsr
	 */
	public static function getOrdersAndBuyersByCond($cond, $fsOrder = array('*'), $fsUsr = array()) {
		if (!is_string($cond) || !is_array($fsOrder) || !is_array($fsUsr)) {
			return false;
		}
		$sFsOrder = implode(',a.', $fsOrder);
		$sFsOrder = 'a.' . $sFsOrder;
		$sFs = $sFsOrder;
		if (!empty($fsUsr)) {
			$sFsUsr = implode(',b.', $fsUsr);
			$sFsUsr = 'b.' . $sFsUsr;
			$sFs .= ',' . $sFsUsr;
		}
		$sql = "select $sFs
			from orders as a left join usr as b
			on a.buyer_code = b.code
			where $cond";
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
	 * orders: 查询，根据自定义条件cond
	 * @param string $cond
	 */
	public static function existOrderByCond($cond) {
		if (!is_string($cond)) {
			return false;
		}
		$sql = "select 1 from orders where exist $cond";
		$db = Zend_Registry::get('DB');
		try {
			$result = $db->fetchRow($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		if (!empty($result)) {
			return true;
		}
		return FALSE;
	}
	
	
	
} //End: class OrderService