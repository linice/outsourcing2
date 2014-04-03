<?php
include_once 'BaseService2.php';
include_once 'BaseService.php';


class AclService {
	/**
	 * resource：查询所有资源
	 * @param array(string) $fields
	 */
	public static function getResources($fields = array('*')) {
		return BaseService::getAll('resource', $fields);
	}
	
	
	/**
	 * resource：判断资源是否公开
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 */
	public static function isRscPub($module, $controller, $action) {
		$module = addslashes($module);
		$controller = addslashes($controller);
		$action = addslashes($action);
		$cond = "module = '$module' and controller = '$controller' and action = '$action'";
		$result = BaseService::getOneByCond('resource', 'public', $cond);
		if (!$result || $result == 'Y') { //如果资源不存在，或者资源公开，则返回true
			return true;
		}
		return false;
	}
	
	
	/**
	 * permission：判断给定角色是否有权访问给定资源
	 * @param string $rscCode
	 * @param string $roleCode
	 */
	public static function hasPriv($rscCode, $roleCode) {
		$rscCode = addslashes($rscCode);
		$roleCode = addslashes($roleCode);
		$sql = "select enabled from permission where role_code = '$roleCode' and resource_code = '$rscCode'";
		$hasPriv = BaseService::getOneBySql($sql);
		if ($hasPriv == 'Y') {
			return true;
		}
		return false;
	}
	
	
} //End: class Admin_AclService