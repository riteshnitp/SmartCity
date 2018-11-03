<?php
$gnm = (isset($_POST['gi']))? $_POST['gi'] : '';
$gid = (isset($_POST['gid']))? $_POST['gid'] : '';
$name = (isset($_POST['name']))? $_POST['name'] : '';
$usnid = (isset($_POST['usnid']))? $_POST['usnid'] : '';
$gnm = preg_replace('/[^A-Za-z0-9]/', '', $gnm);
$gnm = trim($gnm);
$return_val = 'error';
if($gnm != '' && $gid != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
	$cus_obj = new CheckUserSession();
	$vf = $cus_obj->checkSession($name, $usnid);
	if(!$vf) { exit; }
	//
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.InitGroupChat.php');
	$igc_obj = new InitGroupChat();
	$return_val = $igc_obj->initGChat($name, $gid, $gnm);
}
echo $return_val; exit;
?>
