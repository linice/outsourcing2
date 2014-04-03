<?php
include_once 'BaseController.php';
include_once 'BaseService2.php';
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'LpService.php';
include_once 'BaseService.php';


class LoginController extends BaseController
{
	//登陆信息
	private $email = NULL;
	private $passwd = NULL;
	private $isAutoLogin = NULL;
	
	
    /**
     * 登陆页面
     */
    public function indexAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('LOGIN'));
	    $crumb = array('uri' => '/login', 'name' => $this->tr->translate('LOGIN'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
    /**
     * 普通用户激活
     * 激活自定义条件：密码为用户邮箱的md5值。同时，用户还未激活，即表usr的enabled为''，时间24小时之内。
     * 修改表usr的enabled为Y，同时转向到主页
     */
    public function validateusrAction() {
    	$this->_helper->layout()->disableLayout();
    	
    	//获取登陆邮箱、密码
		$this->email = trim($this->_getParam('email'));
		$this->passwd = trim($this->_getParam('passwd'));
		
		//邮箱的md5值与密码相等，则，验证链接（即url）有效
		if ($this->passwd == md5($this->email)) {
			$cond = "email = '{$this->email}' and enabled = '' and update_time > DATE_SUB(now(),INTERVAL 1 day)";
			$usr = BaseService::getRowByCond('usr', $cond, array('*'));
			if (!empty($usr)) {
				$this->auth->usr = $usr;
				BaseService::updateByCond('usr', array('enabled' => 'Y'), "email = '{$this->email}'");
				BaseService::updateByCond('resume', array('enabled' => 'Y'), "email = '{$this->email}'");
				$this->view->tip = $this->tr->translate('VALIDATE_SUCC');
				return;
			}
			//激活错误
			$cond = "email = '{$this->email}'";
			$usr = BaseService::getRowByCond('usr', $cond, array('*'));
			if (!empty($usr)) { //邮箱存在，表明过期或者已经激活
				$this->view->tip = $this->tr->translate('VALIDATE_HAVE_EXPIRED');
				return;
			}
		}
		$this->view->tip = $this->tr->translate('VALIDATE_INFO_ERR');
    }

    
    /**
     * Login
     */
    public function loginAction()
    {
		//获取登陆邮箱、密码及是否自动登录
		$this->email = trim($this->_getParam('login_email'));
		$this->passwd = trim($this->_getParam('login_passwd'));
		$this->isAutoLogin = trim($this->_getParam('isAutoLogin'));
		$md5Passwd = md5($this->passwd);
		//los2 test
//		$parms = $this->_getAllParams();
//		var_dump($parms);
//		exit;
	    //验证登陆信息：昵称，邮箱，密码
		if (empty($this->email)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('email_can_not_be_empty'));
			exit(json_encode($ret));
		} else if (!UtilService::isEmail($this->email)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('email_is_not_real'));
			exit(json_encode($ret));
		}
		if (empty($this->passwd)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('password_can_not_be_empty'));
			exit(json_encode($ret));
		}
		
		//根据邮箱和密码查询用户，并判断用户是否有效
//		$fields = array('id', 'code', 'role_code', 'email', 'nickname');
		$usr = BaseService::getRowByCond('usr', "email = '{$this->email}' and passwd = '$md5Passwd' and enabled = 'Y' ", array('*'));
		if (!empty($usr)) { //如果输入的用户信息正确
			UsrService::refreshLastLoginDate($usr['id']);
			if ($usr['role_code'] == 'LP') {
				$services = LpService::getServiceByLpCode($usr['code']);
				$usr['services'] = $services;
			}
			//设置session，自动登录的有效期为30天，否则为php.ini的session.gc_maxlifetime
			//以上是想要达到的目标，而实际上是：
			//默认session有效期为：1440s，而调用函数setExpirationSeconds后，值为属性session.gc_maxlifetime的值
			$this->auth->usr = $usr;
			//log
//			$jsUsr = json_encode($usr);
//			$log = array('level' => 1, 'msg' => "login usr: $jsUsr", 'class' => __CLASS__, 'func' => __FUNCTION__);
//			LogService::saveLog($log);
			if ($this->isAutoLogin == 'Y') {
				//这一行似乎没有起作用，session表里的lifetime存储的值是86400（这个值是php.ini文件里的属性：session.gc_maxlifetime）
				$this->auth->setExpirationSeconds(3600*24*30);
				setcookie('email', $this->email, time() + 3600*24*30, '/');
				setcookie('passwd', md5($this->passwd), time() + 3600*24*30, '/');
			} else {
//				$this->auth->setExpirationSeconds(10);
			}
			$ret = array('err' => 0, 'msg' => $this->tr->translate('login_success'));
    		exit(json_encode($ret));
		} else { //如果输入的用户登陆信息有误
			$usr2 = UsrService::getUsrByEmail($this->email, array('enabled'));
			if (!empty($usr2)) { //如果邮箱存在
				if ($usr2['enabled'] == '') {
					$ret = array('err' => 1, 'msg' => $this->tr->translate('HAS_NOT_VERIFIED'));
		    		exit(json_encode($ret));
				} elseif ($usr2['enabled'] == 'N') {
					$ret = array('err' => 1, 'msg' => $this->tr->translate('HAS_UNSUBSCRIBE'));
		    		exit(json_encode($ret));
				} elseif ($usr2['enabled'] == 'Y') {
					$ret = array('err' => 1, 'msg' => $this->tr->translate('PASSWD_ERR'));
		    		exit(json_encode($ret));
				}
			} else {
				$ret = array('err' => 1, 'msg' => $this->tr->translate('email_not_exists'));
	    		exit(json_encode($ret));
			}
		}
    }
    
        
    /**
     * Logout
     */
    public function logoutAction()
    {
		Zend_Session::destroy();
		setcookie('email', '', time() - 3600, '/');
		setcookie('passwd', '', time() - 3600, '/');
		
		//return
		$ret = array('err' => 0, 'msg' => $this->tr->translate('logout_success'));
		exit(json_encode($ret));
    }


} //End: class LoginController