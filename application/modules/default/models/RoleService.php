<?php
include_once 'BaseService.php';


class RoleService
{
	/**
	 * role: 查询
	 * @param string $code
	 */
	public static function getAllRoleCodes() {
		$roleCodes = array();
		$roles = BaseService::getAll('role', array('distinct code'));
		foreach ($roles as $role) {
			$roleCodes[] = $role['code'];
		}
		return $roleCodes;
	}
	
	
} //End: class RoleService