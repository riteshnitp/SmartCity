<?php
$vals = (isset($_POST['vals']))? $_POST['vals'] : '';
$name = (isset($_POST['unm']))? $_POST['unm'] : '';
$usnid = (isset($_POST['usnid']))? $_POST['usnid'] : '';
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$name = trim($name);
// $vals = file_get_contents('/var/www/html/local/t.txt');
$vals = json_decode($vals, 1);
// file_put_contents('/var/www/html/local/t.txt', json_encode($vals));
$op = array();
if(is_array($vals) && count($vals) > 0 && $name != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
	$cus_obj = new CheckUserSession();
	$vf = $cus_obj->checkSession($name, $usnid);
	if(!$vf) { exit; }
	//
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckContacts.php');
	$co_obj = new CheckContacts();
	$op = $co_obj->matchContacts($name, $vals);
}
// echo json_encode($op);
exit;
/*
{"1":{"id":"2","ims":[],"organizations":"[]","lastname":"","emails":"[\"jo@seph.com\",\"t2@users.com\"]","firstname":"Joseph","addresses":"[{\"id\":\"7\",\"zip\":null,\"street\":\"Xyz\",\"state\":null,\"pobox\":null,\"type\":\"1\",\"country\":null,\"city\":null}]","note":null,"phones":"[\"1023-456-789\",\"1013-445-789\"]"},"0":{"id":"1","ims":[],"organizations":"[]","lastname":"","emails":"[\"t1@users.com\"]","firstname":"T1","addresses":"[{\"id\":\"2\",\"zip\":null,\"street\":\"Abcd\",\"state\":null,\"pobox\":null,\"type\":\"1\",\"country\":null,\"city\":null}]","note":null,"phones":"[\"1 234-567-890\"]"}}
*/
?>
