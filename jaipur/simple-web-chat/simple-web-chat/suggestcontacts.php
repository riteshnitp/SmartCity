<?php
$name = (isset($_POST['name']))? $_POST['name'] : '';
$usnid = (isset($_POST['usnid']))? $_POST['usnid'] : '';
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$name = trim($name);
$op = array();
if($name != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
	$cus_obj = new CheckUserSession();
	$vf = $cus_obj->checkSession($name, $usnid);
	if(!$vf) { exit; }
	//
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.SuggestContacts.php');
	$co_obj = new SuggestContacts();
	$dtls = $co_obj->suggestedContacts($name, $vals);
}
echo json_encode($dtls); exit;
?>
