<?php
include_once 'BaseAdminController.php';
include_once 'EventService.php';

class Admin_Admin_EventController extends BaseAdminController
{
	private $event = array('id'=>NULL, 'code'=>NULL, 'subject'=>NULL, 'type'=>NULL, 'place'=>NULL, 'num'=>NULL, 'city'=>NULL,
							'cost'=>NULL, 'deadline'=>NULL, 'content'=>NULL, 'start_time'=>NULL, 'end_time'=>NULL);
	
    /**
     * 活动目录
     */
    public function eventcatalogAction() {
		$this->layout->headTitle($this->tr->translate('event_catalog'));
		$crumb = array('uri' => '/admin/admin_event/eventcatalog', 'name' => $this->tr->translate('event_catalog'));
		$this->view->layout()->crumbs = array($crumb);
		
		$result = EtcService::getEtcsByType('EVENT_TYPE');
		$this->view->eventType = $result;
    }

    /**
     * 添加活动目录
     */
    public function addeventcatalogAction() {
		$this->layout->headTitle($this->tr->translate('add_event_catalog'));
		$crumb = array('uri' => '/admin/admin_event/addeventcatalog', 'name' => $this->tr->translate('add_event_catalog'));
		$this->view->layout()->crumbs = array($crumb);
		
		$fid = $this->_getParam('fid');
		if (!empty($fid)) {
			$event = EtcService::getEtcById($fid);
			if ($event !== FALSE) {
				$this->view->event = $event;
			}
		}
    }
    
    public function saveeventcatalogAction() {
    	$fid = $this->_getParam('fid');
    	$code = $this->_getParam('code');
    	$name = $this->_getParam('name');
    	
    	if (empty($code)) {
    		$ret = array('err' => 1, 'msg' => '简码不能为空');
			exit(json_encode($ret));
    	} else if (empty($name)) {
    		$ret = array('err' => 1, 'msg' => '目录名不能为空');
			exit(json_encode($ret));
    	} else {
    		$etc = array();
    		$etc['code'] = $code;
    		$etc['name'] = $name;
    		$etc['type'] = 'EVENT_TYPE';
    		$ret = TRUE;
    		if (empty($fid)) {
    			$fid = EtcService::saveEtc($etc);
    			$ret = $fid;
    		} else {
    			$ret = EtcService::updateEtc($fid, $etc);
    		}
    		if ($ret !== FALSE) {
	    		$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'), 'fid' => $fid);
    		} else {
	    		$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
    		}
			exit(json_encode($ret));
    	}
    }
    
    /**
     * 活动留言
     */
    public function eventmsgAction() {
		$this->layout->headTitle($this->tr->translate('event_msg'));
		$crumb = array('uri' => '/admin/admin_event/eventmsg', 'name' => $this->tr->translate('event_msg'));
		$this->view->layout()->crumbs = array($crumb);
    }

    /**
     * 活动列表
     */
    public function eventlistAction() {
		$this->layout->headTitle($this->tr->translate('event_list'));
		$crumb = array('uri' => '/admin/admin_event/eventlist', 'name' => $this->tr->translate('event_list'));
		$this->view->layout()->crumbs = array($crumb);
		
		$result = EventService::searchCountByType();
		$this->view->countMap = $result;
    }
    
    /**
     * 查找活动中的活动
     */
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

    /**
     * 发起活动
     */
    public function initiateeventAction() {
		$this->layout->headTitle($this->tr->translate('initiate_event'));
		$crumb = array('uri' => '/admin/admin_event/initiateevent', 'name' => $this->tr->translate('initiate_event'));
		$this->view->layout()->crumbs = array($crumb);
		
		$fid = $this->_getParam('fid');
		$event = $this->event;
		if (!empty($fid)) {
			$event = EventService::findEventById($fid);
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
	
	/**
	 * 站内活动详情
	 */
	public function eventdetailAction() {
		$this->layout->headTitle($this->tr->translate('site_event_detail'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/admin/admin_event/eventlist', 'name' => $this->tr->translate('site_event'));
		$crumbs[] = array('uri' => '/admin/admin_event/eventdetail', 'name' => $this->tr->translate('site_event_detail'));
		$this->view->layout()->crumbs = $crumbs;
		
		$id = $this->_getParam('fid');
		$event = EventService::findEventById($id);
		$members = EventService::searchEventMember($id);
		$this->view->detail = $event;		
		$this->view->members = $members;		
	}
}