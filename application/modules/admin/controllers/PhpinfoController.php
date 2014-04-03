<?php
class Admin_PhpinfoController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }
	
    public function phpinfoAction()
    {
        // action body
//        $this->view->render('phpinfo/index.phtml');
		//下面这句话会输出view：test/index.phtml
		//前提是该Action首先能找到自己的view，否则会出错
//        echo $this->view->render('test/index.phtml');
    }


}
