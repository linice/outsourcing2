<?php
/**
 * 使用memcache
 */
include_once 'LogService.php';
include_once 'BaseService.php';
include_once 'BaseService2.php';


class EtcService2 { //extends BaseService {
	/**
	 * etc：查询
	 * @param array(string) $fields
	 */
	public static function getEtcs($fields = array('*'), $delMc = false) {
		if (empty($fields) || !is_array($fields)) {
			return false;
		}
		$strFields = implode(',', $fields);
		$sql = "select $strFields from etc 
			where enabled = 'Y'
			order by type, idx";
		return BaseService2::getAllBySql($sql, $delMc);
	}


	/**
	 * etc：查询，根据类型。
	 * 排序：根据idx
	 * @param string $type
	 * @param array(string) $fields
	 */
	public static function getEtcsByType($type, $fields = array('*'), $delMc = false) {
		if (empty($type) || !is_string($type)) {
			return false;
		}
		if (empty($fields) || !is_array($fields)) {
			return false;
		}
		$type = addslashes($type);
		$strFields = implode(',', $fields);
		$sql = "select $strFields from etc 
			where type = '$type'
				and enabled = 'Y' 
			order by idx";
		return BaseService2::getAllBySql($sql, $delMc);
	}

	
	/**
	 * etc：查询，根据type, code
	 * @param string $type
	 * @param string $code
	 */
	public static function getEtcValue($type, $code, $delMc = false) {
		if (empty($type) || !is_string($type)) {
			return false;
		}
		if (empty($code) || !is_string($code)) {
			return false;
		}
		$type = addslashes($type);
		$sql = "select value from etc 
			where type = '$type'
				and code = '$code'
				and enabled = 'Y'";
		return BaseService2::getOneBySql($sql, $delMc);
	}

	
	/**
	 * etc：查询，根据类型。
	 * 排序：根据idx
	 * @param array(string) $types
	 * @param array(string) $fields
	 */
	public static function getEtcsByTypes($types, $fields = array('*'), $delMc = false) {
		if (empty($types) || !is_array($types)) {
			return false;
		}
		if (empty($fields) || !is_array($fields)) {
			return false;
		}
		$strTypes = implode("','", $types);
		$strTypes = "'" . $strTypes . "'";
		$strFields = implode(',', $fields);
		$sql = "select $strFields from etc 
			where type in ($strTypes)
				and enabled = 'Y' 
			order by type, idx";
		return BaseService2::getAllBySql($sql, $delMc);
	}


} //End: class EtcService2