<?php
class UsrSkillService {
	public static $table ='usr_skill';
	/**
	 * usr_skill: 增加
	 * @param array(field => value) $row
	 */
	public static function saveUsrSkill($row = array()) {
		if (!is_array($row)) {
			return false;
		}
		$db = Zend_Registry::get('DB');
		try {
			$db->insert('usr_skill', $row);
		} catch (Exception $e) {
			$jsRow = json_encode($row);
			$log = array('level' => 3, 'msg' => "row: $jsRow, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return true;
	}


	/**
	 * usr_skill: 批量增加
	 * @param array(array(field => value)) $rows
	 */
	public static function saveUsrSkills($rows = array()) {
		if (!is_array($rows)) {
			return false;
		}
		$db = Zend_Registry::get('DB');
		$fields = array();
		foreach ($rows as $row) {
			foreach ($row as $field => $value) {
				$fields[] = $field;
			}
			break;
		}
		$strFields = implode(',', $fields);
		$sql = "insert usr_skill ($strFields) values ";
		foreach ($rows as $r) {
			$sql .= "('" . implode("','", $r) . "'),";
		}
		$sql = rtrim($sql, ' ,');
		try {
			$db->query($sql);
		} catch (Exception $e) {
			$jsRows = json_encode($rows);
			$log = array('level' => 3, 'msg' => "row: $jsRows, {$e->getMessage()}", 'class' => __CLASS__, 'func' => __FUNCTION__);
			LogService::saveLog($log);
			return FALSE;
		}
		return true;
	}

	public static function getTalentByCondition($condition='',$type='*') {
    	$db = Zend_Registry::get('DB');
        $table = self::$table;
		$sql =" select $type from $table where 1   ";
		$sql.=$condition;
//		echo $sql;
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
	 * 取得一个满足条件的SQL语句

	 */
	public static function setSql($field,$value){
		if(!empty($value)){
			$where=" ( ";
			$i=1;
			foreach($value as $val){
				if($i==1){
					$where.=" {$field} like '$val,%' or {$field} like '%,$val,%' or {$field} like '%,$val' ";
				}else{
					$where.=" or {$field} like '$val,%' or {$field} like '%,$val,%' or {$field} like '%,$val'  ";
				}
				$i++;
			}
			$where.=" ) ";
			return $where;
		}else{
			return ' 1 ';
		}
	}

    public static function getByCondition($condition=null,$type='*',$page=null,$pageSize=null) {
		$db = Zend_Registry::get('DB');
        $table = self::$table;
		$sql =" select $type from $table where 1   ";

		if(isset($condition['usr_code']) && $condition['usr_code']!=''){
				$sql .=" and usr_code = '{$condition['usr_code']}'  ";
		}

        if ($page!=null && $pageSize!=null){
            $start = $pageSize*($page-1);
            $sql .= ' order by department_id';
            $sql .= " limit $start,".$pageSize;
        }

		if($type =='count(*)'){
			return $db->fetchone($sql);
		}else{
			return $db->fetchAll($sql);
		}
    }
}


