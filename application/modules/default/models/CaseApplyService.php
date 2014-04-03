<?php
include_once 'LogService.php';
include_once 'BaseService.php';
include_once 'UtilService.php';
include_once 'CaseAttentionService.php';
include_once 'InviteService.php';
include_once 'ResumeService.php';

class CaseApplyService {
	private static $table = 'case_apply';
	/**
	 * 普通人员应聘案件
	 * @param array(field => value) $row
	 */
	public static function saveCaseApply($row = array()) {
		$db = BaseService::getDb();
		$db->beginTransaction();
		$row['status'] = 'NO_VOTE';
		$row['remark'] = '';
		$row['apply_date'] = UtilService::getCurrentTime();
		$ret = BaseService::save(self::$table, $row);
		$flag = FALSE;
		if ($ret !== FALSE) {
			$state = array('case_apply_id'=>$ret, 'status'=> $row['status'], 'is_current'=>'Y',
						'updated_time'=>UtilService::getCurrentTime(), 'remark'=>$row['remark']);
			$flag = BaseService::save('case_apply_record', $state);
		}
		if ($flag !== FALSE) {
			$flag = CaseAttentionService::cancelAttentionCases($row['case_code'], $row['usr_code']);
		}
		if ($flag !== FALSE) {
			$flag = InviteService::applyInviteCase($row['case_code'], $row['resume_code']);
		}
		if ($flag == FALSE) {
			$db->rollback();
		} else {
			$db->commit();
		}
		return $flag;
	}
	
	/**
	 * 法人应聘
	 * @param unknown_type $caseCode
	 * @param unknown_type $lp_code
	 * @param unknown_type $empList
	 */
	public static function saveCaseApplyForLp($caseCode, $lpCode, $empList) {
		$db = BaseService::getDb();
		$db->beginTransaction();
		$codearray = explode(',', $empList);
		$flag = FALSE;
		$ret = BaseService::fetchAllBySql(BaseService::getDb()->quoteInto("select resume_code from case_apply ca where resume_code in (?) and case_code='$caseCode'", $codearray));
		$exists = array();
		if ($ret !== FALSE && !empty($ret)) {
			foreach ($ret as $retV) {
				array_push($exists, $retV['resume_code']);
			}
			$codearray = array_diff($codearray, $exists);
		}
		foreach ($codearray as $emp) {
			$row = array('case_code'=>$caseCode, 'usr_code'=>$lpCode, 'resume_code'=> $emp, 'status'=>'NO_VOTE', 
					'remark'=>'', 'apply_date'=>UtilService::getCurrentTime());
			$ret = BaseService::save(self::$table, $row);
			if ($ret !== FALSE) {
				$state = array('case_apply_id'=>$ret, 'status'=> $row['status'], 'is_current'=>'Y',
							'updated_time'=>UtilService::getCurrentTime(), 'updated_usr'=>$lpCode, 'remark'=>$row['remark']);
				$flag = BaseService::save('case_apply_record', $state);
			} else {
				$flag = FALSE;
			}
		}
		if ($flag !== FALSE) {
			$flag = InviteService::applyInviteCase($caseCode, $codearray);
			//法人应该不用取消关注
			//$flag = CaseAttentionService::cancelAttentionCases($caseCode, $lp_code);
			
		}
		if ($flag == FALSE) {
			$db->rollback();
		} else {
			$db->commit();
		}
		return $flag;
	}
	
	/**
	 * 管理员应聘
	 * @param unknown_type $caseCode
	 * @param unknown_type $lp_code
	 * @param unknown_type $empList
	 */
	public static function saveCaseApplyForAdmin($caseCode, $empList, $adminCode) {
		$db = BaseService::getDb();
		$db->beginTransaction();
		$codearray = explode(',', $empList);
		$flag = FALSE;
		$ret = BaseService::fetchAllBySql(BaseService::getDb()->quoteInto("select resume_code from case_apply ca where resume_code in (?) and case_code='$caseCode'", $codearray));
		$exists = array();
		if ($ret !== FALSE && !empty($ret)) {
			foreach ($ret as $retV) {
				array_push($exists, $retV['resume_code']);
			}
			$codearray = array_diff($codearray, $exists);
		}
		foreach ($codearray as $emp) {
			$row = array('case_code'=>$caseCode, 'usr_code'=>new Zend_Db_Expr("(select case when lp_code is null or lp_code = '' then talent_code else lp_code end from resume where code='$emp')"), 'resume_code'=> $emp, 'status'=>'NO_VOTE', 
					'remark'=>'', 'apply_date'=>UtilService::getCurrentTime());
			$ret = BaseService::save(self::$table, $row);
			if ($ret !== FALSE) {
				$state = array('case_apply_id'=>$ret, 'status'=> $row['status'], 'is_current'=>'Y',
							'updated_time'=>UtilService::getCurrentTime(), 'updated_usr'=>$adminCode, 'remark'=>$row['remark']);
				$flag = BaseService::save('case_apply_record', $state);
			} else {
				$flag = FALSE;
			}
		}
		if ($flag !== FALSE) {
			$flag = InviteService::applyInviteCase($caseCode, $codearray);
			//法人应该不用取消关注
			//$flag = CaseAttentionService::cancelAttentionCases($caseCode, $lp_code);
		}
		if ($flag == FALSE) {
			$db->rollback();
		} else {
			$db->commit();
		}
		return $flag;
	}
	
