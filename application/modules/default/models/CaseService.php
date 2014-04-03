<?php
include_once 'LogService.php';
include_once 'BaseService.php';
include_once 'UtilService.php';
include_once 'AKBService.php';
include_once 'UsrService.php';
include_once 'CaseBaseService.php';
include_once 'CaseApplyService.php';
include_once 'BaseService2.php';


class CaseService {
	private static $table = "cases";
	
	/**
	 * 查找已发布案件，用于主页面的案件查询
	 * @param unknown_type $option		各种查询条件
	 * @param unknown_type $usr_code  	当前用户，用于关联应聘以及关注信息
	 * @param unknown_type $page		分页信息
	 * @param unknown_type $user_role  	用于控制可见度
	 */
	public static function findReleaseCasesFront($option, $usr_code, $page=NULL, $user_role=NULL) {
		$where = array("type='R'");
		return self::findCasesFront($option, $usr_code, $where, $page, $user_role);
	}
	
	/**
	 * 查找有效案件，用于管理员主页面的案件查询
	 * @param unknown_type $option		各种查询条件
	 * @param unknown_type $usr_code  	当前用户，用于关联应聘以及关注信息
	 * @param unknown_type $page		分页信息
	 * @param unknown_type $user_role  	用于控制可见度
	 */
	public static function findEffectiveCasesFront($option, $usr_code, $page=NULL, $user_role=NULL) {
		$where = array("type in ('R', 'C')");
		return self::findCasesFront($option, $usr_code, $where, $page, $user_role);
	}

	/**
	 * 查找历史案件，用于管理员主页面的案件查询
	 * @param unknown_type $option		各种查询条件
	 * @param unknown_type $usr_code  	当前用户，用于关联应聘以及关注信息
	 * @param unknown_type $page		分页信息
	 * @param unknown_type $user_role  	用于控制可见度
	 */
	public static function findHistoryCasesFront($option, $usr_code, $page=NULL, $user_role=NULL) {
		$where = array("type = 'E'");
		return self::findCasesFront($option, $usr_code, $where, $page, $user_role);
	}
	
	/**
	 * 案件检索查询方法以
	 * @param unknown_type $option
	 * @param unknown_type $usr_code
	 * @param unknown_type $where
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function findCasesFront($option, $usr_code, $where=array(), $page=NULL, $user_role=NULL, $orderBy=NULL) {
		$value = isset($option["casename"]) ? trim($option["casename"]) : NULL;
		$casetype = isset($option["casetype"]) ? $option["casetype"] : NULL;
		$careers = isset($option['careers']) ? $option['careers'] : NULL;
		$languages = isset($option['languages']) ? $option['languages'] : NULL;
		$industries = isset($option['industries']) ? $option['industries'] : NULL;
		$workplace = isset($option['workplace']) ? $option['workplace'] : NULL;
		$unitPrice = isset($option['unitprice']) ? $option['unitprice'] : NULL;
		$range = isset($option['range']) ? $option['range'] : NULL;
		
		if(!empty($value)) {
			switch($range) {
				case 'case_id':		array_push($where, "t.code like '%$value%'");break;
				case 'case_name':	array_push($where, "t.name like '%$value%'");break;
				case 'CASE_NAME':	array_push($where, "t.name like '%$value%'");break;
				case 'lp_id':		array_push($where, "t.lp_code like '%$value%'");break;
				case 'admin_id':		array_push($where, "t.lp_code like '%$value%'");break;
				case 'LP_NAME':		array_push($where, "u.fullname like '%$value%'");break;
				default :array_push($where, "1");break;
			}
		}
		if(!empty($casetype)) {
			switch($casetype) {
				case "cases_new": 		array_push($where, "(To_Days(now())-To_Days(t.release_date)) <= 30");
					if (empty($orderBy) && (empty($page) || empty($page['sortname']))) {
						$orderBy='t.last_modify_time desc';
					}
					break;
				case "cases_recommend": array_push($where, "t.akb >= 1");
					if (empty($orderBy) && (empty($page) || empty($page['sortname']))) {
						$orderBy='t.akb desc, t.last_modify_time desc';
					}
					break;
			}
		}
		array_push($where, BaseService::arrayToWhere('t.careers',$careers));
		array_push($where, BaseService::arrayToWhere('t.languages',$languages));
		array_push($where, BaseService::arrayToWhere('t.industries',$industries));
		array_push($where, BaseService::arrayToWhere('t.working_place',$workplace));

		if(!empty($unitPrice)) {
			$unitPrice = $unitPrice+0;
			array_push($where, "t.unit_price_low >= $unitPrice");
		}
		$join[] = CaseBaseService::caseJoinLp(TRUE);
		if (!empty($usr_code)) {
			if ($user_role !== 'ADMIN') {
				$join[] = CaseBaseService::caseJoinCaseApply(TRUE, $usr_code);
			}
			$join[] = CaseBaseService::caseJoinCaseAttention(TRUE, $usr_code);
		}
		return CaseBaseService::findCasesWithRole($where, array("*"), $join, $page, $user_role, $orderBy);
	}
	
	/**
	 * 根据案件ID查找单个案件
	 * @param unknown_type $id
	 * @param unknown_type $user_role
	 */
	public static function findCaseById($id, $user_role=null) {
		$sql = "select t.*, u.code lp_code, u.fullname lp_name from cases t ";
		$join = "inner join usr u on t.lp_code = u.code ";
		$where = "where t.id = '$id' ";
		$sql = $sql.$join.$where;
		return BaseService::fetchRowBySql($sql);
	}
	
