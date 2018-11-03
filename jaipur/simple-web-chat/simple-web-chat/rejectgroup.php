<?php
$grpnm = (isset($_POST['grpnm']))? $_POST['grpnm'] : '';
$name = (isset($_POST['name']))? $_POST['name'] : '';
$usnid = (isset($_POST['usnid']))? $_POST['usnid'] : '';
$grpnm = preg_replace('/[^A-Za-z0-9]/', '', $grpnm);
$grpnm = trim($grpnm);
$return_val = 'error';
if($grpnm != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
	$cus_obj = new CheckUserSession();
	$vf = $cus_obj->checkSession($name, $usnid);
	if(!$vf) { exit; }
	//
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.RemoveContacts.php');
	$rcs_obj = new RemoveContacts();
	$return_val = $rcs_obj->removeGroup($name, $grpnm);
}
echo $return_val; exit;
?>