	/**
	 * 修改案件应聘，案件应聘流程发生变化时的操作
	 * @param unknown_type $id
	 * @param unknown_type $row
	 */
	public static function updateCaseApply($id, $row=array(), $usrCode='') {
		$db = BaseService::getDb();
		$ret = BaseService::updateByPrimaryKey(self::$table, $id, $row);
		var_dump($ret);
		if ($ret != FALSE) {
			if (empty($row['status'])) {
				return $ret;
			}
			$where[] = $db->quoteInto('case_apply_id = ?', $id);
			$where[] = $db->quoteInto('is_current = ?', 'Y');
			$ret = BaseService::update('case_apply_record', $where, array('is_current'=>'N'));
			if ($ret != FALSE) {
				$state = array('case_apply_id'=>$id, 'status'=> $row['status'], 'is_current'=>'Y', 'updated_usr'=>$usrCode,
						'updated_time'=>UtilService::getCurrentTime(), 'remark'=>$row['remark'], 'reason'=>$row['reason']);
				$ret = BaseService::save('case_apply_record', $state);
				if ($ret != FALSE) {
					return $ret;
				}
			}
		}
		return FALSE;
	}
	
	/**
	 * 删除案件应聘
	 * @param unknown_type $ids
	 */
	public static function deleteCaseApply($applyIds) {
		$db = BaseService::getDb();
		$db->beginTransaction();
		$ret = BaseService::deleteByPrimaryKey(self::$table, $applyIds);
		if ($ret !== FALSE) {
			if (is_array($applyIds)) 
				$where = $db->quoteInto('case_apply_id in (?)', $applyIds);
			else 
				$where = $db->quoteInto('case_apply_id = ?', $applyIds);
			$ret = BaseService::delete('case_apply_record', $where);
			if ($ret !== FALSE) {
				$db->commit();
				return $ret;
			}
		}
		$db->rollback();
		return FALSE;
	}
	
	/**
	 * 删除一个案件的应聘信息
	 * @param unknown_type $caseCode
	 */
	public static function deleteCaseApplyByCaseCode($caseCode) {
		$ret = BaseService::delete(self::$table, "case_code = '$caseCode'");
		if ($ret !== FALSE) {
			$ret = BaseService::delete('case_apply_record', "exists(select 1 from case_apply ca where case_apply_id = ca.id and ca.case_code='$caseCode')");
		}
		if ($ret !== FALSE) {
			$ret = BaseService::update('case_invite', "is_apply='Y' and is_effective='Y' and case_code='$caseCode'", array('is_apply'=>'N'));
		}
		return $ret;
	}
	
	/**
	 * 根据ID查找应聘记录
	 * @param unknown_type $id
	 * @param unknown_type $row
	 */
	public static function findCaseApplyById($id, $row=array('*')) {
		return BaseService::findByPrimaryKey(self::$table, $id, $row);
	}

	/**
	 * 根据案件ID查找该案件下的所有应聘人员信息
	 * @param unknown_type $caseId
	 */
	public static function findUsrListByCaseCode($caseCode) {
		$sql = "select u.*, get_resume_biz_info(u.code) biz_info, t.status apply_status, t.remark apply_remark, t.reason apply_reason, t.cancel_body, t.id apply_id from case_apply t, resume u where t.resume_code=u.code and t.case_code = '$caseCode'";
		return BaseService::fetchAllBySql($sql);
	}
	
