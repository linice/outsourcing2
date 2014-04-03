<?php
include_once 'BaseAdminController.php';
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


class Admin_LoginController extends BaseAdminController {
    /**
     * 登陆页面
     */
    public function indexAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('admin_login'));
		$crumb = array('uri' => '/admin/login/index', 'name' => $this->tr->translate('admin_login'));
		$this->view->layout()->crumbs = array($crumb);
		
		//所有人都可以登陆到这个页面，如果已登陆，则各角色的人员跳转到各角色的主页。
		//即：管理员=》管理员中心，普通用户=》普通用户中心，法人=》法人中心，等
		if (!empty($this->auth->usr)) {
			switch ($this->auth->usr['role_code']) {
				case 'ADMIN':
					$this->_redirect('/admin/admin_index/index');
					break;
				case 'LP':
					$this->_redirect('/lp_lp/lp');
					break;
				case 'MEMBER':
					$this->_redirect('/usr_usr/usr');
					break;
				default:
					$this->_redirect('/?pwd=10');
					break;
			}
		}
    }
    
    
    /**
     * Login
     */
    public function loginAction() {
    	//获取登陆昵称、密码及是否自动登录
		$roleCode = trim($this->_getParam('roleCode', 'ADMIN'));
		$nickname = trim($this->_getParam('nickname'));
		$passwd = trim($this->_getParam('passwd'));
		$md5Passwd = md5($passwd);
		
	    if (empty($roleCode)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('ROLE') . $this->tr->translate('CAN_NOT_BE') . $this->tr->translate('EMPTY'));
			exit(json_encode($ret));
		}
	    if (empty($nickname)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('username_can_not_be_empty'));
			exit(json_encode($ret));
		}
    	if (empty($passwd)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('password_can_not_be_empty'));
			exit(json_encode($ret));
		}
		$usr = BaseService::getRowByCond('usr', "role_code = '$roleCode' and nickname = '$nickname' and passwd = '$md5Passwd' and enabled = 'Y'", array('*'));
    	if (!empty($usr)) { //如果输入的用户信息正确
			UsrService::refreshLastLoginDate($usr['id']);
			$this->auth->usr = $usr;
			$ret = array('err' => 0, 'msg' => $this->tr->translate('login_success'));
    		exit(json_encode($ret));
		} else { //如果输入的用户登陆信息有误
			$usr2 = BaseService::getRowByCond('usr', "nickname='$nickname' and role_code = 'ADMIN'", array('enabled'));
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
				$ret = array('err' => 1, 'msg' => $this->tr->translate('USERNAME_NOT_EXISTS'));
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
		
		//return
		$ret = array('err' => 0, 'msg' => $this->tr->translate('logout_success'));
		exit(json_encode($ret));
    }
    


    
} //End: class Admin_LoginController