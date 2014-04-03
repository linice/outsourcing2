<?php
	header("Cache-Control:no-cache,must-revalidate"); 
	$handle = popen('/app/os2/svnup.sh', 'r');  
	$read = stream_get_contents($handle); //需要 PHP5 或更高版本 
	echo '<pre>';
	printf($read);
	echo '</pre>';
	pclose($handle);
	exit;