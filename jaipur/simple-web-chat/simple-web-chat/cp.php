<?php
$name = (isset($_POST['unm']))? $_POST['unm'] : '';
$email = (isset($_POST['eid']))? $_POST['eid'] : '';
if(is_string($name)) { $name = trim($name); } else { $name = ''; }
if(is_string($email)) { $email = trim($email); } else { $email = ''; }
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$name = trim($name);
$return_val = 'error';
if($name != '' && $email != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.ModifyPass.php');
	$mp_obj = new ModifyPass();
	$return_val = $mp_obj->modifyPasscode($name, $email);
}
echo $return_val; exit;
?>
