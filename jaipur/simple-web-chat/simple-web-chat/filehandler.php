<?php
include_once('common.php');
$name = (isset($_POST['name']))? $_POST['name'] : '';
$usnid = (isset($_POST['usnid']))? $_POST['usnid'] : '';
//
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
$cus_obj = new CheckUserSession();
$vf = $cus_obj->checkSession($name, $usnid);
if(!$vf) { exit; }
//
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Reply.php');
$rpl_obj = new Reply();
$return_val = $rpl_obj->fileCopy($_FILES['file']['name'], $_FILES['file']['tmp_name'], $_FILES['file']['type'], $name);
echo json_encode(array('txt' => $return_val)); exit;
?>