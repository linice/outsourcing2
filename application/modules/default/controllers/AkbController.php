<?php
include_once 'BaseController.php';
include_once 'BaseService2.php';
include_once 'UtilService.php';
include_once 'EtcService.php';
include_once 'BaseService.php';


class AkbController extends BaseController
{
    /**
     * 入金查询页面
     */
    public function depositakbAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('DEPOSIT_AKB_QUERY'));
    	$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
	    $crumbs[] = array('uri' => '/akb/depositakb', 'name' => $this->tr->translate('DEPOSIT_AKB_QUERY'));
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
    	$crumbs = array(); //用于导航
    	$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
	    $crumbs[] = array('uri' => '/akb/consumeakb', 'name' => $this->tr->translate('HISTORY_CONSUME_DETAIL_QUERY'));
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
     * 查询购买点数
     */
    public function getdepositakbAction() {
    	//获取参数：分页，最小入金金额，最大入金金额，服务
    	$this->getPagination();
    	$sParam = $this->_getParam('param');
    	$usrCode = $this->auth->usr['code'];
    	if (!empty($sParam)) {
	    	$param = json_decode($sParam, true);
	    	foreach ($param as $p) {
	    		switch ($p['name']) {
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
	    	$cond = " usr_code = '$usrCode' ";
	    	if (!empty($minAmt)) {
	    		$cond .= " and amt >= $minAmt ";
	    	}
	    	if (!empty($maxAmt)) {
	    		$cond .= " and amt <= $maxAmt ";
	    	}
	    	if (!empty($svc)) {
	    		$cond .= " and svc = '$svc' ";
	    	}
	    	$sql = "select * from akb_deposit
	    		where $cond
	    		order by create_time desc
	    	";
    	} else {
	    	$sql = "select * from akb_deposit where usr_code = '$usrCode' order by create_time desc";
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
     * 查询消费点数
     */
    public function getconsumeakbAction() {
    	//获取参数：分页，最小入金金额，最大入金金额，服务
    	$this->getPagination();
    	$sParam = $this->_getParam('param');
    	
    	//生成sql
    	$cond = ' 1 ';
    	if (!empty($sParam)) {
	    	$param = json_decode($sParam, true);
	    	foreach ($param as $p) {
	    		switch ($p['name']) {
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
	    	if (!empty($minPoints)) {
	    		$cond .= " and points_consume >= $minPoints";
	    	}
	    	if (!empty($maxPoints)) {
	    		$cond .= " and points_consume <= $maxPoints";
	    	}
	    	if (!empty($svc)) {
	    		$cond .= " and svc = '$svc'";
	    	}
	    	$sql = "select * from akb_consume
	    		where $cond
	    		order by create_time desc
	    	";
    	} else {
	    	$sql = "select * from akb_consume where $cond order by create_time desc";
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





} //End: class AkbController