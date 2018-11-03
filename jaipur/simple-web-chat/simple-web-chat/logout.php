<?php
$name = (isset($_POST['name']))? $_POST['name'] : '';
$usnid = (isset($_POST['usnid']))? $_POST['usnid'] : '';
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$name = trim($name);
$usnid = preg_replace('/[^A-Za-z0-9]/', '', $usnid);
$usnid = trim($usnid);
$resp = '';
if($name != '' && $usnid != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
	$cus_obj = new CheckUserSession();
	$vf = $cus_obj->checkSession($name, $usnid);
	if(!$vf) { exit; }
	//
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.ClearUserSession.php');
	$cs_obj = new ClearUserSession();
	$resp = $cs_obj->clearSession($name,$usnid);
}
echo $resp; exit;
?>
