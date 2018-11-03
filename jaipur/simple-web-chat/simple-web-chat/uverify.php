<?php
$name = (isset($_GET['name']))? $_GET['name'] : '';
$code = (isset($_GET['code']))? $_GET['code'] : '';
if(is_string($code)) { $code = trim($code); } else { $code = ''; }
if(is_string($name)) { $name = trim($name); } else { $name = ''; }
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$name = trim($name);
$return_val = 'Error, <a href="'.$site_url.'sc.php">Back To App</a>';
if($code != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.UserVerification.php');
	$uv_obj = new UserVerification();
	$return_val = $uv_obj->verifyUser($name, $code);
}
echo $return_val; exit;
?>