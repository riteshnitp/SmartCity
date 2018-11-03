<?php
include_once('common.php');
$name_val = (isset($_GET['name']))? $_GET['name'] : '';
$fl = (isset($_GET['fl']))? base64_decode($_GET['fl']) : '';
$name = trim(substr($name_val, 0, strpos($name_val, '|')));
$usnid = trim(str_replace($name.'|', '', $name_val));
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
if($name != '' && $usnid != '' && $fl != '') {
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
	$cus_obj = new CheckUserSession();
	$vf = $cus_obj->checkSession($name, $usnid);
	if(!$vf) { exit; }
	//
	$unm = '';
	if(strpos($fl,':-:') !== false) {
		$unm = substr($fl, 0, strpos($fl,':-:'));
	}
	// echo $unm;
	// echo '<br/>';
	$fl = str_replace(':-:', DIRECTORY_SEPARATOR, $fl);
	// $file = $site_path.'pub'.DIRECTORY_SEPARATOR . str_replace(':-:', DIRECTORY_SEPARATOR, $fl);
	$file = $fsds_obj->path('pub') . $fl;
	// echo '<br/>';
	// $file_url = $site_url.'pub/' . $fl;
	// echo '<br/>';
	// $pcf = $site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR . str_replace(':-:', DIRECTORY_SEPARATOR, $fl).'_-_'.$name.'.s';
	$pcf = $fsds_obj->path('tmpfs') . $fl.'_-_'.$name.'.s';
	// check for shared with group
	$flag = false;
	$udtls = $fsds_obj->get('users', $name.'.u');
	if(isset($udtls['grp']) && is_array($udtls['grp'])) {
		foreach ($udtls['grp'] as $key => $value) {
			$grpnm = $group_prefix.$value.$group_sep.$gkv;
			$flag = $fsds_obj->exists('tmpfs', $unm . DIRECTORY_SEPARATOR . $fl.'_-_'.$grpnm.'.s');
			if($flag) { break; }
		}
	}
	// echo '<br/>';
	if($name === $unm || file_exists($pcf) || $flag === true) {
		if(file_exists($file)) {
			// echo '<a href="'.$file_url.'">Click here to access file</a>';
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			// header("Content-type: application/x-download");
			header('Content-Disposition: attachment; filename='.basename($file));
			header("Content-Transfer-Encoding: Binary");
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
         	header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
		} else {
			echo 'Sorry file no more exists';
		}
	} else {
		echo 'Sorry, cant access requested file.';
	}
}
exit;
?>