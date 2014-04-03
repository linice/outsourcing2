<?php
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'ResumeService.php';
//include_once 'download.class.php';
include_once 'Download.php';
include_once 'BaseController.php';


class Test_DlController extends BaseController
{
    /**
     * 下载 
     */
    public function dlfileAction() {
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);

		$filepath = '/var/www/html/os2/application/../docs/resume/Usr12007/package.zip';
		$downname = '包裹.zip';
		
		Download::dl($filepath, $downname);
    }
    
    
    public function dlzipAction() {
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		
		$filepath = '/var/www/html/os2/application/../docs/resume/Usr12007/package.zip'; //最终生成的文件名（含路径）
		$downname = '包裹.zip';

		Download::dl($filepath, $downname);
    }
    
    
    /**
     * test
     */
    public function testAction() {
    	exit('hi');
    }
    
    	
} //End: class Test_TestController