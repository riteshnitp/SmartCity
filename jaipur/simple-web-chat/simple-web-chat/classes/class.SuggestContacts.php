<?php
/**
 * This class provides suggested contacts of a user
*/

class SuggestContacts
{

	/**
	 * Fetches suggested contacts of a user
	 * @param name name of user
	*/
	function suggestedContacts($name)
	{
		global $site_path, $site_url, $fsds_obj;
		// $fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		$uresnm = $name.'.u';
		$udtls = array();
		// if(is_file($fl) && file_exists($fl)) {
		if($fsds_obj->exists('users', $uresnm)) {
			// $udtl = file_get_contents($fl);
			$udtl = $fsds_obj->get('users', $uresnm);
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
		}
		if(!isset($udtls['scon']) || !is_array($udtls['scon'])) { 
			$udtls['scon'] = array(); 
		}
		return $udtls['scon'];
	}

}
?>