<?php
include_once 'BaseService.php';
include_once 'UsrService.php';
include_once 'EtcService.php';
include_once 'UtilService.php';
include_once 'AKBService.php';
include_once 'CaseBaseService.php';
include_once 'BaseService2.php';


/**
 * 法人相关操作
 * @author GONGM
 */
class LpService {
	/**
	 * 注册一个新法人
	 * 从etc中根据类型以及名称取得注册法人时奖励的AK币，直接保存到用户属性中
	 * @param unknown_type $usr
	 */
	public static function regNewLp($usr, $reg=FALSE) {
		if (!is_array($usr)) return FALSE;
		$akb = EtcService::getValueByTypeAndName('DEFAULT', 'LP_REG_AKB')+0;
		$temp = EtcService::getValueByTypeAndName('DEFAULT', 'LP_EMAIL_TEMP');
		$usr['create_time'] = UtilService::getCurrentTime();
		$usr['last_login_time'] = UtilService::getCurrentTime();
		$usr['balance'] = $akb;
		if (!empty($temp)) {
			$usr['lp_propose_temp'] = $temp;
		}
		if ($reg === FALSE)
			$usr['enabled'] = "";
		else 
			$usr['enabled'] = "Y";
		$ret = UsrService::addUsr($usr);
		if ($akb > 0 && $ret !== FALSE) {
			$consumeAkb = array('usr_code' => $usr['code'], 'in_out' => 'IN', 
						'points_consume' => $akb, 'points_rest' => $akb,
						'remark' => 'LP Register', 'create_time' => UtilService::getCurrentTime());
			$result = BaseService::addRow('akb_consume', $consumeAkb);
			if ($result) {
				return $ret;
			}
		}
    	return $ret;
	}
	
	
	/**
	 * 根据公司名称查找法人
	 * @param string $name
	 * @param array(string) $fields
	 */
	public static function getLpByName($name, $fields = array('*')) {
		if (!is_string($name) || !is_array($fields)) return false;
		$strFields = implode(',', $fields);
		$sql = "select $strFields from usr where role_code='LP' and fullname = '$name'";
		return BaseService::fetchRowBySql($sql);
	}
	
	
	/**
	 * 根据主键修改法人信息
	 * @param unknown_type $id
	 * @param unknown_type $row
	 */
	public static function updateLp($id, $row = array()) {
		if (!is_array($row)) return false;
		return BaseService::updateByPrimaryKey('usr', $id, $row);
	}
	
	
	/**
	 * 审核法人
	 * @param unknown_type $code
	 */
	public static function checkLp($code) {
		$db = BaseService::getDb();
		$db->beginTransaction();
		$retVal = BaseService::updateByCode('usr', array('enabled'=>'Y'), $code);
		if ($retVal !== FALSE) {
			$cd = UtilService::getCurrentTime();
			$retVal = BaseService::exeAddSql("insert into lp_setting (lp_code, setting_code, on_or_off, rete, update_date) 
				select '$code', code, 'Y', '1', '$cd' from lp_service where need_setting='Y' and type='SETTING'");
		}
		if ($retVal === FALSE)
			$db->rollback();
		else 
			$db->commit();
		return $retVal;
	}
	
	
	/**
	 * 删除法人
	 * @param unknown_type $code
	 */
	public static function stopLp($code) {
		return BaseService::update('usr', "code='$code'", array('enabled'=>'N'));
	}
	
	public static function deleteLp($code) {
		return BaseService::delete('usr', "code='$code'");
	}
	
	
	/**
	 * 为法人开能一个服务
	 * 服务表使用有效这个标识，一个法人的一个服务最多只能有一个是有效的
	 * @param unknown_type $row
	 */
	public static function turnOnService($row = array()) {
		$db = BaseService::getDb();
		$effective = UtilService::dateDiff(date('Y-m-d'), $row['start_date'], 'DAY') >=0 && 
				UtilService::dateDiff($row['end_date'], date('Y-m-d'), 'DAY') >=0 ? 'Y' : 'N';
		$ret = TRUE;
		//开通当期有效的服务
		if ($effective == 'Y') {
			//删除当前服务表的数据
			$where[] = $db->quoteInto('lp_code = ?', $row['lp_code']);
			$where[] = $db->quoteInto('service_code = ?', $row['service_code']);
			$ret = BaseService::delete('lp_service_setting', $where);
			if ($ret !== FALSE) {
				//当期有效，当直接插入服务表
				$setting = array('lp_code'=>$row['lp_code'], 'service_code'=> $row['service_code'], 'is_current'=>'Y',
						'create_time'=>UtilService::getCurrentTime(), 'effective'=>'Y',
						'start_date'=>$row['start_date'], 'end_date'=>$row['end_date']);
				$setting['remark'] = isset($row['remark']) ? $row['remark'] : NULL; 
				$ret = BaseService::save('lp_service_setting', $setting);
				if ($ret !== FALSE) {
					//当期有效，则将服务纪录表的有效纪录改为无效，插入当前纪录为有效
					$where[] = $db->quoteInto('effective = ?', 'Y');
					$ret = BaseService::update('lp_service_setting_record', $where, array('effective'=>'N'));
				}
			}
		}
		//插入服务开通纪录表
		if ($ret !== FALSE) {
			$setting = array('lp_code'=>$row['lp_code'], 'service_code'=> $row['service_code'], 'is_current'=>'Y',
					'create_time'=>UtilService::getCurrentTime(), 'effective'=>$effective,
					'start_date'=>$row['start_date'], 'end_date'=>$row['end_date']);
			$setting['remark'] = isset($row['remark']) ? $row['remark'] : NULL; 
			$ret = BaseService::save('lp_service_setting', $setting);
			//纪录AKB的消耗纪录
			if ($ret !== FALSE && $row['akb']+0 > 0) {
				$pre_akb = UsrService::getUsrByCode($row['lp_code']);
				$pre_akb = $pre_akb['balance'];
				$l_date = UtilService::dateAddDay();
				
				$consumeAkb = array('usr_code' => $row['lp_code'], 'in_out' => 'OUT', 
						'points_consume' => $row['akb'], 'points_rest' => $pre_akb-$row['akb'],
						'service_setting_id'=>$ret, 'create_time' => UtilService::getCurrentTime());
				BaseService::addRow('akb_consume', $consumeAkb);
			}
		}
		return $ret;
	}
	
	
	/**
	 * 根据法人编码得到法人的有效服务
	 * @param unknown_type $lp_code
	 */
	public static function findAllServiceByLpCode($lp_code) {
		$sql = "select ls.code service_code, ss.effective, ss.start_date, ss.end_date from lp_service ls left join lp_service_setting ss on ls.code=ss.service_code and lp_code='$lp_code' where ls.need_setting = 'Y' and type='SERVICE'";
		return BaseService::fetchAllBySql($sql);
	}
	
	
	/**
	 * 根据法人编码得到法人的有效服务
	 * @param unknown_type $lp_code
	 */
	public static function findAllSettingByLpCode($lp_code) {
		$sql = "select ls.code setting_code, lst.id, lst.on_or_off, lst.rete, lst.remark, ls.name from lp_service ls left join lp_setting lst on ls.code=lst.setting_code and lst.lp_code='$lp_code' where ls.need_setting = 'Y' and ls.type='SETTING'";
		return BaseService::fetchAllBySql($sql);
	}
	
	
	/**
	 * 设置法人编码得到法人的有效服务
	 * @param unknown_type $lp_code
	 */
	public static function getServiceByLpCode($lp_code) {
		$ret = self::findAllServiceByLpCode($lp_code);
		$services = array();
		if ($ret !== FALSE) {
			foreach ($ret as $service) {
				$services[$service['service_code']] = $service;
			}
		}
		return $services;
	}
	
	
	/**
	 * 查找法人下的员工及面试信息
	 * @param unknown_type $lp_code
	 * @param unknown_type $case_code
	 */
	public static function findEmpListWithApplyInfoForLpByLpCode($lp_code, $codeOrName, $page) {
		$sql = "select r.*, get_resume_biz_info(r.code) biz_info";
		//员工的总被募集数，不管最后处理结果如何
		$sql = $sql.", (select count(1) from case_invite invite where invite.resume_code=r.code and invite.is_effective='Y') count_invite";
		$sql = $sql.", (select count(1) from case_apply ca where ca.resume_code=r.code) count_apply";
		$sql = $sql.", (select count(1) from case_apply ca, case_apply_record cr where ca.id=cr.case_apply_id and ca.resume_code=r.code and ca.status='INTERVIEW_ADJUST') count_adjust";
		$sql = $sql.", (select count(1) from case_apply ca, case_apply_record cr where ca.id=cr.case_apply_id and ca.resume_code=r.code and ca.status='INTERVIEW_OK') count_ok";
		$sql = $sql." from resume r where lp_code='$lp_code' ";
		$where = CaseBaseService::genWhereOptionOnEmpNameOrCode($codeOrName, 'r');
		$sql = $sql.' and '.$where;
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
	
	
	/**
	 * 查找法人下的员工
	 * @param unknown_type $lp_code
	 * @param unknown_type $case_code
	 */
	public static function findEmpListWithForLpByLpCode($lpCode, $codeOrName, $page) {
		$sql = "select r.*, get_resume_biz_info(r.code) as biz_info
			from resume r where r.lp_code='$lpCode' ";
		$where = CaseBaseService::genWhereOptionOnEmpNameOrCode($codeOrName, 'r');
		$sql = $sql.' and '.$where;
		return BaseService::findByPaginationWithSQL($sql, $page);
	}
	
	
	/**
	 * 查找法人下的员工
	 * @param unknown_type $lp_code
	 * @param unknown_type $case_code
	 */
	public static function getEmpListForLpByLpCodeAndHasApply($lpCode, $hasApply, $codeOrName, $pg) {
		if ($hasApply == 'Y') {
			$sql = "select r.*, get_resume_biz_info(r.code) as biz_info
				from resume r 
				left join case_apply as b
				on r.talent_code = b.emp_code
				where b.status in ('NO_VOTE', 'RECOMMEND', 'NO_RECOMMEND', 'INTERVIEW_ADJUST', 'INTERVIEW_INFORM', 'INTERVIEW_DECIDE') 
					and r.lp_code='$lpCode' ";
		} else {
			$sql = "select r.*, get_resume_biz_info(r.code) as biz_info
				from resume r where r.lp_code='$lpCode' ";
		}
		$where = CaseBaseService::genWhereOptionOnEmpNameOrCode($codeOrName, 'r');
		$sql = $sql.' and '.$where;
		return BaseService::findByPaginationWithSQL($sql, $pg);
	}
	
	
	/**
	 * 关于法人的综合查询方法
	 * @param unknown_type $option
	 * @param unknown_type $field
	 * @param unknown_type $page
	 */
	public static function findLpByOption($option, $fields=array('*'), $page=NULL, $reg=TRUE) {
		if (empty($option)) $option = array();
		if (!is_array($option)) return FALSE;
		if ($reg === TRUE) {
			array_push($option, "enabled='Y'");
		} else if ($reg !== FALSE) {
			array_push($option, "enabled='$reg'");
		}
		array_push($option, "role_code='LP'");
		$strFields = implode(',', $fields);
		$sql = "select $strFields from usr where ";
		$whereOption = implode(' and ', $option);
		$sql = $sql.$whereOption;
		if (isset($page['sortname']) && !empty($page['sortname'])) {
			$sql = 'select * from ('.$sql.') _tmp order by '.$page['sortname'].' '.$page['sortorder'];
		}
		if (empty($page)) {
			return BaseService::fetchAllBySql($sql);
		} else {
			return BaseService::findByPaginationWithSQL($sql, $page);
		}
	}
	
	
	/**
	 * 邮件模板设定
	 * @param unknown_type $lpCode
	 * @param unknown_type $temp
	 */
	public static function modifyEmailTemp($lpCode, $temp) {
		return UsrService::updateUsrByCode($lpCode, array('lp_propose_temp'=>$temp));
	}
	
	
	/**
	 * 设定邮件头及邮件尾
	 * @param unknown_type $lpCode
	 * @param unknown_type $temp
	 * @param unknown_type $row
	 */
	public static function modifyEmailOption($lpCode, $temp, $header, $footer) {
		$db = BaseService::getDb();
		$tempHeader = $temp == 'temp1' ? 'Ls8' : 'Ls9';
		$tempFooter = $temp == 'temp1' ? 'Ls10' : 'Ls11';
		$db->beginTransaction();
		$rows = self::findEmailOption($lpCode, $temp);
		$options = array();
		if (!empty($rows) && count($rows) > 0) {
			foreach ($rows as $option) {
				$options[$option['option_code']] = $option;
			}
		}
		$retVal = true;
		if (!empty($header)) {
			if (isset($options[$tempHeader])) {
				$retVal = BaseService::update('lp_option', "lp_code='$lpCode' and option_code='$tempHeader'", array('description'=>$header));
			} else {
				$row = array('lp_code'=>$lpCode, 'option_code'=>$tempHeader, 'description'=>$header);
				$retVal = BaseService::save('lp_option', $row);
			}
		}
		if ($retVal && !empty($footer)) {
			if (isset($options[$tempFooter])) {
				$retVal = BaseService::update('lp_option', "lp_code='$lpCode' and option_code='$tempFooter'", array('description'=>$footer));
			} else {
				$row = array('lp_code'=>$lpCode, 'option_code'=>$tempFooter, 'description'=>$footer);
				$retVal = BaseService::save('lp_option', $row);
			}
		}
		if ($retVal !== FALSE) {
			$db->commit();
		} else {
			$db->rollback();
		}
		return $retVal;
	}
	
	
	/**
	 * 查找邮件头跟尾的设置
	 * @param unknown_type $lpCode
	 */
	public static function findEmailOption($lpCode, $temp=NULL) {
		$db = BaseService::getDb();
		$optionCodes = empty($temp) ? array('Ls8', 'Ls9', 'Ls10', 'Ls11') : ($temp == 'temp1' ? array('Ls8','Ls10'):array('Ls9','Ls11'));
		$sql = $db->quoteInto("select * from lp_option where option_code in (?) and lp_code='$lpCode'", $optionCodes);
		return BaseService::fetchAllBySql($sql);
	}
	
	
	/**
	 * 修改法人设置
	 * @param unknown_type $lpCode
	 * @param unknown_type $settings
	 */
	public static function modifyLpSetting($lpCode, $settings) {
		$db = BaseService::getDb();
		$sql = "select * from lp_setting lst where lst.lp_code='$lpCode'";
		$lpSettings = BaseService::fetchAllBySql($sql);
		$retVal = true;
		$db->beginTransaction();
		foreach ($settings as $setting) {
			$setting_code = $setting['setting_code'];
			if (in_array($setting_code, $lpSettings)) {
				$retVal = BaseService::update('lp_setting', "lp_code='$lpCode' and setting_code='$setting_code'", $setting);
			} else {
				$retVal = BaseService::save('lp_setting', $setting);
			}
			if ($retVal === FALSE) break;
		}
		if ($retVal !== FALSE) {
			$db->commit();
		} else {
			$db->rollback();
		}
		return $retVal;
	}
} //End: class LpService