	/**
	 * 根据简历ID查找该案件下的应聘人员的简历列表信息
	 * @deprecated
	 * @param unknown_type $ids
	 * @param unknown_type $caseId
	 */
	public static function findUsrListByResumeIds($ids, $casecode) {
		if (!is_string($ids)) return FALSE;
		$db = BaseService::getDb();
		$idarray = explode(',', $ids);
		$sql = $db->quoteInto("select u.*, t.status apply_status, t.remark apply_remark, t.reason apply_reason, t.cancel_body, t.id apply_id from case_apply t, resume u where t.resume_code=u.code and t.case_code='$casecode' and u.id in (?)", $idarray);
		return BaseService::fetchAllBySql($sql);
	}
	
	/**
	 * 根据应聘ID得到应聘案件的人员简历列表
	 * @param unknown_type $ids
	 */
	public static function findUsrListByApplyIds($applyIds) {
		$db = BaseService::getDb();
		$idarray = explode(',', $applyIds);
		$sql = $db->quoteInto("select u.*, get_resume_biz_info(u.code) biz_info, t.status apply_status, t.remark apply_remark, t.reason apply_reason, t.id apply_id from case_apply t, resume u where t.resume_code=u.code and t.id in (?)", $idarray);
		return BaseService::fetchAllBySql($sql);
	}
	
	/**
	 * 根据应聘ID得到应聘案件的人员简历列表
	 * @param unknown_type $ids
	 */
	public static function findCaseListByApplyIds($ids) {
		$db = BaseService::getDb();
		$idarray = explode(',', $ids);
		$sql = $db->quoteInto("select c.*, u.fullname lp_name, t.status apply_status, t.remark apply_remark, t.reason apply_reason, t.id apply_id from case_apply t, cases c, usr u where t.case_code=c.code and c.lp_code=u.code and t.id in (?)", $idarray);
		return BaseService::fetchAllBySql($sql);
	}
	
	/**
	 * 根据简历ID查找指定案件下已进行面试调整的简历，应聘，面试信息
	 * @param unknown_type $ids
	 * @param unknown_type $casecode
	 */
	public static function findInterviewAdjustByApplyIds($applyIds) {
		$db = BaseService::getDb();
		$idarray = explode(',', $applyIds);
		$sql = $db->quoteInto("select u.*, t.status apply_status, t.remark apply_remark, t.reason apply_reason, t.id apply_id, i.expect_interview_date1, i.expect_interview_date2, i.expect_interview_date3, i.id interviewId from case_apply t, interview i, resume u where i.case_apply_id=t.id and t.resume_code=u.code and t.id in (?)", $idarray);
		return BaseService::fetchAllBySql($sql);
	}
	
	/**
	 * 保存面试调整信息
	 * @param unknown_type $row
	 */
	public static function saveInterview($row = array()) {
		return BaseService::save('interview', $row);
	}
	
	/**
	 * 保存面试结果信息
	 * @param unknown_type $row
	 */
	public static function saveInterviewResult($row = array()) {
		return BaseService::save('interview_result', $row);
	}
	
	/**
	 * 修改面试调整信息
	 * @param unknown_type $interviewId
	 * @param array $row
	 */
	public static function updateInterview($interviewId, array $row) {
		return BaseService::updateByPrimaryKey('interview', $interviewId, $row);
	}
	
	/**
	 * 对应聘的案件进行验证，是否可以进行应聘
	 * @param unknown_type $case_code
	 * @param unknown_type $usr_code
	 */
	public static function valCaseApplyForUsr($case_code, $usr_code, $resume=NULL) {
		$ret = array();
		if (empty($resume)) 
			$resume = ResumeService::getResumesByTalentCode($usr_code);
		if (!empty($resume)) {
			$resume = $resume[0];
			if ($resume === FALSE || $resume['enabled'] !== 'Y') 
				$ret['enabled'] = 'resume_is_not_enabled';	//简历不可用
			if ($resume['ok_base'] !== 'Y') 
				$ret['base'] = 'resume_base_is_not_comp';	//简历基本信息不完善
			if ($resume['ok_biz'] !== 'Y') 
				$ret['biz'] = 'resume_biz_is_not_comp';	//简历业务信息不完善
			if ($resume['ok_prj'] !== 'Y') 
				$ret['prj'] = 'resume_prj_is_not_comp';	//简历项目信息不完善
			if ($resume['ok_other'] !== 'Y') 
				$ret['other'] = 'resume_other_is_not_comp';	//简历其它信息不完善
		} else {
			$ret['hasresume'] = 'YOU_HAVE_NOT_ADDED_RESUME';	//简历不可用
		}
		$caseApply = BaseService::fetchOneBySql("select count(1) count from ".self::$table." where usr_code='$usr_code' and case_code='$case_code'");
		if ($caseApply === FALSE || $caseApply+0 > 0)
			$ret['applied'] = 'you_have_apply_the_case_already';	//已经应聘过该案件
		$caseApply = BaseService::fetchOneBySql("select count(1) count from ".self::$table." t where not exists(select 1 from case_invite ci where ci.case_code=t.case_code and ci.resume_code=t.resume_code and ci.is_apply='Y') and t.apply_date >= CURDATE() and t.usr_code='$usr_code'");
		if ($caseApply === FALSE || $caseApply+0 >= 2)
			$ret['toomuch'] = 'you_have_apply_too_much_case_already';	//当天应聘案件超过两个，而且这两个案件中没有从募集应聘的
		return count($ret) == 0 ? TRUE : $ret;
	}
	
