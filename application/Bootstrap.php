<?php
include_once 'UsrService.php';


// application/Bootstrap.php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * environment variables
	 */
	protected function _initEnv() {
		Zend_Registry::set('DOMAIN', 'http://jinzai-anken.com');
	}
	
	
	/**
	 * 初始化Layout 
	 */
	protected function _initView()
    {
        // Initialize view
        $view = new Zend_View(array('encoding'=>'UTF-8'));
        //下面3句来自Zend/view/Helper/Doctype.php
//        const XHTML11             = 'XHTML11';
//    	const XHTML1_STRICT       = 'XHTML1_STRICT';
//    	const XHTML1_TRANSITIONAL = 'XHTML1_TRANSITIONAL';
//        $view->doctype('XHTML1_TRANSITIONAL');
//        $view->headTitle('CentOS ZF');
 
        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);
        
        Zend_Registry::set('LAYOUT', $view);
 
		$view->addHelperPath(APPLICATION_PATH . '/../library/App/View/Helper/', 'App_View_Helper_');
        // Return it, so that it can be stored by the bootstrap
        return $view;
    }
    
    
	/**
	 * 初始化Log
	 */
	protected function _initLog() {
		Zend_Registry::set('LOG_FILE_PATH', APPLICATION_PATH . '/modules/admin/views/scripts/util/log.phtml');
//		error_log(date('Y-m-d H:i:s') . ' test log<br />', 3, Zend_Registry::get('LOG_FILE_PATH'));
	}
    
    
    /**
     * 初始化DB 
     */
	protected function _initDb(){
		$resource = $this->getPluginResource('multidb');
		$resource->init();
		$db = $resource->getDb('outsourcing2');
		$dbMytest = $resource->getDb('mytest');
		Zend_Db_Table_Abstract::setDefaultAdapter($db);

		$db->query('set names utf8');
		
		Zend_Registry::set('DB', $db);
		Zend_Registry::set('DB_MYTEST', $dbMytest);
	}

    
	/**
     * 初始化Session
     */
    protected function _initSession() {
		$config = array(
		    'name'           => 'session',
		    'primary'        => 'id',
		    'modifiedColumn' => 'modified',
		    'lifetimeColumn' => 'lifetime',
		    'dataColumn'     => 'data'
		);
		
		//create your Zend_Session_SaveHandler_DbTable and
		//set the save handler for Zend_Session
		Zend_Session::setSaveHandler(new Zend_Session_SaveHandler_DbTable($config));
		 
		//start your session!
		Zend_Session::start();
    }
	
    
    /**
     * 国际化
     */
    protected function _initTranslate ()
	{
		$resources = $this->getOption('resources');
		$resource = $resources['translate'];
//		$resource = $this->getPluginResource('translate');
		if (!isset($resource['data'])) {
			throw new Zend_Application_Resource_Exception('Sorry, no corresponding language\'s file!');
		}
		$adapter = isset($resource['adapter']) ? $resource['adapter'] : Zend_Translate::AN_ARRAY;
		$auth = new Zend_Session_Namespace('AUTH');
		//判断session里是否写了locale变量，如果有则取session的变量，
		//否则，取系统配置application.ini的默认locale
		//因此，可以在主页里设置中、英文切换按钮。
		//也可以在cookie里设置locale
		if ($auth->locale) {
			$locale = $auth->locale;
//			error_log('session locale: ' . $locale . "\n", 3, Zend_Registry::get('LOG_FULL_FILENAME'));
		} else {
			$locale =isset($resource['locale']) ? $resource['locale'] : 'zh_CN';
			$auth->locale = $locale;
//			error_log('configure locale: ' . $resource['locale'] . "\n", 3, Zend_Registry::get('LOG_FULL_FILENAME'));
		}
		$data = '';
		if (isset($resource['data'][$locale])) {
			$data = $resource['data'][$locale];
		}
		$translateOptions =isset($resource['options']) ? $resource['options'] : array();
		$translate = new Zend_Translate($adapter, $data, $locale, $translateOptions);
		Zend_Form::setDefaultTranslator($translate);
		Zend_Registry::set($resource['registry_key'], $translate);
		return $translate;
	}
	
	
	/**
	 * 初始化邮件发送的$transport
	 * 何时需要：如通过mail.163.com的邮件服务器，需要用户名和密码登陆时需要
	 */
	protected function _initEmail() {
//		$transport = new Zend_Mail_Transport_Smtp('os2.com.dev');
//		Zend_Mail::setDefaultTransport($transport);
	}
	
	
	/**
	 * 自动登陆
	 */
	protected function _initAutoLogin() {
		$auth = new Zend_Session_Namespace('AUTH');
		if (!isset($auth->usr)) {
			if (!empty($_COOKIE['email']) && !empty($_COOKIE['passwd'])) {
				$auth->usr = UsrService::getUsrByEmailAndPasswd($_COOKIE['email'], $_COOKIE['passwd']);
			}
		}
	}
	
	
	/**
	 * 初始化memcache
	 */
	protected function _initMc() {
		$mc = new Memcache;
		$result = $mc->connect('127.0.0.1', 11211); //返回类型为bool
		Zend_Registry::set('MC', $mc);
	}
	
	
	
	
} //End: application/Bootstrap.php