<?php
/**
 * This class contains logic to find contacts of user
*/
class UserContacts
{
	/**
	 * Find groups and contacts of user
	 * @param name name of user
	*/
	function getUserGroupsNContacts($name)
	{
		global $site_path, $fsds_obj;

		$udtls = array();
		// $fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		$uresnm = $name.'.u';
		// if(is_file($fl) && file_exists($fl)) {
		if($fsds_obj->exists('users', $uresnm)) {
			// $udtl = file_get_contents($fl);
			$udtl = $fsds_obj->get('users', $uresnm);
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
		}
		if(!isset($udtls['con']) || !is_array($udtls['con'])) { $udtls['con'] = array(); } else { $udtls['con'] = array_filter($udtls['con']); }
		if(!isset($udtls['conr']) || !is_array($udtls['conr'])) { $udtls['conr'] = array(); } else { $udtls['conr'] = array_filter($udtls['conr']); }
		if(!isset($udtls['grp']) || !is_array($udtls['grp'])) { $udtls['grp'] = array(); } else { $udtls['grp'] = array_filter($udtls['grp']); }
		if(!isset($udtls['grpr']) || !is_array($udtls['grpr'])) { $udtls['grpr'] = array(); } else { $udtls['grpr'] = array_filter($udtls['grpr']); }
		sort($udtls['con']); sort($udtls['conr']); asort($udtls['grp']); asort($udtls['grpr']);
		if(count($udtls['con']) > 0) {
			foreach ($udtls['con'] as $key => $value) {
				// if(file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$value.'.u')) {
				if($fsds_obj->exists('users', $value.'.u')) {
					// $ol = file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$value.'.u');
					$ol = $fsds_obj->exists('onlineusers', $value.'.u');
					// $cdtl = file_get_contents($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$value.'.u');
					$cdtl = $fsds_obj->get('users', $value.'.u');
					$cdtls = array();
					if(trim($cdtl) != '') {
						$cdtls = @ json_decode($cdtl, 1);
						$cdtl = '';
						if(!is_array($cdtls)) { $cdtls = array(); }
					}
					if(isset($cdtls['lst']) && trim($cdtls['lst']) != '') {
						$udtls['con'][$key] = array();
						$udtls['con'][$key][$value] = ($ol)? 'ol:'.$cdtls['lst'] : $cdtls['lst'];
					}
				}
			}
		}
		return $udtls;
	}
}
?>