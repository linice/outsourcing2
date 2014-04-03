<?php
include_once 'LogService.php';
include_once 'BaseService.php';
include_once 'UtilService.php';
include_once 'ActiveEtcService.php';
include_once 'BaseService2.php';

/**
 * 活动
 * @author GONGM
 */
class EventService {
	
	private static $table = 'event';
	
	public static function saveEvent($row = array()) {
		$currtime = UtilService::getCurrentTime();
		$row['create_time'] = $currtime;
		$row['update_time'] = $currtime;
		return BaseService::save(self::$table, $row);
	}
	
	public static function saveEventAsPublish($row = array(), $usr_code) {
    	$row['update_by'] = $usr_code;
		if (empty($row['id'])) {
			$row['creator'] = $usr_code;
	    	$row['enabled'] = 'Y';
	    	$row['publish_time'] = UtilService::getCurrentTime();
			$row['code'] = ActiveEtcService::genCode('EVENT_CODE');
	    	return self::saveEvent($row);
		} else {
			return self::updateEvent($row['id'], $row);
		}
	}
	
	public static function saveEventAsDraft($row = array(), $usr_code) {
		$row['code'] = ActiveEtcService::genCode('EVENT_CODE');
		$row['creator'] = $usr_code;
    	$row['enabled'] = '';
    	$row['update_by'] = $usr_code;
    	//$row['publish_time'] = NULL;
    	return self::saveEvent($row);
	}
	
	public static function updateEvent($id, $row = array()) {
		return BaseService::updateByPrimaryKey(self::$table, $id, $row);
	}
	
	public static function deleteEvent($id) {
		return BaseService::deleteByPrimaryKey(self::$table, $id);
	}
	
	public static function findEventById($id) {
		$sql = 'select t.*, u1.fullname, u2.fullname update_by_name from '.self::$table." t 
			left join usr u1 on t.creator = u1.code 
			left join usr u2 on t.update_by = u2.code where t.id='$id'";
		return BaseService::fetchRowBySql($sql);
	}
	
	public static function searchEventList($searchParam, $page, $where=NULL) {
		$sql = 'select t.*, u.fullname from '.self::$table." t left join usr u on t.creator = u.code where 1=1";
		if (!empty($where)) {
			$sql = $sql.' and '.$where;
		}
		if (!empty($searchParam)) {
			$whereArray = array();
			foreach ($searchParam as $key => $value) {
				switch($key) {
					case 'subject':		array_push($whereArray, "t.subject like '%$value%'");break;
					//case 'subject':		array_push($whereArray, "t.subject like '%$value%'");break;
					default :			array_push($whereArray, "t.$key = '$value'");break;
				}
			}
			$whereOption = implode(' and ', $whereArray);
			$whereOption = empty($whereOption) ? $whereOption : " and ".$whereOption;
			$sql = $sql.$whereOption;
		}
		if (isset($page['sortname']) && !empty($page['sortname'])) {
			$sql = 'select * from ('.$sql.') _tmp order by '.$page['sortname'].' '.$page['sortorder'];
		} else {
			$sql = 'select * from ('.$sql.') _tmp order by update_time desc';
		}
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
	
	public static function searchActiveEventList($searchParam, $page) {
		if (!empty($searchParam)) {
			$searchParam['enabled'] = 'Y';
		}
		return self::searchEventList($searchParam, $page);
	}
	
	public static function searchHistoryEventList($searchParam, $page) {
		if (!empty($searchParam)) {
			$searchParam['enabled'] = 'N';
		}
		return self::searchEventList($searchParam, $page);
	}
	
	public static function searchRelationEventList($searchParam, $usr_code, $page) {
		$where = "exists(select 1 from event_reply r where r.event_id = t.id and r.reply_by='$usr_code')";
		return self::searchEventList($searchParam, $page, $where);
	}
	
	public static function searchActiveRelationEventList($searchParam, $usr_code, $page) {
		if (!empty($searchParam)) {
			$searchParam['enabled'] = 'Y';
		}
		return self::searchRelationEventList($searchParam, $usr_code, $page);
	}
	
	public static function searchHistoryRelationEventList($searchParam, $usr_code, $page) {
	if (!empty($searchParam)) {
			$searchParam['enabled'] = 'N';
		}
		return self::searchRelationEventList($searchParam, $usr_code, $page);
	}
	
	public static function searchCountByType() {
		$sql = 'select count(t.id) count, etc.code type, etc.name from etc left join '.self::$table." t on t.type=etc.code and t.enabled='Y' where etc.type='EVENT_TYPE' group by etc.code, etc.name";
		return BaseService::fetchAllBySql($sql);
	}
	
	public static function searchEventMember($fid) {
		$sql = "select distinct t.reply_by, u.fullname from event_reply t
			left join usr u on u.code = t.reply_by
			where t.regist_num >=1 and t.reply_id <=0 and t.event_id = '$fid'";
		return BaseService::fetchAllBySql($sql);
	}
	
	public static function saveEventReply($row) {
		$updateRow = array('reply_num'=>new Zend_Db_Expr("reply_num+1"));
		$rst = BaseService::updateByPrimaryKey(self::$table, $row['event_id'], $updateRow);
		if ($rst !== FALSE) {
			return BaseService::save("event_reply", $row);
		}
		return FALSE;
	}
	
	public static function updateEventReply($where, $row) {
		$row = array('update_times'=>new Zend_Db_Expr('update_times+1'));
		return BaseService::update("event_reply", $where, $row);
	}
	
	public static function regist($row) {
		$regist_num = isset($row['regist_num']) ? $row['regist_num'] : 0;
		$rst = TRUE;
		$sql = "select regist_num, num from ".self::$table." where id=".$row['event_id'];
		$event = BaseService::fetchRowBySql($sql);
		if ($event['num'] < $event['regist_num'] + $regist_num) {
			return "ERROR_EVENT_REG_FULL";
		}
		$sql = "select count(1) count from event_reply where regist_num >= 1 and reply_by = '".$row['update_by']."' and event_id=".$row['event_id'].'';
		$count = BaseService::fetchOneBySql($sql);
		if ($count > 0) {
			return "ERROR_EVENT_REG_MORE";
		}
		if ($regist_num+0>0) {
			$updateRow = array('regist_num'=>new Zend_Db_Expr("regist_num+".$regist_num));
			$rst = BaseService::updateByPrimaryKey(self::$table, $row['event_id'], $updateRow);
		}
		if ($rst !== FALSE) {
			return self::saveEventReply($row);
		}
		return FALSE;
	}
	
	public static function cancelRegist($row, $usrCode) {
		return self::updateEventReply("regist_num>=1 and reply_by='$usrCode' and event_id=".$row['event_id'], $row);
	}
	
	public static function searchEventReplyList($fid, $page) {
		$sql = "select t.*, u.fullname from event_reply t
			left join usr u on u.code = t.reply_by
			where t.reply_id <=0 and t.event_id = '$fid' order by create_time desc";
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
}