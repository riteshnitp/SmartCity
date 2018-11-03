<?php
$name = (isset($_POST['unm']))? $_POST['unm'] : '';
$email = (isset($_POST['eid']))? $_POST['eid'] : '';
$pass = (isset($_POST['pass']))? $_POST['pass'] : '';
$cpcode = (isset($_POST['cp']))? $_POST['cp'] : '';
$usnid = (isset($_POST['usn']))? $_POST['usn'] : '';
$phno = (isset($_POST['phno']))? $_POST['phno'] : '';
if(is_string($name)) { $name = trim($name); } else { $name = ''; }
if(is_string($email)) { $email = trim($email); } else { $email = ''; }
if(is_string($pass)) { $pass = trim($pass); } else { $pass = ''; }
if(is_string($cpcode)) { $cpcode = trim($cpcode); } else { $cpcode = ''; }
if(is_string($usnid)) { $usnid = trim($usnid); } else { $usnid = ''; }
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$name = trim($name);
$usnid = preg_replace('/[^A-Za-z0-9]/', '', $usnid);
$usnid = trim($usnid);
$phno = preg_replace('/[^0-9+-]/', '', $phno);
$phno = trim($phno);
$return_val = 'error';
if($name != '' && (($email != '' && $pass != '') || $usnid != '')) {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserIdentity.php');
	$cui_obj = new CheckUserIdentity();
	$return_val = $cui_obj->checkIdentity($name, $email, $pass, $cpcode, $usnid, $phno);
}
echo $return_val; exit;
?>
