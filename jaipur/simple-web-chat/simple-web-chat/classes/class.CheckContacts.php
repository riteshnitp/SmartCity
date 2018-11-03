 <?php
/**
 * This class contains logic to check user contacts exists based on email and phone number
*/
class CheckContacts
{
    /**
     * Match contacts of user
     * @param name name of user
     * @param lcons user contacts list
    */
    function matchContacts($name, $lcons)
    {
        global $site_path, $fsds_obj;
		//
		$emails = array();
		$phones = array();
		$scons = array();
        if(is_array($lcons) && count($lcons) > 0) {
            foreach($lcons as $key => $vals) {
				$emails = array_merge($emails, $vals['emails']);
				$phones = array_merge($phones, $vals['phones']);
			}
			$phones = array_map(create_function('$val', '$val = preg_replace("/[^0-9]/","",$val); return $val;'), $phones);
			$phones = array_values(array_unique($phones));
			$emails = array_values(array_unique($emails));
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
            if(!isset($udtls['con']) || !is_array($udtls['con'])) { $udtls['con'] = array(); }
            if(!isset($udtls['conr']) || !is_array($udtls['conr'])) { $udtls['conr'] = array(); }
			if(!isset($udtls['scon']) || !is_array($udtls['scon'])) { $udtls['scon'] = array(); }
            // print_r($udtl); exit;
			// $pfl = $site_path.'files'.DIRECTORY_SEPARATOR.'upn'.DIRECTORY_SEPARATOR;
			// $efl = $site_path.'files'.DIRECTORY_SEPARATOR.'uem'.DIRECTORY_SEPARATOR;
			foreach($emails as $key => $val) {
				if($fsds_obj->exists('useremails', $val.'.e')) {
					$tmp = '';
					// $tmp = trim(file_get_contents($efl.$val.'.e'));
					$tmp = trim($fsds_obj->get('useremails', $val.'.e'));
					if($tmp != '' && $fsds_obj->exists('users', $tmp.'.u')) {
						$scons[] = $tmp;
					}
				}
			}
			foreach($phones as $key => $val) {
				if($fsds_obj->exists('userphones', $val.'.p')) {
					$tmp = '';
					// $tmp = trim(file_get_contents($pfl.$val.'.p'));
					$tmp = trim($fsds_obj->get('userphones', $val.'.p'));
					if($tmp != '' && $fsds_obj->exists('users', $tmp.'.u')) {
						$scons[] = $tmp;
					}
				}
			}
			$scons = array_diff($scons, $udtls['con']);
			$scons = array_diff($scons, $udtls['conr']);
			$scons = array_merge($udtls['scon'], $scons);
			$scons = array_values(array_unique($scons));
			$udtls['scon'] = $scons;
			// @ file_put_contents($fl, json_encode($udtls), LOCK_EX);
			$fsds_obj->put('users', $uresnm, json_encode($udtls), LOCK_EX);
			// @ chmod($fl, 0774);
			$fsds_obj->chperm('users', $uresnm, 0774);
        }
        return true; 	// $udtls['scon'];
    }
}
?>