<?php
include_once 'Download.php';
include_once 'BaseController.php';


class File_FileController extends BaseController
{
    /**
     * 下载文件
     */
    public function dlfileAction() {
    	//禁止layout & view render
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		
		//获取参数：下载文件路径及下载名
		$filepath = trim($this->_getParam('filepath'));
		$downname = trim($this->_getParam('downname'));
		
		//验证参数：下载文件路径及下载名
		if (empty($filepath)) {
			$this->_redirect('/error/error');
		}
		if (empty($downname)) {
			$downname = basename($filepath);
		}
		
		//下载文件：如，简历
		Download::dl($filepath, $downname);
    }
    
    
    /**
     * test
     */
    public function testAction() {
    	var_dump('hi');
    	exit;
    }
    
    	
} //End: class File_FileController