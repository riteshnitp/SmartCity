<?php
$cnm = (isset($_POST['c']))? $_POST['c'] : '';
$name = (isset($_POST['name']))? $_POST['name'] : '';
$usnid = (isset($_POST['usnid']))? $_POST['usnid'] : '';
$cnm = preg_replace('/[^A-Za-z0-9]/', '', $cnm);
$cnm = trim($cnm);
$return_val = 'error';
if($cnm != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
	$cus_obj = new CheckUserSession();
	$vf = $cus_obj->checkSession($name, $usnid);
	if(!$vf) { exit; }
	//
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.RejectRequest.php');
	$rr_obj = new RejectRequest();
	$return_val = $rr_obj->rejectCRequest($name, $cnm);
}
echo $return_val; exit;
?>
