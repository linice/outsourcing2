<?php
/**
 * BaseService2相对于BaseService的区别：结合使用memcache
 * 
 * 查询函数标准：
 * 把$fields放在最后的原因，字段可以为默认值：array('*')
 * public function getAllByCond($table, $cond, $fields) 
 * 
 * 修改变量标准：
 * public function updateByCond($table, $row, $cond)
 */


include_once 'LogService.php';


class BaseService2 {
	/**
	 * 取得DB对象
	 * $dbVal的取值为数据库的全局变量名:DB, DB_MYTEST
	 */
	public static function getDb($dbVal = 'DB') {
		return Zend_Registry::get($dbVal);
	}
	
	
	/**
	 * 取得MC对象
	 */
	public static function getMc() {
		return Zend_Registry::get('MC');
	}
	
	
	/**
	 * 根据SQL查找多条数据
	 * @param string $sql
	 */
	public static function getAllBySql($sql, $delMc = FALSE) {
		$db = self::getDb();
		$mc = self::getMc();
		$mcKey = md5($sql);
		
		//如果$delMc为true，则表示在更新了数据库信息时，删除mc变量
		if ($delMc) {
			return $mc->delete($mcKey, 0);
		}
		
		//查询mc变量，如果失败，再查询数据库，同时设置mc变量
		try {
			$result = $mc->get($mcKey);
			if ($result === false) {
				$result = $db->fetchAll($sql);
				if ($result) {
					$mc->set($mcKey, $result, 0, 3600);
				}
			}
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	/**
	 * 根据SQL查找一条数据的一个字段
	 * @param string $sql
	 */
	public static function getOneBySql($sql, $delMc = FALSE) {
		$db = self::getDb();
		$mc = self::getMc();
		$mcKey = md5($sql);
		
		//如果$delMc为true，则表示在更新了数据库信息时，删除mc变量
		if ($delMc) {
			return $mc->delete($mcKey);
		}
		
		//查询mc变量，如果失败，再查询数据库，同时设置mc变量
		try {
			$result = $mc->get($mcKey);
			if ($result === false) {
				$result = $db->fetchOne($sql);
				if ($result) {
					$mc->set($mcKey, $result, 0, 3600);
				}
			}
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	/*
	 * 查找一个表的所有数据
	 * @param string $dbVal，数据库标识
	 * @param string $table，表名
	 * @param array(string) $fields，字段
	 */
	public static function getAll($dbVal, $table, $fields = array('*')) {
		if (empty($table) || !is_array($fields)) {
			return false;
		}
		$db = self::getDb($dbVal);
		$strFields = implode(',', $fields);
		$sql = "select $strFields from $table";
		try {
			$result = $db->fetchAll($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	
	
	
} //End: class BaseService2