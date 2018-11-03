<?php
// Notes:
// once data inserted into db del all files in tmp paths i.e. genpath & pripath if file name exists in db with unique message id
// also make sure to set $manage_tmp_files = false; in common.php file else there won't be any file left to read
include_once('common.php');
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Process.php');
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.FSDS.php');
$fsds_obj = new FSDS();
// paths to check for messages
// $genpath = $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR;
// $pripath = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR;
$genpath = $fsds_obj->path('genmsg');
$pripath = $fsds_obj->path('usermsg');
// $usrpaths = array();
/*if(file_exists($pripath)) {
	$usrpaths = scandir($pripath);
}*/
$usrpaths = $fsds_obj->listing('usermsg', '');
$msgids = array();
// 
$msg = array();
$gmcount = 0;
$pmcount = 0;
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.LoadMessagesToDB.php');
if(is_array($usrpaths) && count($usrpaths) > 0) {
	foreach ($usrpaths as $path) {
		if($path != '.' && $path != '..') { 	// && file_exists(filename)
			// $files = scandir($pripath.$path);
			$files = $fsds_obj->listing('usermsg', $path);
			foreach ($files as $file) {
				if($file != '.' && $file != '..' && $fsds_obj->exists('usermsg', $path.DIRECTORY_SEPARATOR.$file) && strpos($file, '_') === false) {
					$id = str_replace('.mt', '', $file);
					// $umsg = file_get_contents($pripath.$path.DIRECTORY_SEPARATOR.$file);
					$umsg = $fsds_obj->get('usermsg', $path.DIRECTORY_SEPARATOR.$file);
					// for each message strip tags and read text till '(' which is sender and replace sender from path to get receiver
					$tmptxt = substr($umsg, 0, strpos($umsg, '('));
					$sender = trim(strip_tags($tmptxt));
					$receiver = str_replace(array($sender,$name_sep), '', $path);
					// identify if receiver is a group or not and apply logic if required accordingly
					if(strpos($receiver, $group_prefix) === 0) {
						// receiver is here a group
						// apply your code here as per your requirement and database, if required
					}
					$msg[$path][] = array($id, $sender, $receiver, $umsg);
					$msgids[] = array($id, $pripath.$path.DIRECTORY_SEPARATOR.$file);
					if($pmcount > 0 && $pmcount % 1000 == 0) {
						//
						// your code to insert into your database
						//
						// clear msg array
						$msg = array();
						// $pmcount = 0;
					}
					$pmcount++;
				}
			}
		}
	}
}
//
if(count($msg) > 0) {
	//
	// your code to insert into your database
	//
}
//
$genfiles = $fsds_obj->listing('genmsg', '');
// if(file_exists($genpath)) {
// $genfiles = scandir($genpath);
if(is_array($genfiles) && count($genfiles) > 0) {
	foreach ($genfiles as $file) {
		if($file != '.' && $file != '..' && is_file($genpath.$file) && strpos($file, '_') === false) {
			// receiver here is not a specific user as its a broadcast message
			// apply logic as per requirement
			//
			// your code here
			//
		}
	}
}
// }
// del tmp files
$count = $pos = 0;
$fdel = array();
$msgidary = array();
$midary = array(); 	// will contain all msg_id that are inserted into db
$pr_obj = new Process();
foreach($msgids as $key => $val) {
	$mids .= $val[1].',';
	if($count > 0 && $count % 100 == 0) {
		$mids = trim($mids,',');
		// query database and get msg_id from db table where msg_id in ($mids);
		// $midary[$rec['msg_id']] = $rec['id']; 	// inside a loop for reading records fetched from db 
		//
		// your code here
		//
		if(is_array($midary)) {
			$msgidary = array_slice($msgids, $pos, $count);
			foreach($msgidary as $k => $v) {
				if(isset($midary[$v[0]])) { 	// this condition is because of this code above: $midary[$rec['msg_id']] = $rec['id'];
					$fdel[] = $v[1];
				}
			}
			$msgidary = array();
			if(count($fdel) > 0) {
				$args = array('site_path'=>$site_path, 'class'=>'Unlink', 'function'=>'removeFile', 'params' => array($fdel, 1));
				$prs = $pr_obj->genProcess($args);
			}
			$pos = $count;
		}
	}
	$count++;
}
//
if($count > 0) {
	$mids = trim($mids,',');
	// query database and get msg_id from db table where msg_id in ($mids);
	// $midary[$rec['msg_id']] = $rec['id']; 	// inside a loop for reading records fetched from db 
	//
	// your code here
	//
	if(is_array($midary)) {
		$msgidary = array_slice($msgids, $pos, $count);
		foreach($msgidary as $k => $v) {
			if(isset($midary[$v[0]])) { 	// this condition is because of this code above: $midary[$rec['msg_id']] = $rec['id'];
				$fdel[] = $v[1];
			}
		}
		$msgidary = array();
		if(count($fdel) > 0) {
			$args = array('site_path'=>$site_path, 'class'=>'Unlink', 'function'=>'removeFile', 'params' => array($fdel, 1));
			$prs = $pr_obj->genProcess($args);
		}
		$pos = $count;
	}
}
//
?>