<?php
$name = (isset($_POST['name']))? $_POST['name'] : '';;
$usnid = (isset($_POST['usnid']))? $_POST['usnid'] : '';
$ci = (isset($_POST['ci']))? trim($_POST['ci']) : '';
$typ = (isset($_POST['all']))? trim($_POST['all']) : '';
$gcid = (isset($_POST['gcid']))? trim($_POST['gcid']) : '';
$msg = $_POST['vMessage'];
$msg = trim($msg);
//
include_once('common.php');
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
$cus_obj = new CheckUserSession();
$vf = $cus_obj->checkSession($name, $usnid);
if(!$vf) { exit; }
//
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Reply.php');
// include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Process.php');
$rpl_obj = new Reply();
// $pr_obj = new Process();
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$ci = preg_replace('/[^A-Za-z0-9]/', '', $ci);
$gcid = preg_replace('/[^A-Za-z0-9:-_~]/', '', $gcid);
if($name != '' && ($ci != '' || $gcid != '')) {
	$return_val = $rpl_obj->sendMessage($name, $ci, $msg, $typ, $gcid);
	/*
	$args = array('site_path'=>$site_path,'site_uri'=>$site_uri,'class'=>'Reply', 'function'=>'sendMessage',
					'params' => array($name, $ci, $msg, $typ, $gcid)
				);
	$return_val = $pr_obj->genProcess($args);
	//
	if(isset($_FILES['files']['name']) && is_array($_FILES['files']['name']) && count(array_filter($_FILES['files']['name'])) > 0) {
		$flary = $_FILES;
		include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Reply.php');
		$rpl_obj = new Reply();
		for($l=0; $l < count($_FILES['files']['name']); $l++) {
			if($_FILES['files']['error'][$l] == 0) {
				$filnm = preg_replace('/[^A-Za-z0-9\._-]/', '', $_FILES['files']['name'][$l]);
				if($filnm != '') {
					/*
					$args = array('site_path'=>$site_path,'site_uri'=>$site_uri,'class'=>'Reply', 'function'=>'fileCopy',
									'params' => array($filnm, $_FILES['files']['tmp_name'][$l], $_FILES['files']['type'][$l], $name)
								);
					$txt = $pr_obj->genProcess($args);
					*
					$flary['files']['msg'][$l] = $rpl_obj->fileCopy($filnm, $_FILES['files']['tmp_name'][$l], $_FILES['files']['type'][$l], $name);
					
				}
			}
		}
		//
		if(isset($flary['files']['msg']) && is_array($flary['files']['msg']) && count(array_filter($flary['files']['msg'])) > 0) {
			$args = array('site_path'=>$site_path,'site_uri'=>$site_uri,'class'=>'Reply', 'function'=>'setFileFlag',
							'params' => array($name, $ci, $txt, $flary, $typ, $gcid)
						);
			$return_val = $pr_obj->genProcess($args);
		}
	}*/
}
echo '';
exit;
?>
