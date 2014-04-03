<?php
include_once 'BaseController.php';
include_once 'UtilService.php';
include_once 'BaseService2.php';
include_once 'UsrService.php';
include_once 'ResumeService.php';
include_once 'Test/C.php';
include_once 'EtcService.php';
include_once 'BaseService.php';


class Test_TestController extends Zend_Controller_Action
{
	private $layout = null;
	private $auth = null;
	private $tr = null;
	
    public function init()
    {
        /* Initialize action controller here */
//    	include_once 'Acl/permission.php';
		$this->layout = Zend_Registry::get('LAYOUT');
		$this->auth = new Zend_Session_Namespace('AUTH');
		$this->tr = Zend_Registry::get('TRANSLATE');

//		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
//    		&& $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
//	    	$this->_helper->layout()->disableLayout();
//			$this->_helper->viewRenderer->setNoRender(TRUE);
//    	}
	    if ($this->_request->isXmlHttpRequest()) {
//			$fc = Zend_Controller_Front::getInstance();
//			$fc->setParam('noViewRenderer', true);
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
		}
    }
    
    
    /**
     * 临时测试 
     */
    public function tmpAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('TEST'));
	    $crumb = array('uri'=>'/test_test/tmp', 'name'=>$this->tr->translate('TEST'));
		$this->view->layout()->crumbs = array($crumb);
		
		//tmp
	    $row = BaseService::getRowByCode('resource', 'default/front_talent/talentsearchresult', array('code'));
	    var_dump($row);
	    exit;
    }
    
    
    /**
     * 通用测试 
     */
    public function testAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('TEST'));
	    $crumb = array('uri'=>'/test_test/test', 'name'=>$this->tr->translate('TEST'));
		$this->view->layout()->crumbs = array($crumb);
		
		$instance = new C();
		$assigned = $instance;
		var_dump($instance);
		echo '<br />';
		$instance->a = 'Hello';
		$assigned->a = 'World';
		var_dump($instance);
		echo '<br />';
		var_dump($assigned);
		exit;
    }
    
    
    public function connstatusAction() {
    	ignore_user_abort(TRUE);// 设定关闭浏览器也执行程序
		set_time_limit(0);      // 设定响应时间不限制，默认为30秒
		$cnt = 0;
		
		while (TRUE)
		{
			// Did the connection fail?
		    if(connection_status() != CONNECTION_NORMAL) {
		        break;
		    }
			
			if (in_array(date('s'), array('00', '15', '30', '45'))) {
				$cnt++;
				$test2 = array('name' => date('Y-m-d H:i:s'));
				BaseService::addRow('test2', $test2);
			}
		}
		echo '<br />';
		exit($cnt);
    }
    
    
    /**
     * Session信息 
     */
    public function sessionAction() {
    	var_dump($this->auth->usr);
    	echo '<br />';
    	echo '<br />';
    	var_dump($this->auth->locale);
    	echo '<br />';
    	exit;
    }
    
    
    /**
     * Session信息 
     */
    public function unsetsessAction() {
    	Zend_Session::destroy();
    	exit('Success');
    }
    
    
    /**
     * cookie信息 
     */
    public function cookieAction() {
//    	$this->_helper->layout()->disableLayout();
//		$this->_helper->viewRenderer->setNoRender(TRUE);
//    	var_dump(session_cache_expire());
//    	echo '<br />';
//    	setcookie('email', 'a@qq.com', time()+3600*24*30);
//    	setcookie('passwd', 'passwd3', time()+3600*24*30);
//		$_COOKIE['email'] = 'b@qq.com';
//    	setcookie('email', null, time() - 3600);
//    	setcookie('passwd', null, time() - 3600);
    	var_dump($_COOKIE);
    	echo '<br />';
    	var_dump($_COOKIE['email']);
    	echo '<br />';
    	var_dump($_COOKIE['passwd']);
    	echo '<br />';
    	exit;
    }
    
    
    /**
     * web app 配置参数
     */
    public function webparamAction() {
    	echo 'PUBLIC_PATH: ' . APPLICATION_PATH . '<br />';
    	echo 'DOCUMENT_ROOT: ' . $_SERVER['DOCUMENT_ROOT'] . '<br />';
    	exit;
    }
    
	
	/**
	 * phpinfo 
	 */
	public function phpinfoAction() {
	    $this->layout->headTitle('outsourcing2 phpinfo');
	    $crumb = array('uri'=>'/index/phpinfo', 'name'=>'phpinfo');
		$this->view->layout()->crumbs = array($crumb);
		phpinfo();
		exit;
    }
    
    
    /**
     *  fc: front controller
     */
    public function fcAction() {
    	$fc = Zend_Controller_Front::getInstance();
    	$fc->setParam('noViewRenderer', true);
    	
    	echo 'fc';
    }
    
	
    /**
	 * test call proc
	 */
	public function insertcasestestAction() {
		try {
			//call proc
			$db = Zend_Registry::get('DB');
			$sql = "call insert_cases_test()";
			$db->query($sql);
			$ret = array('err' => 0, 'msg' => 'Call proc success.');
			echo json_encode($ret);
		} catch (Exception $e) {
			$ret = array('err' => 1, 'msg' => $e->getMessage());
			echo json_encode($ret);
		}
		exit;
	}
	
	
	/**
	 * test: module, controller, action, uri
	 */
	public function uriAction() {
//        $this->_redirect();
		$module = $this->_request->getModuleName();
		$controller = $this->_request->getControllerName();
		$action = $this->_request->getActionName();
		$uri = $this->_request->getRequestUri(); //test_test/url
		var_dump(array($module, $controller, $action, $uri));
		exit;
	}
	

	/**
	 * 测试返回空值 
	 */
	public function getusrAction() {
//		$usr = UsrService::getUsrByEmail('abcdefg@qq.com');
		$usr = UsrService::getUsrByCode('Usr12005');
		var_dump($usr);
		exit;
	}
    
	
	/**
	 * UtilService的函数测试
	 */
	public function utilAction() {
		$email = 'los@163.com.20120728230608';
		$result = UtilService::isEmail($email);
		var_dump($result);
		exit;
	}
	
	
	/**
	 * server ip & client ip
	 */
	public function ipAction() {
		echo $_SERVER['SERVER_ADDR'];
		echo '<br />';
		echo $_SERVER['REMOTE_ADDR'];
		echo '<br />';
		exit;
	}
	
	
} //End: class Test_TestController