	/**
	 * 根据案件编码查找单个案件
	 * @param String $code
	 */
	public static function findCaseByCode($code) {
		$sql = "select t.*, u.code lp_code, u.fullname lp_name from cases t ";
		$join = "inner join usr u on t.lp_code = u.code ";
		$where = "where t.code = '$code' ";
		$sql = $sql.$join.$where;
		return BaseService::fetchRowBySql($sql);
	}

	/**
	 * 根据案件ID查找单个案件
	 * @param unknown_type $id
	 * @param unknown_type $user_role
	 */
	public static function findCaseByIdWithUsrAttention($case_code, $usr_code=null) {
		$str = !empty($usr_code) ? ',ca.usr_code attention_user_code' : '';
		$sql = 'select t.*, u.code lp_code, u.fullname lp_name '.$str.' 
			from cases t 
			inner join usr u on t.lp_code = u.code ';
		if (!empty($usr_code)) {
			$sql.=" left join case_attention ca on t.code=ca.case_code and ca.usr_code='$usr_code' ";
		}
		$sql .= "where t.code = '$case_code' ";
		return BaseService::fetchRowBySql($sql);
	}
	
	/**
	 * 查看人数自增1
	 * Enter description here ...
	 * @param unknown_type $case_id
	 */
	public static function addViewNumById($case_code) {
		$row = array('viewers'=>new Zend_Db_Expr('viewers+1'));
		return BaseService::update(self::$table, "code='$case_code'", $row);
	}

