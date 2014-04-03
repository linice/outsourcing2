<?php
include_once 'LogService.php';
include_once 'UtilService.php';
include_once 'BaseService.php';
include_once 'ResumeService.php';


class UsrService {
	/**
	 * usr: 增加
	 * @param array(field => value) $row
	 */
	public static function addUsr($row = array()) {
		if (!is_array($row)) {
			return false;
		}
		$db = Zend_Registry::get('DB');
		try {
			//$db->insert的返回值是影响数据库表记录的行数，一般情况下为1；
			//测试时，当即将插入的记录与已有的某一记录的primary key重复时，会产生异常，但不会终止执行。
			$db->insert('usr', $row);
			
			//sql: SELECT @@identity as id，当id自己设定时，不能正确返回最后插入记录的id；
			//当id为数据库系统自增时，可以正确返回最后插入记录的id
//			$sqlSelect = "SELECT @@identity as id";
//			$result = $db->fetchAll($sqlSelect);
			$lastInsertId = $db->lastInsertId();
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "row: $jsRow, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return $lastInsertId;
//		return $result[0]['id'];
	}
	
	
	/**
	 * usr: 增加
	 * @param string $code
	 * @param array(field => value) $row
	 */
	public static function saveUsrByCode($code, $row) {
		if (!is_string($code)) {
			return false;
		}
		if (!is_array($row)) {
			return false;
		}
		$code = addslashes($code);
		$db = Zend_Registry::get('DB');
		$usr = self::getUsrByCode($code, array(1));
		if (empty($usr)) {
			$result = self::addUsr($row);
		} else {
			$result = self::updateUsrByCode($code, $row);
		}
		return $result;
	}
	
	
	/**
	 * usr: 修改
	 * @param array(field => value) $row
	 */
	public static function updateUsrByCode($code, $row = array()) {
		if (empty($code) || !is_string($code)) {
			return false;
		}
		if (!is_array($row)) {
			return false;
		}
		$db = Zend_Registry::get('DB');
		$where = $db->quoteInto('code = ?', $code);
		try {
			$db->update('usr', $row, $where);
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "row: $jsRow, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}

	
	/**
	 * usr: 修改
	 * @param array(field => value) $row
	 */
	public static function updateUsrByEmail($email, $row = array()) {
		if (!is_string($email) || !UtilService::isEmail($email)) {
			return false;
		}
		if (!is_array($row)) {
			return false;
		}
		$db = Zend_Registry::get('DB');
		$where = $db->quoteInto('email = ?', $email);
		try {
			$db->update('usr', $row, $where);
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "row: $jsRow, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}

	
	/**
	 * 在用户表里验证邮箱是否被注册
	 * @param string $email
	 */
	public static function existEmail($email) {
		$result = self::getUsrByEmail($email, array(1));
		if (!empty($result)) {
			return true;
		}
		return false;
	}
	
	
	/**
	 * usr: 查询，根据email
	 * @param string $code
	 * @param array(string) $fields
	 */
	public static function getUsrByCode($code, $fields = array('*')) {
		if (!is_string($code)) {
			return false;
		}
		if (!is_array($fields)) {
			return false;
		}
		$db = Zend_Registry::get('DB');
		$strFields = implode(',', $fields);
		$sql = "select $strFields from usr where code = '$code' limit 1";
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
	 * usr: 查询，根据email
	 * @param string $email
	 * @param array(string) $fields
	 */
	public static function getUsrByEmail($email, $fields = array('*')) {
		if (!is_string($email)) {
			return false;
		}
		if (!is_array($fields)) {
			return false;
		}
		$cond = " email = '$email' ";
		return BaseService::getRowByCond('usr', $cond, $fields);
	}
	
	
	/**
	 * usr：查询，根据email，passwd
	 * @param string $email
	 * @param string $passwd
	 * @param array(string) $fields
	 */
	public static function getUsrByEmailAndPasswd($email, $passwd, $fields=array('*')) {
		if (!is_string($email) || !is_string($passwd)) {
			return FALSE;
		}
		
		$strFields = implode(',', $fields);
		$email = addslashes($email);
		$passwd = addslashes($passwd);
		$sql = "select $strFields from usr
			where email = '$email'
				and passwd = '$passwd'
				and enabled = 'Y'";
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
	 * 更新最近登陆日期为当前日期
	 * @param unknown_type $usr_id
	 */
	public static function refreshLastLoginDate($usr_id) {
		if (empty($usr_id)) return FALSE;
		$last_login_time = date('Y-m-d H:i:s');
		$row = array("last_login_time"=>$last_login_time);
		if (BaseService::updateByPrimaryKey("usr", $usr_id, $row) != FALSE) {
			return $last_login_time;
		};
		return FALSE;
	}
	
	
	/**
	 * usr：查询法人数量
	 * @param string $roleCode
	 */
	public static function getUsrsCntByRoleCode($roleCode) {
		if (!is_string($roleCode)) {
			return FALSE;
		}
		$roleCode = addslashes($roleCode);
		$sql = "select count(*) as cnt
			FROM usr
			where role_code = '$roleCode'";
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
	 * usr：查询法人数量
	 * @param string $roleCode
	 * @param string $fullname
	 */
	public static function getUsrsCntByRoleCodeAndLikeFullname($roleCode, $fullname) {
		if (!is_string($roleCode)) {
			return FALSE;
		}
		if (!is_string($fullname)) {
			return FALSE;
		}
		$roleCode = addslashes($roleCode);
		$fullname = addslashes($fullname);
		$sql = "select count(*) as cnt
			FROM usr
			where role_code = '$roleCode'
				and fullname like '%$fullname%'";
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
	 * usr：查询法人
	 * @param int $page
	 * @param int $pageSize
	 * @param string $roleCode
	 * @param array(string) $fields
	 */
	public static function getUsrsByPageAndRoleCode($page, $pageSize, $roleCode, $fields = array('*')) {
		if (!is_numeric($page)) {
			return false;
		}
		if (!is_numeric($pageSize)) {
			return false;
		}
		if (!is_string($roleCode)) {
			return false;
		}
		if (!is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$offset = ($page - 1) * $pageSize;
		$sql = "select $strFields
			FROM usr
			where role_code = '$roleCode'
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
	 * usr：查询法人
	 * @param int $page
	 * @param int $pageSize
	 * @param string $roleCode
	 * @param array(string) $fields
	 */
	public static function getUsrsByPageAndRoleCodeAndLikeFullname($page, $pageSize, $roleCode, $fullname, $fields = array('*')) {
		if (!is_numeric($page)) {
			return false;
		}
		if (!is_numeric($pageSize)) {
			return false;
		}
		if (!is_string($roleCode)) {
			return false;
		}
		if (!is_string($fullname)) {
			return false;
		}
		if (!is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$offset = ($page - 1) * $pageSize;
		$sql = "select $strFields
			FROM usr
			where role_code = '$roleCode'
				and fullname like '%$fullname%'
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
	 * usr: 查询，根据自定义条件cond
	 * @param string $cond
	 * @param array(string) $fields
	 */
	public static function getUsrsByCond($cond, $fields = array('*')) {
		if (!is_string($cond) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$sql = "select $strFields from usr where $cond";
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
	 * 关于法人的综合查询方法
	 * @param unknown_type $option
	 * @param unknown_type $field
	 * @param unknown_type $page
	 */
	public static function findUsrByOption($option, $fields=array('*'), $page=NULL, $orderBy=NULL) {
		if (!is_array($option)) return FALSE;
		if (!isset($option['enabled'])) {
			array_push($option, "enabled='Y'");
		} else {
			array_push($option, "enabled='".$option['enabled']."'");
			unset($option['enabled']);
		}
		array_push($option, "role_code='MEMBER'");
		$strFields = implode(',', $fields);
		$sql = "select $strFields from usr where ";
		$whereOption = implode(' and ', $option);
		$sql = $sql.$whereOption;
		if (!empty($orderBy)) {
			$sql = $sql.' order by '.$orderBy;
		}
		//var_dump($sql);
		if (empty($page)) {
			return BaseService::fetchAllBySql($sql);
		} else {
			return BaseService::findByPaginationWithSQL($sql, $page);
		}
	}
	
	
	public static function findNewRegUsr($page) {
		$sql = 'select r.*, get_resume_biz_info(r.code) biz_info 
			from usr u inner join resume r 
			on u.code = r.talent_code
			where (To_Days(now())-To_Days(u.create_time)) <= 30 
			order by bid_points, u.create_time desc';
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
	
	
	/**
	 * usr, resume：修改邮箱
	 * 需要修改usr表的登陆邮箱，收件邮箱；以及resume表的邮箱
	 * @param string $email
	 * @param string $newEmail
	 */
	public static function changeEmail($email, $newEmail) {
		if (empty($email) || empty($newEmail)) {
			return false;
		}
		$db = BaseService::getDb();
		$db->beginTransaction();
		$upUsr = array('email' => $newEmail, 'email_consignee' => $newEmail);
		$result = BaseService::updateByCond('usr', $upUsr, "email = '{$email}'");
		if ($result) {
			$result2 = BaseService::updateByCond('resume', array('email' => $newEmail), "email = '$newEmail'");
			if ($result2) {
				$db->commit();
				return TRUE;
			}
		}
		$db->rollback();
		return FALSE;
	}
	
	
	/**
	 * 判断人才是否是给定法人的员工
	 */
	public static function isEmpOfLP($talentCode, $lpCode) {
		$rsm = ResumeService::getRsmByTalentCode($talentCode, array('lp_code'));
		if (!empty($rsm) && $rsm['lp_code'] == $lpCode) {
			return true;
		}
		return false;
	}
	
	
	
	
	
} //End: class UsrService