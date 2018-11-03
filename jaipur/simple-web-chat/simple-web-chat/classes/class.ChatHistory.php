<?php
/**
 * This class contains logic to display users chat history
*/
class ChatHistory
{
	/**
	 * displays user history
	 * @param name name of user
	 * @param q password of user
	*/
	function getHistory($name, $q, $t='cm')
	{
		global $site_path, $name_sep, $fsds_obj;

		$eauth = false;
		if(isset($_POST) && count($_POST) > 0 && isset($_POST['pass']) && trim($_POST['pass']) != '') {
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
				$pass = $_POST['pass'];
				if(isset($udtls['prf']) && is_array($udtls['prf']) && count($udtls['prf']) > 0 && isset($udtls['prf']['paswd']) && trim($udtls['prf']['paswd']) === $pass) {
					$tm = $q.'-'.gmdate('Y-m');
					if($t == 'lm') {
						$tm = $q.'-'.gmdate('Y-m', strtotime('-1 months'));
					}
					$cnt = '';
					if($q == 'General'.$name_sep.$name || $q == $name.$name_sep.'General') {
						// $hf = $site_path.'h'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html';
						$cnt = $fsds_obj->get('history', $name.DIRECTORY_SEPARATOR.$tm.'.html');
					} else {
						// $hf = $site_path.'h'.DIRECTORY_SEPARATOR.'uh'.DIRECTORY_SEPARATOR.$q.DIRECTORY_SEPARATOR.$tm.'.html';
						$cnt = $fsds_obj->get('userhistory', $q.DIRECTORY_SEPARATOR.$tm.'.html');
					}
					// if(file_exists($hf)) {
					if(trim($cnt) != '') {
						echo $cnt; 	// = file_get_contents($hf);
					} else {
						echo "<br /> &nbsp; No Messages Available";
					}
				}
				$eauth = true;
			}
		}
		return $eauth;
	}
}
?>