<?php
/**
 * This class contains logic to verify user's account
*/
class UserVerification
{

	/**
	 * function to verify user
	 * @param name name of user
	 * @param code verification code
	*/
	function verifyUser($name, $code)
	{
		global $site_path, $fsds_obj;

		$return_val = 'Error, <a href="'.$site_url.'sc.php">Back To App</a>';
		// $fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		$uresnm = $name,'.u';
		// if(is_file($fl) && file_exists($fl)) {
		if($fsds_obj->exists('users', $uresnm)) {
			// $udtl = file_get_contents($fl);
			$udtl = $fsds_obj->get('users', $uresnm);
			$udtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
			if(isset($udtls['prf']) && is_array($udtls['prf']) && count($udtls['prf']) > 0 && isset($udtls['prf']['vcode']) && trim($udtls['prf']['vcode']) === $code) {
				unset($udtls['prf']['vcode']);
				// @ file_put_contents($fl, json_encode($udtls), LOCK_EX);
				$fsds_obj->put('users', $uresnm, json_encode($udtls), LOCK_EX);
				// @ chmod($fl, 0774);
				$fsds_obj->chperm('users', $uresnm, 0774);
				$return_val = 'Success, <a href="'.$site_url.'sc.php">Back To App</a>';
				// add to email and phone list
				if(isset($udtls['phone']) && trim($udtls['phone']) != '') {
					$phno = preg_replace('/[^0-9]/','',$udtls['phone']);
					if(trim($phno) != '') {
						// file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'upn'.DIRECTORY_SEPARATOR.$phno.'.p', $udtls['prf']['name']);
						$fsds_obj->put('userphones', $phno.'.p', $udtls['prf']['name']);
					}
				}
				if(isset($udtls['email']) && trim($udtls['email']) != '') {
					// file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'uem'.DIRECTORY_SEPARATOR.$udtls['email'].'.e', $udtls['prf']['name']);
					$fsds_obj->put('useremails', $udtls['prf']['email'].'.e', $udtls['prf']['name']);
				}
			} else {
				$return_val = 'Error, <a href="'.$site_url.'sc.php">Back To App</a>';
			}
		}
		return $return_val;
	}
}
?>