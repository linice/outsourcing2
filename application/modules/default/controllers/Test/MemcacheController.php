<?php
include_once 'UsrService.php';
include_once 'BaseController.php';


class Test_MemcacheController extends BaseController
{
    /**
     * memcache: OOP
     */
    public function memcacheAction() {
    	try {
	    	$mc = new Memcache;
			$mc->connect('127.0.0.1', 11211) or die ("Could not connect");
			$version = $mc->getVersion();
			echo "Server's version: $version";
			echo '<br />';
			
			$stats = $mc->getStats();
			var_dump($stats);
			echo '<br />';
			
			
//			$tmp_object = new stdClass;
//			$tmp_object->str_attr = 'test';
//			$tmp_object->int_attr = 123;
//			
//			$mc->set('key', $tmp_object, 0, 10) or die ("Failed to save data at the server");
////			$mc->add('key2', $tmp_object, 0, 60) or die ("Failed to save data at the server");
////			$mc->replace('key2', $tmp_object, 0, 60) or die ("Failed to save data at the server");
//			echo "Store data in the cache (data will expire in 10 seconds)<br />";
//			//$get_result = $mc->get('key');
//			$get_result = $mc->get('key2');
//			echo "Data from the cache:<br />";
//			var_dump($get_result);
//			echo '<br />';
//			
//			$mc->delete('key2', 10);
//			echo '<br />';
//			var_dump($mc->get('key2')); //bool(false)
//			echo '<br />';

			$mc->set('key', 'value20120719', 0, 10);
			$result = $mc->get('key');
			var_dump($result);
			echo '<br />';
			
			$mc->delete('key', 0);
			$result2 = $mc->get('key');
			var_dump($result2);
			echo '<br />';
    	} catch (Exception $e) {
    		echo 'Exception: ' . $e->getMessage();
    	}
		exit('Success');
    }
    
    
    
    
	
    
	
	
	
	
} //End: class Test_MemcacheController