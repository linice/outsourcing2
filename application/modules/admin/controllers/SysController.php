<?php
include_once 'BaseAdminController.php';
include_once 'Util/SysService.php';
include_once 'BaseService2.php';
include_once 'EtcService.php';
include_once 'BaseService.php';
include_once 'ResourceService.php';


class Admin_SysController extends BaseAdminController
{
    public function sysAction()
    {
        //导航
    	$this->layout->headTitle($this->tr->translate('SYS_UTIL'));
		$crumb = array('uri' => '/admin/sys/sys', 'name' => $this->tr->translate('SYS_UTIL'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
	public function flushmcAction() {
    	BaseService::initMc();
    	$result = BaseService::$mc->flush();
    	if ($result) {
	    	$ret = array('err' => 0, 'msg' => 'Flush memcache success.');
	    	exit(json_encode($ret));
    	}
    	$ret = array('err' => 1, 'msg' => 'Flush memcache error.');
    	exit(json_encode($ret));
    }
    
    
    /**
     * 清空资源和权限
     */
    public function clearrscsAction() {
		$result = ResourceService::clearRscs();
		if ($result) {
			$ret = array('err' => 0, 'msg' => 'Clear resources success.');
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => 'Error');
		exit(json_encode($ret));
    }
    
    
    /**
     * 搜索并保存所有资源
     */
    public function searchandsaveresourcesAction() {
    	ini_set('memory_limit', '-1');
		set_time_limit(0);
		$modulesDir = APPLICATION_PATH . '/modules';
//		var_dump($modulesDir);
//		exit;
		$result = SysService::searchAndSaveResources($modulesDir);
		if ($result) {
			$ret = array('err' => 0, 'msg' => 'Search and save resources success');
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => 'Error');
		exit(json_encode($ret));
    }
    
    
    /**
     * 记录日志
     */
    public function logAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('WEB_LOG'));
		$crumb = array('uri' => '/admin', 'name' => $this->tr->translate('WEB_LOG'));
		$this->view->layout()->crumbs = array($crumb);
    }

    
    /**
     * 用于依据表数据生成插入sql语句的页面
     */
    public function tableAction() {
    	$this->layout->headTitle($this->tr->translate('table'));
		$crumb = array('uri' => '/test_table/table', 'name' => $this->tr->translate('table'));
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
    /**
     * 生成表中，指定字段的insert sql
     */
    public function gentableinssqlAction() {
    	$table = trim($this->_getParam('tableName'));
    	$strFields = trim($this->_getParam('fieldNames'));
    	$fields = array();
    	if (empty($strFields)) {
    		$fields = array('*');
    	} else {
    		$strFields = str_replace(' ', '', $strFields);
    		$fields = explode(',', $strFields);
    	}
		$sql = SysService::genTableInsSql($table, $fields);
		$ret = array('err' => 0, 'msg' => $this->tr->translate('generate_table_insert_sql_success'), 'data' => $sql);
		exit(json_encode($ret));
    }
    
    
    /**
     * 在日本VPS上，从中国VPS恢复数据库
     */
    public function recoverdbfromcnAction() {
    	//不限运行时间和内存
    	ini_set('memory_limit', '-1');
		set_time_limit(0);
    	
    	//当在日本服务器上运行Web时，才可以从中国备份数据库
    	$ip = $_SERVER['SERVER_ADDR'];
    	if ($ip == '61.113.63.74') {
	    	system('/app/os2/cp_db_os2_from_cn_to_jp.sh', $result);
	    	if ($result === 0) {
		    	$ret = array('err' => 0, 'msg' => 'Recover database outsourcing2 from China success.');
				exit(json_encode($ret));
	    	}
    	}
    	$ret = array('err' => 1, 'msg' => 'Recover database outsourcing2 from China error.');
		exit(json_encode($ret));
    }
    
    
    /**
     * 备份数据库
     */
    public function backupdbAction() {
    	//不限运行时间和内存
    	ini_set('memory_limit', '-1');
		set_time_limit(0);
		header("Cache-Control:no-cache,must-revalidate"); 
		$dt_begin = date('Y-m-d H:i:s'); //开始时间
//		$today = date('Ymd');
		
    	//查询杂项：数据库备份时间，备份路径，结果通知收件邮箱
		$etcs = EtcService::getEtcsByType('DB_DUMP', array('code', 'value'));
		foreach ($etcs as $v) {
			switch ($v['code']) {
				case 'DIR':
					$dir = $v['value'];
					break;
				case 'EMAIL':
					$email = $v['value'];
					break;
			}
		}
		//los2 test
		if (empty($dir)) {
			$dir = '/data/mysql/os2_prod';
		}
		$filename = 'os2_prod_dump_' . date('YmdHis') . '.sql.gz';
    	$path = rtrim($dir, '/') . '/' . $filename;
    	$sh = 'mysqldump --host=127.0.0.1 --port=11001 --user=db_bak --password=10 --triggers --routines --flush-logs --master-data=1 --single-transaction  outsourcing2 | gzip -9 > ' . $path . ' 2>>/data/log/os2_prod_dump.log';
//    	$sh = 'mysqldump --host=127.0.0.1 --port=11001 --user=db_bak --password=10 --triggers --routines --flush-logs --master-data=1 --single-transaction  outsourcing2  > ' . $path . ' 2>>/data/log/os2_prod_dump.log';
    	//los2 test
//    	system('touch /data/mysql/test.sql', $result0);
		//los2 change begin
//		$path = '/data/mysql/os2_prod/os2_prod_dump_' . $today . '.sql.gz';
//		$sh = '/app/mysql/os2_dev/dump_os2_dev.sh';
//		$sh = '/app/mysql/os2_prod/dump_os2_prod.sh';
		//los2 change end
    	system($sh, $result);
    	$dt_end = date('Y-m-d H:i:s'); //结束时间
    	
    	//los2 test
    	if ($result === 0) {
    		$db_dump = array('path' => $path, 'dt_begin' => $dt_begin, 'dt_end' => $dt_end, 'status' => 'OK');
    		BaseService::addRow('db_dump', $db_dump);
    		//发送邮件给管理员
    		$fromEmail = 'admin@jinzai-anken.com';
	    	$fromName = 'admin';
	    	$toEmail = 'linice01@163.com';
	    	$toName = 'linice01';
	    	$subject = $this->tr->translate('DATABASE') . $this->tr->translate('BACKUP');
	    	$body = $path . ' | ' . $dt_begin . ' | ' . $dt_end . ' | OK';
	    	$mail = new Zend_Mail('utf-8');
	    	$mail->setFrom($fromEmail, $fromName);
			$mail->addTo($toEmail, $toName);
			$mail->setSubject($subject);
			$mail->setBodyHtml($body);
			$result = $mail->send();
    		//Success
	    	$ret = array('err' => 0, 'msg' => 'Backup database outsourcing2 success.');
			exit(json_encode($ret));
    	}
    	$db_dump = array('path' => $path, 'dt_begin' => $dt_begin, 'dt_end' => $dt_end, 'status' => 'BAD');
    	BaseService::addRow('db_dump', $db_dump);
    	//发送邮件给管理员
    	$fromEmail = 'admin@jinzai-anken.com';
	    $fromName = 'admin';
	    $toEmail = 'linice01@163.com';
	    $toName = 'linice01';
	    $subject = $this->tr->translate('DATABASE') . $this->tr->translate('BACKUP');
	    $body = $path . ' | ' . $dt_begin . ' | ' . $dt_end . ' | BAD';
	    $mail = new Zend_Mail('utf-8');
	    $mail->setFrom($fromEmail, $fromName);
		$mail->addTo($toEmail, $toName);
		$mail->setSubject($subject);
		$mail->setBodyHtml($body);
		$result = $mail->send();
    	//Error
    	$ret = array('err' => 1, 'msg' => 'Backup database outsourcing2 error.');
		exit(json_encode($ret));
    }
    
    
    /**
     * svn update
     */
    public function svnupdateAction() {
    	//不限运行时间和内存
    	ini_set('memory_limit', '-1');
		set_time_limit(0);
		$sh = 'svn update /var/www/html/outsourcing2 --username=los --password=10';
    	system($sh, $result);
    	exit('$result: ' . $result);
    }
    
    
    
    
    

} //End: class Admin_SysController