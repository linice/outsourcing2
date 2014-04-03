<?php
include_once 'LogService.php';
include_once 'BaseService.php';
include_once 'UtilService.php';
include_once 'BaseService2.php';


class AKBService {
	/**
	 * 插入一条案件竞价消耗的AKB纪录
	 * @param array $row
	 */
	public static function insertCaseAKBRecord(array $row) {
		return BaseService::save("case_akb_record", $row);
	}
	
	
	/**
	 * akb_deposit,akb_consume：保存
	 * @param array(field => value) $depositAkb
	 */
	public static function saveDepositAkb($depositAkb) {
		$db = BaseService::getDb();
		$db->beginTransaction();
		//添加入金
		$result = BaseService::addRow('akb_deposit', $depositAkb);
		if ($result) {
			//更新剩余点数
			$sql = "update usr set balance = balance + {$depositAkb['points']} where code = '{$depositAkb['usr_code']}'";
			$result2 = BaseService::exeUpSql($sql);
			if ($result2) {
				//获取最新剩余点数
				$balance = BaseService::getOneBySql("select balance from usr where code = '{$depositAkb['usr_code']}'");
				if ($balance !== false) {
					$consumeAkb = array(
						'usr_code' => $depositAkb['usr_code'], 'in_out' => 'IN', 
						'points_consume' => $depositAkb['points'], 'points_rest' => $balance, 
						'svc' => $depositAkb['svc'], 'create_usr' => $depositAkb['create_usr'], 
						'create_time' => $depositAkb['create_time'], 'update_usr' => $depositAkb['update_usr']
					);
					$result3 = BaseService::addRow('akb_consume', $consumeAkb);
					if ($result3) {
						$db->commit();
						return true;
					}
				}
			} //End: if ($result2)
		} //End: if ($result)
		$db->rollback();
		return FALSE;
	} 
	
	
} //End: class AKBService