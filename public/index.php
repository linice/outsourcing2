<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE);//错误报告级别E_ALL|E_STRICT
error_reporting(E_ALL & ~E_NOTICE);
//date_default_timezone_set('Europe/London');
//date_default_timezone_set('Asia/Shanghai');//'Asia/Shanghai' 亚洲/上海
//date_default_timezone_set('Asia/Chongqing');//其中Asia/Chongqing'为“亚洲/重庆”
date_default_timezone_set('PRC');//其中PRC为“中华人民共和国”
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
//define('APPLICATION_ENV', 'production');

//var_dump(get_include_path());
//exit;
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(APPLICATION_PATH),
	realpath(APPLICATION_PATH . '/../library'), //工程里的library
    realpath(APPLICATION_PATH . '/../public'),
    realpath(APPLICATION_PATH . '/modules/default/models'),
    realpath(APPLICATION_PATH . '/modules/admin/models')
)));


/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


//调用Bootstrap.php以及运行Controller，再指向View
$application->bootstrap()->run();

//los2 test begin
//var_dump($_SERVER);
//test begin
//var_dump($_SERVER['SCRIPT_FILENAME']);
//var_dump($_SERVER['SCRIPT_NAME']);
//var_dump($_SERVER['PHP_SELF']);
//var_dump($_SERVER['REDIRECT_URL']);
//var_dump($_SERVER['REQUEST_URI']);
//$_SERVER['REQUEST_URI'] = '/';
//var_dump($_SERVER['REQUEST_URI']);
//los2 test end

//End: index.php