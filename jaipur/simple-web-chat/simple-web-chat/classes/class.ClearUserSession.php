<?php
/**
 * This class contains logic to clear user's session
*/
class ClearUserSession
{
	/**
	 * checks user session 
	 * @param name name of user
	 * @param usid user session identifier
	*/
	function clearSession($name, $usnid)
	{
		global $site_path, $site_url, $fsds_obj;
		$return_val = false;
		if(trim($name) == '' || trim($usnid) == '') {
			return $return_val;
		}
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
			if(isset($udtls['prf']) && is_array($udtls['prf'])) { 	// && count($udtls['prf']) > 0
				if(isset($udtls['prf']['name']) && $udtls['prf']['name'] == $name && isset($udtls['prf']['usnid']) && $udtls['prf']['usnid'] == $usnid) {
					unset($udtls['prf']['usnid']);
				}
			}
			// @ file_put_contents($fl, json_encode($udtls), LOCK_EX);
			$fsds_obj->put('users', $uresnm, json_encode($udtls), LOCK_EX);
			// @ chmod($fl, 0774);
			$fsds_obj->chperm('users', $uresnm, 0774);
			$return_val = true;
		}
		return $return_val;
	}
}
?>