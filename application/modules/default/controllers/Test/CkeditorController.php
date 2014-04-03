<?php
include_once 'UsrService.php';
include_once 'UtilService.php';
include_once 'BaseController.php';


class Test_CkeditorController extends BaseController
{
    /**
     * ckeditor测试
     */
    public function ckeditorAction() {
    	$this->layout->headTitle('Ckeditor');
	    $crumb = array('uri'=>'/test_ckeditor/ckeditor', 'name'=>'Ckeditor');
		$this->view->layout()->crumbs = array($crumb);
    }
    
    
	/**
	 * phpinfo 
	 */
	public function phpinfoAction() {
	    $this->layout->headTitle('outsourcing2 phpinfo');
	    $crumb = array('uri'=>'/index/phpinfo', 'name'=>'phpinfo');
		$this->view->layout()->crumbs = array($crumb);
		phpinfo();
		exit;
    }
    
    
} //End: class Test_CkeditorController