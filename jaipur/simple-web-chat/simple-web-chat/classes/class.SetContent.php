<?php
/**
 * Common class to set file content
*/
class SetContent
{

	/**
	 * function to set file content
	 * @param filepath path to file
     * @param content content to set
	*/
	function setContentToFile($filepath, $content)
	{
        @ chmod($filepath, 0774);
		file_put_contents($filepath, $content);
        @ chmod($filepath, 0774);
	}
	
    /**
	 * function to append content to file
	 * @param filepath path to file
     * @param content content to append
	*/
	function appendContentToFile($filepath, $content)
	{
        @ chmod($filepath, 0774);
		file_put_contents($filepath, $content, FILE_APPEND | LOCK_EX);
        @ chmod($filepath, 0774);
	}
    
}
?>