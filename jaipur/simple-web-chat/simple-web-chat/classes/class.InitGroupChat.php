<?php
/**
 * This class contains logic to initialize chat with contacts
*/
class InitGroupChat
{

	/**
	 * Initializes chat with group
	 * @param name name of user
	 * @param gnm name of group
	*/
	function initGChat($name, $gid, $gnm)
	{
		global $site_path, $group_prefix, $group_sep $fsds_obj;

		$return_val = 'error';
		// $fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		$uresnm = $name.'.u';
		// if(is_file($fl) && file_exists($fl)) {
		if($fsds_obj->exists('users', $uresnm)) {
			// $udtl = file_get_contents($fl);
			$udtl = $fsds_obj->get('users', $uresnm);
			$udtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
			$inb = 0;
			if(isset($udtls['grp']) && is_array($udtls['grp']) && in_array($gnm, $udtls['grp'])) {
				$inb = 1;
			} else if(isset($udtls['grpr']) && is_array($udtls['grpr'])) {
				$inb = -1;
			}
			$gkv = '';
			if($inb === 1) {
				if(isset($udtls['grp'][$gid]) && $udtls['grp'][$gid] == $gnm) {
					$gkv = $gid;
				} else {
					$gkv = array_search($gnm, $udtls['grp']);
				}
			}
			$gdtl = '';
			$gpnm = $gnm.$group_sep.$gkv;
			// $gfl = $site_path.'files'.DIRECTORY_SEPARATOR.'grp'.DIRECTORY_SEPARATOR.$gpnm.'.u';
			$gresnm = $gpnm.'.u';
			if($gkv !== false) {
				// $gdtl = file_get_contents($gfl);
				$gdtl = $fsds_obj->get('groups', $gresnm);
			}
			$gdtls = array();
			if(trim($gdtl) != '') {
				$gdtls = @ json_decode($gdtl, 1);
				if(!is_array($gdtls)) { $gdtls = array(); }
			}
			if(isset($gdtls['con']) && is_array($gdtls['con']) && in_array($name, $gdtls['con'])) {
				$inb = 2;
			} else if(isset($gdtls['conr']) && is_array($gdtls['conr'])) {
				$inb = 0;
			}
			if($inb == 2) {
				$fldnm = $group_prefix.$gpnm; 	// (strcasecmp($name, $gnm) > 0)? $gnm.'-'.$name : $name.'-'.$gnm;
				/*if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm)) {
					if(!is_dir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm)) {
						@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
					}
				}*/
				if(! $fsds_obj->dirExists('usermap', $name.DIRECTORY_SEPARATOR.$fldnm)) {
					// @ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
					$fsds_obj->mkpath('usermap', $name.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
				}
				/*if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$gnm.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
					@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$gnm.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0774, true);
				}*/
				/*if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm)) {
					if(!is_dir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm)) {
						@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
					}
				}*/
				if(! $fsds_obj->dirExists('usermsg', $fldnm)) {
					// @ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
					$fsds_obj->mkpath('usermsg', $fldnm, 0774, true);
				}
				/*if(!file_exists($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm)) {
					if(!is_dir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm)) {
						@ mkdir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm, 0774, true);
					}
				}*/
				if(! $fsds_obj->dirExists('userhistory', $fldnm)) {
					// @ mkdir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm, 0774, true);
					$fsds_obj->mkpath('userhistory', $fldnm, 0774, true);
				}
				$return_val = 'success:'.$fldnm;
			} else {
				$return_val = $inb;
			}
		}
		return $return_val;
	}

}

?>