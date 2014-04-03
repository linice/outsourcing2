<?php
include_once 'UtilService.php';
include_once 'UsrService.php';
include_once 'EtcService.php';
include_once 'BaseAdminController.php';


class Admin_Admin_CasesearchController extends BaseAdminController {
	
	private $industries = array();
	private $careers = array();
	private $languages = array();
	private $japan = array();
	private $oversea = array();
	
	private $searchFields = array("range"=>NULL, "casename"=>NULL, "casetype"=>NULL,
								"careers"=>array(), "languages"=>array(), "industries"=>array(),
								"workplace"=>array(), "radiobutton"=>null, "startDate"=>NULL,
								"endDate"=>NULL, "unitprice"=>null);

	/**
	 * 初始化案件检索页面各种列表
	 */
	private function initcasepage() {
		//查询杂项，Web app变量
		$etcs = EtcService::getEtcs();
		foreach ($etcs as $etc) {
			if ($etc['type'] == 'CAREER') { //职种
				$this->careers[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'INDUSTRY') { //业务领域
				$this->industries[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'LANGUAGE') { //程序设计语言
				$this->languages[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'WORKING_PLACE_JP') { //日语要求
				$this->japan[$etc['code']] = $this->tr->translate($etc['code']);
			} else if ($etc['type'] == 'WORKING_PLACE_OTHER') { //担当范围
				$this->oversea[$etc['code']] = $this->tr->translate($etc['code']);
			}
		}
		$this->view->industries = $this->industries;
		$this->view->careers = $this->careers;
		$this->view->languages = $this->languages;
		$this->view->japan = $this->japan;
		$this->view->oversea = $this->oversea;
	}
	
	/**
	 * 进入案件检索页面
	 */
	public function casesearchAction() {
		$this->layout->headTitle($this->tr->translate('case_search'));
		$crumb = array('uri' => '/admin/admin_casesearch/casesearch', 'name' => $this->tr->translate('case_search'));
		$this->view->layout()->crumbs = array($crumb);

		$jsSearchFields = $this->_getParam('searchFields');
		$searchFields = (array)json_decode($jsSearchFields);
		
		$this->view->orgSearchFields = $searchFields;
		$this->initcasepage();
	}

	/**
	 * 从param中接收参数到对象ob中
	 * @param unknown_type $ob
	 */
	private function fetchFromParam() {
		foreach (array_keys($this->searchFields) as $value) {
			if (is_array($this->searchFields[$value])) 
				$this->searchFields[$value] = $this->_getParam($value);
			else 
				$this->searchFields[$value] = trim($this->_getParam($value));
		}
	}
	
	/**
	 * 案件检索结果
	 */
	public function casesearchresultAction() {
		//导航
		$this->layout->headTitle($this->tr->translate('case_search_result'));
		$crumb = array('uri' => '/admin/admin_casesearch/casesearchresult', 'name' => $this->tr->translate('case_search_result'));
		$this->view->layout()->crumbs = array($crumb);

		$this->fetchFromParam();
		$this->view->searchFields = $this->searchFields;
	}
}