	/**
	 * 对应聘的案件进行验证，是否可以进行应聘
	 * @param unknown_type $case_code
	 * @param unknown_type $usr_code
	 */
	public static function valCaseApplyForLp($caseCode, $lpCode, $empCodeList=NULL) {
		$case = BaseService::fetchRowBySql("select count(1) count from cases t where lp_code='$lpCode' and code='$caseCode'" );
		if ($case === FALSE || $case['count']+0 > 0) 
			return 'val_apply_self_case_error';//不能应聘自己的案件
//		$caseApply = BaseService::fetchRowBySql("select count(1) count from resume r where enabled='Y' and lp_code='$lp_code' and not exists(select 1 from case_apply ca where ca.resume_code = r.code and ca.case_code='$case_code')" );
//		if ($caseApply === FALSE || $caseApply['count']+0 == 0) 
//			return 'hava_no_emp_can_apply';//没有可应聘的员工
//		$resume = BaseService::fetchRowBySql("select count(1) count from resume r where enabled='Y' and ok_base='Y' and ok_biz='Y' and ok_other='Y' and lp_code='$lp_code' and not exists(select 1 from case_apply ca where ca.resume_code = r.code and ca.case_code='$case_code')" );
//		if ($resume === FALSE || $resume['count']+0 == 0) 
//			return 'LP_RESUME_NOTEXISTS_OR_IMPERFECT';//可应聘员工的简历信息不完整
		return TRUE;
	}
	
