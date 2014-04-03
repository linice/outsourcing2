<?php
/**
 * 下载文件
 * @author Linice
 *
 */
class Download
{
	public static $MIMETypes = array(
			'ez' => 'application/andrew-inset', 
			'hqx' => 'application/mac-binhex40', 
			'cpt' => 'application/mac-compactpro', 
			'doc' => 'application/msword', 
			'docx' => 'application/msword', 
			'bin' => 'application/octet-stream', 
			'dms' => 'application/octet-stream', 
			'lha' => 'application/octet-stream', 
			'lzh' => 'application/octet-stream', 
			'exe' => 'application/octet-stream', 
			'class' => 'application/octet-stream', 
			'so' => 'application/octet-stream', 
			'dll' => 'application/octet-stream', 
			'oda' => 'application/oda', 
			'pdf' => 'application/pdf', 
			'ai' => 'application/postscrīpt', 
			'eps' => 'application/postscrīpt', 
			'ps' => 'application/postscrīpt', 
			'smi' => 'application/smil', 
			'smil' => 'application/smil', 
			'mif' => 'application/vnd.mif', 
			'xls' => 'application/vnd.ms-excel', 
			'xlsx' => 'application/vnd.ms-excel', 
			'ppt' => 'application/vnd.ms-powerpoint', 
			'wbxml' => 'application/vnd.wap.wbxml', 
			'wmlc' => 'application/vnd.wap.wmlc', 
			'wmlsc' => 'application/vnd.wap.wmlscrīptc', 
			'bcpio' => 'application/x-bcpio', 
			'vcd' => 'application/x-cdlink', 
			'pgn' => 'application/x-chess-pgn', 
			'cpio' => 'application/x-cpio', 
			'csh' => 'application/x-csh', 
			'dcr' => 'application/x-director', 
			'dir' => 'application/x-director', 
			'dxr' => 'application/x-director', 
			'dvi' => 'application/x-dvi', 
			'spl' => 'application/x-futuresplash', 
			'gtar' => 'application/x-gtar', 
			'hdf' => 'application/x-hdf', 
			'js' => 'application/x-javascrīpt', 
			'skp' => 'application/x-koan', 
			'skd' => 'application/x-koan', 
			'skt' => 'application/x-koan', 
			'skm' => 'application/x-koan', 
			'latex' => 'application/x-latex', 
			'nc' => 'application/x-netcdf', 
			'cdf' => 'application/x-netcdf', 
			'sh' => 'application/x-sh', 
			'shar' => 'application/x-shar', 
			'swf' => 'application/x-shockwave-flash', 
			'sit' => 'application/x-stuffit', 
			'sv4cpio' => 'application/x-sv4cpio', 
			'sv4crc' => 'application/x-sv4crc', 
			'tar' => 'application/x-tar', 
			'tcl' => 'application/x-tcl', 
			'tex' => 'application/x-tex', 
			'texinfo' => 'application/x-texinfo', 
			'texi' => 'application/x-texinfo', 
			't' => 'application/x-troff', 
			'tr' => 'application/x-troff', 
			'roff' => 'application/x-troff', 
			'man' => 'application/x-troff-man', 
			'me' => 'application/x-troff-me', 
			'ms' => 'application/x-troff-ms', 
			'ustar' => 'application/x-ustar', 
			'src' => 'application/x-wais-source', 
			'xhtml' => 'application/xhtml+xml', 
			'xht' => 'application/xhtml+xml', 
			'zip' => 'application/zip', 
			'au' => 'audio/basic', 
			'snd' => 'audio/basic', 
			'mid' => 'audio/midi', 
			'midi' => 'audio/midi', 
			'kar' => 'audio/midi', 
			'mpga' => 'audio/mpeg', 
			'mp2' => 'audio/mpeg', 
			'mp3' => 'audio/mpeg',
			'wma' => 'audio/mpeg', 
			'aif' => 'audio/x-aiff', 
			'aiff' => 'audio/x-aiff', 
			'aifc' => 'audio/x-aiff', 
			'm3u' => 'audio/x-mpegurl', 
			'ram' => 'audio/x-pn-realaudio', 
			'rm' => 'audio/x-pn-realaudio', 
			'rpm' => 'audio/x-pn-realaudio-plugin', 
			'ra' => 'audio/x-realaudio', 
			'wav' => 'audio/x-wav', 
			'pdb' => 'chemical/x-pdb', 
			'xyz' => 'chemical/x-xyz', 
			'bmp' => 'image/bmp', 
			'gif' => 'image/gif', 
			'ief' => 'image/ief', 
			'jpeg' => 'image/jpeg', 
			'jpg' => 'image/jpeg', 
			'jpe' => 'image/jpeg', 
			'png' => 'image/png', 
			'tiff' => 'image/tiff', 
			'tif' => 'image/tiff', 
			'djvu' => 'image/vnd.djvu', 
			'djv' => 'image/vnd.djvu', 
			'wbmp' => 'image/vnd.wap.wbmp', 
			'ras' => 'image/x-cmu-raster', 
			'pnm' => 'image/x-portable-anymap', 
			'pbm' => 'image/x-portable-bitmap', 
			'pgm' => 'image/x-portable-graymap', 
			'ppm' => 'image/x-portable-pixmap', 
			'rgb' => 'image/x-rgb', 
			'xbm' => 'image/x-xbitmap', 
			'xpm' => 'image/x-xpixmap', 
			'xwd' => 'image/x-xwindowdump', 
			'igs' => 'model/iges', 
			'iges' => 'model/iges', 
			'msh' => 'model/mesh', 
			'mesh' => 'model/mesh', 
			'silo' => 'model/mesh', 
			'wrl' => 'model/vrml', 
			'vrml' => 'model/vrml', 
			'css' => 'text/css', 
			'html' => 'text/html', 
			'htm' => 'text/html', 
			'asc' => 'text/plain', 
			'txt' => 'text/plain', 
			'rtx' => 'text/richtext', 
			'rtf' => 'text/rtf', 
			'sgml' => 'text/sgml', 
			'sgm' => 'text/sgml', 
			'tsv' => 'text/tab-separated-values', 
			'wml' => 'text/vnd.wap.wml', 
			'wmls' => 'text/vnd.wap.wmlscrīpt', 
			'etx' => 'text/x-setext', 
			'xsl' => 'text/xml', 
			'xml' => 'text/xml', 
			'mpeg' => 'video/mpeg', 
			'mpg' => 'video/mpeg', 
			'mpe' => 'video/mpeg', 
			'qt' => 'video/quicktime', 
			'mov' => 'video/quicktime', 
			'mxu' => 'video/vnd.mpegurl', 
			'avi' => 'video/x-msvideo', 
			'movie' => 'video/x-sgi-movie', 
			'wmv' => 'application/x-mplayer2',
			'ice' => 'x-conference/x-cooltalk'
		);
	
	
	/** 
  	* 获取浏览器的类型
  	*/  
	public static function getUserAgentBrowserType() {
		$userAgent = strtolower($_SERVER[HTTP_USER_AGENT]);
		if (strpos($userAgent, 'msie') !== false) {
			return 'msie';
		} else if (strpos($userAgent, 'firefox') !== false) {
			return 'firefox';
		} else if (strpos($userAgent, 'applewebkit') !== false) {
			return 'applewebkit';
		} else if (strpos($userAgent, 'opera') !== false) {
			return 'opera';
		} else if (strpos($userAgent, 'safari') !== false) {
			return 'safari';
		} else {
			return 'other';
		}
	}
	
	
	/**
	 * 下载文件 
	 * @param string $filepath: 文件路径
	 * @param string $downname: 下载文件名
	 */
	public static function dl($filepath, $downname)
	{
		if(empty($filepath) || !file_exists($filepath))
		{
			return false;
		}
		if(empty($downname)) {
			$downname = basename($filepath);	
		}
		// 文件扩展名 
		$pathinfo = pathinfo($filepath);
		$fileExt = $pathinfo['extension'];
		// 文件类型 
		$fileType = self::$MIMETypes[$fileExt] ? self::$MIMETypes[$fileExt] : 'application/octet-stream';
		// 读取文件
		//简述: ob_end_clean() 清空并关闭输出缓冲, 详见手册 
		//说明: 关闭输出缓冲, 使文件片段内容读取至内存后即被送出, 减少资源消耗 
//		ob_end_clean();
		//HTTP头信息: 指示客户机可以接收生存期不大于指定时间（秒）的响应 
//		header('Cache-control: max-age=31536000'); //31536000s = 1年
		//HTTP头信息: 缓存文件过期时间(格林威治标准时)
//		header('Expires: ' . gmdate('D, d M Y H:i:s', time()+31536000) . ' GMT'); 
		//HTTP头信息: 文件在服务期端最后被修改的时间 
		//Cache-control,Expires,Last-Modified 都是控制浏览器缓存的头信息 
		//在一些访问量巨大的门户, 合理的设置缓存能够避免过多的服务器请求, 一定程度下缓解服务器的压力 
//		header('Last-Modified: ' . gmdate('D, d M Y H:i:s' , filemtime($filepath) . ' GMT')); 
		//HTTP头信息: 文档的编码(Encode)方法, 因为附件请求的文件多样化, 改变编码方式有可能损坏文件, 故为none 
//		header('Content-Encoding: none');
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		//HTTP头信息: 告诉浏览器当前请求的文件类型.
		//1.始终指定为application/octet-stream, 就代表文件是二进制流, 始终提示下载. 
		//2.指定对应的类型, 如请求的是mp3文件, 对应的MIME类型是audio/mpeg, IE就会自动启动Windows Media Player进行播放. 
		header('Content-type: ' . $fileType); 
		//HTTP头信息: 如果为attachment, 则告诉浏览器, 在访问时弹出”文件下载”对话框, 并指定保存时文件的默认名称(可以与服务器的文件名不同) 
		//如果要让浏览器直接显示内容, 则要指定为inline, 如图片, 文本 
		//防止中文乱码，空格也能正确编码，如果用urlencode，则空格会转化为加号（+）。
//		$userAgent = self::getUserAgentBrowserType();
//		$encoded_downname = urlencode($downname);
//		$encoded_downname = str_replace("+", "%20", $encoded_downname);
//		if ($userAgent == 'msie' || $userAgent == 'safari' || $userAgent == 'applewebkit') {
//			header('Content-Disposition: attachment; filename="' . $encoded_downname . '"'); 
//		} else if ($userAgent == 'firefox' || $userAgent == 'opera') {
//			header('Content-Disposition: attachment; filename="' . $downname . '"'); 
//		} else {
//			header('Content-Disposition: attachment; filename="' . $encoded_downname . '"'); 
//		}
		header('Content-Disposition: attachment; filename="' . $downname . '"'); 
		//告诉浏览器，这是二进制文件
		header("Content-Transfer-Encoding: binary");
		//HTTP头信息: 告诉浏览器文件长度 
		//(IE下载文件的时候不是有文件大小信息么?) 
		header('Content-Length: ' . filesize($filepath)); 
//		// 打开文件(二进制只读模式)
//		$fp = fopen($filepath, 'rb'); 
//		// 输出文件
//		fpassthru($fp); 
//		// 关闭文件
//		fclose($fp);
		@readfile($filepath);
		//让Xsendfile发送文件：这个不能用，可能还需要Apache相关的配置
//    	header("X-Sendfile: $filepath");
	}
} //End: class Download




