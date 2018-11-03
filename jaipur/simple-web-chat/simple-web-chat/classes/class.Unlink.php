<?php
/**
 * Common class to unlink files
*/
class Unlink
{

	/**
	 * function unlink file
	 * @param filepath path of file to remove
	 * @param rel flag to determine for removal of related files
	*/
	function removeFile($filepath, $rel=0)
	{
		// @ chmod($filepath, 0774);
		$rs = 0;
		$op = array();
		$dirname = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR;
		if(is_array($filepath)) {
			$i = 0;
			$flnm = '';
			foreach($filepath as $val) {
				if(file_exists($val) && strpos($val, $dirname.'tmp'.DIRECTORY_SEPARATOR) === 0) {
					// @ unlink($val);
					$flnm .= ' '.$val;
					// $rs = @ exec('rm '.$val.' > /dev/null &', $op);
					if($rel == 1) {
						$flnm .= ' '.dirname($val).DIRECTORY_SEPARATOR.'*_'.basename($val);
						// $rs = @ exec('rm '.dirname($val).DIRECTORY_SEPARATOR.'*_'.basename($val).' > /dev/null &', $op);
					}
				}
				if(trim($flnm) != '' && $i % 1000 == 0) {
					$rs = @ exec('rm '.$flnm.' > /dev/null &', $op);
					$flnm = '';
				}
			}
			if(trim($flnm) != '') {
				$rs = @ exec('rm '.$flnm.' > /dev/null &', $op);
			}
		} else {
			if(strpos($filepath, $dirname.'tmp'.DIRECTORY_SEPARATOR) === 0) {
				@ unlink($filepath);
				if($rel == 1) {
					$rs = @ exec('rm '.dirname($filepath).DIRECTORY_SEPARATOR.'*_'.basename($filepath));
				}
			}
		}
		
	}
	
}

?>