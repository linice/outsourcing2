<?php
class LogService
{
	/**
	 * logs: 保存
	 * @param array(field => value) $row
	 */
	public static function saveLog($row = array()) {
		if (!is_array($row)) {
			error_log(date('Y-m-d H:i:s') . "\t saveLog: Param is not a row. \n", 3, Zend_Registry::get('LOG_FULL_FILENAME'));
			return false;
		}
		$db = Zend_Registry::get('DB');
		try{
			$db->insert('logs', $row);
		} catch (Exception $e) {
			$strRow = json_encode($row);
			error_log(date('Y-m-d H:i:s') . "\t saveLog: row = $strRow, {$e->getMessage()} \n", 3, Zend_Registry::get('LOG_FULL_FILENAME'));
			return false;
		}
		return true;
	}
}