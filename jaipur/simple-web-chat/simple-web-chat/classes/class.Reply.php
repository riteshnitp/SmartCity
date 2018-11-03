<?php
// include required class
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Process.php');
//
/**
 * This class contains logic to send / reply message
*/
class Reply
{
	/**
	 * Send new messages
	 * @param name name of user
	 * @param ci contact identifier
	 * @param msg text message
	 * @param typ broadcast or specific contacts or individual contact / group or all contacts
	*/
	function sendMessage($name, $ci, $msg, $typ, $gcid='')
	{
		global $site_path, $smileys, $group_prefix, $name_sep, $group_sep, $fsds_obj;
		// file_put_contents('/var/www/html/sc/swc/tmp/ts.t', json_encode(array($name, $ci, $msg, $typ, $gcid)));
		// $return_val = 'error';
		$utc_time = gmdate('Y-m-d H:i:s');
		$date = gmdate('Y-m-d h:i:s a', strtotime($utc_time));
		$flnm = "";
		$cids = array();
		$sfls = array();
		$crnms = array();
		$hmsg = '';
		// $uflnm = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		$uresnm = $name.'.u';
		$pr_obj = new Process();
		// if(is_file($uflnm) && file_exists($uflnm)) {
		if($fsds_obj->exists('users', $uresnm)) {
			$ufn = uniqid('', true);
			// $udtl = file_get_contents($uflnm);
			$udtl = $fsds_obj->get('users', $uresnm);
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
			}
			if(!is_array($udtls)) { $udtls = array(); }
			if(!isset($udtls['con'])) { $udtls['con'] = array(); }
			if(!isset($udtls['grp'])) { $udtls['grp'] = array(); }
			//
			if($ci != '') {
				if(strpos($ci, ',') !== false) {
					$cids = @ explode(',', $cids);
					$cids = array_values(array_unique(array_filter($cids)));
				} else {
					$cids[] = $ci;
				}
			}
			if($typ == 'public') {
				// $sfls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR;
				$sfls[] = $fsds_obj->path('genmsg');
				$crnms[] = 'General';
			} else if($typ == 'all') {
				//
				$ufls = array();
				/*if(file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR)) {
					$ufls = scandir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR);
				} else {
					@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR, 0774, true);
				}*/
				if($fsds_obj->dirExists('usermap', $name)) {
					$ufls = $fsds_obj->listing('usermap', $name);
				} else {
					$fsds_obj->mkpath('usermap', $name, 0774, true);
				}
				if(is_array($ufls)) {
					foreach ($ufls as $key => $val) {
						if(in_array($val, array('.','..'))) {
							unset($ufls[$key]);
						} else {
							if(in_array($val, $udtls['con'])) {
								$fldnm = (strcasecmp($name, $val) > 0)? $val.$name_sep.$name : $name.$name_sep.$val;
								/*if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
									@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
									if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
										@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
									}
									$ufls[$key] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR;
									// $crnms[] = $fldnm;
								}*/
								if(! $fsds_obj->dirExists('usermap', $name.DIRECTORY_SEPARATOR.$fldnm)) {
									$fsds_obj->mkpath('usermap', $name.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
									if(! $fsds_obj->dirExists('usermsg', $fldnm)) {
										$fsds_obj->mkpath('usermsg', $fldnm, 0774, true);
									}
									$ufls[$key] = $fsds_obj->path('usermsg').$fldnm.DIRECTORY_SEPARATOR;
									// $crnms[] = $fldnm;
								}
								//
							}
						}
					}
				}
				//
				$sfls[] = $fsds_obj->path('genmsg'); 	// $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR;
				if(count($ufls) > 0) { $sfls = array_merge($sfls, $ufls); }
				$crnms[] = 'General';
			} else if($typ == 'contacts') {
				$ufls = array();
				foreach ($cids as $key => $val) {
					if(in_array($val, $udtls['con'])) {
						$fldnm = (strcasecmp($name, $val) > 0)? $val.$name_sep.$name : $name.$name_sep.$val;
						/*if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
							@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
							//
							if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
								@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
							}
							$ufls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR;
							$crnms[] = $val;
						}*/
						if(! $fsds_obj->dirExists('usermap', $name.DIRECTORY_SEPARATOR.$fldnm)) {
							$fsds_obj->mkpath('usermap', $name.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
							//
							if(! $fsds_obj->dirExists('usermsg', $fldnm)) {
								$fsds_obj->mkpath('usermsg', $fldnm, 0774, true);
							}
							$ufls[] = $fsds_obj->path('usermsg').$fldnm.DIRECTORY_SEPARATOR;
							$crnms[] = $val;
						}
						//
					}
				}
				if(count($ufls) > 0) { $sfls = array_merge($sfls, $ufls); }
			} else if(trim($ci) != '' && trim(strtolower($ci)) != 'general') {
				$gkv = '';
				$fldnm = '';
				if(in_array($ci, $udtls['con'])) {
					$fldnm = (strcasecmp($name, $ci) > 0)? $ci.$name_sep.$name : $name.$name_sep.$ci;
				// } else if(in_array($ci, $udtls['grp'])) {
				} else if(trim($gcid) != '' && isset($udtls['grp'][$gcid]) && $udtls['grp'][$gcid] = $ci) {
					$gkv = $gcid; 	// array_search($ci, $udtls['grp']);
					$fldnm = $group_prefix.$ci.$group_sep.$gkv;
				}
				if($fldnm != '') {
					/*if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
						@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
						//
						if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
							@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
						}
					}*/
					if(! $fsds_obj->dirExists('usermap', $name.DIRECTORY_SEPARATOR.$fldnm)) {
						$fsds_obj->mkpath('usermap', $name.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
						//
						if(! $fsds_obj->dirExists('usermsg', $fldnm)) {
							$fsds_obj->mkpath('usermsg', $fldnm, 0774, true);
						}
					}
					// $sfls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR;
					$sfls[] = $fsds_obj->path('usermsg').$fldnm.DIRECTORY_SEPARATOR;
					if($gkv != '') {
						$crnms[] = $fldnm;
					} else {
						$crnms[] = $ci;
					}
				}
			} else {
				$sfls[] = $fsds_obj->mkpath('genmsg'); 	// $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR;
				$crnms[] = 'General';
			}
			$sfls = array_values(array_unique($sfls));
			$crnms = array_values(array_unique($crnms));
			//
			if(trim($msg) != '') {
				// $msg = $name.': '.$msg;
				$sm_vals = array_values($smileys);
				$sm_keys = array_keys($smileys);
				$sm_keys = array_map(function($val) { return $val = '<img title="'.(substr($val,0,strpos($val,'.'))).'" src="images/smileys/'.$val.'" />'; }, $sm_keys);
				$msg = str_replace($sm_vals, $sm_keys, $msg);
				//
				$cust_imgs = $fsds_obj->listing('img', 'custom'); 	// scandir($site_path.'images/custom/');
				$custom_ikeys = $custom_ivals = array();
				foreach($cust_imgs as $k => $v) {
					if(!in_array($v, array('.','..','chompy.gif'))) {
						if(strpos($v,'.') !== false) {
							$vl = substr($v,0, strpos($v,'.'));
						}
						$custom_ikeys[] = "[($vl)]";
						$custom_ivals[] = '<img class="smileys" src="images/custom/'.$v.'" title="'.$vl.'" alt="[('.$vl.')]" style="background:#a0a0a0;" />';
					}
				}
				$msg = str_replace($custom_ikeys, $custom_ivals, $msg);
				//
				$msg = str_ireplace(array("<br />\n","<br />\r","<br />\r\n", "`", "<script", "</script", "</ script", "<iframe", '<img src="" />'), array("<br />","<br />","<br />", "'", "&lt;script", "&lt;/script", "&lt;/script", "&lt;iframe",''), nl2br($msg));
				// $hmsg = $msg." <i style='float:right;'>(".$date.")</i><hr style='border-style:dashed;' />";
				if(is_array($_FILES) && isset($_FILES['files']['files']) && count(array_filter($_FILES['files']['name'])) > 0) {
					// $msg = $msg." <i style='float:right;'>(".$date.")</i><hr style='border-style:dashed;' />";
				} else {
					// $msg = $msg." <i style='float:right;'>(".$date.")</i><br/>";
				}
				$msg = '<div class="msg"><b>'.$name.'</b> '.' <i title="UTC-Time:'.$utc_time.'">('.$date.')</i> '.'<div>'.$msg.'</div></div>';
				$hmsg = $msg; 	// . "<hr style='border-style:dashed;' />";
				foreach ($sfls as $key => $value) {
					file_put_contents($value.$ufn.'.mt', $msg);
					@ chmod($value.$ufn.'.mt', 0774);
				}
			}
			// $msg = $hmsg = "";
			$msg = "";
			if(isset($_FILES['files']['name']) && is_array($_FILES['files']['name']) && count(array_filter($_FILES['files']['name'])) > 0) {
				for($l=0; $l < count($_FILES['files']['name']); $l++) {
					if($_FILES['files']['error'][$l] == 0) {
						$filnm = preg_replace('/[^A-Za-z0-9\._-]/', '', $_FILES['files']['name'][$l]);
						if($filnm != '') {
							$fl = $this->fileCopy($filnm, $_FILES['files']['tmp_name'][$l], $_FILES['files']['type'][$l], $name);
							//$fls = array_merge($fls, array($fl));
							// if($msg == "") { $msg  = $hmsg = $name.': '.'File(s) : '; }
							// $msg = $fl . "<i style='float:right;'>(".$date.")</i>" . (($l == (count($_FILES['files']['name'])-1))? "<br/>" : "<hr style='border-style:dashed;' />");
							// $hmsg = $fl . "<i style='float:right;'>(".$date.")</i>" . "<hr style='border-style:dashed;' />";
							if($fl != '') {
								if($msg == "") { $msg  = 'File(s) : '; } 	// $hmsg =
								$msg .= "<br/>".$fl.' ';
								// $hmsg .= "<br/>".$fl.' ';
								if(is_array($crnms) && count($crnms) > 0) {
									foreach ($crnms as $k => $v) {
										// mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR, 0774, true);
										// chmod($site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name, 0774);
										$fsds_obj->mkpath('tmpfs', $name, 0774, true);
										// $rmflnm = $site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$filnm.'_-_*.s';
										// @ array_map('unlink', glob($rmflnm));
										// @ unlink($site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$filnm.'_-_General.s');
										// @ file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$filnm.'_-_'.$v.'.s', '');
										// @ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$filnm.'_-_'.$v.'.s', 0774);
										$fsds_obj->put('tmpfs', $name.DIRECTORY_SEPARATOR.$filnm.'_-_'.$v.'.s', '');
										$fsds_obj->chperm('tmpfs', $name.DIRECTORY_SEPARATOR.$filnm.'_-_'.$v.'.s', 0774);
									}
								}
							}
						}
					}
				}
				//
				if(trim($msg) != '') {
					// $ufln = uniqid(true);
					// $msg = $msg . " <i style='float:right;'>(".$date.")</i><br/>";
					$msg = '<div class="msg"><b>'.$name.'</b> '.' <i title="UTC-Time:'.$utc_time.'">('.$date.')</i> '.'<div>'.$msg.'</div></div>'; 	// style='float:right;'
					$hmsg = $hmsg . $msg; 	// . "<hr style='border-style:dashed;' />";
					foreach ($sfls as $key => $value) {
						$flnm = $value.$ufn.'.mt';
						if(trim($flnm) != '') { 	// && is_file($flnm) && file_exists($flnm)) {
							// $nmsg = str_replace('/download.php?fl=','/download.php?'.''.'fl=',$msg);
							file_put_contents($flnm, $msg, FILE_APPEND);
							/* $fl = fopen($flnm, "a+");
							fwrite($fl, $msg);
							fclose($fl); */
							@ chmod($flnm, 0774);
						}
					}
				}
				//
			}
			$hdtl = array($crnms, $hmsg);
			$args = array('site_path'=>$site_path, 'class'=>'AfterMsgSend', 'function'=>'afterMessageSend', 'params' => array($site_path, $group_prefix, $name_sep, $name, $hdtl));
			$rs = $pr_obj->genProcess($args);
		}
	}
	
	function setFileFlag($name, $ci, $msg, $files, $typ, $gcid='')
	{
		global $site_path, $smileys, $group_prefix, $name_sep;
		// file_put_contents('/var/www/html/sc/swc/tmp/ts.t', json_encode(array($name, $ci, $msg, $typ, $gcid)));
		// $return_val = 'error';
		$utc_time = gmdate('Y-m-d H:i:s');
		$date = gmdate('Y-m-d h:i:s a', strtotime($utc_time));
		$flnm = "";
		$cids = array();
		$sfls = array();
		$crnms = array();
		$uflnm = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		if(is_file($uflnm) && file_exists($uflnm)) {
			$ufn = uniqid(true);
			$udtl = file_get_contents($uflnm);
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
			}
			if(!is_array($udtls)) { $udtls = array(); }
			if(!isset($udtls['con'])) { $udtls['con'] = array(); }
			if(!isset($udtls['grp'])) { $udtls['grp'] = array(); }
			//
			if($ci != '') {
				if(strpos($ci, ',') !== false) {
					$cids = @ explode(',', $cids);
					$cids = array_values(array_unique(array_filter($cids)));
				} else {
					$cids[] = $ci;
				}
			}
			if($typ == 'public') {
				$sfls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR;
				$crnms[] = 'General';
			} else if($typ == 'all') {
				//
				$ufls = array();
				if(file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR)) {
					$ufls = scandir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR);
				} else {
					@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR, 0774, true);
				}
				if(is_array($ufls)) {
					foreach ($ufls as $key => $val) {
						if(in_array($val, array('.','..'))) {
							unset($ufls[$key]);
						} else {
							if(in_array($val, $udtls['con'])) {
								$fldnm = (strcasecmp($name, $val) > 0)? $val.$name_sep.$name : $name.$name_sep.$val;
								if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
									@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
									if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
										@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
									}
									$ufls[$key] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR;
									// $crnms[] = $fldnm;
								}
							}
						}
					}
				}
				//
				$sfls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR;
				if(count($ufls) > 0) { $sfls = array_merge($sfls, $ufls); }
				$crnms[] = 'General';
			} else if($typ == 'contacts') {
				$ufls = array();
				foreach ($cids as $key => $val) {
					if(in_array($val, $udtls['con'])) {
						$fldnm = (strcasecmp($name, $val) > 0)? $val.$name_sep.$name : $name.$name_sep.$val;
						if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
							@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
							//
							if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
								@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
							}
							$ufls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR;
							$crnms[] = $val;
						}
					}
				}
				if(count($ufls) > 0) { $sfls = array_merge($sfls, $ufls); }
			} else if(trim($ci) != '' && trim(strtolower($ci)) != 'general') {
				$fldnm = '';
				if(in_array($ci, $udtls['con'])) {
					$fldnm = (strcasecmp($name, $ci) > 0)? $ci.$name_sep.$name : $name.$name_sep.$ci;
				// } else if(in_array($ci, $udtls['grp'])) {
				} else if(trim($gcid) != '' && isset($udtls['grp'][$gcid]) && $udtls['grp'][$gcid] = $ci) {
					$gkv = $gcid; 	// array_search($ci, $udtls['grp']);
					$fldnm = $group_prefix.$ci.$name_sep.$gkv;
				}
				if($fldnm != '') {
					if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
						@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
						//
						if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
							@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
						}
					}
					$sfls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR;
					$crnms[] = $ci;
				}
			} else {
				$sfls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR;
				$crnms[] = 'General';
			}
			$sfls = array_values(array_unique($sfls));
			$crnms = array_values(array_unique($crnms));
			//
			$msg = $hmsg = "";
			if(isset($files['files']['name']) && is_array($files['files']['name']) && count(array_filter($files['files']['name'])) > 0) {
				for($l=0; $l < count($files['files']['name']); $l++) {
					if($files['files']['error'][$l] == 0) {
						$filnm = preg_replace('/[^A-Za-z0-9\._-]/', '', $files['files']['name'][$l]);
						if($filnm != '') {
							// $fl = $this->fileCopy($filnm, $files['files']['tmp_name'][$l], $files['files']['type'][$l], $name);
							//$fls = array_merge($fls, array($fl));
							// if($msg == "") { $msg  = $hmsg = $name.': '.'File(s) : '; }
							// $msg = $fl . "<i style='float:right;'>(".$date.")</i>" . (($l == (count($_FILES['files']['name'])-1))? "<br/>" : "<hr style='border-style:dashed;' />");
							// $hmsg = $fl . "<i style='float:right;'>(".$date.")</i>" . "<hr style='border-style:dashed;' />";
							if(isset($files['files']['msg'][$l]) && $files['files']['msg'][$l] != '') {
								if($msg == "") { $msg  = $hmsg = 'File(s) : '; }
								$msg .= "<br/>".$files['files']['msg'][$l].' '; 	// $fl
								// $hmsg .= "<br/>".$fl.' ';
								if(is_array($crnms) && count($crnms) > 0) {
									foreach ($crnms as $k => $v) {
										mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR, 0774, 1);
										chmod($site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name, 0774);
										// $rmflnm = $site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$filnm.'_-_*.s';
										// @ array_map('unlink', glob($rmflnm));
										// @ unlink($site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$filnm.'_-_General.s');
										@ file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$filnm.'_-_'.$v.'.s', '');
										@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$filnm.'_-_'.$v.'.s', 0774);
									}
								}
							}
						}
					}
				}
				//
				if(trim($msg) != '') {
					// $ufln = uniqid(true);
					// $msg = $msg . " <i style='float:right;'>(".$date.")</i><br/>";
					$msg = '<div class="msg"><b>'.$name.'</b> '.' <i title="UTC-Time:'.$utc_time.'">('.$date.')</i> '.'<div>'.$msg.'</div></div>'; 	// style='float:right;'
					foreach ($sfls as $key => $value) {
						$flnm = $value.$ufn.'.mt';
						if(trim($flnm) != '') { 	// && is_file($flnm) && file_exists($flnm)) {
							// $nmsg = str_replace('/download.php?fl=','/download.php?'.''.'fl=',$msg);
							file_put_contents($flnm, $msg, FILE_APPEND);
							/* $fl = fopen($flnm, "a+");
							fwrite($fl, $msg);
							fclose($fl); */
							@ chmod($flnm, 0774);
						}
					}
				}
				//
			}
			//
		}
	}

	/**
	 * used for file uploadAfterMsgSend
	 * @param fileName name of file
	 * @param filePath path of file to copy from
	 * @param fileType type of file
	*/
	function fileCopy($fileName, $filePath, $fileType='', $name='')
	{
		global $site_path, $site_url, $fsds_obj;
		// if(strlen($fileName) != '') {
			// $fileName = substr($fileName,0,10) . substr($fileName, strrpos($fileName,'.'));
		// }
		// $fileName = str_replace(array(' ','-'), '', $fileName);
		$fileName = preg_replace('/[^A-Za-z0-9\._-]/', '', $fileName);
		// if($fileName == '') { $fileName = 'file'; }
		if($name == '' || $fileName == '') { return ''; }
		/*
		mkdir($site_path."pub".DIRECTORY_SEPARATOR.$name, 0774, true);
		chmod($site_path."pub".DIRECTORY_SEPARATOR.$name, 0774);
		$file = $site_path."pub".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fileName;
		*/
		$fsds_obj->mkpath('pub', $name, 0774, true);
		$file = $fsds_obj->path('pub').$name.DIRECTORY_SEPARATOR.$fileName;
		// $url = $site_url."pub".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fileName;
		$url = $site_url.'/download.php?fl='.urlencode(base64_encode($name.':-:'.$fileName));
		$json = '';
		$txt = '';
		if(move_uploaded_file($filePath, $file)) {
			@ chmod($file, 0774);
			$txt = "<a class='uflnk' href=\"$url\" target=\"_blank\">$fileName</a>"; 	// (file)
			// $json = array('txt' => $txt);
		}
		// if(is_array($json)) { $json = json_encode($json); }
		return $txt;
	}

}
?>