	/**
	 * 查找指定法人已发布案件列表
	 * @param unknown_type $usrId
	 * @param unknown_type $caseNameOrCode
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function searchReleaseCaseList($lp_code, $caseNameOrCode=array(), $page=NULL) {
		$join[] = CaseBaseService::caseJoinLp(true);
		$where = array();
		array_push($where, CaseBaseService::genWhereOptionOnCaseNameOrCode($caseNameOrCode));
		array_push($where, "type in ('R')");
		return CaseBaseService::findCasesForSelf($lp_code, $where, array('*'), $join, $page);
	}
	
	/**
	 * 查找指定法人发布的有效案件列表
	 * @param unknown_type $usrId
	 * @param unknown_type $caseNameOrCode
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function searchEffectiveCaseList($lp_code, $caseNameOrCode=array(), $page=NULL) {
		$join[] = CaseBaseService::caseJoinLp(true);
		$where = array();
		array_push($where, CaseBaseService::genWhereOptionOnCaseNameOrCode($caseNameOrCode));
		array_push($where, "type in ('R', 'C')");
		return CaseBaseService::findCasesForSelf($lp_code, $where, array('*'), $join, $page);
	}
	
	/**
	 * 查找指定法人发布的有效案件数量
	 * @param unknown_type $usrId
	 * @param unknown_type $caseNameOrCode
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function countEffectiveCases($lp_code) {
		$sql = "select count(1) count from cases c where c.type in ('R', 'C') and c.lp_code='$lp_code'";
		return BaseService::fetchOneBySql($sql);
	}
	
	/**
	 * 查找指定法人员工数量
	 * @param unknown_type $usrId
	 * @param unknown_type $caseNameOrCode
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function countEffectiveEmp($lp_code) {
		$sql = "select count(1) count 
			from resume r 
			where ok_base='Y' and ok_biz='Y' and ok_other='Y' and enabled='Y' 
			and r.lp_code='$lp_code'"
		;
		return BaseService::fetchOneBySql($sql);
	}

	/**
	 * 查找指定法人员工数量
	 * @param unknown_type $usrId
	 * @param unknown_type $caseNameOrCode
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function countAllEmp($lp_code) {
		$sql = "select count(1) count from resume r where r.lp_code='$lp_code'";
		return BaseService::fetchOneBySql($sql);
	}
	
	/**
	 * 查找指定法人发布的历史案件列表
	 * @param unknown_type $usrId
	 * @param unknown_type $caseNameOrCode
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function searchHistoryCaseList($lp_code, $caseNameOrCode=array(), $page=NULL) {
		$join[] = CaseBaseService::caseJoinLp(true);
		$where = array();
		array_push($where, CaseBaseService::genWhereOptionOnCaseNameOrCode($caseNameOrCode));
		array_push($where, "type = 'E'");
		return CaseBaseService::findCasesForSelf($lp_code, $where, array('*'), $join, $page);
	}
	
	/**
	 * 查找指定法人发布的草稿案件列表
	 * @param unknown_type $usrId
	 * @param unknown_type $caseNameOrCode
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function searchDraftCasesList($lp_code, $caseNameOrCode = array(), $page = null) {
		$join[] = CaseBaseService::caseJoinLp(true);
		$where = array();
		array_push($where, CaseBaseService::genWhereOptionOnCaseNameOrCode($caseNameOrCode));
		array_push($where, "type = 'U'");
		if(!empty($lp_code))
			return CaseBaseService::findCasesForSelf($lp_code, $where, array('*'), $join, $page);
		else
			return CaseBaseService::findCasesWithoutRole($where, array('*'), $join, $page);
	}
	
	/**
	 * 手动关闭一个案件，填入关闭标记以及关闭日期
	 * @param unknown_type $ids ex: 1,2,3
	 */
	public static function closeCases($ids) {
		$db = BaseService::getDb();
		$row = array("type"=>"C", "close_date"=>UtilService::getCurrentTime());
		if (is_array($ids)) 
			$where = $db->quoteInto("type !='C' and id in (?)", $ids);
		else 
			$where = $db->quoteInto("type !='C' and id = ?", $ids);
		return BaseService::update(self::$table, $where, $row);
	}
	
