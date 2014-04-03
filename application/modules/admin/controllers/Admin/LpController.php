<?php
include_once 'UtilService.php';
include_once 'BaseAdminController.php';
include_once 'LpService.php';
include_once 'ActiveEtcService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


/**
 * 管理员操作法人
 * @author GONGM
 */
class Admin_Admin_LpController extends BaseAdminController {

	//法人注册信息
	private $baseinfo = array('companyName' => null, 'website' => null, 'telephone' => null,
							'address' => null, 'email' => null,
							'passwd' => null, 'passwd2' => null, 'linkman' => null);
	
	//法人修改信息
	private $modBaseinfo = array('code' => null, 'fullname' => NULL, 'lp_address' => NULL, 'website' => NULL, 
		'lp_linkman' => NULL, 'tel' => NULL, 'email' => NULL); 
	
	
	/**
	 * 法人统计
	 */
	public function lpstatsAction() {
		//导航
		$this->layout->headTitle($this->tr->translate('lp_stats'));
		$crumb = array('uri' => '/admin/admin_lp/lpstats', 'name' => $this->tr->translate('lp_stats'));
		$this->view->layout()->crumbs = array($crumb);
	}

	
	/**
	 * 法人注册，营业员注册
	 */
	public function addlpAction() {
		$this->layout->headTitle($this->tr->translate('add_lp'));
		$crumb = array('uri' => '/admin/admin_lp/addlp', 'name' => $this->tr->translate('add_lp'));
		$this->view->layout()->crumbs = array($crumb);
	}
	
	
	/**
     * 获取页面信息装载进baseinfo中
     */
	private function fetchFromParam() {
    	foreach (array_keys($this->baseinfo) as $value) {
    		$this->baseinfo[$value] = trim($this->_getParam($value));
    	}
    }
    
    
	/**
     * 受理法人注册 
     */
    public function savelpAction() {
   		$this->fetchFromParam();
		if (empty($this->baseinfo['companyName'])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('companyName_can_not_be_empty'), 'errField' => "companyName");
			exit(json_encode($ret));
		}
		if (empty($this->baseinfo["linkman"])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('linkman_can_not_be_empty'), 'errField' => "linkman");
			exit(json_encode($ret));
		}
		if (empty($this->baseinfo["telephone"])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('telephone_can_not_be_empty'), 'errField' => "telephone");
			exit(json_encode($ret));
		} else if (!UtilService::isTel($this->baseinfo["telephone"])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('telephone_style_error'), 'errField' => "telephone");
			exit(json_encode($ret));;
		}
		if (empty($this->baseinfo["email"])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('email_can_not_be_empty'), 'errField' => "email");
			exit(json_encode($ret));
		} else if (!UtilService::isEmail($this->baseinfo["email"])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('email_is_not_real'), 'errField' => "email");
			exit(json_encode($ret));
		}
		if (empty($this->baseinfo["passwd"])) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('password_can_not_be_empty'), 'errField' => "passwd");
			exit(json_encode($ret));
		}
		if ($this->baseinfo["passwd"] != $this->baseinfo["passwd2"]) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('passwords_are_not_equal'), 'errField' => "passwd2");
			exit(json_encode($ret));
		}
		
		//查验邮箱是否被注册
    	$dbLp = UsrService::getUsrByEmail($this->baseinfo["email"], array('1'));
    	if ($dbLp !== FALSE) { //返回值不为false，说明此邮箱已被注册
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_ALREADY_EXIST'), 'errField' => "email");
    		exit(json_encode($ret));
    	}
    	
		$usr = array();
    	$usr['role_code'] = 'LP';
    	$usr['email'] = $this->baseinfo['email'];
		$usr['passwd'] = md5($this->baseinfo['passwd']);
		$usr['nickname'] = $this->baseinfo['companyName'];
		$usr['tel'] = $this->baseinfo['telephone'];
		$usr['lp_address'] = $this->baseinfo['address'];
		$usr['lp_linkman'] = $this->baseinfo['linkman'];
		$usr['website'] = $this->baseinfo['website'];
		$usr['fullname'] = $this->baseinfo['companyName'];
    	
		$db = $this->db;
		$db->beginTransaction();
		
    	//查询数据库获取用户唯一编号
    	$lpCode = ActiveEtcService::genCode('LP_CODE');
    	$usr['code'] = $lpCode;
    	
    	$result = LpService::regNewLp($usr, TRUE);
    	if ($result !== FALSE) {
    		$db->commit();
    		$ret = array('err' => 0, 'msg' => $this->tr->translate('lp_reg_succ'));
    		exit(json_encode($ret));
    	} else {
    		$db->rollback();
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('lp_reg_error'));
    		exit(json_encode($ret));
    	}
    }
    
    
    /**
     * 保存法人信息的修改
     */
    public function modlpAction() {
    	//获取参数
    	foreach ($this->modBaseinfo as $k => $v) {
    		$this->modBaseinfo[$k] = trim($this->_getParam($k));
    	}
    	
    	//验证参数
    	$tips = array();
    	$can_not_be_nul = $this->tr->translate('CAN_NOT_BE') . $this->tr->translate('NULL');
    	if (empty($this->modBaseinfo['fullname'])) {
    		$tips[] = array('label' => 'tip_fullname', 'desc' => $this->tr->translate('COM_NAME') . $can_not_be_nul);
    	}
    	if (empty($this->modBaseinfo['lp_linkman'])) {
    		$tips[] = array('label' => 'tip_lp_linkman', 'desc' => $this->tr->translate('CONTACT') . $can_not_be_nul);
    	}
    	if (empty($this->modBaseinfo['tel'])) {
    		$tips[] = array('label' => 'tip_tel', 'desc' => $this->tr->translate('TEL') . $can_not_be_nul);
    	}
    	if (empty($this->modBaseinfo['email'])) {
    		$tips[] = array('label' => 'tip_email', 'desc' => $this->tr->translate('EMAIL') . $can_not_be_nul);
    	} else if (!UtilService::isEmail($this->modBaseinfo['email'])) {
    		$tips[] = array('label' => 'tip_email', 'desc' => $this->tr->translate('EMAIL') . $this->tr->translate('ILLEGAL'));
    	}
    	if (!empty($tips)) {
	    	$ret = array('err' => 2, 'msg' => '', 'tips' => $tips);
	    	exit(json_encode($ret));
    	}
    	
    	//数据库级别的验证
    	$tips = array();
	    //查验邮箱是否被注册
	    $cond = " code != '{$this->modBaseinfo['code']}' and email = '{$this->modBaseinfo['email']}' ";
    	$dbUsr = BaseService::getOneByCond('usr', '1', $cond); 
    	if ($dbUsr) { //返回值为真，说明此邮箱已被注册
    		$tips[] = array('label' => 'tip_email', 'desc' => $this->tr->translate('EMAIL_ALREADY_EXIST'));
    	}
    	if (!empty($tips)) {
	    	$ret = array('err' => 2, 'msg' => '', 'tips' => $tips);
	    	exit(json_encode($ret));
    	}
    	
    	//保存
    	$result = BaseService::updateByCode('usr', $this->modBaseinfo, $this->modBaseinfo['code']);
    	if ($result) {
    		$ret = array('err' => 0, 'msg' => $this->tr->translate('MODIFY') . $this->tr->translate('LP') . $this->tr->translate('SUCCESS'));
	    	exit(json_encode($ret));
    	}
    	$ret = array('err' => 1, 'msg' => $this->tr->translate('MODIFY') . $this->tr->translate('LP') . $this->tr->translate('FAILED'));
	    exit(json_encode($ret));
    }

    
	/**
	 * 法人一览
	 */
	public function lplistAction() {
		//导航
		$this->layout->headTitle($this->tr->translate('lp_list'));
		$crumb = array('uri' => '/admin/admin_lp/lplist', 'name' => $this->tr->translate('lp_list'));
		$this->view->layout()->crumbs = array($crumb);
	}
	
	
	/**
	 * 法人一览--查询
	 */
	public function searchlplistAction() {
		$this->getPagination();
		$cases = array();
		$range = $this->_getParam('range');
		$sValue = $this->_getParam('sValue');
		$cases = "(select count(1) from cases c where c.lp_code=usr.code and c.type!='U') caseCnt";
		$emps = "(select count(1) from resume r where r.lp_code=usr.code and enabled='Y') empCnt";
		//查找所有的法人，包括失效以及未审核的
		$lpList = LpService::findLpByOption($this->genLpOption($range, $sValue), array('*', $cases, $emps), $this->pagination, $reg=FALSE);
		exit(json_encode($lpList));
	}
	
	
	/**
	 * 审核法人
	 */
	public function checklpAction() {
		$code = trim($this->_getParam('code'));
		if (empty($code)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
			exit(json_encode($ret));
		}
		$result = LpService::checkLp($code);
		if ($result !== FALSE) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
		} else {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
		}
		exit(json_encode($ret));
	}
	
	
	/**
	 * 停止法人
	 */
	public function stoplpAction() {
		$code = trim($this->_getParam("code"));
		if (empty($code)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
			exit(json_encode($ret));
		}
		$retVal = LpService::stopLp($code);
		if ($retVal !== FALSE) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
		} else {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
		}
		exit(json_encode($ret));
	}

	/**
	 * 删除法人
	 */
	public function deletelpAction() {
		$code = trim($this->_getParam("code"));
		if (empty($code)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
			exit(json_encode($ret));
		}
		$retVal = LpService::deleteLp($code);
		if ($retVal !== FALSE) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('op_success'));
		} else {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('op_error'));
		}
		exit(json_encode($ret));
	}


	/**
	 * 法人基本信息
	 */
	public function baseinfoAction() {
		//导航
		$this->layout->headTitle($this->tr->translate('lp_baseinfo'));
		$crumb = array('uri' => '/admin/admin_lp/baseinfo', 'name' => $this->tr->translate('lp_baseinfo'));
		$this->view->layout()->crumbs = array($crumb);

		//获取参数
		$lpCode = $this->_getParam('lpCode');
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
		
		//验证参数并传递到View
		if (!empty($lpCode)) {
			$fsUsr = array('*');
			$lp = BaseService::getRowByCond('usr', "code = '$lpCode'", $fsUsr);
			$this->view->svcs = $svcs;
			$this->view->lp = $lp;
		}
	}

	
	/**
	 * 法人基本信息编辑
	 */
	public function editbaseinfoAction() {
		//导航
		$this->layout->headTitle($this->tr->translate('lp_baseinfo_edit'));
		$crumb = array('uri' => '/admin/admin_lp/editbaseinfo', 'name' => $this->tr->translate('lp_baseinfo_edit'));
		$this->view->layout()->crumbs = array($crumb);
	}


	/**
	 * 法人账户消费记录
	 */
	public function acctconsumerecordAction() {
		//导航
		$this->layout->headTitle($this->tr->translate('lp_acct_consume_record'));
		$crumb = array('uri' => '/admin/admin_lp/acctconsumerecord', 'name' => $this->tr->translate('lp_acct_consume_record'));
		$this->view->layout()->crumbs = array($crumb);
	}


	/**
	 * 法人账户入金查询
	 */
	public function acctdepositinquiryAction() {
		//导航
		$this->layout->headTitle($this->tr->translate('lp_acct_deposit_inquiry'));
		$crumb = array('uri' => '/admin/admin_lp/acctdepositinquiry', 'name' => $this->tr->translate('lp_acct_deposit_inquiry'));
		$this->view->layout()->crumbs = array($crumb);
	}

	
} //End: class Admin_Admin_LpController