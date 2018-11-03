<?php
/**
 * This class contains logic to modify user's passcode
*/
class ModifyPass
{

	/**
	 * function to verify user
	 * @param name name of user
	 * @param code verification code
	*/
	function modifyPasscode($name, $email)
	{
		global $site_path, $site_url, $fsds_obj;

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
			if(isset($udtls['prf']) && is_array($udtls['prf']) && count($udtls['prf']) > 0) { 	// && !isset($udtls['prf']['vcode'])
				if($udtls['prf']['name'] == $name && $udtls['prf']['email'] == $email && !isset($udtls['prf']['vcode'])) {
					$udtls['prf']['cpcode'] = uniqid(true);
					// @ file_put_contents($fl, json_encode($udtls), LOCK_EX);
					$fsds_obj->put('users', $uresnm, json_encode($udtls), LOCK_EX);
					// @ chmod($fl, 0774);
					$fsds_obj->chperm('users', $uresnm, 0774);
					//
					include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Mail.php');
					$mailer_obj = new Mail();
					$mbody = '<a href="'.$site_url.'sc.php?name='.$name.'&cpcode='.$udtls['prf']['cpcode'].'">'.$site_url.'sc.php?name='.$name.'&cpcode='.$udtls['prf']['cpcode'].'</a>';
					$rtrn_val = $mailer_obj->sendMail($email, $mbody);
					if(!$rtrn_val) {
						$return_val = 'e-error';
					}
					$return_val = 'wait';
				}
			}
		}
		return $return_val;
	}
}
?>
