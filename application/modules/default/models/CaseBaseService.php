<?php
include_once 'LogService.php';
include_once 'BaseService.php';
include_once 'UtilService.php';
include_once 'CaseService.php';

class CaseBaseService {
	private static $table = "cases";
	
	/**
	 * 关联案件创建法人信息 inner join
	 * @param $joinLp
	 * @param $fields
	 */
	public static function caseJoinLp($joinLp, $fields=array('u.fullname lp_name, u.lp_linkman, u.tel lp_tel')) {
		return $joinLp ? array("type"=>"inner join", "table"=>"usr u", "on"=>"t.lp_code=u.code", "fields"=>$fields):NULL;
	}

	/**
	 * 关联关注信息 left join 需要得到指定人员是否有关注该案件
	 * @param $joinLp
	 * @param $fields
	 */
	public static function caseJoinCaseAttention($join, $usr_code, $fields=array('attention.usr_code attention_usr_code')) {
		return $join ? array("type"=>"left join", "table"=>"case_attention attention", "on"=>"t.code=attention.case_code and attention.usr_code='$usr_code'", "fields"=>$fields):NULL;
	}

	/**
	 * 关联关注信息 left join 需要得到指定人员是否有关注该案件
	 * @param $joinLp
	 * @param $fields
	 */
	public static function caseInnerJoinCaseAttention($join, $usr_code, $fields=array('attention.usr_code attention_usr_code')) {
		return $join ? array("type"=>"inner join", "table"=>"case_attention attention", "on"=>"t.code=attention.case_code and attention.usr_code='$usr_code'", "fields"=>$fields):NULL;
	}

	/**
	 * 关联应聘信息 left join 需要得到指定人员是否有应聘过该案件
	 * @param $joinLp
	 * @param $fields
	 */
	public static function caseJoinCaseApply($join, $usr_code, $fields=array('apply.id apply_id')) {
		return $join ? array("type"=>"left join", "table"=>"case_apply apply", "on"=>"t.code=apply.case_code and apply.usr_code='$usr_code'", "fields"=>$fields):NULL;
	}
	
	/**
	 * 对当前用户的角色进行转换
	 * @param unknown_type $usr_role
	 */
	public static function genVisibility($usr_role) {
		if (empty($usr_role)) return "VISIBILITY_ADMIN_MEMBER_LP";
		return $usr_role;
	}
	
	/**
	 * 法人中心下的各种案件列表的下拉单查询条件转换
	 * @param unknown_type $nameOrCode
	 */
	public static function genWhereOptionOnCaseNameOrCode($nameOrCode) {
		if (empty($nameOrCode) || !is_array($nameOrCode)) return '1';
		$range = $nameOrCode['range'];
		$value = $nameOrCode['value'];
		$where = '';
		switch ($range) {
			case 'code': 
			case 'name': $where = "t.$range like '%$value%'";break;
			case 'all': $where = "(t.name like '%$value%' or t.code like '%$value%')";break;
			default: $where = '1';break;
		}
		return $where;
	}

	/**
	 * 法人中心下的各种案件列表的下拉单查询条件转换
	 * @param unknown_type $nameOrCode
	 */
	public static function genWhereOptionOnEmpNameOrCode($nameOrCode, $st='t') {
		if (empty($nameOrCode) || !is_array($nameOrCode)) return '1';
		$range = $nameOrCode['range'];
		$value = $nameOrCode['value'];
		$where = '';
		switch ($range) {
			case 'code': $where = "$st.code like '%$value%'";break;
			case 'name': $where = "$st.fullname like '%$value%'";break;
			case 'all': $where = "($st.fullname like '%$value%' or $st.code like '%$value%')";break;
			default: $where = '1';break;
		}
		return $where;
	}
	
