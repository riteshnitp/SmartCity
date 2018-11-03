<?php
/**
 * Common class to set online flag of user
*/
class Online
{

	/**
	 * function to set online flag of user
	 * @param filepath path to file
	*/
	function setOnline($filepath)
	{
		$mtime = @ filemtime($filepath);
        if(! file_exists($filepath) || ($mtime && $mtime < strtotime("-30 seconds"))) {
            file_put_contents($filepath, json_encode(array('lastseen'=>gmdate('Y-m-d H:i:s'))));
            @ chmod($filepath, 0774);
        }
	}
	
}
?>