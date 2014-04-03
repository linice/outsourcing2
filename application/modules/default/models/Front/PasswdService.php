<?php
class PasswdService {
	public static function genPasswd($length = 8) {
	    // 密码字符集，可任意添加你需要的字符
	    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$lenStr = strlen($str);
	    $passwd = '';
	    for ($i = 0; $i < $length; $i++ ) {
	        // 这里提供两种字符获取方式
	        // 第一种是使用 substr 截取$chars中的任意一位字符；
	        // 第二种是取字符数组 $chars 的任意元素
	        // $passwd .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	        $passwd .= $str[mt_rand(0, $lenStr - 1)];
	    }
	    return $passwd;
	}
}