	/**
	 * MEMBER登陆查询其应聘过的案件
	 * @param unknown_type $usr_code
	 */
	public static function findCaseListByUsrCode($usr_code, $option=array(), $page=null) {
		$sql = "select cs.*, u.fullname lp_name, ca.status apply_status, ca.cancel_body apply_cancel_body,
				ca.remark apply_remark, ca.reason apply_reason, ca.apply_date, ca.id apply_id,
				r.fullname, r.code resume_code, r.talent_code 
				from cases cs 
				inner join usr u on cs.lp_code = u.code 
				inner join case_apply ca on ca.case_code=cs.code
				inner join resume r on ca.resume_code = r.code
				where cs.type in ('R', 'C') and ca.usr_code = '$usr_code'";
		if (!empty($option))
			$sql = $sql.' and '.implode(' and ', $option);
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
	
	/**
	 * 检查案件应聘记录是否可以被取消
	 * 1、如果当前应聘流程已经处于应聘成功/应聘失败/应聘取消状态则返回错误
	 * 2、如果当前应聘流程处于面试进行中，则提示不能进行应聘取消
	 * @param unknown_type $ids
	 */
	public static function valCancelCaseApply($ids) {
		$db = BaseService::getDb();
		$idarray = explode(',', $ids);
		$sql = $db->quoteInto("select count(1) count from case_apply ca where ca.status in ('APPLY_CANCEL','INTERVIEW_OK','INTERVIEW_LOSE') and ca.id in (?)", $idarray);
		$ret = BaseService::fetchOneBySql($sql);
		if ($ret === FALSE || $ret+0>0)
			return 'CANNOT_APPLYCASE_WITH_RESULT';
		$sql = $db->quoteInto("select count(1) count from case_apply ca where ca.status in ('INTERVIEW_ADJUST','INTERVIEW_DECIDE','INTERVIEW_INFORM') and ca.id in (?)", $idarray);
		$ret = BaseService::fetchOneBySql($sql);
		if ($ret === FALSE || $ret+0>0)
			return 'CANNOT_APPLYCASE_IN_PROCESS';
		return TRUE;
	}
	
	/**
	 * 根据人员编码得到其简历编码
	 * @param unknown_type $usr_code
	 */
	public static function findResumeCodeByUsrCode($usr_code) {
		$sql = "select code from resume r where enabled='Y' and ok_base='Y' and ok_biz='Y' and ok_other='Y' and talent_code = '$usr_code'";
		$ret = BaseService::fetchAllBySql($sql);
		if ($ret !== FALSE && !empty($ret)) {
			return $ret[0]['code'];
		} else 
			return FALSE;
	}
	
	public static function findHistoryCaseListByUsrCode($usr_code, $page) {
		$sql = "select cs.*, u.fullname lp_name, ca.status apply_status, ca.remark apply_remark, ca.reason apply_reason, ca.apply_date, ca.id apply_id from cases cs 
				inner join usr u on cs.lp_code = u.code 
				inner join case_apply ca on ca.case_code=cs.code
				where cs.type ='E' and ca.usr_code = '$usr_code'";
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
	
	/**
	 * 查找法人下的可应聘员工
	 * @deprecated 暂时无人调用
	 * @param unknown_type $lp_code
	 * @param unknown_type $case_code
	 */
	public static function findEmpListForLpByLpCode($lp_code, $case_code, $codeOrName, $page) {
		$sql = "select r.*, get_resume_biz_info(r.code) biz_info from resume r where not exists(select 1 from case_apply ca where ca.resume_code=r.code and ca.case_code='$case_code') and r.enabled='Y' and r.ok_base='Y' and r.ok_biz='Y' and r.ok_other='Y' and lp_code='$lp_code' ";
		$where = CaseBaseService::genWhereOptionOnEmpNameOrCode($codeOrName, 'r');
		$sql = $sql.' and '.$where;
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
	
	/**
	 * 查找指定人员的简历，且在指定案件中没有过应聘纪录的
	 * @param unknown_type $resumeCodes
	 * @param unknown_type $caseCode
	 */
	public static function findResumeListByResumeCodesExpApply($resumeCodes, $caseCode) {
		$resumeArray = explode(',', $resumeCodes);
		//显示有效募集数量，包括应聘了的
		$sql = "select r.*, get_resume_biz_info(r.code) biz_info, ca.id apply_id, (select count(1) from case_invite ci where r.code=ci.resume_code and ci.case_code='$caseCode' and ci.is_effective='Y') invite_id 
			from resume r 
			left join case_apply ca on r.code=ca.resume_code and ca.case_code='$caseCode' 
			where r.code in (?)";
		$sql = BaseService::getDb()->quoteInto($sql, $resumeArray);
		return BaseService::fetchAllBySql($sql);
	}
	
	/**
	 * 法人应聘案件确认
	 * @param unknown_type $resumeCodes
	 * @param unknown_type $caseCode
	 */
	public static function findResumeListByResumeCodesForLpApply($resumeCodes, $caseCode) {
		$resumeArray = explode(',', $resumeCodes);
		$sql = "select r.*, get_resume_biz_info(r.code) biz_info, ca.id apply_id,
			(select count(1) from case_apply c where r.code=c.resume_code and not exists(select 1 from case_invite ci where ci.resume_code=c.resume_code and ci.case_code = c.case_code and ci.is_apply='Y') and c.apply_date >= CURDATE()) cnt_apply 
			from resume r 
			left join case_apply ca on r.code=ca.resume_code and case_code='$caseCode' 
			where r.code in (?)";
		$sql = BaseService::getDb()->quoteInto($sql, $resumeArray);
		return BaseService::fetchAllBySql($sql);
	}
	
	/**
	 * 查找所有员工的应聘信息
	 */
	public static function findAllEmpOrUsrListForApplyInfo($optioin, $page=NULL) {
		$sql = "select u.*, get_resume_biz_info(u.code) biz_info, 
			(select count(1) from case_invite ci where ci.resume_code = u.code and is_effective='Y') count_invite, 
			(select count(1) from case_apply ca where ca.resume_code=u.code) count_apply, 
			(select count(1) from case_apply_record car, case_apply ca where ca.id = car.case_apply_id and ca.resume_code=u.code and ca.status='INTERVIEW_ADJUST') count_adjust, 
			(select count(1) from case_apply ca where ca.resume_code=u.code and ca.status='INTERVIEW_OK') count_ok 
			from resume u
			where u.enabled='Y'";
		if(!empty($optioin)) {
			$where = implode(' and ', $optioin);
			$sql = $sql.' and '. $where;
		}
		if (isset($page['sortname']) && !empty($page['sortname'])) {
			$sql = 'select * from ('.$sql.') tmp order by '.$page['sortname'].' '.$page['sortorder'];
		}
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
	
}