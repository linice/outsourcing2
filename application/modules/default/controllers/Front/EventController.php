<?php 
include_once 'BaseController.php';
include_once 'EventService.php';
include_once 'UtilService.php';

/**
 * 活动
 * @author GONGM
 */
class Front_EventController extends BaseController
{
	private $event = array('id'=>NULL, 'code'=>NULL, 'subject'=>NULL, 'type'=>NULL, 'place'=>NULL, 'num'=>NULL, 'city'=>NULL,
							'cost'=>NULL, 'deadline'=>NULL, 'content'=>NULL, 'start_time'=>NULL, 'end_time'=>NULL);
	
	/**
	 * 站内活动主页面
	 */
	public function eventAction() {
		$this->layout->headTitle($this->tr->translate('site_event'));
		$crumb = array('uri' => '/front_event/event', 'name' => $this->tr->translate('site_event'));
		$this->view->layout()->crumbs = array($crumb);
		
		$result = EventService::searchCountByType();
		$this->view->result = $result;
	}
	
	
	/**
	 * 站内活动详情
	 */
	public function eventdetailAction() {
		$this->layout->headTitle($this->tr->translate('site_event_detail'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_event/event', 'name' => $this->tr->translate('site_event'));
		$crumbs[] = array('uri' => '/front_event/eventdetail', 'name' => $this->tr->translate('site_event_detail'));
		$this->view->layout()->crumbs = $crumbs;
		
		$id = $this->_getParam('fid');
		$event = EventService::findEventById($id);
		$members = EventService::searchEventMember($id);
		$this->view->detail = $event;		
		$this->view->members = $members;		
	}
	
	
	/**
	 * 发起活动页面
	 */
	public function initiateeventAction() {
		$this->layout->headTitle($this->tr->translate('initiate_event'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_event/event', 'name' => $this->tr->translate('site_event'));
		$crumbs[] = array('uri' => '/front_event/initiateevent', 'name' => $this->tr->translate('initiate_event'));
		$this->view->layout()->crumbs = $crumbs;
		
		$fid = $this->_getParam('fid');
		$event = $this->event;
		if (!empty($fid)) {
			$event = EventService::findEventById($fid);
			if (empty($event) || ($this->auth->usr['role_code'] != 'ADMIN' && $event['creator'] != $this->auth->usr['code'])) {
				$this->showErrorMsg('/front_event/event', 'initiate_event');
			}
		}
		
		$eventType = array();
		$etcs = EtcService::getEtcs();
		foreach ($etcs as $etc) {
			if ($etc['type'] == 'EVENT_TYPE') { //国家：日本，中国
				$eventType[$etc['code']] = $etc['name'];
			}
		}
		
		$this->view->eventType = $eventType;
		$this->view->event = $event;
	}
	
	/**
	 * 发布新活动
	 */
	public function publisheventAction() {
		$this->fetchFromParam();
		$result = EventService::saveEventAsPublish($this->event, $this->auth->usr['code']);
		if ($result !== FALSE) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'), 'id'=>$result);
			exit(json_encode($ret));
		} else {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
			exit(json_encode($ret));
		}
	}

	/**
	 * 发布新活动
	 */
	public function saveeventasdraftAction() {
		$this->fetchFromParam();
		$result = EventService::saveEventAsDraft($this->event, $this->auth->usr['code']);
		if ($result !== FALSE) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'), 'id'=>$result);
			exit(json_encode($ret));
		} else {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
			exit(json_encode($ret));
		}
	}
	
	private function fetchFromParam() {
		foreach (array_keys($this->event) as $value) {
			if (is_array($this->event[$value])) 
				$this->event[$value] = $this->_getParam($value);
			else 
				$this->event[$value] = trim($this->_getParam($value));
		}
	}
	
	public function searchsingleeventlistAction() {
		$this->getPagination();
		$events = array();
		$subject = $this->_getParam('subject');
		$type = $this->_getParam('type');
		$dbevent = EventService::searchEventList(self::genSearchVal($subject, $type, $this->auth->usr['code']), $this->pagination);
		if (!empty($dbevent['Rows'])) {
			foreach ($dbevent['Rows'] as $currevent) {
				array_push($events, $this->eventToView($currevent));
			}
		}
		exit(json_encode($this->genPagination($dbevent['Total'], $events)));
	}
	
	public function searchactiveeventlistAction() {
		$this->getPagination();
		$events = array();
		$searchVal = $this->_getParam('searchVal');
		$type = $this->_getParam('type');
		$dbevent = EventService::searchActiveEventList(self::genSearchVal($searchVal, $type), $this->pagination);
		if (!empty($dbevent['Rows'])) {
			foreach ($dbevent['Rows'] as $currevent) {
				array_push($events, $this->eventToView($currevent));
			}
		}
		exit(json_encode($this->genPagination($dbevent['Total'], $events)));
	}
	
	public function searchHistorylistAction() {
		$this->getPagination();
		$events = array();
		$searchVal = $this->_getParam('searchVal');
		$type = $this->_getParam('type');
		$dbevent = EventService::searchHistoryEventList(self::genSearchVal($searchVal, $type), $this->pagination);
		if (!empty($dbevent['Rows'])) {
			foreach ($dbevent['Rows'] as $currevent) {
				array_push($events, $this->eventToView($currevent));
			}
		}
		exit(json_encode($this->genPagination($dbevent['Total'], $events)));
	}
	
	public function searchsingleactiveeventlistAction() {
		$this->getPagination();
		$events = array();
		$searchVal = $this->_getParam('searchVal');
		$type = $this->_getParam('type');
		$dbevent = EventService::searchActiveRelationEventList(self::genSearchVal($searchVal, $type), $this->auth->usr['code'], $this->pagination);
		if (!empty($dbevent['Rows'])) {
			foreach ($dbevent['Rows'] as $currevent) {
				array_push($events, $this->eventToView($currevent));
			}
		}
		exit(json_encode($this->genPagination($dbevent['Total'], $events)));
	}

	public function searchsinglehistoryeventlistAction() {
		$this->getPagination();
		$events = array();
		$searchVal = $this->_getParam('searchVal');
		$type = $this->_getParam('type');
		$usr_code = $this->auth->usr['code'];
		$dbevent = EventService::searchHistoryRelationEventList(self::genSearchVal($searchVal, $type), $usr_code, $this->pagination);
		if (!empty($dbevent['Rows'])) {
			foreach ($dbevent['Rows'] as $currevent) {
				array_push($events, $this->eventToView($currevent));
			}
		}
		exit(json_encode($this->genPagination($dbevent['Total'], $events)));
	}
	
	private function genSearchVal($subject=NULL, $type=NULL, $usrcode=NULL) {
		$val = array();
		if (!empty($subject))
			$val['subject'] = $subject;
		if (!empty($type))
			$val['type'] = $type;
		if (!empty($usrcode))
			$val['creator'] = $usrcode;
		return $val;
	}
	
	public function registAction() {
		$type = $this->_getParam('type');
		$num = $this->_getParam('regist_num');
		$content = $this->_getParam('content');
		$event_id = $this->_getParam('event_id');
		$reply = array();
		$reply['event_id'] = $event_id;
		$reply['content'] = $content;
		$usrCode = $this->auth->usr['code'];
		$reply['update_by'] = $usrCode;
		$reply['update_time'] = UtilService::getCurrentTime();
		$db = $this->db;
		$db->beginTransaction();
		$result = TRUE;
		if ($type == 'regist') {
			$reply['regist_num'] = $num + 1;
			$reply['reply_by'] = $usrCode;
			$reply['create_time'] = UtilService::getCurrentTime();
			$result = EventService::regist($reply);
		} else {
			$reply['regist_num'] = 0;
			$reply['content'] = $content;
			$result = EventService::cancelRegist($reply, $usrCode);
		}
		if ("ERROR_EVENT_REG_FULL" == $result || 'ERROR_EVENT_REG_MORE' == $result) {
			$db->rollback();
			$ret = array('err' => 1, 'msg' => $this->tr->translate($result));
			exit(json_encode($ret));
		} else if ($result !== FALSE) {
			$db->commit();
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
			exit(json_encode($ret));
		} else {
			$db->rollback();
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
			exit(json_encode($ret));
		}
	}
	
	public function searcheventreplylistAction() {
		$this->getPagination();
		$fid = $this->_getParam('fid');
		$result = EventService::searchEventReplyList($fid, $this->pagination);
		exit(json_encode($result));
	}
	
	public function replyAction() {
		$content = $this->_getParam('content');
		$event_id = $this->_getParam('event_id');
		
		$reply = array();
		$reply['event_id'] = $event_id;
		$reply['content'] = $content;
		$usrCode = $this->auth->usr['code'];
		if (empty($usrCode)) $usrCode = '';
		$reply['update_by'] = $usrCode;
		$reply['update_time'] = UtilService::getCurrentTime();
		
		$db = $this->db;
		$db->beginTransaction();
		$reply['reply_by'] = $usrCode;
		$reply['create_time'] = UtilService::getCurrentTime();
		$reply['content'] = $content;
		$result = EventService::regist($reply, $usrCode);
		if ($result !== FALSE) {
			$db->commit();
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
			exit(json_encode($ret));
		} else {
			$db->rollback();
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
			exit(json_encode($ret));
		}
	}
	
	public function deleteAction() {
		$ids = $this->_getParam('ids');
		$result = FALSE;
		if (!empty($ids)) {
			$value = explode(',', $ids);
			$result = EventService::deleteEvent($value);
		}
		if ($result !== FALSE) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
			exit(json_encode($ret));
		} else {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
			exit(json_encode($ret));
		}
	} 
}