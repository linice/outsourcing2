<?php
include_once 'UtilService.php';
include_once 'BaseService2.php';
include_once 'Acl/AclService.php';
include_once 'BaseService.php';
include_once 'EtcService.php';


class AbstractController extends Zend_Controller_Action {
	//声明变量
    protected $layout = null;
	protected $auth = null;
	protected $tr = null;
	protected $db = null;
	protected $pagination = array('limit'=>20,'start'=>0, 'sortname'=>NULL, 'sortorder'=>'ASC');
	protected $orderBy = null;
	protected $limit = 20;
	protected $start = 0;
	private $now = NULL;
	private $orderByArray = array('unitPriceValue'=>'unit_price',
				'unitPriceValueView'=>'unit_price', 'period'=>'end_date',
				'caseCnt'=>'caseCnt', 'empCnt'=>'empCnt'); 
	
	
	/**
	 * 设计原则是，各角色（GUEST, MEMBER, LP, EMP, EDITOR, ASSIST, ADMIN）通过界面操作，或url来访问资源（Action）。
	 * 先判断此资源是否公开，是，则
	 * 此时，就需要验证用户是否已登陆，若已登陆，则判断该用户是否有权限访问。 
	 */
	protected function ckPerm() {
		//获取参数：资源：模块，控制器，方法
		$module = trim(strtolower($this->_request->getModuleName()));
		$controller = trim(strtolower($this->_request->getControllerName()));
		$action = trim(strtolower($this->_request->getActionName()));
		$rscCode = $module . '/' . $controller . '/' . $action;
		
		//判断该资源是否公开：若公开，则所有人（包括游客）都有权限访问，否则，需要判断用户是否登录，且是否具有对该资源的权限
		$isPub = AclService::isRscPub($module, $controller, $action);
//		var_dump(array($module, $controller, $action));
//		var_dump($isPub);
//		exit;
		if (!$isPub) {
			//检查用户是否已登陆，若未登陆，返回未登陆信息（Ajax），转到登陆页面（Not Ajax）。
			$auth = new Zend_Session_Namespace('AUTH');
			$tr = Zend_Registry::get('TRANSLATE');
			//登陆框用到的js&css
			$js_css = '<link href="/css/global.css" rel="stylesheet" type="text/css" />
				<link href="/js/jquery/ui/themes/redmond/jquery-ui-1.8.21.custom.css" rel="stylesheet" type="text/css" />
				<script src="/js/jquery/jquery-1.7.2.js" type="text/javascript"></script>
				<script src="/js/jquery/ui/jquery-ui-1.8.21.custom.js" type="text/javascript"></script>
				<script src="/js/global.js" type="text/javascript"></script>';
			//如果用户未登陆，则弹出登录框让用户登陆
			if (empty($auth->usr)) {
				//dlg：登陆
				//登陆框内容
				$dlg = '<div id="dlg_login" title="' . $tr->translate('LOGIN') . '" style="display: none; text-align:center;">
					<div id="tip_dlg_login" class="tip"></div>
					<form id="form_dlg_login" action="" method="post">
						<div style="margin:10px 0 10px 0;">'
						. $tr->translate('EMAIL') . '：<input id="dlg_login_email" name="login_email" type="text" value="" />
						</div>
						<div>' 
						. $tr->translate('PASSWORD') . '：<input id="dlg_login_passwd" name="login_passwd" type="password" value="" />
						</div>
						<div>
							<input type="checkbox" name="dlg_isAutoLogin" name="isAutoLogin" value="Y" />'
						. $tr->translate('AUTO_LOGIN')
					. '</div>
					</form>
					<div style="margin:10px 0 10px 0;">
						<button id="btn_dlg_login" type="button">' . $tr->translate('LOGIN') . '</button>
						<span style="margin-left:20px;"><a href="/front_register/register" target="_blank">' . $tr->translate('MEMBER') . $tr->translate('REGISTER') . '</a></span>
						<span style="margin-left:20px;"><a href="/front_lpreg/lpreg" target="_blank">' . $tr->translate('LP') . $tr->translate('REGISTER') . '</a></span>
					</div>
				</div>';
				$html = $js_css. $dlg . "<script type='text/javascript'>
					$('#dlg_login').dialog({
						autoOpen: true,
						modal: true,
						width: 400,
						height: 400
					});
				</script>";
				if($this->_request->isXmlHttpRequest()){
					$ret = array('err' => 1, 'msg' => $html);
					exit(json_encode($ret));
				} else {
					exit($html);
				}
			} else { //如果用户已登陆，则判断该用户的角色对当下要访问的资源是否有权限
				$hasPriv = AclService::hasPriv($rscCode, $auth->usr['role_code']);
				if (!$hasPriv) {
					$dlg = '<div id="dlg_no_priv" title="' . $tr->translate('TIP') . '" style="display: none; text-align:center;">
							<div>' . $tr->translate('YOU_HAVE_NO_PRIV_TO_VISIT') . '</div>
							<div style="margin:20px 0 10px 0;">
								<button id="btn_dlg_cfm_no_priv" type="button">' . $tr->translate('SURE') . '</button>
							</div>
						</div>';
					$html = $js_css. $dlg . "<script type='text/javascript'>
							$('#dlg_no_priv').dialog({
								autoOpen: true,
								modal: true,
								width: 300
							});
						</script>";
					if($this->_request->isXmlHttpRequest()){
						$ret = array('err' => 1, 'msg' => $html);
						exit(json_encode($ret));
					} else {
						exit($html);
					}
				}
			}
		}
	}
	
	
	/**
     * 统计当前在线的访问者信息：访问者IP，访问的uri，最后访问时间。
     * 以半小时计。
     */
    protected function statOlVisitors() {
    	//获取访问者的ID，和访问的uri
    	$ip = $_SERVER['REMOTE_ADDR'];
    	$uri = $_SERVER['REQUEST_URI'];
    	$now = date('Y-m-d H:i:s');
    	
    	//查询半小时内该IP是否存在于访问者表visitor
    	$cond = "ip = '$ip' and update_time > DATE_SUB(now(),INTERVAL 30 minute)";
    	$result = BaseService::getOneByCond('visitor', '1', $cond);
    	//如果访问者IP存在，则更新访问者记录
    	if (!empty($result)) {
    		$visitor = array('uri' => $uri, 'update_time' => $now);
    		BaseService::updateByCond('visitor', $visitor, $cond);
    	} else { //如果访问者IP不存在，则插入访问信息
    		$visitor = array('ip' => $ip, 'uri' => $uri, 'update_time' => $now);
    		BaseService::addRow('visitor', $visitor);
    	}
    }
    
	
    protected function getText($label) {
    	return $this->tr->translate($label);
    }
    
