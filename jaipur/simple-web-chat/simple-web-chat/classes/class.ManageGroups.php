<?php
/**
 * This class contains logic to manage user groups
*/
class ManageGroups
{
	/**
	 * manage user groups
	 * @param name name of user
	 * @param grpnms names of groups
	 * @param grpcon names of contacts
	*/
	function manageUserGroups($name, $grpnms, $grpcon)
	{
		global $site_path, $group_prefix, $group_sep, $fsds_obj;

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
			$gnms = array();
			if(strpos($grpnms, ',')) {
				$gnms = @ explode(',', $grpnms);
			} else {
				$gnms[] = $grpnms;
			}
			$gnms = array_values(array_unique($gnms));
			if(!isset($udtls['grp']) || !is_array($udtls['grp'])) { $udtls['grp'] = array(); }
			if(!isset($udtls['grpr']) || !is_array($udtls['grpr'])) { $udtls['grpr'] = array(); }
			//
			$grpconray = array();
			if(strpos($grpcon, ',') !== false) {
				$grpconray = @ explode(',', $grpcon);
			} else {
				$grpconray[] = $grpcon;
			}
			$grpconray[] = $name;
			if(!isset($udtls['con']) || !is_array($udtls['con'])) { $udtls['con'] = array(); }
			if(count($grpconray) > 0) { 	// && count($udtls['con']) > 0
				$nics = array_diff($grpconray, $udtls['con']);
				$grpconray = array_diff($grpconray, $nics);
			}
			//
			if(count($grpconray) > 0) {
				foreach ($gnms as $ky => $vl) {
					$grpnm = trim($vl);
					if($grpnm != '') {
						//
						$inreq = false;
						$gkv = array_search($grpnm, $udtls['grp']);
						if($gkv === false) {
							$gkv = array_search($grpnm, $udtls['grpr']);
							if($gkv !== false) { $inreq = true; }
						}
						$gresnm = '';
						if($gkv !== false && !$inreq) {
							$gnm = $grpnm.$group_sep.$gkv;
							// $gfl = $site_path.'files'.DIRECTORY_SEPARATOR.'grp'.DIRECTORY_SEPARATOR.$gnm.'.u';
							$gresnm = $gnm.'.u';
						}
						//
						if($gkv !== false && $gresnm != '' && $fsds_obj->exists('groups', $gresnm) && ! $inreq) { 	// edit
							// $gdtl = file_get_contents($gfl);
							$gdtl = $fsds_obj->get('groups', $gresnm);
							$gdtls = array();
							if(trim($gdtl) != '') {
								$gdtls = @ json_decode($gdtl, 1);
								if(!is_array($gdtls)) { $gdtls = array(); }
							}
							$grpcons = array();
							if(isset($gdtls['con'])) {
								$grpcons = $gdtls['con'];
							}
							$grpconrs = array();
							if(isset($gdtls['conr'])) {
								$grpconrs = $gdtls['conr'];
							}
							//
							if(count($grpconray) > 0) {
								$grpconray = array_diff($grpconray, $grpcons);
								$grpconrs = array_merge($grpconrs, $grpconray);
								$grpconrs = array_values(array_unique($grpconrs));
								//
								$gdtls = array('con'=>$grpcons,'conr'=>$grpconrs);
								$gdtl = json_encode($gdtls);
								// file_put_contents($gfl, $gdtl, LOCK_EX);
								$fsds_obj->put('groups', $gresnm, $gdtl, LOCK_EX);
								if(count($grpconray) > 0) {
									foreach ($grpconray as $key => $value) {
										// $cfl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$value.'.u';
										$cresnm = $value.'.u';
										$cdtl = $fsds_obj->get('users', $cresnm);
										$cdtls = array();
										if(trim($cdtl) != '') {
											$cdtls = @ json_decode($cdtl, 1);
											if(!is_array($cdtls)) { $cdtls = array(); }
										}
										if(!isset($cdtls['grp']) || !is_array($cdtls['grp'])) { $cdtls['grp'] = array(); }
										if(!isset($cdtls['grpr']) || !is_array($cdtls['grpr'])) { $cdtls['grpr'] = array(); }
										if(isset($cdtls['grp'][$gkv]) && $cdtls['grp'][$gkv] == $grpnm) {
											// $cdtls['grp'][$gkv] = $grpnm;
										} else {
											if(isset($cdtls['grpr'][$gkv]) && $cdtls['grpr'][$gkv] == $grpnm) {
												//
											} else {
												$cdtls['grpr'][$gkv] = $grpnm;
											}
										}
										/*if(isset($cdtls['grpr'][$gkv]) && $cdtls['grpr'][$gkv] == $grpnm) {
											unset($cdtls['grpr'][$gkv]);
										}*/
										// file_put_contents($cfl, json_encode($cdtls), LOCK_EX);
										$fsds_obj->put('users', $cresnm, json_encode($cdtls), LOCK_EX);
									}
								}
							}
							// echo 'success'; exit;
						} else if($gkv === false) { 	// add
							$gkv = uniqid();
							$gnm = $grpnm.$group_sep.$gkv;
							// $gfl = $site_path.'files'.DIRECTORY_SEPARATOR.'grp'.DIRECTORY_SEPARATOR.$gnm.'.u';
							// $ugfld = $site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$group_prefix.$gnm.DIRECTORY_SEPARATOR;
							// $tugfld = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$group_prefix.$gnm.DIRECTORY_SEPARATOR;
							$grpconray = array();
							if(strpos($grpcon, ',') !== false) {
								$grpconray = @ explode(',', $grpcon);
							} else {
								$grpconray[] = $grpcon;
							}
							$k = array_search($name, $grpconray);
							if($k !== false) {
								unset($grpconray[$k]);
								$grpconray = array_values($grpconray);
							}
							$gdtls = array('con'=>array($name),'conr'=>$grpconray);
							$gdtl = json_encode($gdtls);
							/*file_put_contents($gfl, $gdtl, LOCK_EX);
							mkdir($ugfld, 0774, true);
							mkdir($tugfld, 0774, true);*/
							$fsds_obj->put('groups', $gnm.'.u', $gdtl, LOCK_EX);
							$fsds_obj->mkpath('usermap', $name.DIRECTORY_SEPARATOR.$group_prefix.$gnm, 0774, true);
							$fsds_obj->mkpath('usermsg', $group_prefix.$gnm, 0774, true);
							//
							if(count($grpconray) > 0) {
								foreach ($grpconray as $key => $value) {
									// $cfl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$value.'.u';
									$cresnm = $value.'.u';
									// $cdtl = file_get_contents($cfl);
									$cdtl = $fsds_obj->get('users', $cresnm);
									$cdtls = array();
									if(trim($cdtl) != '') {
										$cdtls = @ json_decode($cdtl, 1);
										if(!is_array($cdtls)) { $cdtls = array(); }
									}
									if(!isset($cdtls['grp']) || !is_array($cdtls['grp'])) { $cdtls['grp'] = array(); }
									if(!isset($cdtls['grpr']) || !is_array($cdtls['grpr'])) { $cdtls['grpr'] = array(); }
									if(isset($cdtls['grp'][$gkv]) && $cdtls['grp'][$gkv] == $grpnm) {
										// $cdtls['grp'][$gkv] = $grpnm;
									} else {
										if(isset($cdtls['grpr'][$gkv]) && $cdtls['grpr'][$gkv] == $grpnm) {
											//
										} else {
											$cdtls['grpr'][$gkv] = $grpnm;
										}
									}
									/*if(isset($cdtls['grpr'][$gkv]) && $cdtls['grpr'][$gkv] == $grpnm) {
										unset($cdtls['grpr'][$gkv]);
									}*/
									// file_put_contents($cfl, json_encode($cdtls), LOCK_EX);
									$fsds_obj->put('users', $cresnm, json_encode($cdtls), LOCK_EX);
								}
							}
							$udtls['grp'][$gkv] = $grpnm;
							// echo 'success'; exit;
						}
						//
					}
				}
				$udtl = json_encode($udtls);
				// file_put_contents($fl, $udtl, LOCK_EX);
				$fsds_obj->put('users', $uresnm, $udtl, LOCK_EX);
				$return_val = 'success';
			}
		}
		return $return_val;
	}
}
?>