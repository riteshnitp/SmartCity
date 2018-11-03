<?php
$name = (isset($_POST['name']))? $_POST['name'] : '';
$usnid = (isset($_POST['usnid']))? $_POST['usnid'] : '';
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$name = trim($name);
$udtls = array();
if($name != '' && $usnid != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
	$cus_obj = new CheckUserSession();
	$vf = $cus_obj->checkSession($name, $usnid);
	if(!$vf) { exit; }
	//
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.UserContacts.php');
	$uc_obj = new UserContacts();
	$udtls = $uc_obj->getUserGroupsNContacts($name);
}
echo json_encode($udtls); exit;
?>
