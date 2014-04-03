<?php
include_once 'BaseController.php';
include_once 'LpService.php';
include_once 'ResumeService.php';
class Lp_TalentController extends BaseController {
    
    /**
     * 提案
     */
    public function talentproposeAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('talent_propose'));
		$crumbs = array();
		$crumbs[] = array('uri' => '/lp_lp/lp', 'name' => $this->tr->translate('lp_center'));
		$crumbs[] = array('uri' => '/lp_talent/talentpropose', 'name' => $this->tr->translate('talent_propose'));
		$this->view->layout()->crumbs = $crumbs;
		
		//获取参数
		//ids为逗号分隔的resume code(s)字符串，如：Rsm12009,Rsm12024
		$sRsmCodes = trim($this->_getParam('ids'));
		$rsmCodes = explode(',', $sRsmCodes);
		$sRsmCodes = implode("','", $rsmCodes);
		$sRsmCodes = "'" . $sRsmCodes . "'";

		//查询员工简历信息
    	$fsRsm = array('code', 'date_graduate', 'date_arrive_jp', 'ja_qualification', 'ja_ability', 'able_work_date_choice', 
    		'able_work_date', 'salary_min_display', 'salary_exp_display',
    		'salary_min', 'salary_exp', 'remark_lp', 'update_time', 'talent_code', 'fullname', 'sex', 'birthday', 'tel');
    	$sFsRsm = implode(',', $fsRsm);
		$empsSql = "select $sFsRsm
			from resume
			where code in ($sRsmCodes)";
		$emps = BaseService::getAllBySql($empsSql);
		$fsRsmBiz = array('type', 'biz', 'biz_name', 'age');
		foreach ($emps as &$emp) {
			$cond = "resume_code = '{$emp['code']}' and type = 'FRAMEWORK'";
			$rsmBizs = BaseService::getAllByCond('resume_biz', $fsRsmBiz, $cond);
			$this->rsmDetailToView($emp, $rsmBizs);
		}
		
		//view
		$this->view->resumes = $emps;
    }
    
    
    /**
     * 法人提案发送邮件
     */
    public function sendtalentproposeAction() {
    	$emailAddress = $this->_getParam('emailAddress');
    	$emailTitle = $this->_getParam('emailTitle');
    	$emailContent = $this->_getParam('emailContent');
    	$emailBcc = $this->_getParam('emailBcc');
    	$emailCc = $this->_getParam('emailCc');
    	
		if (empty($emailAddress)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_ADDRESS_CANNOT_BE_NULL'));
			exit(json_encode($ret));
		} else if (!UtilService::isEmail($emailAddress)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_ADDRESS_IS_NOT_EMAIL'));
			exit(json_encode($ret));
		}
		if (empty($emailTitle)) {
			$ret = array('err' => 1, 'msg' => $this->tr->translate('EMAIL_TITLE_CANNOT_BE_NULL'));
			exit(json_encode($ret));
		}
    	
    	$fromEmail = 'los2@os2.com.dev';
    	$fromName = 'los2';
    	$toEmail = $emailAddress;
    	
    	$mail = new Zend_Mail('utf-8');
    	$mail->setFrom($fromEmail, $fromName);
		$mail->addTo($toEmail);
		if (!empty($emailBcc))
    		$mail->addBCc($emailBcc);
    	if (!empty($emailCc))
    		$mail->addCc($emailCc);
		$mail->setSubject($emailTitle);
		$mail->setBodyHtml($emailContent); //HTML格式，被解析成红色的：Red Hello World!
		$result = $mail->send();
		//$result = mail($toEmail, $emailTitle, $emailContent);
		//exit();
		if ($result !== FALSE) {
			$this->showSuccessMsg();
		} else {
			$this->showErrorMsg();
		}
    }
    
} //End: class Lp_TalentController