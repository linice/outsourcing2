<?php
include_once 'ActiveEtcService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';
include_once 'LogService.php';
include_once 'UtilService.php';


class ResumeService {
	private static $auth = NULL;
	
	
	/**
	 * resume_prj: 先删除旧的简历——项目经验，然后再保存
	 * @param array(array(field => value)) $rows
	*/
	public static function saveResumePrj($rows = array()) {
		if (!is_array($rows)) {
			return false;
		}
		$row = current($rows);
		$resumeCode = $row['resume_code'];
		$db = Zend_Registry::get('DB');
		$db->beginTransaction();
		$result = self::delResumePrjByResumeCode($resumeCode);
		$result2 = BaseService::addRows('resume_prj', $rows);
		if ($result === FALSE || $result2 === FALSE) {
			$db->rollback();
			return false;
		} else {
			$db->commit();
			return TRUE;
		}
	}
	
	
	/**
	 * resume: 更新
	 * @param string $code: resume的唯一编码
	 * @param array(field => value) $row
	 */
	public static function saveResumeByCode($code, $row = array()) {
		if (empty($code) || !is_string($code)) {
			return FALSE;
		}
		if (empty($row) || !is_array($row)) {
			return false;
		}
		$resume = self::getResumeByCode($code, array(1));
		$db = Zend_Registry::get('DB');
		if (empty($resume)) {
			if (empty(self::$auth)) {
				self::$auth = new Zend_Session_Namespace('AUTH');
			}
			$row['create_usr_code'] = self::$auth->usr['code'];
			$row['create_time'] = date('Y-m-d H:i:s');
			return BaseService::addRow('resume', $row);
		} else {
			return self::updateResumeByCode($code, $row);
		}
	}
	
	
	/**
	 * resume_biz: 先删除旧的简历——业务，然后再保存
	 * @param array(array(field => value)) $rows
	*/
	public static function saveResumeBiz($rows = array()) {
		if (!is_array($rows)) {
			return false;
		}
		$row = current($rows);
		$resumeCode = $row['resume_code'];
		$db = Zend_Registry::get('DB');
		$db->beginTransaction();
		$result = self::delResumeBizByResumeCode($resumeCode);
		$result2 = BaseService::addRows('resume_biz', $rows);
		$result3 = self::updateResumeByCode($resumeCode, array('ok_biz' => 'Y'));
		if ($result === FALSE || $result2 === FALSE || $result3 === FALSE) {
			$db->rollback();
			return false;
		} else {
			$db->commit();
			return TRUE;
		}
	}
	
	
	/**
	 * resume_biz: 删除
	 * @param string $resumeCode: 简历编码
	 */
	public static function delResumeBizByResumeCode($resumeCode) {
		if (!is_string($resumeCode)) {
			return false;
		}
		$resumeCode = addslashes($resumeCode);
		$cond = "resume_code = '$resumeCode'";
		return BaseService::delByCond('resume_biz', $cond);
	}
	
	
	/**
	 * resume_prj: 删除
	 * @param string $resumeCode: 简历编码
	 */
	public static function delResumePrjByResumeCode($resumeCode) {
		if (!is_string($resumeCode)) {
			return false;
		}
		$resumeCode = addslashes($resumeCode);
		$cond = "resume_code = '$resumeCode'";
		return BaseService::delByCond('resume_prj', $cond);
	}
	
	
	/**
	 * resume: 更新
	 * @param string $code: resume的唯一编码
	 * @param array(field => value) $row
	 */
	public static function updateResumeByCode($code, $row = array()) {
		//如果是有竞价，则扣减法人的点数
		if (!empty($row['bid_points'])) {
			$db = Zend_Registry::get('DB');
			$db->beginTransaction();
			$result = BaseService::updateByCode('resume', $row, $code);
			if ($result) {
				//扣减法人的点数
				$bid_points = $row['bid_points'];
				$lpCode = BaseService::getOneByCode('resume', $code, 'lp_code');
				$sql = "update usr as a set balance = balance - $bid_points
					where a.code = '$lpCode'
				";
				$result2 = BaseService::exeUpSql($sql);
				if ($result2) {
					$db->commit();
					return TRUE;
				}
			}
			$db->rollback();
			return FALSE;
		} else {
			$jsRow = json_encode($row);
			$log = array('level' => 1, 'msg' => "row: $jsRow, resume_code: $code", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return BaseService::updateByCode('resume', $row, $code);
		}
	}
	
	
	/**
	 * resume: 更新
	 * @param array(string) $codes: resume的唯一编码
	 * @param array(field => value) $row
	 */
	public static function updateResumeByCodes($codes, $row = array()) {
		if (!is_array($codes) || !is_array($row)) {
			return false;
		}
		$strCodes = implode("','", $codes);
		$strCodes = "'" . $strCodes . "'";
		$sql = "update resume set ";
		foreach ($row as $field => $value) {
			$field = addslashes($field);
			$value = addslashes($value);
			$sql .= " $field = '$value', ";
		}
		$sql = rtrim($sql, ', ');
		$sql .= " where code in ($strCodes)";
		return BaseService::exeUpSql($sql);
	}
	
	
	/**
	 * resume: 更新
	 * @param string $talentCode: resume的唯一编码
	 * @param array(field => value) $row
	 */
	public static function updateResumeByTalentCode($talentCode, $row = array()) {
		if (!is_string($talentCode) || !is_array($row)) {
			return false;
		}
		$talentCode = addslashes($talentCode);
		$db = Zend_Registry::get('DB');
		$cond = $db->quoteInto('talent_code = ?', $talentCode);
		try {
			$db->update('resume', $row, $cond);
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "talent_code: $talentCode, row: $jsRow, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return TRUE;
	}
	

	/**
	 * resume: 查询，根据talent_code
	 * @param string $talentCode
	 * @param array(string) $fields
	 */
	public static function getResumesByTalentCode($talentCode, $fields = array('*')) {
		if (!is_string($talentCode) || !is_array($fields)) {
			return false;
		}
		$cond = "talent_code = '$talentCode'";
		return BaseService::getAllByCond('resume', $fields, $cond);
	}
	
	
	/**
	 * resume: 查询，根据talent_code
	 * @param string $talentCode
	 * @param array(string) $fields
	 */
	public static function getRsmByTalentCode($talentCode, $fields = array('*')) {
		if (empty($talentCode) || !is_string($talentCode)) {
			return false;
		}
		if (empty($fields) || !is_array($fields)) {
			return false;
		}
		$cond = "talent_code = '$talentCode'";
		return BaseService::getRowByCond('resume', $cond, $fields);
	}
	
	
	/**
	 * resume: 查询，根据简历编码code
	 * @param string $code
	 * @param array(string) $fields
	 */
	public static function getResumeByCode($code, $fields = array('*')) {
		if (empty($code) || !is_string($code)) {
			return false;
		}
		if (empty($fields) || !is_array($fields)) {
			return false;
		}
		$code = addslashes($code);
		$cond = "code = '$code'";
		return BaseService::getRowByCond('resume', $cond, $fields);
	}
	

	/**
	 * resume_biz: 查询，根据简历编码code
	 * @param string $resumeCode
	 * @param array(string) $fields
	 */
	public static function getResumeBizsByResumeCode($resumeCode, $fields = array('*')) {
		if (!is_string($resumeCode)) {
			return false;
		}
		if (!is_array($fields)) {
			return false;
		}
		$resumeCode = addslashes($resumeCode);
		$cond = "resume_code = '$resumeCode'";
		return BaseService::getAllByCond('resume_biz', $fields, $cond);
	}
	
	
	/**
	 * resume_biz: 查询，根据简历编码code
	 * @param string $rsmCode
	 * @param array(string) $fields
	 */
	public static function getRsmBizsByRsmCodeForTalentDetail($rsmCode, $fields = array('*')) {
		if (empty($rsmCode) || !is_string($rsmCode)) {
			return false;
		}
		if (empty($fields) || !is_array($fields)) {
			return false;
		}
		$rsmCode = addslashes($rsmCode);
		$sFs = implode(', ', $fields);
		$sql = "select $sFs from resume_biz where resume_code = '$rsmCode' order by level, type, biz";
		return BaseService::getAllBySql($sql);
	}
	
	
	/**
	 * resume_prj: 查询，根据简历编码code
	 * @param string $resumeCode
	 * @param array(string) $fields
	 */
	public static function getResumePrjsByResumeCode($resumeCode, $fields = array('*')) {
		if (!is_string($resumeCode)) {
			return false;
		}
		if (!is_array($fields)) {
			return false;
		}
		$resumeCode = addslashes($resumeCode);
		$cond = "resume_code = '$resumeCode'";
		return BaseService::getAllByCond('resume_prj', $fields, $cond);
	}
	
	
	/**
	 * resume：查询法人所属员工
	 * @param int $page
	 * @param int $pageSize
	 * @param string $cond：以参数形式传递的变化的查询条件，$cond本身用and连接
	 * @param array(string) $fields
	 */
	public static function getResumesByPageAndCond($page = 1, $pageSize = 20, $cond = '', $fields = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($cond) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$offset = ($page - 1) * $pageSize;
		$sql = "select $strFields
			FROM resume
			where $cond
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
	 * resume: 查询
	 * @param string $cond：以参数形式传递的变化的查询条件，$cond本身用and连接
	 * @param array(string) $fields
	 */
	public static function getResumesByCond($cond = '', $fields = array('*')) {
		if (!is_string($cond) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$sql = "select $strFields from resume
			where $cond";
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
	 * resume, resume_biz: 查询，根据自定义条件cond
	 * @param string $cond
	 * @param array(string) $fields
	 */
	public static function getRmAndRmBizByCond($cond, $fieldsRm = array('*'), $fieldsRmBiz = array('*')) {
		if (!is_string($cond) || !is_array($fieldsRm) || !is_array($fieldsRmBiz)) {
			return false;
		}
		$strFieldsRm = implode(',a.', $fieldsRm);
		$strFieldsRm = 'a.' . $strFieldsRm;
		$strFieldsRmBiz = implode(',b.', $fieldsRmBiz);
		$strFieldsRmBiz = 'b.' . $strFieldsRmBiz;
		$sql = "select $strFieldsRm, $strFieldsRmBiz
			from resume as a left join resume_biz as b
			on a.code = b.resume_code
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
	 * resume, resume_biz: 查询，根据自定义条件cond
	 * @param int $page
	 * @param int $pageSize
	 * @param string $cond
	 * @param array(string) $fields
	 */
	public static function getRmAndRmBizByPageAndCond($page, $pageSize, $cond, $fieldsRm = array('*'), $fieldsRmBiz = array('*')) {
		if (!is_numeric($page) || !is_numeric($pageSize) || !is_string($cond) || !is_array($fieldsRm) || !is_array($fieldsRmBiz)) {
			return false;
		}
		$offset = ($page - 1) * $pageSize;
		$strFieldsRm = implode(',a.', $fieldsRm);
		$strFieldsRm = 'a.' . $strFieldsRm;
		$strFieldsRmBiz = implode(',b.', $fieldsRmBiz);
		$strFieldsRmBiz = 'b.' . $strFieldsRmBiz;
		$sql = "select $strFieldsRm, $strFieldsRmBiz
			from resume as a left join resume_biz as b
			on a.code = b.resume_code
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
	 * 根据简历代码查找简历
	 * @param unknown_type $codes
	 */
	public static function getResumesByCodes($codes) {
		$resumeArray = explode(',', $codes);
		$sql = BaseService::getDb()->quoteInto("select r.*, get_resume_biz_info(r.code) biz_info from resume r where r.code in (?)", $resumeArray);
		return BaseService::fetchAllBySql($sql);
	}
	
	/**
	 * 为指定案件查找可以应聘的所有人员，管理员应聘界面查询
	 * @param unknown_type $case_code
	 * @param unknown_type $searchFields
	 * @param unknown_type $page
	 */
	public static function findAllUsrForCaseApply($searchFields, $page) {
		$sql = "select r.*, get_resume_biz_info(r.code) biz_info from resume r where r.enabled='Y'";
		if (!empty($searchFields)) {
			$sql = $sql.' and '.self::genSearchFields($searchFields, 'r');
		}
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
	
	private static function genSearchFields($searchFields, $st = 't') {
		if (empty($searchFields) || !is_array($searchFields)) return '1';
		$db = BaseService::getDb();
		$range = $searchFields['range'];
		$value = $searchFields['sValue'];
		$where = '';
		switch ($range) {
			case 'usr_code': $where = "$st.talent_code like '%$value%'";break;
			case 'usr_name': $where = "$st.fullname like '%$value%'";break;
			case 'tel': $where = "$st.tel like '%$value%'";break;
			case 'OS': $where = $db->quoteInto("exists(select 1 from resume_biz rb where rb.resume_code = $st.code and type='$range' and rb.biz in (?))", explode(',', $value));break;
			case 'DB': $where = $db->quoteInto("exists(select 1 from resume_biz rb where rb.resume_code = $st.code and type='$range' and rb.biz in (?))", explode(',', $value));break;
			case 'FRAMEWORK': $where = $db->quoteInto("exists(select 1 from resume_biz rb where rb.resume_code = $st.code and type='$range' and rb.biz in (?))", explode(',', $value));break;
			case 'BIZ': $where = $db->quoteInto("exists(select 1 from resume_biz rb where rb.resume_code = $st.code and type='$range' and rb.biz in (?))", explode(',', $value));break;
			default: $where = '1';break;
		}
		return $where;
	}
	
	
	/**
	 * 统计所有有效人才数量
	 */
	public static function cntAllValidTalents() {
		$cond = " ok_base = 'Y' and ok_biz = 'Y' and ok_prj = 'Y' and ok_other = 'Y' and is_open = 'Y' and enabled = 'Y' ";
		return BaseService::getOneByCond('resume', 'count(*)', $cond);
	}
	
	
	/**
	 * 查询推荐人才
	 */
	public static function getRecmdTalentsByPg($fsRsm, $pg) {
		$sFsRsm = UtilService::implodeArrByPrefixX($fsRsm, 'a');
		$sql = "select $sFsRsm, get_rsm_biz(a.code)
			from resume as a
			where a.bid_points > 0 
				and a.is_open = 'Y' 
				and a.enabled = 'Y' 
				and a.ok_base = 'Y' 
				and a.ok_biz = 'Y' 
				and a.ok_prj = 'Y' 
				and a.ok_other = 'Y'
			order by a.bid_points desc";
		return BaseService::getByPageWithSql($sql, $pg);
	}
	
	
	/**
	 * 查询新人才
	 */
	public static function getNewTalentsByPg($fsRsm, $pg) {
		$sFsRsm = UtilService::implodeArrByPrefixX($fsRsm, 'a');
		$sql = "select $sFsRsm, get_rsm_biz(a.code)
			from resume as a
			where a.is_open = 'Y' 
				and a.enabled = 'Y' 
				and a.ok_base = 'Y' 
				and a.ok_biz = 'Y' 
				and a.ok_prj = 'Y' 
				and a.ok_other = 'Y'
				and a.create_time > DATE_SUB(now(),INTERVAL 1 month)
			order by a.create_time desc";
		return BaseService::getByPageWithSql($sql, $pg);
	}
	
	
	/**
	 * 查询居住地在中国的新人才
	 */
	public static function getChinaNewTalentsByPg($fsRsm, $pg) {
		$sFsRsm = UtilService::implodeArrByPrefixX($fsRsm, 'a');
		$sql = "select $sFsRsm, get_rsm_biz(a.code)
			from resume as a
			where a.is_open = 'Y' 
				and a.enabled = 'Y' 
				and a.ok_base = 'Y' 
				and a.ok_biz = 'Y' 
				and a.ok_prj = 'Y' 
				and a.ok_other = 'Y'
				and a.actual_residence_cntry = 'CN'
				and a.create_time > DATE_SUB(now(),INTERVAL 1 month)
			order by a.bid_points desc, a.create_time desc";
		return BaseService::getByPageWithSql($sql, $pg);
	}
	
	
} //End: class ResumeService