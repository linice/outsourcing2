<?php
include_once 'BaseAdminController.php';
include_once 'BaseService2.php';
include_once 'BaseService.php';


class Admin_Admin_EmailController extends BaseAdminController
{
    /**
     * 邮件模板列表
     */
    public function emailtpllistAction() {
    	//导航
		$this->layout->headTitle('邮件模板列表');
		$crumb = array('uri' => '/admin/admin_email/emaillist', 'name' => '邮件模板列表');
		$this->view->layout()->crumbs = array($crumb);
    }


    /**
     * 查询邮件模板列表
     */
    public function getemailtpllistAction() {
    	//获取参数：分页
    	$this->getPagination();
    	
    	//查询邮件模板
    	$fsEmailTpl = array('id', 'name', 'title', 'content');
    	$sFsEmailTpl = implode(',', $fsEmailTpl);
		$sql = "select $sFsEmailTpl from email_tpl";
		$pgEmailTpls = BaseService::getByPageWithSql($sql, $this->pagination);
		
		//ret
		$ret = array('err' => 0, 'msg' => 'Success', 'Total' => $pgEmailTpls['Total'], 'Rows' => $pgEmailTpls['Rows']);
		exit(json_encode($ret));
    }


    /**
     * 邮件设定页面
     */
    public function emailsetAction() {
    	//导航
		$this->layout->headTitle($this->tr->translate('EMAIL_TPL_SET'));
		$crumb = array('uri' => '/admin/admin_email/emailset', 'name' => $this->tr->translate('EMAIL_TPL_SET'));
		$this->view->layout()->crumbs = array($crumb);
		
		//获取参数：邮件模板ID
		$etId = $this->_getParam('etId', 0);
		if(!empty($etId)) {
			$emailTpl= BaseService::getRowById('email_tpl', $etId);
			$this->view->jsEmailTpl = json_encode($emailTpl);
		}
		$this->view->etId = $etId;
    }
    
    
    /**
     * 设定邮件模板
     */
    public function setemailtplAction() {
    	//获取参数
    	$etId = trim($this->_getParam('etId', 0));
    	$name = trim($this->_getParam('name'));
		$title = trim($this->_getParam('title'));
		$content = trim($this->_getParam('content'));
		
		//验证参数
		if (empty($name)) {
			$ret = array('err' => 2, 'msg' => $this->tr->translate('PLEASE_ENTER') . $this->tr->translate('EMAIL_NAME'),
				'label' => 'label_name'
			);
			exit(json_encode($ret));
		}
		if (empty($title)) {
			$ret = array('err' => 2, 'msg' => $this->tr->translate('PLEASE_ENTER') . $this->tr->translate('EMAIL_TITLE'),
				'label' => 'label_title'
			);
			exit(json_encode($ret));
		}
		if (empty($content)) {
			$ret = array('err' => 2, 'msg' => $this->tr->translate('PLEASE_ENTER') . $this->tr->translate('EMAIL_CONTENT'),
				'label' => 'label_content'
			);
			exit(json_encode($ret));
		}
		
		//组合邮件模板记录
		$emailTpl = array();
		$emailTpl['name'] = $name;
		$emailTpl['title'] = $title;
		$emailTpl['content'] = $content;
		
		//保存或更新
		if (empty($etId)) { //新邮件模板
			$result = BaseService::addRow('email_tpl', $emailTpl);
		} else {
			$result = BaseService::updateById('email_tpl', $emailTpl, $etId);
		}
		
		//Success
		if ($result) {
			$ret = array('err' => 0, 'msg' => $this->tr->translate('SAVE') . $this->tr->translate('EMAIL_TPL') . $this->tr->translate('SUCCESS'));
			exit(json_encode($ret));
		}

		//Error
		$ret = array('err' => 1, 'msg' => $this->tr->translate('SAVE') . $this->tr->translate('EMAIL_TPL') . $this->tr->translate('FAILED'));
		exit(json_encode($ret));
    }
    
    
    /**
     * 删除邮件模板
     */
    public function delemailtplAction() {
    	//获取参数
    	$etIds = $this->_getParam('etIds');
    	
    	//删除邮件模板
    	$result = BaseService::delByIds('email_tpl', $etIds);
    	if ($result) {
    		$ret = array('err' => 0, 'msg' => 'Success');
			exit(json_encode($ret));
    	}
    	$ret = array('err' => 1, 'msg' => 'Error');
		exit(json_encode($ret));
    }





} //End: class Admin_Admin_EmailController