	/**
	 * 查找指定法人下的有效案件以及应聘信息
	 * @param unknown_type $lp_code
	 * @param unknown_type $option
	 * @param unknown_type $page
	 */
	public static function searchEffectiveApplyMgtCaseList($lpCode, $option, $page) {
		$sql = "select * from (select t.*, usr.fullname lp_name, usr.lp_linkman, usr.tel lp_tel";
		$sql = $sql.", (select count(1) from case_apply ca where ca.case_code=t.code) count_total";
		$sql = $sql.", (select count(1) from case_apply ca where ca.case_code=t.code and ca.status in ('NO_VOTE', 'NO_RECOMMEND', 'RECOMMEND')) count_no_vote";
		$sql = $sql.", (select count(1) from case_apply ca where ca.case_code=t.code and ca.status in ('INTERVIEW_ADJUST', 'INTERVIEW_DECIDE', 'INTERVIEW_INFORM')) count_process";
		$sql = $sql.", (select count(1) from case_apply ca where ca.case_code=t.code and ca.status = 'INTERVIEW_OK') count_ok";
		$sql = $sql.", (select count(1) from case_apply ca where ca.case_code=t.code and ca.status in ('INTERVIEW_LOSE', 'APPLY_CANCEL')) count_loss";
		$sql = $sql. " from cases t inner join usr on usr.code=t.lp_code";
		if (!empty($lpCode)) {
			$sql .=" and t.lp_code='$lpCode'";
		}
		$sql = $sql." where t.type in ('R', 'C')";
		$sql = $sql.' and '.CaseBaseService::genWhereOptionOnCaseNameOrCode($option);
		$sql = $sql.') temp ';
		$sql = empty($page['sortname']) ? $sql : $sql.' order by '.$page['sortname'].' '.$page['sortorder'];
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
	
	/**
	 * 查找指定法人下的有效案件以及应聘信息
	 * @param unknown_type $lp_code
	 * @param unknown_type $option
	 * @param unknown_type $page
	 */
	public static function searchHistoryApplyMgtCaseList($lpCode, $option, $page, $orderBy=NULL) {
		$sql = "select * from (select t.*, usr.fullname lp_name, usr.lp_linkman, usr.tel lp_tel";
		$sql = $sql.", (select count(1) from case_apply ca where ca.case_code=t.code) count_total";
		$sql = $sql.", (select count(1) from case_apply ca where ca.case_code=t.code and ca.status in ('NO_VOTE', 'NO_RECOMMEND', 'RECOMMEND')) count_no_vote";
		$sql = $sql.", (select count(1) from case_apply ca where ca.case_code=t.code and ca.status in ('INTERVIEW_ADJUST', 'INTERVIEW_DECIDE', 'INTERVIEW_INFORM')) count_process";
		$sql = $sql.", (select count(1) from case_apply ca where ca.case_code=t.code and ca.status = 'INTERVIEW_OK') count_ok";
		$sql = $sql.", (select count(1) from case_apply ca where ca.case_code=t.code and ca.status in ('INTERVIEW_LOSE', 'APPLY_CANCEL')) count_loss";
		$sql = $sql. " from cases t inner join usr on usr.code=t.lp_code";
		if (!empty($lpCode)) {
			$sql .=" and t.lp_code='$lpCode'";
		}
		$sql = $sql." where t.type = 'E'";
		$sql = $sql.' and '.CaseBaseService::genWhereOptionOnCaseNameOrCode($option);
		$sql = $sql.') temp ';
		$sql = empty($page['sortname']) ? $sql : $sql.' order by '.$page['sortname'].' '.$page['sortorder'];
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
	
	/**
	 * 创建一个草稿
	 * @param unknown_type $row
	 */
	public static function createDraftCase($row = array()) {
		$row["code"] = ActiveEtcService::genCode("CS_CODE");
//		if (!empty($row["unit_price_low"])) {
//			$low = EtcService::getValueByTypeAndName("UNIT_PRICE_VIEW_SETTING", "LOW")+0;
//			$row["unit_price_low_view"] = $row["unit_price_low"] - $low;
//		}
//		if (!empty($row["unit_price_high"])) {
//			$high = EtcService::getValueByTypeAndName("UNIT_PRICE_VIEW_SETTING", "HIGH")+0;
//			$row["unit_price_high_view"] = $row["unit_price_high"] + $high;
//		}
		$row["type"] = 'U';
		return self::saveCase($row);
	}
	
	/**
	 * 发布案件，如果案件不存在则直接保存为已发布案件，如果案件已存在则直接修改案件状态为已发布即可
	 * @param int $caseId
	 * @param array $row
	 * @param $lp_code 竞价操作者
	 */
	public static function publishCase($caseId, array $row = null, $lp_code=null) {
		if (empty($row)) $row = array();
		$row["type"] = "R";
		$row["release_date"] = UtilService::getCurrentTime();
		if (empty($caseId)) {
			$row["code"] = ActiveEtcService::genCode("CS_CODE");
			$ret = self::saveCase($row);
		} else {
			$ret = self::updateCase($caseId, $row);
			$code = self::findCaseById($caseId);
			$row["code"] = $code['code'];
		}
		if ($ret !== FALSE) {
			$p_case = $row;
			if (!empty($p_case["akb"]) && $p_case["akb"]+0 > 0 && $p_case["akb"]+0 < 90000000) {
				$int_date = EtcService::getValueByTypeAndName("CASE_OPTION", "TIMELINESS_INTERVEL")+0;
				$end_date = UtilService::dateAddDay(date("Y-m-d"), $int_date);
				$case_AKB_row = array("lp_code"=>$lp_code, "case_code"=>$p_case["code"],
					"amount"=>$p_case["akb"], "start_date"=>date("Y-m-d"), "usr"=>$lp_code, "end_date"=>$end_date);
				$ret = AKBService::insertCaseAKBRecord($case_AKB_row);
				if ($ret !== FALSE) {
					$pre_akb = UsrService::getUsrByCode($lp_code);
					$pre_akb = $pre_akb["balance"];
					$consumeAkb = array('usr_code' => $lp_code, 'in_out' => 'OUT', 
						'points_consume' => $p_case['akb'], 'case_akb_record_id' => $ret, 
						'points_rest' => $pre_akb-$p_case['akb'], 'create_time' => UtilService::getCurrentTime());
					$ret = BaseService::addRow('akb_consume', $consumeAkb);
					if ($ret) {
						return UsrService::updateUsrByCode($lp_code, array("balance"=>$pre_akb-$p_case["akb"]));
					}
				}
			} else {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	/**
	 * 保存一个案件
	 */
	public static function saveCase($row = array()) {
		$sysdate = UtilService::getCurrentTime();
		$row["create_date"] = $sysdate;
		$row["last_modify_time"] = $sysdate;
		return BaseService::save(self::$table, $row);
	}
	
	/**
	 * 修改case
	 * @param unknown_type $id 需要修改数据的案件ID
	 * @param unknown_type $row 需要修改数据的列
	 */
	public static function updateCase($id, $row = array()) {
//		if (!empty($row["unit_price_low"])) {
//			$low = EtcService::getValueByTypeAndName("UNIT_PRICE_VIEW_SETTING", "LOW")+0;
//			$row["unit_price_low_view"] = $row["unit_price_low"] - $low;
//		} else {
//			$row["unit_price_low_view"] = NULL;
//		}
//		if (!empty($row["unit_price_high"])) {
//			$high = EtcService::getValueByTypeAndName("UNIT_PRICE_VIEW_SETTING", "HIGH")+0;
//			$row["unit_price_high_view"] = $row["unit_price_high"] + $high;
//		} else {
//			$row["unit_price_high_view"] = NULL;
//		}
		$sysdate = UtilService::getCurrentTime();
		$row["last_modify_time"] = $sysdate;
		return BaseService::updateByPrimaryKey(self::$table, $id, $row);
	}
	
	/**
	 * 删除案件
	 * @param unknown_type $ids 可以是单个的ID也可以是一个ID组成的array
	 */
	public static function deleteCases($ids) {
		return BaseService::deleteByPrimaryKey(self::$table, $ids);
	}
	
	/**
	 * 删除案件，逻辑删除，修改案件状态为删除
	 * @param unknown_type $ids 可以是单个的ID也可以是一个ID组成的array
	 */
	public static function deleteCasesLogic($ids) {
		$db = BaseService::getDb();
		if (is_array($ids)) 
			$where = $db->quoteInto("type!='D' and id in (?)", $ids);
		else 
			$where = $db->quoteInto("type!='D' and id = ?", $ids);
		$row = array("type" => "D", "delete_date" => UtilService::getCurrentTime());
		return BaseService::update(self::$table, $where, $row);
	}
	
	/**
	 * 根据IDS列表得到所有的指定的案件
	 * @param unknown_type $ids
	 */
	public static function findCasesByIds($ids) {
		$idArray = explode(',', $ids);
		$sql = $db->quoteInto("select t.*, u.code lp_code, u.fullname lp_name from cases t inner join usr u on t.lp_code = u.code where t.id in (?)", $idArray);
		return BaseService::fetchAllBySql($sql);
	}
	
	public static function searchCaseCreatorList($option, $fields=array('*'), $page=NULL) {
		if (empty($option)) $option = array();
		if (!is_array($option)) return FALSE;
		array_push($option, "role_code in ('LP', 'ADMIN')");
		$strFields = implode(',', $fields);
		$sql = "select $strFields from usr where enabled='Y'";
		$whereOption = implode(' and ', $option);
		if (!empty($whereOption)) {
			$sql = $sql.' and '.$whereOption;
		}
		if (empty($page)) {
			return BaseService::fetchAllBySql($sql);
		} else {
			return BaseService::findByPaginationWithSQL($sql, $page);
		}
	}

}


