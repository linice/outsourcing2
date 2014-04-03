<?php
include_once 'LogService.php';
include_once 'BaseService.php';
include_once 'UtilService.php';

/**
 * 案件募集
 * @author GONGM
 */
class InviteService {
	private static $table = 'case_invite';
	
	/**
	 * 插入一条系统金币的使用纪录
	 * @param array $row
	 */
	public static function saveInvite(array $row) {
		return BaseService::save(self::$table, $row);
	}
	
	/**
	 * 查找所有募集过指定员工的有效案件
	 * @param unknown_type $usrCode
	 * @param unknown_type $option
	 * @param unknown_type $page
	 */
	public static function findInviteCaseListByUsrCode($usrCode, $option, $page) {
		$where = array("type in ('R', 'C')");
		array_push($where, CaseBaseService::genWhereOptionOnCaseNameOrCode($option));
		$join[] = CaseBaseService::caseJoinLp(true);
		return self::findInviteCasesByUsrCode($usrCode, $option, $join, $page);
	}
	
	/**
	 * 查找指定人关注的募集中案件
	 * @param unknown_type $usrId
	 * @param unknown_type $caseNameOrCode
	 * @param unknown_type $joinLp
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	private static function findInviteCasesByUsrCode($usr_code, $option, $joinTb, $page=NULL) {
		$where = empty($option) ? array() : (is_array($option) ? $option : array($option));
		$joinTb[] = self::caseJoinInvite(TRUE, $usr_code);
		return CaseBaseService::findCasesWithoutRole($where, array("*"), $joinTb, $page);
	}
	
	/**
	 * 查找指定ID关注的募集中案件
	 * @param unknown_type $usrId
	 * @param unknown_type $caseNameOrCode
	 * @param unknown_type $joinLp
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	private static function findInviteCasesByIds($ids, $option, $joinTb, $page=NULL) {
		$where = empty($option) ? array() : (is_array($option) ? $option : array($option));
		$joinTb[] = self::caseJoinInviteWithIds(TRUE, $ids);
		return CaseBaseService::findCasesWithoutRole($where, array("*"), $joinTb, $page);
	}
	
	/**
	 * 
	 * @param unknown_type $join
	 * @param unknown_type $usr_code
	 * @param unknown_type $fields
	 */
	private static function caseJoinInvite($join, $usr_code, $fields=array('invite.invite_date,invite.id invite_id')) {
		return $join ? array("type"=>"inner join", "table"=>"case_invite invite", "on"=>"t.code=invite.case_code and invite.is_apply='N' and invite.is_effective='Y' and invite.usr_code='$usr_code'", "fields"=>$fields):NULL;
	}
	
	/**
	 * 
	 * @param unknown_type $join
	 * @param unknown_type $usr_code
	 * @param unknown_type $fields
	 */
	private static function caseJoinInviteWithIds($join, $ids, $fields=array('invite.invite_date,invite.id invite_id, invite.resume_code')) {
		$db = BaseService::getDb();
		return $join ? array("type"=>"inner join", "table"=>"case_invite invite", "on"=>$db->quoteInto("t.code=invite.case_code and invite.is_apply='N' and invite.is_effective='Y' and invite.id in (?)", $ids), "fields"=>$fields):NULL;
	}
	
	/**
	 * 对案件募集进行应聘
	 * @param unknown_type $caseCode
	 * @param unknown_type $resumeCode
	 */
	public static function applyInviteCase($caseCode, $resumeCode) {
		$db = BaseService::getDb();
		$rows = array('is_apply'=>'Y');
		if (is_array($resumeCode)) {
			$where = $db->quoteInto("is_apply='N' and case_code = '$caseCode' and resume_code in (?)", $resumeCode);
		} else {
			$where = "is_apply='N' and case_code = '$caseCode' and resume_code = '$resumeCode'";
		}
		return BaseService::update(self::$table, $where, $rows);
	}
	
	/**
	 * 查找指定员工被募集的指定ID的案件列表
	 * @param unknown_type $ids
	 * @param unknown_type $usr_code
	 */
	public static function findInviteCasesByCaseIds($ids, $usr_code) {
		$db = BaseService::getDb();
		$idArray = explode(',', $ids);
		$option = array($db->quoteInto("t.id in (?)", $idArray));
		$join[] = CaseBaseService::caseJoinLp(true);
		return self::findInviteCasesByUsrCode($usr_code, $option, $join);
	}
	
	/**
	 * 查找指定员工被募集的指定ID的案件列表
	 * @param unknown_type $ids
	 * @param unknown_type $usr_code
	 */
	public static function findInviteCasesByInviteIds($ids) {
		$db = BaseService::getDb();
		$idArray = explode(',', $ids);
		$join[] = CaseBaseService::caseJoinLp(true);
		return self::findInviteCasesByIds($idArray, null, $join);
	}

	/**
	 * 检查是否可以对指定的案件进行募集拒绝
	 * @param unknown_type $ids
	 */
	public static function valRefuseCase($ids) {
		return TRUE;		
	}
	
	/**
	 * 募集拒绝
	 * @param unknown_type $where
	 * @param unknown_type $row
	 */
	public static function refuseCase($where, $row) {
		$row = empty($row) ? array() : $row;
		$row['refuse_date'] = UtilService::getCurrentTime();
		$row['is_effective'] = 'N';
		$where = empty($where) ? "is_apply='N' and is_effective='Y'" : "is_apply='N' and is_effective='Y' and ".$where;
		return BaseService::update(self::$table, $where, $row);
	}
	
	/**
	 * 募集验证
	 * 1、验证重复募集
	 * @param unknown_type $resumeStrs
	 * @param unknown_type $caseCode
	 */
	public static function valInviteTalent($resumeStrs, $caseCode) {
		$resumeArray = explode(',', $resumeStrs);
		$sql = BaseService::getDb()->quoteInto("select count(1) from case_invite ci where ci.is_apply='N' and ci.is_effective='Y' and ci.resume_code in (?) and ci.case_code='$caseCode'", $resumeArray);
		$retVal = BaseService::fetchOneBySql($sql);
		return $retVal+0 > 0 ? "invate_case_already" : TRUE;
	}
	
}


