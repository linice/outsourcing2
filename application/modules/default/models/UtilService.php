<?php
include_once 'LogService.php';


class UtilService {
	/**
	 * 判断是否是手机号码
	 * @param string $mobile
	 */
	public static function isMobile($mobile) {
//		$pattern = '/^0?1[358]\d{9}$/';
		$pattern = '/[\d\-\+]+/';
	//	var mobile = "18676660186";
		return preg_match($pattern, $mobile);
	}
	
	
	/**
	 * 判断是否是电话号码
	 * @param string $tel
	 */
	public static function isTel($tel) {
//		$pattern = '/^\+?(d{2})?0?\d{2,3}[-_－—\s]?\d{7,8}([-_－—\s]\d{3,})?$/';
		$pattern = '/[\d\-\+]+/';
	//	str = "746 4831012 123";
		return preg_match($pattern, $tel);
	}
	
	
	/**
	 * 判断是否是邮箱地址
	 * @param string $email
	 */
	public static function isEmail($email) {
		//摘自php和MySQL开发
		$pattern = '/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]+$/';
		return preg_match($pattern, $email);
	}
	
	
	/**
	 * 判断是否是4-24位的半角英语字符
	 * @param string $passwd
	 */
	public static function isPasswd($passwd) {
		$pattern = '/^[\w\-]{4,24}$/';
		return preg_match($pattern, $passwd);
	}
	
	
	/**
	 * 获取当前时间，格式：2012-01-02 13:14:15
	 */
	public static function getCurrentTime() {
		return date('Y-m-d H:i:s');
	}
	
	
	/**
	 * 计算满岁
	 * @param birthday string, eg: 1970-01-01, 1970-1-1, 1970/01/01, 1970/1/1或可能日期后面跟有时间 
	 * @returns int
	 */
	public static function calcFullAge($birthday) {
		//验证生日格式是否合法
		if (empty($birthday) || $birthday == '0000-00-00') {
			return 0;
		}
		$pattern = '/^\d{4}([\/\-]\d{1,2}){2}/';
		if (!preg_match($pattern, $birthday)) {
			return 0;
		}
		//用空格把日期时间字符串分割成日期与时间，如'1970-01-01 10:02:01'分割成array('1970-01-01', '10:02:01')
		$birthdayArray = explode(' ', $birthday);
		$birthdayDate = $birthdayArray[0];
		if (strpos($birthdayDate, '/') !== false) {
			$birthdayDateArray = explode('/', $birthdayDate);
		} else if (strpos($birthdayDate, '-') !== false) {
			$birthdayDateArray = explode('-', $birthdayDate);
		}
		
		//计算年龄
		$fullAge = date('Y') - $birthdayDateArray[0];
		if (date('n') < intval($birthdayDateArray[1]) 
			|| (date('n') == intval($birthdayDateArray[1]) && date('j') < intval($birthdayDateArray[2]))) {
			$fullAge--;
		}
		return $fullAge;
	}
	
	/**
	 * 得到两个时间之间的差 $f_date-$l_date $type='SS'返回秒数/='DAY'返回天数
	 * Enter description here ...
	 * @param unknown_type $f_date
	 * @param unknown_type $l_date
	 * @param unknown_type $type
	 */
	public static function dateDiff($f_date, $l_date, $type='SS') {
		$t1 = strtotime($f_date);
		$t2 = strtotime($l_date);
		$ret=NULL;
		switch ($type) {
			case 'DAY':
			case 'day': $time=$t1-$t2;$ret=ceil($time/86400);break;
			default:$ret=$t1-$t2;break;
		}
		return $ret;
	}
	
	/**
	 * 对日期$s_date加$days天，并返回一个日期字符串
	 * @param unknown_type $s_date
	 * @param unknown_type $days
	 */
	public static function dateAddDay($s_date, $days) {
		if ($days >= 0) 
			$days = '+'.$days;
		return date('Y-m-d', strtotime($s_date.$days.'day'));
	}
	
	public static function isPositive($number) {
		if (empty($number)) return false;
		if ($number+0 <= 0) return false;
		return true;
	}
	
	public static function escapeHtml($value) {
	}
	
	
	/**
	 * 把数组用前导x，转换成字符串，
	 * 如：array(f1, f2, f3) => x.f1,x.f2,x.f3
	 * 主要用于数据库sql的拼接，注意：x是变量不是“x”
	 * @param array(string) $arr
	 * @param string $x
	 */
	public static function implodeArrByPrefixX($arr, $x) {
		$str = implode(",$x.", $arr);
		if (!empty($str)) {
			$str = "$x." . $str;
		}
		return $str;
	}
	
	
	
} //End: class UtilService