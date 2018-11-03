<?php
include_once('common.php');
$name = "t1";
$ci = "dev";
$typ = (isset($_GET['all']))? trim($_GET['all']) : '';
$gcid = (isset($_GET['gcid']))? trim($_GET['gcid']) : '';
$msg = "hello";
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Reply.php');
$rpl_obj = new Reply();
// include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Process.php');
// $pr_obj = new Process();
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$ci = preg_replace('/[^A-Za-z0-9]/', '', $ci);
$gcid = preg_replace('/[^A-Za-z0-9:-_~]/', '', $gcid);
if($name != '' && ($ci != '' || $gcid != '')) {
	$msg .= ' '.date('Y-m-d H:i:s');
	// file_put_contents('/var/www/html/tmp/t.t','');
	$return_val = $rpl_obj->sendMessage($name, $ci, $msg, $typ, $gcid);
	/*
	$args = array('site_path'=>$site_path,'site_uri'=>$site_uri,'class'=>'Reply', 'function'=>'sendMessage',
					'params' => array($name, $ci, $msg, $typ, $gcid)
				);
	// print_r($args); exit;
	$return_val = $pr_obj->genProcess($args);
	*/
}
echo '';
exit;
// http://127.0.0.1:90/sc/swc/lt.php, (https://rtcamp.com/tutorials/linux/sysctl-conf/)
?>