    protected function getArrayText($arg = array()) {
    	$ret = array();
    	foreach (array_values($arg) as $value) {
    		array_push($ret, $this->getText($value));
    	}
    	return $ret;
    }
    
    protected function getBoolean($bool, $value=array('Y'=>'有', 'N'=>'无')) {
    	if (empty($bool)) return '';
    	return $value[$bool];
    }
    
    
    /**
     * 获取分页参数
     */
    protected function getPagination() {
    	$page = $this->_getParam('page', 1);
    	$this->limit = $this->_getParam('pagesize', 20);
    	$sortname = $this->_getParam('sortname');
    	$sortorder = $this->_getParam('sortorder');
    	$this->start = ($page - 1) * $this->limit;
    	$this->pagination['limit'] = $this->limit;
    	$this->pagination['start'] = $this->start;
    	$pattern = '/[A-Z]/';
    	if (isset($this->orderByArray[$sortname])) {
    		$sortname = $this->orderByArray[$sortname];
    	} else if (preg_match($pattern, $sortname)) {
    		$sortname = NULL;
    		$sortorder = NULL;
    	}
    	$this->pagination['sortname'] = $sortname;
    	$this->pagination['sortorder'] = $sortorder;
    	$this->orderBy = !empty($sortname) && !empty($sortorder) ? $sortname.' '.$sortorder : NULL;
    }
    
    
    /**
     * 生成分页的数组
     */
    protected function genPagination($total=0, $rows=array()) {
		return array('Total'=> $total, 'Rows'=>$rows);
    }
    
    /**
     * copy一个新案件
     * @param unknown_type $case
     */
    protected  function copyNewCase($case=array()) {
    	//$case['careers'] = str_replace(',', ';', $case['careers']);
    	//$case['languages'] = str_replace(',', ';', $case['languages']);
    	$case['id'] = NULL;
		$case['code'] = NULL;
		$case['akb'] = NULL;
		$case['type'] = 'U';
		$case['lp_code'] = NULL;
		$case['lp_name'] = NULL;
		$case['timeliness'] = NULL;
		$case['start_date'] = NULL;
		$case['start_date_current'] = NULL;
		$case['end_range'] = NULL;
		$case['end_date'] = NULL;
		return $case;
    }
    
