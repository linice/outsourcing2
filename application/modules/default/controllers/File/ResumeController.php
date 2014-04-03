<?php
include_once 'Download.php';
include_once 'FileService.php';
include_once 'BaseController.php';


class File_ResumeController extends BaseController
{
    /**
     * 上传简历
     */
    public function uploadresumeAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('upload_resume'));
		$crumb = array('uri' => '/usr_resume/uploadresume', 'name' => $this->tr->translate('upload_resume'));
		$this->view->layout()->crumbs = array($crumb);
		
    }
    
    
	/**
     * 处理文件上传 
     */
    public function handleuploadresumeAction() {
    	//ajax
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		//js文件上传插件uploadify，会影响session，在此需要重要获取session
		$this->auth = new Zend_Session_Namespace('AUTH');
		
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
		//los2 test
//		var_dump($this->auth->usr);
//		var_dump($dir);
//		exit;
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
     * 查询简历文件
     */
    public function getresumefileAction() {
    	//获取参数：分页，只显示未处理简历（针对管理员）
		$page = trim($this->_getParam('page', 1));
		$pageSize = trim($this->_getParam('pagesize', 20));
		$onlyDisplayUnhandled = trim($this->_getParam('onlyDisplayUnhandled'));
		$resumeName = trim($this->_getParam('resumeName'));
		
		//los2 test
//		var_dump($this->auth->usr);
//		exit;
		
	    //查询简历文件
		$fields = array('id', 'talent_code', 'lp_code', 'basename_c', 'create_time', 'status');
	    if ($this->auth->usr['role_code'] == 'LP') {
			$resumeFilesCnt = FileService::getFilesCntByLpCodeAndType($this->auth->usr['code'], 'RESUME');
			$resumeFiles = FileService::getFilesAndUsrsByPageAndLpCodeAndType($page, $pageSize, $this->auth->usr['code'], 'RESUME', $fields);
	    } elseif ($this->auth->usr['role_code'] == 'MEMBER') {
			$resumeFilesCnt = FileService::getFilesCntByTalentCodeAndType($this->auth->usr['code'], 'RESUME');
			$resumeFiles = FileService::getFilesByPageAndTalentCodeAndType($page, $pageSize, $this->auth->usr['code'], 'RESUME', $fields);
	    } elseif ($this->auth->usr['role_code'] == 'ADMIN') {
	    	if ($onlyDisplayUnhandled == 'Y' && !empty($resumeName)) {
				$resumeFilesCnt = FileService::getFilesCntByTypeAndStatusAndLikeBasename('RESUME', 'UNHANDLED', $resumeName);
				$resumeFiles = FileService::getFilesByPageAndTypeAndStatusAndLikeBasename($page, $pageSize, 'RESUME', 'UNHANDLED', $resumeName, $fields);
	    	} else if ($onlyDisplayUnhandled == 'Y') {
				$resumeFilesCnt = FileService::getFilesCntByTypeAndStatus('RESUME', 'UNHANDLED');
				$resumeFiles = FileService::getFilesByPageAndTypeAndStatus($page, $pageSize, 'RESUME', 'UNHANDLED', $fields);
	    	} else if (!empty($resumeName)) {
				$resumeFilesCnt = FileService::getFilesCntByTypeAndLikeBasename('RESUME', $resumeName);
				$resumeFiles = FileService::getFilesByPageAndTypeAndLikeBasename($page, $pageSize, 'RESUME', $resumeName, $fields);
	    	} else {
				$resumeFilesCnt = FileService::getFilesCntByType('RESUME');
				$resumeFiles = FileService::getFilesByPageAndType($page, $pageSize, 'RESUME', $fields);
	    	}
	    }
		//格式化$resumeFiles
		$status = array('' => '', 'HANDLED' => $this->tr->translate('HANDLED'), 'UNHANDLED' => $this->tr->translate('UNHANDLED'));
		foreach ($resumeFiles as &$resumeFile) {
			$resumeFile['create_time'] = date('Y-m-d', strtotime($resumeFile['create_time']));
			$resumeFile['status'] = $status[$resumeFile['status']];
		}
		
		//返回
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $resumeFilesCnt, 'Rows' => $resumeFiles);
		exit(json_encode($ret));
    }
    
    
    /**
     * 删除简历文件
     */
    public function delresumefileAction() {
    	//获取参数：简历文件ID
    	$resumeFileIds = $this->_getParam('resumeFileIds');
    	
    	//验证参数：简历文件ID不为空
    	if (empty($resumeFileIds)) {
	    	$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_SELECT_ROW'));
			exit(json_encode($ret));
    	}
    	
    	//删除简历文件
    	$result = FileService::delFileByIds($resumeFileIds);
    	if ($result === TRUE) {
    		$ret = array('err' => 0, 'msg' => $this->tr->translate('DEL_RESUME_FILE_SUCC'));
			exit(json_encode($ret));
    	}
    	$ret = array('err' => 1, 'msg' => $this->tr->translate('DEL_RESUME_FILE_ERR'));
		exit(json_encode($ret));
    }
    
    
    /**
     * 下载简历文件
     */
    public function dlresumefileAction() {
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
			
    	//获取参数：简历文件ID
    	$resumeFileIds = $this->_getParam('resumeFileIds');
    	
    	//验证参数：简历文件ID不为空
    	if (empty($resumeFileIds)) {
	    	$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_SELECT_ROW'));
			exit(json_encode($ret));
    	}
    	
    	//查询简历文件，根据文件ID
    	$files = FileService::getFileByIds($resumeFileIds, array('dir', 'basename_s', 'basename_c'));
    	
    	//如果选择多份简历下载，则打包简历
    	$dir = $files[0]['dir'];
    	if (count($resumeFileIds) > 1) {
	    	$cmd = "cd $dir; zip package.zip ";
	    	foreach ($files as $file) {
	    		$cmd .= $file['basename_s'] . ' ';
	    	}
	    	$result = FALSE;
	    	system($cmd, $result);
	    	if ($result !== 0) {
	    		$ret = array('err' => 1, 'msg' => $this->tr->translate('DOWNLOAD_RESUME_FILE_ERR'));
				exit(json_encode($ret));
	    	}
	    	$filepath = $dir . '/package.zip';
	    	$downname = 'package.zip';
    	} else { //否则，即只有一个简历文件被下载
	    	$filepath = $dir . '/' . $files[0]['basename_s'];
	    	$downname = $files[0]['basename_c'];
    	}
    	
    	$ret = array('err' => 0, 'msg' => $this->tr->translate('DOWNLOAD_RESUME_FILE_SUCC'),
    		'filepath' => $filepath, 'downname' => $downname
    	);
		exit(json_encode($ret));
    }
    
    
    /**
     * 处理简历文件：录入人员将简历录入系统
     */
    public function handleresumefileAction() {
    	//获取参数：简历文件ID
    	$resumeFileIds = $this->_getParam('resumeFileIds');
    	
    	//验证参数：简历文件ID不为空
    	if (empty($resumeFileIds)) {
	    	$ret = array('err' => 1, 'msg' => $this->tr->translate('PLEASE_SELECT_ROW'));
			exit(json_encode($ret));
    	}
    	
    	//处理简历文件
    	$result = FileService::updateFileByIds($resumeFileIds, array('status' => 'HANDLED'));
    	if ($result === TRUE) {
    		$ret = array('err' => 0, 'msg' => 'Success');
			exit(json_encode($ret));
    	}
    	$ret = array('err' => 1, 'msg' => $this->tr->translate('HANDLE_RESUME_FILE_ERR'));
		exit(json_encode($ret));
    }
    
    
    
    
    
    	
} //End: class File_ResumeController