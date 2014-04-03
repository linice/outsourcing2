<?php
/**
 * 查询函数标准：
 * 把$fields放在最后的原因，字段可以为默认值：array('*')
 * public function getAllByCond($table, $cond, $fields) 
 * 
 * 修改变量标准：
 * public function updateByCond($table, $row, $cond)
 * 
 * $cond: 不管什么情况，字符串前后，都空一格
 */


include_once 'LogService.php';


class BaseService {
	/**
	 * 取得DB对象
	 */
	public static function getDb() {
		return Zend_Registry::get('DB');
	}
	
	
	/**
	 * 保存一条数据
	 * @param unknown_type $table 表名
	 * @param unknown_type $row 需要保存数据的列
	 */
	public static function save($table, $row=array()) {
		if (empty($table) || !is_array($row)) {
			return false;
		}
		$db = self::getDb();
		try {
			$db->insert($table, $row);
			
			$lastInsertId = $db->lastInsertId();
			if (empty($lastInsertId)) {
				return true; //有些表没有自增长主键
			}
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "table: $table, row: $jsRow, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $lastInsertId;
	}
	
	
	/**
	 * 根据主键ID修改数据
	 * @param unknown_type $table 表名
	 * @param unknown_type $id	主键ID
	 * @param unknown_type $row	需要修改数据的列
	 */
	public static function updateByPrimaryKey($table, $id, $row=array()) {
		if (empty($table) || empty($id) || !is_array($row)) {
			return false;
		}
		$db = self::getDb();
		$where = $db->quoteInto('id = ?', $id);
		try {
			$db->update($table, $row, $where);
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "row: $jsRow, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * 根据where条件修改数据
	 * @param unknown_type $table
	 * @param unknown_type $where
	 * @param unknown_type $row
	 */
	public static function update($table, $where, $row=array()) {
		if (empty($table) || empty($where) || !is_array($row)) {
			return false;
		}
		$db = self::getDb();
		try {
			$db->update($table, $row, $where);
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "table: $table, row: $jsRow, where: $where, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * 删除数据
	 * @param unknown_type $table
	 * @param unknown_type $ids
	 */
	public static function deleteByPrimaryKey($table, $ids) {
		if (empty($table) || empty($ids)) {
			return false;
		}
		$db = self::getDb();
		if (is_array($ids)) {
			$where = $db->quoteInto('id in (?)', $ids);
		} else {
			$where = $db->quoteInto('id = ?', $ids);
		} 
		try {
			$db->delete($table, $where);
		} catch (Exception $e) {
			$jsId = is_array($ids) ? implode(',', $ids) : $ids; 
			$log = array('level' => 3, 'msg' => "ids: $jsId, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * 删除数据
	 * @param unknown_type $table
	 * @param unknown_type $ids
	 */
	public static function delete($table, $where) {
		if (empty($table) || empty($where)) {
			return false;
		}
		$db = self::getDb();
		try {
			$db->delete($table, $where);
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "row: $jsRow, where: $where, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * 查找一个表的所有数据
	 * @param unknown_type $table
	 * @param unknown_type $fields
	 */
	public static function findAll($table, $fields = array('*')) {
		if (empty($table)) {
			return false;
		}
		$db = self::getDb();
		$strFields = implode(',', $fields);
		$sql = "select $strFields from $table t ";
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
	 * 根据ID查找表中的一条数据
	 * @param unknown_type $table
	 * @param unknown_type $id
	 * @param unknown_type $fields
	 */
	public static function findByPrimaryKey($table, $id, $fields = array('*')) {
		if (empty($table) || empty($id)) {
			return false;
		}
		$db = self::getDb();
		$strFields = implode(',', $fields);
		$sql = "select $strFields from $table t where t.id='$id'";
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
	 * 根据SQL查找一条数据
	 * @param unknown_type $sql
	 */
	public static function fetchRowBySql($sql) {
		$db = self::getDb();
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
	 * 根据SQL查找一条数据
	 * @param unknown_type $sql
	 */
	public static function fetchAllBySql($sql) {
		$db = self::getDb();
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
	 * 根据SQL查找一条数据
	 * @param unknown_type $sql
	 */
	public static function fetchOneBySql($sql) {
		$db = self::getDb();
		try {
			$result = $db->fetchone($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	/**
	 * 根据分页数据查找当前页的数据
	 * @param unknown_type $fields 需要查找的字段，可是以字符串也可以是一个array
	 * @param unknown_type $tables 需要查找的表，如果有关联表则连同join on条件也在内，需要是字符串
	 * @param unknown_type $where 查询条件，字符串
	 * @param unknown_type $page 分页的数据
	 */
	public static function findByPagination($fields, $tables, $where, $page) {
		if (is_array($fields)) 
			$fields = implode(',', $fields);
		$tw = $tables.' '.$where;
		$limit = " limit ".$page['start'].", ".$page['limit'];
		try {
			$sql = 'select '.$fields.' from '.$tw;
			$sqlcount = 'select count(1) count from ('.$sql.') tmp';
			if (!empty($page['sortname']) && !strripos($where, 'order by')) {
				if (empty($page['sortorder'])) {
					$page['sortorder'] = 'ASC';
				}
				$orderBy = ' order by '.$page['sortname'].' '.$page['sortorder'];
				$sql = $sql.$orderBy;
			}
			$sql = $sql.$limit;
			//var_dump($sql);
			$result = self::fetchAllBySql($sql);
			$count = self::fetchOneBySql($sqlcount);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return array("Total"=> $count, "Rows"=> $result);
	}

	
	/**
	 * 根据分页数据查找当前页的数据
	 * @param unknown_type $fields 需要查找的字段，可是以字符串也可以是一个array
	 * @param unknown_type $tables 需要查找的表，如果有关联表则连同join on条件也在内，需要是字符串
	 * @param unknown_type $where 查询条件，字符串
	 * @param unknown_type $page 分页的数据
	 */
	public static function findByPaginationWithSQL($sql, $page) {
		$countSql = 'select count(1) as count from ('.$sql.') tmp';
		/*if (isset($page['sortname']) && !empty($page['sortname'])) {
			$sql = 'select * from ('.$sql.') _tmp order by '.$page['sortname'].' '.$page['sortorder'];
		}*/
		$sql = $sql.' limit '.$page['start'].', '.$page['limit'];
		$result = self::fetchAllBySql($sql);
		$count = self::fetchOneBySql($countSql);
		return array("Total"=> $count, "Rows"=> $result);
	}
	
	
	public static function arrayToWhere($field, $value=array()) {
		if(!empty($value)){
			if (is_string($value) && $value != 'null' && $value != 'NULL') {
				$value = explode(',', $value);
			}
			if (is_array($value)) {
				$where="(";
				$i=1;
				foreach($value as $val){
					if($i > 1)
						$where.=" or ";
					$where.="{$field} like '%$val%'";
					$i++;
				}
				$where.=")";
				return $where;
			}
			return '1';
		} else {
			return '1';
		}
	}
	
	
	/*****************los2 add*****************************/
	/**************add, del, update, get：即顺序为增，删，改，查****************/
	/**
	 * 执行插入类型的sql
	 * @param string $sql
	 */
	public static function exeAddSql($sql) {
		if (empty($sql)) {
			return false;
		}
		//log
		$log = array('level' => 1, 'msg' => "sql: $sql", 'class' => __CLASS__, 'func' => __FUNCTION__);
		LogService::saveLog($log);
		$db = self::getDb();
		try {
			$db->query($sql);
			$lastInsertId = $db->lastInsertId();
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		if ($lastInsertId) {
			return $lastInsertId;
		}
		return true; //有些表没有自增长主键
	}
	
	
	/**
	 * 执行删除类型的sql
	 * @param string $sql
	 */
	public static function exeDelSql($sql) {
		if (empty($sql)) {
			return false;
		}
		//log
		$log = array('level' => 1, 'msg' => "sql: $sql", 'class' => __CLASS__, 'func' => __FUNCTION__);
		LogService::saveLog($log);
		$db = self::getDb();
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
	 * 执行更新类型的sql
	 * @param string $sql
	 */
	public static function exeUpSql($sql) {
		if (empty($sql)) {
			return false;
		}
		//log
		$log = array('level' => 1, 'msg' => "sql: $sql", 'class' => __CLASS__, 'func' => __FUNCTION__);
		LogService::saveLog($log);
		$db = self::getDb();
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
	 * 保存一条数据
	 * @param string $table: 表名
	 * @param array(field => value) $row: 需要保存数据的列
	 */
	public static function addRow($table, $row) {
		if (empty($table) || !is_array($row)) {
			return false;
		}
		$db = self::getDb();
		try {
			$db->insert($table, $row);
			$lastInsertId = $db->lastInsertId();
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "table: $table, row: $jsRow, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		if ($lastInsertId) {
			return $lastInsertId;
		}
		return true; //有些表没有自增长主键
	}
	
	
	/**
	 * talbe: 新增多条记录
	 * @param string $table
	 * @param array(array(field => value)) $rows
	 */
	public static function addRows($table, $rows) {
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
		$sql = "insert $table($strFields) values ";
		foreach ($rows as $r) {
			foreach ($r as &$v) {
				$v = addslashes($v);
			}
			$sql .= "('" . implode("','", $r) . "'),";
		}
		$sql = rtrim($sql, ', ');
		return self::exeAddSql($sql);
	}
	
	
	/**
	 * 删除数据，根据ID，单条记录
	 * @param string $table
	 * @param int $id
	 */
	public static function delById($table, $id) {
		if (empty($table) || empty($id)) {
			return false;
		}
		$db = self::getDb();
		$cond = $db->quoteInto('id = ?', $id);
		try {
			$db->delete($table, $cond);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "table: $table, id: $id, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * 删除数据，根据IDs，批量
	 * @param string $table
	 * @param array(int) $ids
	 */
	public static function delByIds($table, $ids) {
		if (empty($table) || !is_array($ids)) {
			return false;
		}
		$db = self::getDb();
		$cond = $db->quoteInto('id in (?)', $ids);
		try {
			$db->delete($table, $cond);
		} catch (Exception $e) {
			$jsIds = json_encode($ids);
			$log = array('level' => 3, 'msg' => "table: $table, ids: $jsIds, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * 删除数据，根据自定义条件
	 * @param string $table
	 * @param string $cond
	 */
	public static function delByCond($table, $cond) {
		if (empty($table) || empty($cond)) {
			return false;
		}
		$db = self::getDb();
		try {
			$db->delete($table, $cond);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "table: $table, where: $cond, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * 根据主键ID修改数据
	 * @param string $table: 表名
	 * @param array(field => value) $row:	需要修改数据的列
	 * @param int $id:	主键ID
	 */
	public static function updateById($table, $row, $id) {
		if (empty($table) || !is_array($row) || empty($id)) {
			return false;
		}
		$db = self::getDb();
		$cond = $db->quoteInto('id = ?', $id);
		try {
			$db->update($table, $row, $cond);
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "table: $table, row: $jsRow, id: $id, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * 根据code修改数据
	 * @param string $table: 表名
	 * @param array(field => value) $row:	需要修改数据的列
	 * @param string $code:	唯一键code
	 */
	public static function updateByCode($table, $row, $code) {
		if (empty($table) || !is_array($row) || empty($code)) {
			return false;
		}
		$db = self::getDb();
		$cond = $db->quoteInto('code = ?', $code);
		try {
			$db->update($table, $row, $cond);
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "table: $table, row: $jsRow, code: $code, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * 根据where条件修改数据
	 * @param string $table
	 * @param array(field => value) $row
	 * @param string $cond
	 */
	public static function updateByCond($table, $row, $cond) {
		if (empty($table) || !is_array($row) || empty($cond)) {
			return false;
		}
		$db = self::getDb();
		try {
			$db->update($table, $row, $cond);
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "table: $table, row: $jsRow, cond: $cond, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * 查找一个表的所有数据
	 * @param string $table
	 * @param array(string) $fields
	 */
	public static function getAll($table, $fields = array('*')) {
		if (empty($table) || !is_array($fields)) {
			return false;
		}
		$db = self::getDb();
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
	
	
	/**
	 * 根据ID查找表中的一条数据
	 * @param string $table
	 * @param int $id
	 * @param array(string) $fields
	 */
	public static function getRowById($table, $id, $fields = array('*')) {
		if (empty($table) || empty($id) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$sql = "select $strFields from $table where id = '$id'";
		$db = self::getDb();
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
	 * 根据CODE查找表中的一条数据
	 * @param string $table
	 * @param string $code
	 * @param array(string) $fields
	 */
	public static function getRowByCode($table, $code, $fields = array('*')) {
		if (empty($table) || empty($code) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$code = addslashes($code);
		$sql = "select $strFields from $table where code = '$code'";
		$db = self::getDb();
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
	 * 根据SQL查找一条数据
	 * @param string $sql
	 */
	public static function getRowBySql($sql) {
		$db = self::getDb();
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
	 * 根据SQL查找多条数据
	 * @param string $sql
	 */
	public static function getAllBySql($sql) {
		$db = self::getDb();
		try {
			//los2 log
//			$log = array('level' => 1, 'msg' => "sql: $sql", 'class' => __CLASS__, 'func' => __FUNCTION__);
//			LogService::saveLog($log);
			$result = $db->fetchAll($sql);
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
	public static function getOneBySql($sql) {
		$db = self::getDb();
		try {
			$result = $db->fetchOne($sql);
		} catch (Exception $e) {
			$log = array('level' => 3, 'msg' => "sql: $sql, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $result;
	}
	
	
	/**
	 * 根据Code查找一条数据的一个字段
	 * @param string $table 需要查找的表
	 * @param string $code
	 * @param string $field
	 */
	public static function getOneByCode($table, $code, $field) {
		if (empty($table) || empty($code) || empty($field)) {
			return FALSE;
		}
		$sql = "select $field from $table where code = '$code' limit 1";
		return self::getOneBySql($sql);
	}
	
	
	/**
	 * 根据给定条件，查找数据
	 * @param string $tables 需要查找的表
	 * @param string $field: 需要查找的字段
	 * @param string $cond: 查询条件
	 */
	public static function getOneByCond($table, $field, $cond) {
		if (empty($table) || empty($field) || empty($cond)) {
			return FALSE;
		}
		$sql = "select $field from $table where $cond limit 1";
		return self::getOneBySql($sql);
	}
	
	
	/**
	 * 根据给定条件，查找数据
	 * @param string $table 需要查找的表
	 * @param array(string) $fields: 需要查找的字段
	 * @param string $cond: 查询条件
	 */
	public static function getAllByCond($table, $fields, $cond) {
		if (empty($table) || !is_array($fields) || empty($cond)) {
			return FALSE;
		}
		$sFs = implode(',', $fields);
		$sql = "select $sFs from $table where $cond";
		$result = self::getAllBySql($sql);
		return $result;
	}
	
	
	/**
	 * 根据给定条件，查找数据
	 * @param string $tables 需要查找的表
	 * @param string $cond: 查询条件
	 * @param array(string) $fields: 需要查找的字段
	 */
	public static function getRowByCond($table, $cond, $fields) {
		if (empty($table) || !is_array($fields) || empty($cond)) {
			return FALSE;
		}
		$sFs = implode(',', $fields);
		$sql = "select $sFs from $table where $cond limit 1";
		$result = self::getRowBySql($sql);
		return $result;
	}
	
	
	/**
	 * 根据分页数据查找当前页的数据
	 * @param string $tables 需要查找的表
	 * @param array(string) $fields: 需要查找的字段
	 * @param string $cond: 查询条件
	 * @param array(int) $pg: 分页的数据, $pg = array('start' => 10, 'limit' => 10)
	 */
	public static function getByPageWithCond($table, $fields, $cond, $pg) {
		if (empty($table) || !is_array($fields) || empty($cond) || !is_array($pg)) {
			return FALSE;
		}
		$sFs = implode(',', $fields);
		$pgSql = "select $sFs from $table where $cond limit {$pg['start']}, {$pg['limit']}";
		$cntSql = "select count(*) as cnt from $table where $cond";
		$result = self::getAllBySql($pgSql);
		$cnt = self::getOneBySql($cntSql);
		return array("Total"=> $cnt, "Rows"=> $result);
	}

	
	/**
	 * 根据分页数据查找当前页的数据
	 * @param string $sql: 查询语句，例：select * from usr
	 * @param array(int) $pg: 分页的数据
	 */
	public static function getByPageWithSql($sql, $pg) {
		$idx = stripos($sql, 'from');
		$cntSql = 'select count(*) as cnt ' . substr($sql, $idx);
		$pgSql = "$sql limit {$pg['start']}, {$pg['limit']}";
		$cnt = self::getOneBySql($cntSql);
		$result = self::getAllBySql($pgSql);
		return array('Total' => $cnt, 'Rows' => $result);
	}
	
	
	
} //End: class BaseService