    /**
     * 对活动对象进行转义
     */
    protected function eventToView($event=array()) {
    	$event['typeValue'] = $this->getText($event['type']);
    	return $event;
    }
    
    /**
     * 对Case对象进行转义
     */
	protected function caseToView($case=array()) {
		$low = EtcService::getValueByTypeAndName("UNIT_PRICE_VIEW_SETTING", "LOW")+0;
		$high = EtcService::getValueByTypeAndName("UNIT_PRICE_VIEW_SETTING", "HIGH")+0;
    	$case['careersValue'] = join(',', $this->getArrayText(explode(';',$case['careers'])));
    	$businessReq = explode(';',$case['industries']);
    	$technicalReq = explode(';',$case['languages']);
		$case['languagesValue'] = join(',', $this->getArrayText($technicalReq));
		$case['industriesValue'] = join(',', $this->getArrayText($businessReq));
		if (count($technicalReq) >= 3) {
			array_splice($technicalReq, 3);
		}
		$case['businessReqValue'] = $case['industriesValue'];
		$case['technicalReqValue'] = join(',', $this->getArrayText($technicalReq));
    	$case['jplValue'] = $this->getText($case['jpl']);
    	$case['workingPlaceValue'] = $this->getText($case['working_place']);
		$case['caseRangeValue'] = $this->getText($case['case_range_start']).'～'.$this->getText($case['case_range_end']);
		$case['startDateValue'] = (!empty($case['start_date_current']) && $case['start_date_current']=='Y') || ($case['start_date_current']!='Y' && UtilService::dateDiff($case['start_date'], date('Y-m-d'))+0 < 0) ? $this->getText('case_current_date') : $case['start_date'];
		$case['endDateValue'] = empty($case['end_range']) ? $case['end_date'] : $this->getText($case['end_range']);
		$case['period'] = $case['startDateValue'].'～'.$case['endDateValue'];
		$case['delayValue'] = $this->getText($case['delay']);
		$unitPriceValue = $this->getText($case['unit_price']);
		$unitPriceUnit = empty($case['unit_price_unit']) ? $this->getText('unit_price') : $this->getText($case['unit_price_unit']);
		if (UtilService::isPositive($case['unit_price_low']) || UtilService::isPositive($case['unit_price_high'])) {
			if (!UtilService::isPositive($case['unit_price_low'])) {
				$case['unitPriceValue'] = '～'.$case['unit_price_high'].$unitPriceUnit;
				$case['unitPriceValueView'] = '～'.($case['unit_price_high']+0+$high).$unitPriceUnit;
			} else if (!UtilService::isPositive($case['unit_price_high'])) {
				$case['unitPriceValue'] = $case['unit_price_low'].$unitPriceUnit.'～';
				$case['unitPriceValueView'] = ($case['unit_price_low']+0-$low).$unitPriceUnit.'～';
			} else {
				$case['unitPriceValue'] = $case['unit_price_low'].'～'.$case['unit_price_high'].$unitPriceUnit;
				$case['unitPriceValueView'] = ($case['unit_price_low']+0-$low).'～'.($case['unit_price_high']+0+$high).$unitPriceUnit;
			}
		} else {
			$case['unitPriceValue'] = $unitPriceValue;
			$case['unitPriceValueView'] = $unitPriceValue;
		} 
		$case['overtimePayValue'] = $this->getText($case['overtime_pay']);
		if ($case['overtime_pay'] == 'OVERTIME_PAY_Y' && !empty($case['overtime_pay_detail'])) {
			$case['overtimePayValue'] = $case['overtimePayValue'].' '.$case['overtime_pay_detail'].$this->getText('over_pay_label');
		} else if ($case['overtime_pay'] == 'OVERTIME_PAY_C' && !empty($case['overtime_pay_detail'])) {
			$case['overtimePayValue'] = $case['overtimePayValue'].' '.$case['overtime_pay_detail'];
		}
		$case['interviewerValue'] = empty($case['interviewer']) ? 0 : $this->getText($case['interviewer']);
    	$case['ageReqValue'] = $this->getText($case['age_req']);
		if (!empty($case['age_req_begin']) || !empty($case['age_req_end'])) {
			if (empty($case['age_req_begin']))
				$case['ageReqValue'] = $case['age_req_end'].$this->getText('unit_price_below');
			else if (empty($case['age_req_end']))
				$case['ageReqValue'] = $case['age_req_begin'].$this->getText('unit_price_above');
			else
				$case['ageReqValue'] = $case['age_req_begin'].'～'.$case['age_req_end'];
		}
		$case['countryReqValue'] = $this->getText($case['country_req']);
		$case['visibilityValue'] = $this->getText($case['visibility']);
		return $case;
    }
    
