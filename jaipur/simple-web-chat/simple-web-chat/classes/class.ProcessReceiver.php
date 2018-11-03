<?php
/**
 * This class contains logic for receiving process initialization info and calling actual process
*/
class ProcessReceiver
{
	/**
	 * Generate new process
	 * process receiver class will call respective class method
	 * @param args process input parameters array('site_path', 'site_url', 'class_name', 'function_name')
	*/
	function callProcess($args)
	{
        // $doc_root = $args[1];
		$param = file_get_contents($args[1]);
		$params = array();
		if($param != '') {
			$params = @ json_decode($param, 1);
		}
		if(is_array($params)) {
			if(isset($params['site_path']) && $params['site_path'] != '' && isset($params['class']) && $params['class'] != '' && isset($params['function']) && $params['function'] != '') {
				include_once($params['site_path'].'classes'.DIRECTORY_SEPARATOR.'class.'.$params['class'].'.php');
				$obj = new $params['class']();
				call_user_func_array(array($obj, $params['function']), $params['params']);
			}
		}
		@ unlink($args[1]);
	}

}
//
date_default_timezone_set('UTC');
/*
$i=0;
while(1) {
	$i++;
	if($i >= 500000) {
		break;
	}
	// file_put_contents('/var/www/html/sc/swc/tmp/ts.t', date('YmdHis'));
	file_put_contents('/var/www/html/ts.t', date('YmdHis'));
}
*/
include dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'common.php';
$prObj = new ProcessReceiver();
$prObj->callProcess($argv);
?>