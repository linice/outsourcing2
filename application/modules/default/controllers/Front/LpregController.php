<?php
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'LpService.php';
include_once 'ActiveEtcService.php';
include_once 'BaseController.php';

/**
 * 法人注册
 * @author GONGM
 */
class Front_LpregController extends BaseController {
	
	private $baseinfo = array("companyName" => null, "website" => null, "telephone" => null,
							"address" => null, "email" => null,
							"passwd" => null, "passwd2" => null, "linkman" => null);
    
    /**
     * 进入法人注册页面
     */
    public function lpregAction() {
    	//layout
    	if (!empty($this->auth->usr) && in_array($this->auth->usr['role_code'], array('ADMIN', 'ASSIST', 'EDITOR'))) {
    		$this->_helper->layout->setLayout('admin');
    	}
    	
    	//导航
		$this->layout->headTitle($this->tr->translate('lp_register'));
		$crumb = array('uri' => '/front_lpreg/lpreg', 'name' => $this->tr->translate('lp_register'));
		$this->view->layout()->crumbs = array($crumb);
		
		$jsRegisterInfo = $this->_getParam('registerInfo');
    	$registerInfo = json_decode($jsRegisterInfo, true);
    	
    	//View
    	$this->view->registerInfo = $registerInfo;
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
     * 验证公司名称
     * @deprecated
     */
    public function valcompaynameAction() {
    	$companyName = trim($this->_getParam("companyName"));
    	if (empty($companyName)) {
    		exit();
    	} else {
    		$lp = LpService::getLpByName($companyName, array("1"));
    		if ($lp == NULL) {
    			$ret = array('err' => 0);
    			exit(json_encode($ret));
    		} else {
    			$ret = array('err' => 1, 'msg' => $this->tr->translate('companyName_already_exists'), 'errField' => "companyName");
    			exit(json_encode($ret));
    		}
    	}
    }
    
    
    /**
     * JSON验证邮箱
     */
    public function valemailAction() {
   		$email = trim($this->_getParam("email"));
    	if (empty($email)) {
    		exit();
    	} else {
    		$lp = UsrService::getUsrByEmail($email, array("1"));
    		if ($lp == NULL) {
    			$ret = array('err' => 0, 'msg' => "OK");
    			exit(json_encode($ret));
    		} else {
    			$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_ALREADY_EXIST'), 'errField' => "email");
    			exit(json_encode($ret));
    		}
    	}
    }
    
    
 	/**
     * 验证法人注册信息，若信息合法，则转向注册确认页面
     */
    public function registerverifyAction() {
    	$this->fetchFromParam();
		if (empty($this->baseinfo["companyName"])) {
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
		
		//查验公司名称是否被注册
    	$dbLp = LpService::getLpByName($this->baseinfo["companyName"], array("1"));
   		if ($dbLp !== FALSE) { //返回值不为false，说明此邮箱已被注册
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('companyName_already_exists'), 'errField' => "companyName");
    		exit(json_encode($ret));
    	}
		//查验邮箱是否被注册
    	$dbLp = UsrService::getUsrByEmail($this->baseinfo["email"], array('1'));
    	if ($dbLp !== FALSE) { //返回值不为false，说明此邮箱已被注册
    		$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_ALREADY_EXIST'), 'errField' => "email");
    		exit(json_encode($ret));
    	}
    	
		$ret = array('err' => 0, 'msg' => $this->tr->translate('register_information_is_verified_success'), 
			'registerInfo' => json_encode($this->baseinfo));
    	exit(json_encode($ret));
    }
    
    
    /**
     * 法人注册确认页面
     */
    public function regconfirmAction() {
		$this->layout->headTitle($this->tr->translate('lp_reg_confirm'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/front_lpreg/lpreg', 'name' => $this->tr->translate('lp_register'));
		$crumbs[] = array('uri' => '/front_lpreg/regconfirm', 'name' => $this->tr->translate('lp_reg_confirm'));
		$this->view->layout()->crumbs = $crumbs;
		
		$jsRegisterInfo = $this->_getParam('registerInfo');
    	$registerInfo = (array)json_decode($jsRegisterInfo);
    	
    	$this->view->registerInfo = $registerInfo;
    }
    
    
	/**
     * 受理法人注册 
     */
    public function regsuccAction() {
    	//设置页面标题和导航
    	$this->layout->headTitle($this->tr->translate('lp_reg_succ'));
    	$crumbs = array();
		$crumbs[] = array('uri' => '/front_lpreg/lpreg', 'name' => $this->tr->translate('lp_register'));
		$crumbs[] = array('uri' => '/front_lpreg/regsucc', 'name' => $this->tr->translate('lp_reg_succ'));
		$this->view->layout()->crumbs = $crumbs;
		
    	//获取法人注册信息 
    	$jsRegisterInfo = $this->_getParam('registerInfo');
    	$registerInfo = (array)json_decode($jsRegisterInfo);
    	
		//用户的基本信息
		$usr = array();
    	$usr['role_code'] = 'LP';
    	$usr['email'] = $registerInfo['email'];
    	$usr['email_consignee'] = $usr['email'];
		$usr['passwd'] = md5($registerInfo['passwd']);
		$usr['nickname'] = $registerInfo['companyName'];
		$usr['tel'] = $registerInfo['telephone'];
		$usr['lp_address'] = $registerInfo['address'];
		$usr['lp_linkman'] = $registerInfo['linkman'];
		$usr['website'] = $registerInfo['website'];
		$usr['fullname'] = $registerInfo['companyName'];
    	
		$db = $this->db;
		$db->beginTransaction();
		
    	//查询数据库获取用户唯一编号
    	$lpCode = ActiveEtcService::genCode('LP_CODE');
    	$usr['code'] = $lpCode;
    	
    	$result = LpService::regNewLp($usr);
    	if ($result !== FALSE) {
    		$db->commit();
    		$this->showSuccessMsg("/front_lpreg/lpreg", "lp_register", null, "/img/default/front/<?=$auth->locale?>/register_successful_t.gif");
    	} else {
    		$db->rollback();
    		$this->showErrorMsg("/front_lpreg/lpreg", "lp_reg_error");
    	}
    }
    
} //End: class Front_LpregController