    /**
     * 制造Case的查询条件
     * @param unknown_type $range
     * @param unknown_type $casename
     */
    protected function createCaseSearch($range, $casename) {
    	return empty($casename) ? NULL : array('range'=>$range, 'value'=>$casename);
    }

    /**
     * 对CaseApply对象进行转义
     * @param unknown_type $caseapply
     */
	protected function caseapplyToView($caseapply, $toUsr=TRUE) {
		if (empty($caseapply)) return NULL;
		$caseapply['statusValue'] = $this->getText($caseapply['apply_status']);
		$caseapply['reasonValue'] = $this->getText($caseapply['apply_reason']);
		$caseapply['lackValue'] = $caseapply['apply_status'] == 'APPLY_CANCEL' ? $this->getText($caseapply['cancel_body']).$this->getText('CANCEL').' ' : '';
		$caseapply['lackValue'] = $caseapply['lackValue'].$caseapply['reasonValue']/*.' '.$caseapply['apply_remark']*/;
		if ($toUsr == TRUE) {
			$caseapply['sexValue'] = $this->getText($caseapply['sex']);
			$caseapply['age'] = UtilService::calcFullAge($caseapply['birthday']).'岁';
			$caseapply['experience_age'] = UtilService::calcFullAge($caseapply['date_graduate']);
	    	$caseapply['ja_experience_age'] = UtilService::calcFullAge($caseapply['date_arrive_jp']);
	    	$caseapply['experienceValue'] = $caseapply['experience_age'].$this->getText('YEAR').'('.$caseapply['ja_experience_age'].$this->getText('YEAR').$this->getText('JP').')';
			if ($caseapply['able_work_date_choice'] == 'SPECIFY_DATE') {
	    		$caseapply['ableWorkDateValue'] = $caseapply['able_work_date'];
	    	} else {
		    	$caseapply['ableWorkDateValue'] = $this->tr->translate($caseapply['able_work_date_choice']);
	    	}
	    	$caseapply['jpl'] = $this->getText($caseapply['ja_ability']);
	    	
			$biz = '';
			$tech = '';
			if (isset($caseapply['biz_info'])) {
				$bizInfo = (array)json_decode($caseapply['biz_info'], true);
				$resume['bizInfo'] = empty($bizInfo) ? array() : $bizInfo[0];
				if (isset($bizInfo[0]['BIZ'])) {
					foreach ($bizInfo[0]['BIZ'] as $bizcode=>$bizyear) {
						if (!empty($biz)) $biz = $biz.'/'; 
						$biz = $biz.$this->getText($bizcode).$bizyear.$this->getText('YEAR');
					}
				}
				if (isset($bizInfo[0]['FRAMEWORK'])) {
					foreach ($bizInfo[0]['FRAMEWORK'] as $bizcode=>$bizyear) {
						if (!empty($tech)) $tech = $tech.'/'; 
						$tech = $tech.$this->getText($bizcode).$bizyear.$this->getText('YEAR');
					}
				}
			} else {
				$resume['bizInfo'] = array();
			}
			$caseapply['BIZ'] = $biz;
			$caseapply['FRAMEWORK'] = $tech;
			$caseapply['baseinfo'] = $caseapply['fullname'].' '.$caseapply['sexValue'].' '.$caseapply['age'];
			if (!empty($caseapply['experienceValue'])) {
				$caseapply['baseinfo'].=' '.$caseapply['experienceValue'];
			}
			$caseapply['baseinfolong'] = $caseapply['baseinfo'];
			if (!empty($caseapply['jpl'])) {
				$caseapply['baseinfolong'].=' '.$this->getText('JPL').$caseapply['jpl'].$this->getText('unit_level');;
			}
			if (!empty($caseapply['FRAMEWORK'])) {
				$caseapply['baseinfolong'].=' '.$caseapply['FRAMEWORK'];;
			}
			if (!empty($caseapply['ableWorkDateValue'])) {
				$caseapply['baseinfolong'].=' '.$this->getText('WORK_DATE').':'.$caseapply['ableWorkDateValue'];;
			}
		}
		return $caseapply;
	}
	

