<?php
// session_start();
ob_start();
include_once('common.php');
//
$type = $_GET['type'];
$param = $_GET['param'];
$name = substr($param, 0, strpos($param, '|'));
$usnid = str_replace($name.'|', '', $param);
//
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
$cus_obj = new CheckUserSession();
$vf = $cus_obj->checkSession($name, $usnid);
if(!$vf) { exit; }
//
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Messages.php');
$msg_obj = new Messages();
$return_val = $msg_obj->fetchMessages($name, $type);
exit;
// need a cron that will run every certain min to insert messages from file entries into db and upon insert delete those files
?>