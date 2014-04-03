<?php
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'ResumeService.php';
include_once 'BaseController.php';


class Test_FileController extends BaseController 
{
    /**
     * 上传文件：通过input file
     */
	public function uploadAction() {
		//导航
    	$this->layout->headTitle('上传文件');
	    $crumb = array('uri' => '/test_file/upload', 'name' => '上传文件');
		$this->view->layout()->crumbs = array($crumb);
	}
	
	
	/**
	 * 处理上传文件
	 */
	public function handleuploadAction() {
		$file = $_FILES['file'];
		var_dump($file);
		exit;		
	}
	
} //End: class Test_FileController