    /**
     * 把简历组织成用于页面显示的简历
     * @param array($field => $value) $resume
     */
    protected function resumeToView($resume) {
    	$resume['sexValue'] = $this->getText($resume['sex']);
    	$resume['age'] = UtilService::calcFullAge($resume['birthday']);
    	$resume['experience_age'] = UtilService::calcFullAge($resume['date_graduate']);
    	$resume['ja_experience_age'] = UtilService::calcFullAge($resume['date_arrive_jp']);
    	$resume['experienceValue'] = $resume['experience_age'].$this->getText('YEAR').'('.$resume['ja_experience_age'].$this->getText('YEAR').$this->getText('JP').')';
    	if ($resume['able_work_date_choice'] == 'SPECIFY_DATE') {
    		$resume['ableWorkDateChoiceValue'] = $resume['able_work_date'];
    	} else {
	    	$resume['ableWorkDateChoiceValue'] = $this->tr->translate($resume['able_work_date_choice']);
    	}
    	$resume['jpl'] = $this->getText($resume['ja_ability']);
    	$resume['update_date'] = substr($resume['update_time'], 0, 10);
		$biz = '';
		$tech = '';
		if (isset($resume['biz_info'])) {
			$bizInfo = (array)json_decode($resume['biz_info'], true);
			$resume['bizInfo'] = empty($bizInfo) ? array() : $bizInfo[0];
			if (isset($bizInfo[0]['BIZ'])) {
				foreach ($bizInfo[0]['BIZ'] as $bizcode=>$bizyear) {
					if (!empty($biz)) $biz = $biz.'/'; 
					$biz = $biz.$this->getText($bizcode).$bizyear.$this->getText('YEAR');
				}
			}
			if (isset($bizInfo[0]['FRAMEWORK'])) {
				foreach ($bizInfo[0]['FRAMEWORK'] as $bizcode=>$bizyear) {
					if (!empty($tech)) $tech = $tech.'/'; 
					$tech = $tech.$this->getText($bizcode).$bizyear.$this->getText('YEAR');
				}
			}
		} else {
			$resume['bizInfo'] = array();
		}
		$resume['BIZ'] = $biz;
		$resume['FRAMEWORK'] = $tech;
    	$resume['baseinfo'] = $resume['fullname'].' '.$resume['sexValue'].' '.$resume['age'];
		if (!empty($resume['experienceValue'])) {
			$resume['baseinfo'].=' '.$resume['experienceValue'];
		}
		$resume['baseinfolong'] = $resume['baseinfo'];
		if (!empty($resume['jpl'])) {
			$resume['baseinfolong'].=' '.$this->getText('JPL').$resume['jpl'].$this->getText('unit_level');;
		}
		if (!empty($resume['FRAMEWORK'])) {
			$resume['baseinfolong'].=' '.$resume['FRAMEWORK'];;
		}
		if (!empty($resume['ableWorkDateValue'])) {
			$resume['baseinfolong'].=' '.$this->getText('WORK_DATE').':'.$resume['ableWorkDateValue'];;
		}
		return $resume;
    }
    
    
    /**
     * 把简历组织成用于页面显示的简历
     * @param array($field => $value) $resume
     */
    protected function rsmsWithBizToView(&$rsms) {
    	$sex = array('' => '', 'M' => $this->tr->translate('M'), 'F' => $this->tr->translate('F'));
    	foreach ($rsms as &$rsm) {
	    	$rsm['tr_sex'] = $sex[$rsm['sex']];
	    	$age = UtilService::calcFullAge($rsm['birthday']);
			$rsm['age'] =  ($age != false) ? $age : '';
	    	$rsm['experience_age'] = UtilService::calcFullAge($rsm['date_graduate']);
	    	if ($rsm['date_arrive_jp'] == '0000-00-00') {
	    		$rsm['ja_experience_age'] = 0;
	    	} else {
				if (strtotime($rsm['date_arrive_jp']) > strtotime($rsm['date_graduate'])) {
			    	$rsm['ja_experience_age'] = UtilService::calcFullAge($rsm['date_arrive_jp']) + 1;
				} else {
					$rsm['ja_experience_age'] = $rsm['experience_age'];
				}
	    	}
	    	$rsm['experience'] = $this->tr->translate('EXPERIENCE') . $rsm['experience_age'] . $this->tr->translate('YEAR') 
				. '(' . $rsm['ja_experience_age'] . $this->tr->translate('YEAR') . $this->tr->translate('JP') . ')';
	    	$rsm['experience_admin'] = $this->tr->translate('EXPERIENCE') . $rsm['experience_age'] . $this->tr->translate('YEAR') 
				. '(' . $rsm['ja_experience_age'] . $this->tr->translate('YEAR') . ')';
			$rsm['experience_prefix'] = $this->tr->translate('PREFIX_NUM') . $rsm['experience_age'] . $this->tr->translate('YEAR') 
				. '(' . $this->tr->translate('PREFIX_NUM') . $rsm['ja_experience_age'] . $this->tr->translate('YEAR') . $this->tr->translate('JP') . ')';
	    	if ($rsm['able_work_date_choice'] == 'SPECIFY_DATE') {
				$rsm['adjust_able_work_date'] = $rsm['able_work_date'];
			} else {
		    	$rsm['adjust_able_work_date'] = $this->tr->translate($rsm['able_work_date_choice']);
			}
	    	$rsm['ja_ability'] = $this->tr->translate('JA_ABILITY_' . $rsm['ja_ability']);
	    	$rsm['update_date'] = substr($rsm['update_time'], 0, 10);
	    	if (!empty($rsm['last_login_time'])) {
	    		$rsm['last_login_date'] = substr($rsm['last_login_time'], 0, 10);
	    	}
	    	$rsm['arr_biz'] = array();
			$biz = '';
			$fw = '';
			$fwCnt = 3;
			if (isset($rsm['rsm_biz'])) {
				$rsm_biz = json_decode($rsm['rsm_biz'], true);
				$rsm['arr_biz'] = $rsm_biz;
				if (isset($rsm_biz['BIZ'])) {
					foreach ($rsm_biz['BIZ'] as $bizcode => $bizyear) {
						$biz .= $this->getText($bizcode) . ' ' . $bizyear . $this->getText('YEAR') . '/';
					}
				}
				if (isset($rsm_biz['FRAMEWORK'])) {
					foreach ($rsm_biz['FRAMEWORK'] as $bizcode => $bizyear) {
						if ($fwCnt-- > 0) {
							$fw .= $this->getText($bizcode) . ' ' . $bizyear . $this->getText('YEAR') . '/';
						}
					}
				}
			}
			$rsm['biz'] = rtrim($biz, '/ ');
			$rsm['fw'] = rtrim($fw, '/ ');
			$rsm['salary_display'] = $rsm['salary_min_display'] . '～' . $rsm['salary_exp_display'] . $this->tr->translate('TEN_THOUSAND') . $this->tr->translate('YEN') . '/' . $this->tr->translate('MONTH');
    	}
    }
    
    
    /**
     * 因为函数protected function resumeToView($resume=array())，没有考虑到“其它”，
     * 如：biz_other1,2,3; framework_other1,2,3
     * 并且，函数resumeToView直接读出了简历详情，现在简历详情区别于简历一览，另外查询。
     * 所以，该函数及后面rsmDetailToView用于替换它。
     * @param array() $rsms
     */
    protected function rsmsToView(&$rsms) {
		$sex = array('' => '', 'M' => $this->tr->translate('M'), 'F' => $this->tr->translate('F'));
		foreach ($rsms as &$resume) {
			$resume['tr_sex'] = $sex[$resume['sex']];
			$age = UtilService::calcFullAge($resume['birthday']);
			$resume['age'] =  ($age != false) ? $age : '';
			$resume['update_date'] = substr($resume['update_time'], 0, 10);
		}
    }
    
    
    /**
     * 因为函数protected function resumeToView($resume=array())，没有考虑到“其它”，
     * 如：biz_other1,2,3; framework_other1,2,3
     * 并且，函数resumeToView直接读出了简历详情，现在简历详情区别于简历一览，另外查询。
     * 所以，该函数及前面rsmsToView用于替换它。
     * @param array() $resume
     * @param array() $resumeBizs
     */
    protected function rsmDetailToView(&$resume, $resumeBizs) {
    	$sex = array('' => '', 'M' => $this->tr->translate('M'), 'F' => $this->tr->translate('F'));
    	$resume['experience_age'] = UtilService::calcFullAge($resume['date_graduate']) + 1;
    	if (strtotime($resume['date_arrive_jp']) > strtotime($resume['date_graduate'])) {
	    	$resume['ja_experience_age'] = UtilService::calcFullAge($resume['date_arrive_jp']) + 1;
    	} else {
    		$resume['ja_experience_age'] = $resume['experience_age'];
    	}
    	$resume['experience'] = $this->tr->translate('EXPERIENCE') . $resume['experience_age'] . $this->tr->translate('YEAR') 
    		. '(' . $resume['ja_experience_age'] . $this->tr->translate('YEAR') . $this->tr->translate('JP') . ')';
    	$resume['experience_prefix'] = $this->tr->translate('PREFIX_NUM') . $resume['experience_age'] . $this->tr->translate('YEAR') 
    		. '(' . $this->tr->translate('PREFIX_NUM') . $resume['ja_experience_age'] . $this->tr->translate('YEAR') . $this->tr->translate('JP') . ')';
    	$biz = ''; //业务
    	$bizCnt = 3; //业务数量
    	$skill = ''; //技术
    	$skillCnt = 3; //技术数量
    	foreach ($resumeBizs as $resumeBiz) {
    		if ($resumeBiz['type'] == 'BIZ' && $bizCnt > 0) {
    			$bizCnt--;
    			if (substr($resumeBiz['biz'], 0, 4) == 'biz_') {
	    			$biz .= $resumeBiz['biz_name'] . ' ' . $resumeBiz['age'] . $this->tr->translate('YEAR') . ' / ';
    			} else {
	    			$biz .= $this->tr->translate($resumeBiz['biz']) . ' ' . $resumeBiz['age'] . $this->tr->translate('YEAR') . ' / ';
    			}
    		} elseif ($resumeBiz['type'] == 'FRAMEWORK' && $skillCnt > 0) {
    			$skillCnt--;
    			if (substr($resumeBiz['biz'], 0, 10) == 'framework_') {
	    			$skill .= $resumeBiz['biz_name'] . ' ' . $resumeBiz['age'] . $this->tr->translate('YEAR') . ' / ';
    			} else {
	    			$skill .= $this->tr->translate($resumeBiz['biz']) . ' ' . $resumeBiz['age'] . $this->tr->translate('YEAR') . ' / ';
    			}
    		}
    	}
    	$resume['age'] = UtilService::calcFullAge($resume['birthday']);
    	$resume['tr_sex'] = $sex[$resume['sex']];
    	$resume['update_date'] = substr($resume['update_time'], 0, 10);
    	$resume['biz'] = rtrim($biz, '/ ');
    	$resume['skill'] = rtrim($skill, '/ ');
    	$resume['ja_ability'] = $this->tr->translate('JA_ABILITY_' . $resume['ja_ability']);
    	if ($resume['able_work_date_choice'] == 'SPECIFY_DATE') {
    		$resume['adjust_able_work_date'] = $resume['able_work_date'];
    	} else {
	    	$resume['adjust_able_work_date'] = $this->tr->translate($resume['able_work_date_choice']);
    	}
    	$resume['salary_display'] = $resume['salary_min_display'] . '～' . $resume['salary_exp_display'] . $this->tr->translate('TEN_THOUSAND') . $this->tr->translate('YEN') . '/' . $this->tr->translate('MONTH');
    	if (isset($resume['last_login_time'])) {
	    	$resume['last_login_date'] = substr($resume['last_login_time'], 0, 10);
    	}
    }
    
    
	/**
	 * 对CaseApply对象列表进行转义
	 * @param unknown_type $caseapplyList
	 */
	protected function caseapplyListToView($caseapplyList, $toUsr=TRUE) {
		if (empty($caseapplyList)) return NULL;
		$ary = array();
		foreach ($caseapplyList as $caseapply) {
			array_push($ary, $this->caseapplyToView($caseapply, $toUsr));
		}
		return $ary;
	}
	
	/**
	 * 属性复制
	 * @param unknown_type $orgArray
	 * @param unknown_type $toArray
	 */
	protected function copyProperty($orgArray=array(), $toArray=array()) {
		foreach (array_keys($orgArray) as $p) {
			if (isset($toArray[$p])) {
				$toArray[$p] = $orgArray[$p];
			}
		}
	}
	
} //End: class AbstractController