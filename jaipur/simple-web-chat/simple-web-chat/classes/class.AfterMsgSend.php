<?php
/**
 * Common class for processing after fetching messages
*/
class AfterMsgSend
{

	/**
	 * Some extra processing after fetching messages
	 * @param site_path path to site folder
	 * @param group_prefix prefix of group files
	 * @param name name of user
	 * @param dtls message text
	*/
	function afterMessageSend($site_path, $group_prefix, $name_sep, $name, $dtls)
	{
		include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.FSDS.php');
		$fsds_obj = new FSDS();
		// $umdo = $site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
		// clean old history files
		$tm = $name.'-'.gmdate('Y-m', strtotime('-2 months'));
		// if(is_file($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html') && file_exists($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html')) {
		if($fsds_obj->exists('history', $name.DIRECTORY_SEPARATOR.$tm.'.html')) {
			// $mtime = @filemtime($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html');
			// $mtime = $fsds_obj->lastModified('history', $name.DIRECTORY_SEPARATOR.$tm.'.html');
			// @ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
			$fsds_obj->chperm('history', $name.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
			// @ unlink($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html');
			$fsds_obj->del('history', $name.DIRECTORY_SEPARATOR.$tm.'.html');
			/*if($mtime && (strtotime('+1 minutes') - $mtime) > strtotime('-7 days')) {
				@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
				@ file_put_contents($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', '');
				@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
			}*/
		}
		// removing old history files
		if($fsds_obj->exists('usermap', $name)) {
			// $uflds = scandir($umdo);
			$uflds = $fsds_obj->listing('usermap', $name);
			foreach($uflds as $key => $val) {
				/*if(!file_exists($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val)) {
					if(!is_dir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val)) {
						@ mkdir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val, 0774, true);
					}
				}*/
				$tm = $val.'-'.gmdate('Y-m', strtotime('-2 months'));
				if(!in_array($val, array('.', '..'))) { 	// && in_array($val, $cids)
					// if(is_file($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.DIRECTORY_SEPARATOR.$tm.'.html') && file_exists($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.DIRECTORY_SEPARATOR.$tm.'.html')) {
					if($fsds_obj->exists('userhistory', $val.DIRECTORY_SEPARATOR.$tm.'.html')) {
						// @ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
						$fsds_obj->chperm('userhistory', $val.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
						// @ unlink($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.DIRECTORY_SEPARATOR.$tm.'.html');
						$fsds_obj->del('userhistory', $val.DIRECTORY_SEPARATOR.$tm.'.html');
						/*$mtime = @filemtime($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html');
						if($mtime && (strtotime('+1 minutes') - $mtime) > strtotime('-7 days')) {
							@ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html', 0774);
							@ file_put_contents($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html', '');
							@ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html', 0774);
						}*/
					}
					//
				}
			}
		}
		$tm = $name.'-'.gmdate('Y-m');
		// write to history file
		/*if(trim($dtls['gen']) != '') { 	// && $fl = @fopen($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', "a+")
			if(!file_exists($site_path."h".DIRECTORY_SEPARATOR.$name)) {
				if(!is_dir($site_path."h".DIRECTORY_SEPARATOR.$name)) {
					@ mkdir($site_path."h".DIRECTORY_SEPARATOR.$name, 0774, true);
					@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name, 0774);
				}
			}
			//
			@ file_put_contents($this->site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', $dtls['gen'], FILE_APPEND); 	// | LOCK_EX
			// @ fwrite($fl, $dtls['gen']); 	// ."<hr style='border-style:dashed;' />" 	// ." <i style='float:right;'>(".$date.")</i><hr style='border-style:dashed;' />"
			// @ fclose($fl);
			@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
		}*/
		$value = (isset($dtls[1]))? trim($dtls[1]) : '';
		if(is_array($dtls[0]) && $value != '') {
			foreach ($dtls[0] as $key) {
				if($key == 'General') { 	// && $fl = @fopen($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', "a+")
					/* if(!file_exists($site_path."h".DIRECTORY_SEPARATOR.$name)) {
						if(!is_dir($site_path."h".DIRECTORY_SEPARATOR.$name)) {
							@ mkdir($site_path."h".DIRECTORY_SEPARATOR.$name, 0774, true);
							@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name, 0774);
						}
					} */
					if(! $fsds_obj->exists('history', $name)) {
						// if(!is_dir($site_path."h".DIRECTORY_SEPARATOR.$name)) {
							$fsds_obj->mkpath('history', $name, 0774, true);
							// @ chmod($site_path."h".DIRECTORY_SEPARATOR.$name, 0774);
						// }
					}
					//
					// @ file_put_contents($this->site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', $value, FILE_APPEND); 	// | LOCK_EX
					$fsds_obj->put('history', $name.DIRECTORY_SEPARATOR.$tm.'.html', $value, FILE_APPEND);
					// @ fwrite($fl, $dtls['gen']); 	// ."<hr style='border-style:dashed;' />" 	// ." <i style='float:right;'>(".$date.")</i><hr style='border-style:dashed;' />"
					// @ fclose($fl);
					// @ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
					$fsds_obj->chperm('history', $name.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
				} else {
					if(strpos($key, $group_prefix) !== false && strpos($key, $group_prefix) === 0) {
						$fldnm = $key; 	// .$name_sep.$name;
					} else {
						$fldnm = (strcasecmp($name, $key) > 0)? $key.$name_sep.$name : $name.$name_sep.$key;
					}
					/*if(!file_exists($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm)) {
						if(!is_dir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm)) {
							@ mkdir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm, 0774, true);
						}
					}*/
					$tm = $fldnm.'-'.gmdate('Y-m');
					// if($key != 'gen' && trim($value) != '') { 	// && $fl = @fopen($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$tm.'.html', "a+")
						// $vl = $key;
						// @ file_put_contents($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$tm.'.html', $value, FILE_APPEND | LOCK_EX);
						$fsds_obj->put('userhistory', $fldnm.DIRECTORY_SEPARATOR.$tm.'.html', $value, FILE_APPEND | LOCK_EX);
						/*@ fwrite($fl, $value); 	// ."<hr style='border-style:dashed;' />" 	// ." <i style='float:right;'>(".$date.")</i><hr style='border-style:dashed;' />"
						@ fclose($fl);*/
						// @ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
						$fsds_obj->chperm('userhistory', $fldnm.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
					// }
				}
			}
		}
		// reg
		/*
		if(file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u') && is_file($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u')) {
			$udtl = file_get_contents($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', json_encode(array('')));
			$udtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				$udtl = '';
				if(!is_array($udtls)) { $udtls = array(); }
			}
			$udtls['lst'] = gmdate('Y-m-d h:i:s A');
			file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', json_encode($udtls));
			@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', 0774);
		} else {
			$udtls['lst'] = gmdate('Y-m-d h:i:s A');
			file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', json_encode(json_encode($udtls)));
			@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', 0774);
		} 
		*/
		// online
		/*$mtime = filemtime($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u');
		if(! file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u') || ($mtime && $mtime < strtotime("-30 seconds"))) {
			file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u', json_encode(array('lastseen'=>gmdate('Y-m-d H:i:s'))));
			@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u', 0774);
		}*/
	}
    
}
?>