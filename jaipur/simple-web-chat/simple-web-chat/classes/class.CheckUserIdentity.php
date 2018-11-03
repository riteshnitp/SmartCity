<?php
/**
 * This class contains logic to check user's identity
*/
class CheckUserIdentity
{
	/**
	 * checks user identity
	 * @param name name of user
	 * @param email password of user
	 * @param pass password
	 * @param cpcode extra code used for change password
	*/
	function checkIdentity($name, $email, $pass, $cpcode, $usnid, $phno)
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
				if(isset($udtls['prf']['name']) && $udtls['prf']['name'] == $name && isset($udtls['prf']['usnid']) && $udtls['prf']['usnid'] == $usnid) {
					// $usnid = str_replace('.','',uniqid('', true));
					// $udtls['prf']['usnid'] = $usnid;
					// @ file_put_contents($fl, json_encode($udtls), LOCK_EX);
					// @ chmod($fl, 0774);
					$return_val = 'success:'.$name.':'.$usnid;
				} else if(isset($udtls['prf']['name']) && $udtls['prf']['name'] == $name && isset($udtls['prf']['email']) && $udtls['prf']['email'] == $email && isset($udtls['prf']['paswd']) && $udtls['prf']['paswd'] == $pass && !isset($udtls['prf']['vcode'])) {
					$usnid = str_replace('.','',uniqid('', true));
					$udtls['prf']['usnid'] = $usnid;
					// @ file_put_contents($fl, json_encode($udtls), LOCK_EX);
					$fsds_obj->put('users', $uresnm, json_encode($udtls), LOCK_EX);
					// @ chmod($fl, 0774);
					$fsds_obj->chperm('users', $uresnm, 0774);
					$return_val = 'success:'.$name.':'.$usnid;
				} else if($cpcode != '' && isset($udtls['prf']['name']) && $udtls['prf']['name'] == $name 
					&& isset($udtls['prf']['email']) && $udtls['prf']['email'] == $email 
					&& isset($udtls['prf']['cpcode']) && $udtls['prf']['cpcode'] == $cpcode 
					&& !isset($udtls['prf']['vcode'])
				) {
					$usnid = str_replace('.','',uniqid('', true));
					$udtls['prf']['paswd'] = $pass;
					$udtls['prf']['usnid'] = $usnid;
					// @ file_put_contents($fl, json_encode($udtls), LOCK_EX);
					$fsds_obj->put('users', $uresnm, json_encode($udtls), LOCK_EX);
					// @ chmod($fl, 0774);
					$fsds_obj->chperm('users', $uresnm, 0774);
					$return_val = 'success:'.$name.':'.$usnid;
				} else {
					$return_val = 'error';
				}
				/* 
				if($return_val != 'error') {
					// add to email and phone list
					if(isset($udtls['prf']['phone']) && trim($udtls['prf']['phone']) != '') {
						$phno = preg_replace('/[^0-9]/','',$udtls['prf']['phone']);
						if(trim($phno) != '') {
							// file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'upn'.DIRECTORY_SEPARATOR.$phno.'.p', $udtls['prf']['name']);
							$fsds_obj->put('userphones', $phno.'.p', $udtls['prf']['name']);
						}
					}
					if(isset($udtls['prf']['email']) && trim($udtls['prf']['email']) != '') {
						// file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'uem'.DIRECTORY_SEPARATOR.$udtls['prf']['email'].'.e', $udtls['prf']['name']);
						$fsds_obj->put('useremails', $udtls['prf']['email'].'.e', $udtls['prf']['name']);
					}
				}*/
			} else {
				$udtls['prf']['name'] = $name;
				$udtls['prf']['email'] = $email;
				$udtls['prf']['paswd'] = $pass;
				$udtls['prf']['phone'] = $phno;
				$udtls['prf']['vcode'] = uniqid(true);
				// @ file_put_contents($fl, json_encode($udtls), LOCK_EX);
				$fsds_obj->put('users', $uresnm, json_encode($udtls), LOCK_EX);
				// @ chmod($fl, 0774);
				$fsds_obj->chperm('users', $uresnm, 0774);
				$return_val = sendVerificationMail($name, $email, $udtls, $fl);
			}
		} else {
			$udtls['prf']['name'] = $name;
			$udtls['prf']['email'] = $email;
			$udtls['prf']['paswd'] = $pass;
			$udtls['prf']['phone'] = $phno;
			$udtls['prf']['vcode'] = uniqid(true);
			// @ file_put_contents($fl, json_encode($udtls), LOCK_EX);
			$fsds_obj->put('users', $uresnm, json_encode($udtls), LOCK_EX);
			// @ chmod($fl, 0774);
			$fsds_obj->chperm('users', $uresnm, 0774);
			$return_val = sendVerificationMail($name, $email, $udtls, $fl);
		}
		return $return_val;
	}

	private function sendVerificationEMail($name, $email, $udtls, $fl) 
	{
		global $site_path, $site_url;
		$return_val = 'error';
		// mail($email, 'Email Verification', $site_path.'uverify.php/?code='.$udtls['pref']['vcode'], $headers);
		include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Mail.php');
		$mailer_obj = new Mail();
		$mbody = '<a href="'.$site_url.'uverify.php?name='.$name.'&code='.$udtls['prf']['vcode'].'">'.$site_url.'uverify.php?name='.$name.'&code='.$udtls['prf']['vcode'].'</a>';
		$rtrn_val = $mailer_obj->sendMail($email, $mbody);
		if($rtrn_val) {
			$return_val = 'wait';
		} else {
			@ unlink($fl);
			$return_val = 'e-error';
		}
		// $return_val = 'success:'.$name.':'.$usnid;
		return $return_val;
	}

}
?>