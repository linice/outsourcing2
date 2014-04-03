<?php
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'ResumeService.php';
include_once 'BaseController.php';
include_once 'FileService.php';


class Test_UploadifyController extends Zend_Controller_Action
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
     * plupload文件上传主页
     */
    public function uploadifyAction() {
    	//导航
    	$this->layout->headTitle($this->tr->translate('UPLOADIFY'));
	    $crumb = array('uri'=>'', 'name'=>$this->tr->translate('UPLOADIFY'));
		$this->view->layout()->crumbs = array($crumb);
		
		
    }
    
    
    /**
     * 处理文件上传 
     */
    public function handleuploadifyAction() {
    	//ajax
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		/****************上传EXCEL或WORD等格式简历文件, begin***************/
    	//简历文件大小限制
    	$maxFileSize = 1024 * 1024 * 20; //20MB
    	if ($this->auth->usr['role_code'] == 'MEMBER') {
    		$maxFileSize = 1024 * 1024; //1MB
    	} else if ($this->auth->usr['role_code'] == 'LP') {
    		$maxFileSize = 1024 * 1024 * 10; //10MB
    	}
		//设置文件保存位置
		$dir = APPLICATION_PATH .'/../docs/resume/' . $this->auth->usr['code'];
		if (!file_exists($dir)) {
			$mkdirSuccuss = mkdir($dir, 0777, true);
			if (!$mkdirSuccuss) {
				$log = array('level' => 3, 'msg' =>"Make directory error: $dir.", 'class' => __CLASS__, 'func' => __FUNCTION__);
				LogService::saveLog($log);
				$ret = array('err' => 1, 'msg' => $this->tr->translate('UPLOAD_FAILED_PLEASE_CONTACT_ADMINISTRATOR'));
				exit(json_encode($ret));
			}
		}
		//$fullFilenames用于保存存储在服务器中的全路径文件名
//		$fullFilenames = array();
		$file = $_FILES['resumeFile']; //从客户端上传的文件，统一用$file保存
//		var_dump($file);
//		exit;
		if (!empty($file)) {
			if ($file['size'] > $maxFileSize) {
				$msg = $this->tr->translate('SINGLE_RESUME_FILE_SIZE_CAN_NOT_EXCEED') . ($maxFileSize/1024/1024). 'MB';
				$ret = array('err' => 1, 'msg' => $msg);
				exit(json_encode($ret));
			}
			if ($file['error'] > 0) { //文件上传的错误编号大于0，如果为0，表示没有错误
				$msg = null;
				switch ($file['error']) {
					case 1:
						$msg = $this->tr->translate('FILE_EXCEED_UPLOAD_MAX_FILESIZE');
						break;
					case 2:
						$msg = $this->tr->translate('FILE_EXCEED_MAX_FILE_SIZE');
						break;
					case 3:
						$msg = $this->tr->translate('FILE_ONLY_PARTIALLY_UPLOADED');
						break;
					case 4:
						$msg = $this->tr->translate('NO_RESUME_FILE_UPLOAD');
						break;
					case 6:
						$log = array('level' => 3, 'msg' =>'File error 6: Cannot upload file: No temp directory specified.', 'class' => __CLASS__, 'func' => __FUNCTION__);
						LogService::saveLog($log);
						$msg = $this->tr->translate('UPLOAD_FAILED_PLEASE_CONTACT_ADMINISTRATOR');
						break;
					case 7:
						$log = array('level' => 3, 'msg' =>'File error 7: Upload failed: Cannot write to disk.', 'class' => __CLASS__, 'func' => __FUNCTION__);
						LogService::saveLog($log);
						$msg = $this->tr->translate('UPLOAD_FAILED_PLEASE_CONTACT_ADMINISTRATOR');
						break;
				}
				if (in_array($file['error'], array(1, 2, 3, 4, 6, 7))) {
					$ret = array('err' => 1, 'msg' => $msg);
					exit(json_encode($ret));
				}
			} //End: if ($file['error'] > 0)
			//验证文件类型
//			if ($file['type'] != 'text/csv') {
//				$ret = array('err' => 1, 'msg' =>'File type error: It should be csv.');
//				exit(json_encode($ret));
//			}
			//is_uploaded_file判断是通过客户端（即浏览器）上传，而不是黑客攻击 
			//另外，在服务器保存用户定义的文件名，而不要改写在依时间定义的文件名
			if (is_uploaded_file($file['tmp_name'])) {
				$pathinfo = pathinfo($file['name']);
				$basename_s = date('YmdHis') . '_' . rand(1, 1000) . '.' . $pathinfo['extension'];
				$basename_c = $file['name'];
//				$fullFilename = $dir . '/' . $basename_c;
				$fullFilename = $dir . '/' . $basename_s;
				if (!move_uploaded_file($file['tmp_name'], $fullFilename)) {
					$log = array('level' => 3, 'msg' =>'File error 7: Upload failed: Cannot write to disk.', 'class' => __CLASS__, 'func' => __FUNCTION__);
					LogService::saveLog($log);
					$ret = array('err' => 1, 'msg' => $this->tr->translate('UPLOAD_FAILED_PLEASE_CONTACT_ADMINISTRATOR'));
					exit(json_encode($ret));
				}
				$dbFile = array('type' => 'RESUME', 
					'dir' => $dir, 'basename_s' => $basename_s, 
					'basename_c' => $basename_c, 'status' => 'UNHANDLED', 
					'create_time' => date('Y-m-d H:i:s') 
				);
				if ($this->auth->usr['role_code'] == 'MEMBER') {
					$dbFile['talent_code'] = $this->auth->usr['code'];
				} else if ($this->auth->usr['role_code'] == 'LP') {
					$dbFile['lp_code'] = $this->auth->usr['code'];
				}
				//管理员不需要上传简历，主要负责处理上传的简历 
//				else if ($this->auth->usr['role_code'] == 'ADMIN') {
//					$dbFile['admin_code'] = $this->auth->usr['code'];
//				}
				if (FileService::addFile($dbFile)) {
					$ret = array('err' => 0, 'msg' => $this->tr->translate('UPLOAD_RESUME_SUCCESS'));
					exit(json_encode($ret));
				}
			} else {
				$log = array('level' => 2, 'msg' => 'Possible file upload attack. Filename: ' . $file['name'], 'class' => __CLASS__, 'func' => __FUNCTION__);
				LogService::saveLog($log);
				$ret = array('err' => 1, 'msg' => $this->tr->translate('POSSIBLE_FILE_UPLOAD_ATTACK__FLENAME') . ': ' . $file['name']);
				exit(json_encode($ret));
			}
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('UPLOAD_RESUME_ERR'));
		exit(json_encode($ret));
		/****************上传EXCEL或WORD等格式简历文件, end***************/
    }
    
    
    /**
     * 处理文件上传 
     */
    public function handleuploadify0Action() {
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		//获取参数
		$file = $_FILES['resumeFile'];
		var_dump($file);
		exit;
    	/*
			Uploadify
			Copyright (c) 2012 Reactive Apps, Ronnie Garcia
			Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
		*/
		
		// Define a destination
		$targetFolder = '/uploads'; // Relative to the root
		
//		$verifyToken = md5('unique_salt' . $_POST['timestamp']);
		
//		if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
		if (!empty($_FILES)) {
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
			$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
			
			// Validate the file type
			$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			
			if (in_array($fileParts['extension'],$fileTypes)) {
				move_uploaded_file($tempFile,$targetFile);
				echo '1';
			} else {
				echo 'Invalid file type.';
			}
		}
		
//		exit('Success');
    }
    
	
	
} //End: class Test_UploadifyController