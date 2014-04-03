<?php
include_once 'BaseAdminController.php';
include_once 'EtcService.php';
include_once 'UtilService.php';


class Admin_Admin_AdminController extends BaseAdminController
{
    /**
     * 管理员设定
     */
    public function adminsetAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('admin_set'));
		$crumb = array('uri' => '/admin/admin_admin/adminset', 'name' => $this->tr->translate('admin_set'));
		$this->view->layout()->crumbs = array($crumb);
		
		//view
		$this->view->usr = $this->auth->usr;
    }
    
    
    /**
     * Batch设定
     */
    public function batchsetAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('BATCH') . $this->tr->translate('SETTING'));
		$crumb = array('uri' => '/admin/admin_admin/batchset', 'name' => $this->tr->translate('BATCH') . $this->tr->translate('SETTING'));
		$this->view->layout()->crumbs = array($crumb);
		
		//查询杂项：数据库备份时间，备份路径，结果通知收件邮箱
		$etcs = EtcService::getEtcsByType('DB_DUMP', array('code', 'value'));
		foreach ($etcs as $v) {
			switch ($v['code']) {
				case 'DIR':
					$dir = $v['value'];
					break;
				case 'PERIOD':
					$period = $v['value'];
					break;
				case 'EMAIL':
					$email = $v['value'];
					break;
			}
		}
		
		//view
		$this->view->dir = $dir;
		$this->view->period = $period;
		$this->view->email = $email;
    }
    
    
    /**
     * 查询数据库备份记录
     */
    public function getdbdumplistAction() {
    	//获取参数
    	$this->getPagination();
    	
    	$fields = array('path', 'dt_begin', 'dt_end', 'status');
    	$pgDbDumps = BaseService::getByPageWithCond('db_dump', $fields, '1 order by update_time desc', $this->pagination);
    	
    	//返回
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $pgDbDumps['Total'], 'Rows' => $pgDbDumps['Rows']);
		exit(json_encode($ret));
    }
    

    /**
     * 保存管理员设定
     */
    public function savebatchsetAction() {
    	//获取参数
    	$dir = trim($this->_getParam('dir'));
    	$email = trim($this->_getParam('email'));
    	$period = trim($this->_getParam('period'));
    	
    	//los2 test
//    	$result = file_exists('/data/mysql/');
//		var_dump($result);
//		exit;    		
    	
    	//验证参数
    	$tips = array();
    	if (empty($dir)) {
    		$tips[] = array('label' => 'tip_dir', 'desc' => $this->tr->translate('DIR') . $this->tr->translate('CAN_NOT_BE') . $this->tr->translate('NULL'));
    	} else if (!file_exists($dir)) {
    		$tips[] = array('label' => 'tip_dir', 'desc' => $this->tr->translate('DIR') . $this->tr->translate('NOT_EXISTS'));
    	}
    	if (empty($email)) {
    		$tips[] = array('label' => 'tip_email', 'desc' => $this->tr->translate('EMAIL') . $this->tr->translate('CAN_NOT_BE') . $this->tr->translate('NULL'));
    	} else if (!UtilService::isEmail($email)) {
    		$tips[] = array('label' => 'tip_email', 'desc' => $this->tr->translate('EMAIL') . $this->tr->translate('ILLEGAL'));
    	}
    	if (empty($period)) {
    		$tips[] = array('label' => 'tip_period', 'desc' => $this->tr->translate('TIME') . $this->tr->translate('CAN_NOT_BE') . $this->tr->translate('NULL'));
    	} else if (!(is_numeric($period) && strpos($period, '.') === false && ($period >= 0 && $period <= 23))) {
    		$tips[] = array('label' => 'tip_period', 'desc' => $this->tr->translate('TIME') . $this->tr->translate('SHOULD_BE') . $this->tr->translate('0-23'));
    	}
    	if (!empty($tips)) {
	    	$ret = array('err' => 1, 'msg' => '', 'tips' => $tips);
	    	exit(json_encode($ret));
    	}
    	
    	//保存设定
    	$cond = "type = 'DB_DUMP' and code = 'DIR'";
    	BaseService::updateByCond('etc', array('value' => $dir), $cond);
    	$cond = "type = 'DB_DUMP' and code = 'EMAIL'";
    	BaseService::updateByCond('etc', array('value' => $email), $cond);
    	$cond = "type = 'DB_DUMP' and code = 'PERIOD'";
    	BaseService::updateByCond('etc', array('value' => $period), $cond);
    	EtcService::getEtcsByType('DB_DUMP', array('code', 'value'), true);
    	
    	//保存定时任务命令文件：数据库备份
    	$path = '/app/os2/crontab.txt';
    	$lines = file($path);
    	$lines[0] = "0 $period * * * /usr/bin/curl http://www.jinzai-anken.com/admin/sys/backupdb";
    	$file = implode('', $lines);
    	$fp = fopen($path, 'w');
    	fwrite($fp, $file);
    	fclose($fp);
    	
    	//Success
    	$ret = array('err' => 0, 'msg' => $this->tr->translate('SAVE') . $this->tr->translate('SETTING') . $this->tr->translate('SUCCESS'));
    	exit(json_encode($ret));
    }
    
    
	/**
     * 修改管理员信息
     */
    public function saveadmininfoAction() {
    	//获取参数：管理员信息
    	$adminInfo = array('fullname' => '', 'tel' => '', 'email' => '', 'passwd' => '', 'passwd2' => '');
    	foreach ($adminInfo as $k => &$v) {
    		$v = trim($this->_getParam($k, ''));
    	}
		
		if (!empty($adminInfo['passwd'])) {
			if ($adminInfo['passwd'] != $adminInfo['passwd2']) {
				$ret = array('err' => 1, 'msg' => $this->tr->translate('passwords_are_not_equal'));
				exit(json_encode($ret));
			}
		}
		
    	//如果邮箱改变，查验邮箱是否被注册
    	if ($adminInfo['email'] != $this->auth->usr['email']) {
	    	$dbUsr = UsrService::getUsrByEmail($adminInfo['email'], array('1'));
	    	if (!empty($dbUsr)) {
	    		$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_ALREADY_EXIST'));
	    		exit(json_encode($ret));
	    	}
    	}
    	
		if (!empty($adminInfo['passwd'])) {
			$adminInfo['passwd'] = md5($adminInfo['passwd']);
		} else {
			unset($adminInfo['passwd']);	
		}
		unset($adminInfo['passwd2']);
		
//		var_dump($adminInfo);
//		exit;
		//保存管理员信息
		$result = BaseService::updateByCode('usr', $adminInfo, $this->auth->usr['code']);
		if ($result) {
			//更新session里的usr信息
			$this->auth->usr = UsrService::getUsrByEmail($adminInfo['email']);
			$ret = array('err' => 0, 'msg' => $this->tr->translate('UPDATE') . $this->tr->translate('ADMIN') . $this->tr->translate('INFO') . $this->tr->translate('SUCCESS'));
			exit(json_encode($ret));
		}
		$ret = array('err' => 1, 'msg' => $this->tr->translate('UPDATE') . $this->tr->translate('ADMIN') . $this->tr->translate('INFO') . $this->tr->translate('ERROR'));
		exit(json_encode($ret));
    }
    
    
} //End: class Admin_Admin_AdminController