<?php
/**
 * This class contains logic to initialize chat with contacts
*/
class InitContactChat
{

	/**
	 * Initializes chat with contact
	 * @param name name of user
	 * @param cnm name of contact
	*/
	function initCChat($name, $cnm)
	{
		global $site_path, $name_sep, $fsds_obj;

		$return_val = 'error';
		// $fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		$uresnm = $name.'.u';
		// $cfl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$cnm.'.u';
		$cresnm = $cnm.'.u';
		// if(is_file($fl) && file_exists($fl) && is_file($cfl) && file_exists($cfl)) {
		if($fsds_obj->exists('users', $uresnm) && $fsds_obj->exists('users', $cresnm)) {
			// $udtl = file_get_contents($fl);
			$udtl = $fsds_obj->get('users', $uresnm);
			$udtls = array();
			// $cdtl = file_get_contents($cfl);
			$cdtl = $fsds_obj->get('users', $cresnm);
			$cdtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
			if(trim($cdtl) != '') {
				$cdtls = @ json_decode($cdtl, 1);
				if(!is_array($cdtls)) { $cdtls = array(); }
			}
			$inb = 0;
			if(isset($udtls['con']) && is_array($udtls['con']) && in_array($cnm, $udtls['con'])) {
				$inb = 1;
			} /* else if(isset($udtls['conr']) && is_array($udtls['conr'])) {
				$inb = -1;
			}*/
			if($inb == 1 && isset($cdtls['con']) && is_array($cdtls['con']) && in_array($name, $cdtls['con'])) {
				$inb = 2;
			} else if(isset($cdtls['conr']) && is_array($cdtls['conr']) && in_array($name, $cdtls['conr'])) {
				$inb = -1;
			}
			/*
			if(isset($udtls['con']) && is_array($udtls['con']) && in_array($cnm, $udtls['con'])) {
				$inb = 1;
			} else if(isset($udtls['conr']) && is_array($udtls['conr'])) {
				$inb = -1;
			}
			if(isset($cdtls['con']) && is_array($cdtls['con']) && in_array($name, $cdtls['con'])) {
				$inb = 2;
			} else if(isset($cdtls['conr']) && is_array($cdtls['conr'])) {
				$inb = 0;
			}
			*/
			if($inb == 2) {
				$fldnm = (strcasecmp($name, $cnm) > 0)? $cnm.$name_sep.$name : $name.$name_sep.$cnm;
				/*if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm)) {
					if(!is_dir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm)) {
						@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
						@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm, 0774);
					}
				}*/
				if(! $fsds_obj->dirExists('usermap', $name.DIRECTORY_SEPARATOR.$fldnm)) {
					// @ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
					$fsds_obj->mkpath('usermap', $name.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
				}
				/*if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$cnm.DIRECTORY_SEPARATOR.$fldnm)) {
					if(!is_dir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$cnm.DIRECTORY_SEPARATOR.$fldnm)) {
						@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$cnm.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
						@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$cnm.DIRECTORY_SEPARATOR.$fldnm, 0774);
					}
				}*/
				if(! $fsds_obj->dirExists('usermap', $cnm.DIRECTORY_SEPARATOR.$fldnm)) {
					// @ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$cnm.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
					$fsds_obj->mkpath('usermap', $cnm.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
				}
				/*if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm)) {
					if(!is_dir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm)) {
						@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
						@ chmod($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm, 0774);
					}
				}*/
				if(! $fsds_obj->dirExists('usermsg', $fldnm)) {
					// @ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
					$fsds_obj->mkpath('usermsg', $fldnm, 0774, true);
				}
				/*if($cnm == 'General' && !file_exists($site_path."h".DIRECTORY_SEPARATOR.$name)) {
					if(!is_dir($site_path."h".DIRECTORY_SEPARATOR.$name)) {
						@ mkdir($site_path."h".DIRECTORY_SEPARATOR.$name, 0774, true);
						@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name, 0774);
					}
				}*/
				if($cnm == 'General' && ! $fsds_obj->dirExists('history', $name)) {
					// @ mkdir($site_path."h".DIRECTORY_SEPARATOR.$name, 0774, true);
					$fsds_obj->mkpath('history', $name, 0774, true);
				}
				/*if(!file_exists($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm)) {
					if(!is_dir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm)) {
						@ mkdir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm, 0774, true);
						@ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm, 0774);
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