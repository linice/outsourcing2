<?php
include_once 'LogService.php';
include_once 'BaseService.php';

class EtcService {
	/**
	 * etc：查询
	 * @param array(string) $fields
	 */
	public static function getEtcs($fields = array('*')) {
		$strFields = implode(',', $fields);
		$sql = "select $strFields from etc 
			where enabled = 'Y'
			order by type, idx";
		return BaseService::getAllBySql($sql);
	}


	/**
	 * etc：查询，根据类型。
	 * 排序：根据idx
	 * @param string $type
	 * @param array(string) $fields
	 */
	public static function getEtcsByType($type, $fields = array('*')) {
		$type = addslashes($type);
		$strFields = implode(',', $fields);
		$sql = "select $strFields from etc 
			where type = '$type' 
				and enabled = 'Y' 
			order by idx";
		return BaseService::getAllBySql($sql);
	}

	
	/**
	 * etc：查询，根据类型。
	 * 排序：根据idx
	 * @param array(string) $types
	 * @param array(string) $fields
	 */
	public static function getEtcsByTypes($types, $fields = array('*')) {
		if (!is_array($types)) {
			return false;
		}
		$strTypes = implode("','", $types);
		$strTypes = "'" . $strTypes . "'";
		$strFields = implode(',', $fields);
		$sql = "select $strFields from etc 
			where type in ($strTypes)
				and enabled = 'Y' 
			order by type, idx";
		return BaseService::getAllBySql($sql);
	}


	/**
	 * 根据类型及名称得到该自定义项的值
	 * @param unknown_type $type
	 * @param unknown_type $name
	 */
	public static function getValueByTypeAndName($type, $name) {
		$sql = "select name from etc t where t.type = '$type' and t.code = '$name'";
		$ret = BaseService::fetchRowBySql($sql);
		return empty($ret) ? NULL : $ret['name'];
	}
	
	/**
	 * 取得一条记录
	 * @param unknown_type $id
	 * @param unknown_type $fields
	 */
	public static function getEtcById($id, $fields=array('*')) {
		$strFields = implode(',', $fields);
		$sql = "select $strFields from etc 
			where enabled = 'Y' and id = $id";
		return BaseService::getRowBySql($sql);
	}
	
	public static function saveEtc($row) {
		return BaseService::save('etc', $row);
	}
	
	public static function updateEtc($id, $row) {
		return BaseService::updateById('etc', $row, $id);
	}

} //End: class EtcService