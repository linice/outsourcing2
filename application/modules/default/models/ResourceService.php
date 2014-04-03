<?php
include_once 'BaseService.php';
include_once 'PermissionService.php';


class ResourceService
{
	/**
	 * resource: 新增
	 * @param array() $row
	 */
	public static function addRsc($row) {
		return BaseService::addRow('resource', $row);
	}
	
	
	/**
	 * resource: 保存，并将资源保存到权限表permission
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 */
	public static function saveRsc($module, $controller, $action) {
		$db = BaseService::getDb();
		$module = strtolower($module);
		$controller = strtolower($controller);
		$action = strtolower($action);
		$code = $module.'/'.$controller.'/'.$action;
		$dbRsc = self::getRscByCode($code, 1);
		if (empty($dbRsc)) {
			$rsc = array('code' => $code, 'module' => $module, 'controller' => $controller, 'action' => $action);
			$db->beginTransaction();
			$result = self::addRsc($rsc);
			if ($result) {
				$result2 = PermissionService::savePerms($code);
				if ($result2) {
					$db->commit();
					return true;
				}
			}
			$db->rollback();
			return false;
		}
		//表示已经存在
		return -1;
	}
	
	
	/**
	 * resource：清空 
	 */
	public static function clearRscs() {
		$db = BaseService::getDb();
		$sqlRsc = "truncate table resource";
		$sqlPerm = "truncate table permission";
		$db->beginTransaction();
		$result = BaseService::exeDelSql($sqlRsc);
		if ($result) {
			$result2 = BaseService::exeDelSql($sqlPerm);
			if ($result2) {
				$db->commit();
				return true;
			}
		}
		$db->rollback();
		return false;
	}
	
	
	/**
	 * resource: 查询
	 * @param string $code
	 */
	public static function getRscByCode($code, $fields) {
		return BaseService::getRowByCode('resource', $code, $fields);
	}
	
	
	
} //End: class ResourceService