/**
 * 下载图片文件
 * @author Linice
 *
 */
//class Download {
//	/**
//	 * 下载文件
//	 * @param string $filepath
//	 * @param string $downname
//	 */
//	public static function dl($filepath, $downname) {
//		header("Content-type: application/octet-stream");
//		//处理中文文件名
//		$ua = $_SERVER["HTTP_USER_AGENT"];
//		$encoded_filename = urlencode($downname);
//		$encoded_filename = str_replace("+", "%20", $encoded_filename);
//		if (preg_match("/MSIE/", $ua)) {
//			header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
//		} else if (preg_match("/Firefox/", $ua)) {
//			header("Content-Disposition: attachment; filename*=\"utf8''" . $downname . '"');
//		} else {
//			header('Content-Disposition: attachment; filename="' . $downname . '"');
//		}
//		header("Content-Transfer-Encoding: binary");
//		header("Content-Length: ". filesize($filepath));
//		readfile($filepath);
//	}
//	
//	
//} //End: class Download




/**
 * 下载zip压缩包
 */
//class Download {
//	public static function dl($filepath, $downname) {
//		header("Cache-Control: public");   
//		header("Content-Description: File Transfer");   
//		header('Content-disposition: attachment; filename='.$downname); //文件名  
//		header("Content-Type: application/zip"); //zip格式的  
//		header("Content-Transfer-Encoding: binary");    //告诉浏览器，这是二进制文件   
//		header('Content-Length: '. filesize($filepath));    //告诉浏览器，文件大小  
//		@readfile($filepath);
//	}
//} //End: class Download