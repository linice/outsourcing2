<?php
class App_View_Helper_Tr {
	public function tr($code) {
		$tr = Zend_Registry::get('TRANSLATE');
//		return htmlspecialchars($tr->translate($code));
		return $tr->translate($code);
	}
} //End: class Zend_View_Helper_Tr






