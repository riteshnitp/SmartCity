<?php
/**
 * Class to handle File System Data Storage
*/
class FSDS
{

	private $resources;

	/**
	 * constructor inits mailer object
	*/
	function __construct()
	{
		global $site_path, $site_url;
		$this->resources = array(
				'temp' => $site_path.'tmp'.DIRECTORY_SEPARATOR,
				'history' => $site_path.'h'.DIRECTORY_SEPARATOR,
				'userhistory' => $site_path.'h'.DIRECTORY_SEPARATOR.'uh'.DIRECTORY_SEPARATOR,
				'files' => $site_path.'files'.DIRECTORY_SEPARATOR,
				'pub' => $site_path.'pub'.DIRECTORY_SEPARATOR,
				'users' => $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR,
				'groups' => $site_path.'files'.DIRECTORY_SEPARATOR.'grp'.DIRECTORY_SEPARATOR,
				'userphones' => $site_path.'files'.DIRECTORY_SEPARATOR.'upn'.DIRECTORY_SEPARATOR,
				'useremails' => $site_path.'files'.DIRECTORY_SEPARATOR.'uem'.DIRECTORY_SEPARATOR,
				'tmpfs' => $site_path.'files'.DIRECTORY_SEPARATOR.'tfs'.DIRECTORY_SEPARATOR,
				'usermap' => $site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR,
				'onlineusers' => $site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR,
				'genmsg' => $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR,
				'usermsg' => $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR,
				'process' => $site_path.'tmp'.DIRECTORY_SEPARATOR.'proc'.DIRECTORY_SEPARATOR,
				'tmp' => $site_path.'tmp'.DIRECTORY_SEPARATOR.'t'.DIRECTORY_SEPARATOR,
				'img' => $site_path.'images'.DIRECTORY_SEPARATOR,
				'css' => $site_path.'css'.DIRECTORY_SEPARATOR,
				'js' => $site_path.'js'.DIRECTORY_SEPARATOR,
				'audio' => $site_path.'audio'.DIRECTORY_SEPARATOR,
				/* 'classes' => $site_path.'classes'.DIRECTORY_SEPARATOR,
				'lib' => $site_path.'lib'.DIRECTORY_SEPARATOR, */
			);
	}

	/**
	 * function to store data
	 * @param respath resource path
	 * @param datapath data path
	 * @param data data to store
	 * @param options extra options to pass
	*/
	function put($respath, $datapath, $data, $options='')
	{
		$resp = false;
		if(! isset($this->resources[$respath])) {
			return false;
		}
		if($options == '') {
			$resp = file_put_contents($this->resources[$respath].$datapath, $data);
		} else {
			$resp = file_put_contents($this->resources[$respath].$datapath, $data, $options);
		}
		return $resp;
	}

	/**
	 * function to get data
	 * @param respath resource path
	 * @param datapath data path
	 * @param options extra options to pass
	*/
	function get($respath, $datapath)
	{
		$data = '';
		if(! isset($this->resources[$respath])) {
			return $data;
		}
		if($this->exists($respath, $datapath)) {
			$data = file_get_contents($this->resources[$respath].$datapath);
		}
		return $data;
	}

	/**
	 * function to check resource exists
	 * @param respath resource path
	 * @param datapath data path
	*/
	function exists($respath, $datapath)
	{
		$resp = false;
		if(isset($this->resources[$respath])) {
			$resp = is_file($this->resources[$respath].$datapath); 	// file_exists($this->resources[$respath].$datapath) &&
		}
		return $resp;
	}

	/**
	 * function to check resource exists
	 * @param respath resource path
	 * @param datapath data path
	*/
	function dirExists($respath, $datapath)
	{
		$resp = false;
		if(isset($this->resources[$respath])) {
			$resp = is_dir($this->resources[$respath].$datapath); 	// file_exists($this->resources[$respath].$datapath) &&
		}
		return $resp;
	}
	
	/**
	 * function to check resource exists
	 * @param respath resource path
	 * @param datapath data path
	*/
	function pathAvailable($respath)
	{
		$resp = false;
		if(isset($this->resources[$respath])) {
			$resp = is_dir($this->resources[$respath]); 	// file_exists($this->resources[$respath]) &&
		}
		return $resp;
	}

	/**
	 * function to create path
	 * @param respath resource path
	 * @param path string path of resource
	 * @param perm int permission
	 * @param rec boolean recursive
	*/
	function mkpath($respath, $path, $perm='', $rec=false) 
	{
		if(isset($this->resources[$respath])) {
			if($perm != '') {
				@ mkdir($this->resources[$respath].$path, $perm, $rec);
			} else {
				@ mkdir($this->resources[$respath].$path);
			}
		}
	}

	/**
	 * function to change permission
	 * @param respath resource path
	 * @param path string path of resource
	 * @param perm int permission
	 * @param rec boolean recursive
	*/
	function chperm($respath, $path, $perm) 
	{
		if(isset($this->resources[$respath])) {
			@ chmod($this->resources[$respath].$path, $perm);
		}
	}

	/**
	 * function to get path based on resource name
	 * @param resp resource name
	*/
	function path($res) 
	{
		$path = '';
		if(isset($this->resources[$res])) {
			$path = $this->resources[$res];
		}
		return $path;
	}

	/**
	 * function to get list of resources on given path
	 * @param respath resource path
	 * @param path string path of resource
	*/
	function listing($respath, $path) 
	{
		$resp = array();
		if(! isset($this->resources[$respath])) {
			return $resp;
		}
		if($this->dirExists($respath, $path)) {
			$resp = scandir($this->resources[$respath].$path);
		}
		return $resp;
	}

	/**
	 * function to search resources at a given path
	 * @param respath resource path
	 * @param path string path of resource
	 * @param query string query to search
	*/
	function search($respath, $path, $query) 
	{
		$op = array();
        if(! isset($this->resources[$respath]) || trim($query) == '') {
			return $op;
		}
        if(strpos($_SERVER['SERVER_SIGNATURE'], '(Win') !== false) {
            $rtn = exec('dir /B '.$this->resources[$respath].$path.' | findstr '.$query, $op, $rv);
        } else {
            $rtn = exec('find \''.$this->resources[$respath].$path.'\' -name '.escapeshellarg($query), $op, $rv);
        }
        return $op;
	}

	/**
	 * function to get last modified time of data resource
	 * @param respath resource path
	 * @param path string path of resource
	*/
	function lastModified($respath, $path)
	{
		if(! isset($this->resources[$respath])) {
			return false;
		}
		if(file_exists($this->resources[$respath].$path)) {
			return filemtime($this->resources[$respath].$path);
		}
		return false;
	}

	/**
	 * function to delete resource
	 * @param respath resource path
	 * @param path string path of resource
	*/
	function del($respath, $path) 
	{
		if(isset($this->resources[$respath])) {
			@ unlink($this->resources[$respath].$path);
		}
	}

	// grep text path

}

?>
