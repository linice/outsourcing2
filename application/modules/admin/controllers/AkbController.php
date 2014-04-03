<?php
include_once 'BaseAdminController.php';
include_once 'BaseService2.php';
include_once 'UtilService.php';
include_once 'EtcService.php';
include_once 'AKBService.php';
include_once 'BaseService.php';


class Admin_AkbController extends BaseAdminController
{
    /**
     * 入金查询页面
     */
    public function depositakbAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('DEPOSIT_AKB_QUERY'));
    	$crumbs = array();
	    $crumbs[] = array('uri' => '/admin/akb/depositakb', 'name' => $this->tr->translate('DEPOSIT_AKB_QUERY'));
		$this->view->layout()->crumbs = $crumbs;
		
    	//查询杂项，Web app变量
		$types = array('SVC');
		$etcs = EtcService::getEtcsByTypes($types, array('type', 'code', 'name'));
		foreach ($etcs as $etc) {
			switch ($etc['type']) {
				case 'SVC': //服务
					$svcs[] = $etc['code'];
					break;
			}
		}
		
		//view
		$this->view->svcs = $svcs;
    }
    
    
    /**
     * 历史消费明细查询页面
     */
    public function consumeakbAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('HISTORY_CONSUME_DETAIL_QUERY'));
    	$crumbs = array();
	    $crumbs[] = array('uri' => '/admin/akb/consumeakb', 'name' => $this->tr->translate('HISTORY_CONSUME_DETAIL_QUERY'));
		$this->view->layout()->crumbs = $crumbs;
		
		//获取参数
		$lpCode = trim($this->_getParam('lpCode'));
		
		//查询杂项，Web app变量
		$types = array('SVC');
		$etcs = EtcService::getEtcsByTypes($types, array('type', 'code', 'name'));
		foreach ($etcs as $etc) {
			switch ($etc['type']) {
				case 'SVC': //服务
					$svcs[] = $etc['code'];
					break;
			}
		}
		
		//view
		$this->view->lpCode = $lpCode;
		$this->view->svcs = $svcs;
    }
    
    
    /**
     * 查询购买点数
     */
    public function getdepositakbAction() {
    	//获取参数：分页，最小入金金额，最大入金金额，服务
    	$this->getPagination();
    	$sParam = $this->_getParam('param');
    	if (!empty($sParam)) {
	    	$param = json_decode($sParam, true);
	    	foreach ($param as $p) {
	    		switch ($p['name']) {
	    			case 'fullname':
		    			$fullname = $p['value'];
		    			break;
	    			case 'minAmt':
		    			$minAmt = $p['value'];
		    			break;
	    			case 'maxAmt':
		    			$maxAmt = $p['value'];
		    			break;
	    			case 'svc':
		    			$svc = $p['value'];
		    			break;
	    		}
	    	}
	    	$cond = ' 1 ';
	    	if (!empty($fullname)) {
	    		$cond .= " and b.fullname like '%{$fullname}%' ";
	    	}
	    	if (!empty($minAmt)) {
	    		$cond .= " and a.amt >= $minAmt ";
	    	}
	    	if (!empty($maxAmt)) {
	    		$cond .= " and a.amt <= $maxAmt ";
	    	}
	    	if (!empty($svc)) {
	    		$cond .= " and a.svc = '$svc' ";
	    	}
	    	$sql = "select a.*, b.fullname
	    		from akb_deposit as a
				left join usr as b
				on a.usr_code = b.code
	    		where $cond
	    		order by a.create_time desc
	    	";
    	} else {
	    	$sql = "select a.*, b.fullname from akb_deposit as a
				left join usr as b
				on a.usr_code = b.code
		    	order by a.create_time desc";
    	}
    	
    	//查询购买点数
		$pgAkbs = BaseService::getByPageWithSql($sql, $this->pagination);
    	if (!empty($pgAkbs['Rows'])) {
    		foreach ($pgAkbs['Rows'] as &$akb) {
    			$akb['create_date'] = substr($akb['create_time'], 0, 10);
    			$akb['svc_tr'] = $this->tr->translate($akb['svc']);
    		}
    	}
    	
		//返回
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $pgAkbs['Total'], 'Rows' => $pgAkbs['Rows']);
		exit(json_encode($ret));
    }
    

    /**
     * 查询法人的购买点数
     */
    public function getlpdepositakbAction() {
    	//获取参数：分页，最小入金金额，最大入金金额，服务
    	$this->getPagination();
    	$lpCode = trim($this->_getParam('lpCode'));
    	
    	//sql
    	$sql = "select a.* 
    		from akb_deposit as a
    		where a.usr_code = '$lpCode'
	    	order by a.create_time desc";
    	
    	//查询购买点数
		$pgAkbs = BaseService::getByPageWithSql($sql, $this->pagination);
    	if (!empty($pgAkbs['Rows'])) {
    		foreach ($pgAkbs['Rows'] as &$akb) {
    			$akb['create_date'] = substr($akb['create_time'], 0, 10);
    			$akb['svc_tr'] = $this->tr->translate($akb['svc']);
    		}
    	}
    	
		//返回
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $pgAkbs['Total'], 'Rows' => $pgAkbs['Rows']);
		exit(json_encode($ret));
    }


    /**
     * 查询消费点数
     */
    public function getconsumeakbAction() {
    	//获取参数：分页，法人编号，最小入金金额，最大入金金额，服务
    	$this->getPagination();
    	$lpCode = trim($this->_getParam('lpCode'));
    	$sParam = $this->_getParam('param');
    	
    	//校验参数并生成sql
    	$cond = ' 1 ';
    	if (!empty($sParam)) {
	    	$param = json_decode($sParam, true);
	    	foreach ($param as $p) {
	    		switch ($p['name']) {
	    			case 'fullname':
		    			$fullname = $p['value'];
		    			break;
	    			case 'minPoints':
		    			$minPoints = $p['value'];
		    			break;
	    			case 'maxPoints':
		    			$maxPoints = $p['value'];
		    			break;
	    			case 'svc':
		    			$svc = $p['value'];
		    			break;
	    		}
	    	}
	    	if (!empty($fullname)) {
	    		$cond .= " and b.fullname like '%{$fullname}%'";
	    	}
	    	if (!empty($minPoints)) {
	    		$cond .= " and a.points_consume >= $minPoints";
	    	}
	    	if (!empty($maxPoints)) {
	    		$cond .= " and a.points_consume <= $maxPoints";
	    	}
	    	if (!empty($svc)) {
	    		$cond .= " and a.svc = '$svc'";
	    	}
	    	$sql = "select a.*, b.fullname from akb_consume as a
	    			left join usr as b
	    			on a.usr_code = b.code
	    		where $cond
	    		order by a.create_time desc
	    	";
    	} else {
	    	if (!empty($lpCode)) {
	    		$cond .= " and a.usr_code = '$lpCode' ";
	    	}
	    	$sql = "select a.*, b.fullname 
	    		from akb_consume as a
				left join usr as b
				on a.usr_code = b.code
				where $cond
	    		order by a.create_time desc";
    	}
    	
    	//查询购买点数
//    	var_dump($sql);
//    	exit;
		$pgAkbs = BaseService::getByPageWithSql($sql, $this->pagination);
    	if (!empty($pgAkbs['Rows'])) {
    		foreach ($pgAkbs['Rows'] as &$akb) {
    			$akb['create_date'] = substr($akb['create_time'], 0, 10);
    			$akb['in_out_tr'] = $this->tr->translate($akb['in_out']);
    			$akb['svc_tr'] = $this->tr->translate($akb['svc']);
    			$akb['operator_name'] = BaseService::getOneByCond('usr', 'fullname', "code = '{$akb['create_usr']}'");
    		}
    	}
    	
		//返回
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $pgAkbs['Total'], 'Rows' => $pgAkbs['Rows']);
		exit(json_encode($ret));
    }


    /**
     * 保存创建入金 
     */
    public function savedepositakbAction() {
    	//获取参数：入金金额，服务，服务生效日期，服务截止日期，点数
    	$usrCode = trim($this->_getParam('c_usrCode'));
    	$amt = trim($this->_getParam('c_amt'));
    	$svc = trim($this->_getParam('c_svc'));
    	$dateBegin = trim($this->_getParam('dateBegin'));
    	$dateEnd = trim($this->_getParam('dateEnd'));
    	$points = trim($this->_getParam('c_points'));
    	$now = date('Y-m-d H:i:s');
    	
    	//验证参数：入金金额，服务，服务生效日期，服务截止日期，点数
    	if (empty($usrCode)) {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_SELECT') . $this->tr->translate('USR'));
    		exit(json_encode($ret));
    	}
    	if (empty($amt)) {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER') . $this->tr->translate('DEPOSIT_AMT'));
    		exit(json_encode($ret));
    	} else if (!is_numeric($amt)) {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('DEPOSIT_AMT_SHOULD_BE_NUMERIC'));
    		exit(json_encode($ret));
    	}
    	if (empty($dateBegin)) {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER') . $this->tr->translate('SERVICE_TAKE_EFFECT_DATE'));
    		exit(json_encode($ret));
    	}
    	if (empty($dateEnd)) {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER') . $this->tr->translate('SERVICE_DEADLINE'));
    		exit(json_encode($ret));
    	}
    	if (empty($points)) {
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_ENTER') . $this->tr->translate('BUY_POINTS'));
    		exit(json_encode($ret));
    	}
    	
    	//保存入金
    	$depositAkb = array(
    		'usr_code' => $usrCode, 'amt' => $amt, 
    		'svc' => $svc, 'date_begin' => $dateBegin, 
    		'date_end' => $dateEnd, 'points' => $points, 
    		'create_usr' => $this->auth->usr['code'], 'create_time' => $now, 
    		'update_usr' => $this->auth->usr['code']
    	);
    	$result = AKBService::saveDepositAkb($depositAkb);
    	if ($result) {
    		$ret = array('err' => 0, 'msg' => 'Success');
    		exit(json_encode($ret));
    	}
    	$ret = array('err' => 1, 'msg' => 'Error');
    	exit(json_encode($ret));
    }



} //End: class Admin_AkbController