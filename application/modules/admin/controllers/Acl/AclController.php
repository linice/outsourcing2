<?php
include_once 'Acl/AclService.php';


class Admin_Acl_AclController extends Zend_Controller_Action 
{
	public function init() {
	}
	
	public function aclAction() {
		$resourcesOrigin = AclService::getResources(array('name', 'module', 'controller', 'action'));
		$resources = array();
		foreach ($resourcesOrigin as $resourceOrigin) {
			$resources[$resourceOrigin['module']][$resourceOrigin['controller']][] = $resourceOrigin['action'];
		}
//		var_dump($resources);
		$this->view->resourcesOrigin = $resourcesOrigin;
		$this->view->resources = $resources;
	}
	
	
	
	
	
	
	
} //End: class Admin_Acl_IndeController