<?php
include_once 'BaseService.php';
include_once 'RoleService.php';


class PermissionService
{
	//角色编码
	protected static $roleCodes = array();
	
	
	/**
	 * permission: 新增
	 * @param array() $rows
	 */
	public static function addPerms($rows) {
		return BaseService::addRows('permission', $rows);
	}
	
	
	/**
	 * permission: 保存
	 * @param string $rscCode
	 */
	public static function savePerms($rscCode) {
		if (empty(self::$roleCodes)) {
			self::$roleCodes = RoleService::getAllRoleCodes();
		}
		$perms = array();
		foreach (self::$roleCodes as $roleCode) {
			$perms[] = array('role_code' => $roleCode, 'resource_code' => $rscCode, 'enabled' => 'N');
		}
		return self::addPerms($perms);
	}
} //End: class PermissionService