	/**
	 * 根据用户的角色进行可见性限定的
	 * @param unknown_type $where
	 * @param unknown_type $fields
	 * @param unknown_type $joinOption
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function findCasesWithRole($where=array(), $fields=array("*"), $joinOption=NULL, $page=NULL, $user_role=NULL, $orderBy=NULL) {
		return self::findCases($where, $fields, $joinOption, $page, self::genVisibility($user_role), $orderBy);
	}
	
	/**
	 * 不根据用户的角色进行可见性限定的
	 * @param unknown_type $where
	 * @param unknown_type $fields
	 * @param unknown_type $joinOption
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function findCasesWithoutRole($where=array(), $fields=array("*"), $joinOption=NULL, $page=NULL, $orderBy=NULL) {
		return self::findCases($where, $fields, $joinOption, $page, NULL, $orderBy);
	}
	
	/**
	 * 查询自己创建的案件，不用根据角色进行可见性限定
	 * @param unknown_type $where
	 * @param unknown_type $fields
	 * @param unknown_type $joinOption
	 * @param unknown_type $page
	 * @param unknown_type $user_role
	 */
	public static function findCasesForSelf($lp_code, $where=array(), $fields=array("*"), $joinOption=NULL, $page=NULL, $orderBy=NULL) {
		array_push($where, "t.lp_code='$lp_code'");
		return self::findCases($where, $fields, $joinOption, $page, NULL, $orderBy);
	}
	
	/**
	 * 案件统一查找方法
	 * @param unknown_type $where array("a=b","c=d")所有的条件都是and进行联接，如果有or条件，则先联接后再传入
	 * @param unknown_type $fields array(*) 需要查找的cases表中的字段
	 * @param unknown_type $joinOption 联接的各种参数[array(type=>'left join',table=>'table b',on=>"t.oid=b.id",fields=array(b.field))]
	 * @param unknown_type $page 分页参数array(start,limit)
	 * @param unknown_type $user_role 权限，案件有查看权限的限制
	 */
	private static function findCases($where=array(), $fields=array("*"), $joinOption=NULL, $page=NULL, $user_role=NULL, $orderBy=NULL) {
		if (!is_array($where) || !is_array($fields)) return FALSE;
		if (!empty($user_role)) 
			array_unshift($where, "t.visibility like '%$user_role%'");
		$whereOption = implode(' and ', $where);
		$whereOption = empty($whereOption) ? $whereOption : "where ".$whereOption;
		if (!empty($orderBy) && empty($page)) {
			$whereOption = $whereOption.' order by '.$orderBy;
		} else if (!empty($orderBy)) {
			$whereOption = $whereOption.' order by '.$orderBy;
		} else if (empty($page)) {
			$whereOption = $whereOption.' order by t.akb desc';
		}
		$tables = self::$table;
		if (!empty($joinOption) && is_array($joinOption)) {
			if ($fields[0] == "*") 
				array_splice($fields, 0, 1, "t.*");
			$tables = array($tables." t");
			foreach ($joinOption as $joinValue) {
				if (is_array($joinValue)) {
					array_push($tables, $joinValue["type"]);
					array_push($tables, $joinValue["table"]);
					array_push($tables, "on");
					array_push($tables, $joinValue["on"]);
					$fields = array_merge($fields, $joinValue["fields"]);
				}
			}
		}
		if (is_array($tables)) $tables = implode(" ", $tables);
		if (empty($page)) {
			$fieldsStr = implode(",", $fields);
			$sql = "select $fieldsStr from $tables $whereOption";
			return BaseService::fetchAllBySql($sql);
		} else 
			return BaseService::findByPagination($fields, $tables, $whereOption, $page);
	}
	
	/**
	 * 专用于查询案件的数量
	 * @param unknown_type $where
	 * @param unknown_type $user_role
	 */
	public static function countCases($where=array(), $user_role=NULL) {
		if (!is_array($where)) return FALSE;
		if (!empty($user_role)) 
			array_unshift($where, "visibility like '%$user_role%'");
		$whereOption = implode(' and ', $where);
		$whereOption = empty($whereOption) ? $whereOption : "where ".$whereOption;
		$tables = self::$table;
		$sql = "select count(1) from $tables $whereOption";
		return BaseService::fetchOneBySql($sql);
	}
	
}


