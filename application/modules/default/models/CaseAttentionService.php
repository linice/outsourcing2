<?php
include_once 'LogService.php';
include_once 'BaseService.php';
include_once 'UtilService.php';
include_once 'CaseBaseService.php';

class CaseAttentionService {
	private static $table = "case_attention";
	
	/**
	 * 根据用户编码查询该用户的关注案件，案件处于募集中状态
	 * @param unknown_type $usr_code
	 * @param unknown_type $option
	 */
	public static function findReleaseAttentionCaseListForApplyByUsrCode($usr_code, $option=NULL, $page=NULL) {
		$option = array("type='R'");
		$join[] = CaseBaseService::caseJoinLp(true);
		$join[] = CaseBaseService::caseJoinCaseApply(true, $usr_code);
		return self::findAttentionCasesByUsrCode($usr_code, $option, $join, $page, "MEMBER");
	}

	/**
	 * 根据用户编码查询该用户的关注案件，案件处于募集中状态
	 * @param unknown_type $usr_code
	 * @param unknown_type $option
	 */
	public static function findReleaseAttentionCaseListForApplyByLpCode($lp_code, $option=NULL, $page=NULL) {
		$where = array("type='R'");
		array_push($where, CaseBaseService::genWhereOptionOnCaseNameOrCode($option));
		$join[] = CaseBaseService::caseJoinLp(true);
		return self::findAttentionCasesByUsrCode($lp_code, $where, $join, $page, "LP");
	}
	
	/**
	 * 根据用户编码查询该用户的关注案件，案件处于关闭或者结束
	 * @param unknown_type $usr_code
	 * @param unknown_type $option
	 */
	public static function findClosedAttentionCaseListForApplyByUsrCode($usr_code, $option=NULL, $page=NULL) {
		$option = array("type in ('C', 'E')");
		$join[] = CaseBaseService::caseJoinLp(true);
		$join[] = CaseBaseService::caseJoinCaseApply(true, $usr_code);
		return self::findAttentionCasesByUsrCode($usr_code, $option, $join, $page, "MEMBER");
	}
	
	/**
	 * 根据用户编码查询该用户的关注案件，案件处于关闭或者结束
	 * @param unknown_type $usr_code
	 * @param unknown_type $option
	 */
	public static function findClosedAttentionCaseListForApplyByLpCode($lp_code, $option=NULL, $page=NULL) {
		$where = array("type in ('C', 'E')");
		array_push($where, CaseBaseService::genWhereOptionOnCaseNameOrCode($option));
		$join[] = CaseBaseService::caseJoinLp(true);
		return self::findAttentionCasesByUsrCode($lp_code, $where, $join, $page, "LP");
	}
	
	/**
	 * 查找指定人关注的募集中案件
	 * @param unknown_type $usrId
	 * @param unknown_type $caseNameOrCode
	 * @param unknown_type $joinLp
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function findAttentionCasesByUsrCode($usr_code, $option, $joinTb, $page=NULL, $user_role=NULL) {
		$where = empty($option) ? array() : (is_array($option) ? $option : array($option));
		$joinTb = empty($joinTb) ? array() : $joinTb;
		$joinTb[] = CaseBaseService::caseInnerJoinCaseAttention(TRUE, $usr_code);
		//array_push($where, "exists(select 1 from case_attention a where a.case_code = t.code and a.usr_code = '$usr_code')");
		return CaseBaseService::findCasesWithoutRole($where, array("*"), $joinTb, $page);
	}
	
	/**
	 * 取消关注案件
	 * @param unknown_type $case_ids exp: 1,2,3
	 * @param unknown_type $usr_id
	 */
	public static function cancelAttentionCases($case_codes, $usr_code) {
		$db = BaseService::getDb();
		if (is_array($case_codes)) 
			$where[] = $db->quoteInto('case_code in (?)', $case_codes);
		else 
			$where[] = $db->quoteInto('case_code = ?', $case_codes);
		$where[] = $db->quoteInto('usr_code = ?', $usr_code);
		return BaseService::delete("case_attention", $where);
	}
	
	/**
	 * 关注案件
	 * @param unknown_type $case_ids exp: 1,2,3
	 * @param unknown_type $usr_id
	 */
	public static function attentionCases($case_codes, $usr_code) {
		if (is_array($case_codes)) {
			$ret = BaseService::fetchAllBySql(BaseService::getDb()->quoteInto("select case_code from case_attention where case_code in (?) and usr_code = '$usr_code'", $case_codes));
			$exists = array();
			if ($ret !== FALSE && !empty($ret)) {
				foreach ($ret as $retV) {
					array_push($exists, $retV["case_code"]);
				}
				$case_codes = array_diff($case_codes, $exists);
			}
			if (count($case_codes) > 0) {
				$retVal = 0;
				foreach ($case_codes as $code) {
					$row = array("case_code"=>$code, "usr_code"=>$usr_code, "create_date"=>UtilService::getCurrentTime());
					$ret = BaseService::save("case_attention", $row);
					$retVal = $ret === FALSE ? $retVal : $retVal+1;
				}
				return $retVal == 0 ? FALSE : $retVal;
			} else {
				return true;
			}
		} else 
			return BaseService::save("case_attention", array("case_code"=>$case_codes, "usr_code"=>$usr_code, "create_date"=>UtilService::getCurrentTime()));
	}
	
	/**
	 * 对关注案件进行验证
	 * @param unknown_type $case_codes
	 * @param unknown_type $usr_code
	 */
	public static function valAttentionCases($case_codes, $usr_code, $usr_role) {
		if (!is_array($case_codes)) $case_codes = array($case_codes);
		$db = BaseService::getDb();
		$sql = $db->quoteInto("select count(1) count from cases where code in (?) and lp_code='$usr_code'", $case_codes);
		$ret = BaseService::fetchRowBySql($sql);
		if ($ret == FALSE || $ret['count']+0 > 0) 
			return 'val_attention_self_case_error';//不能对自己创建的案件进行关注
		if ($usr_role == 'MEMBER') {
			$sql = $db->quoteInto("select count(1) count from case_apply where case_code in (?) and usr_code='$usr_code'", $case_codes);
			$ret = BaseService::fetchRowBySql($sql);
			if ($ret == FALSE || $ret['count']+0 > 0) 
				return 'val_attention_apply_case_error';//不能对已经进行应聘的案件进行关注
		}
		return TRUE;
	}
	
}


