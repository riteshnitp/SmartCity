<?php

include_once('common.php');

/**
 * Class to combine and load css and js files 
*/
Class CJLoad
{
	var $css_path = "";
	var $js_path = "";
	var $css_code = "";
	var $js_code = "";
	var $css_files = array();
	var $js_files = array();
	var $js = "";
	var $css = "";

	function __construct()
	{
		global $site_path;
		$this->js_path = $site_path . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR;
		$this->css_path = $site_path . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;
	}

	function addCss($files)
	{		
		if(is_array($files)) {
			$len = count($files);
			for($ln = 0; $ln < $len; $ln++) {
				$file = $this->css_path . $files[$ln];
				if(is_file($file) && file_exists($file)) {
					$this->css_files[] .= $file;
				}
			}
		} else if(is_string($files)) {
			$file = $this->css_path . $files;
			if(is_file($file) && file_exists($file)) {
				$this->css_files[] = $file;
			}
		}
	}

	function addJs($files)
	{
		if(is_array($files)) {
			$len = count($files);
			for($ln = 0; $ln < $len; $ln++) {
				$file = $this->js_path . $files[$ln];
				if(is_file($file) && file_exists($file)) {
					$this->js_files[] = $file;
				}
			}
		} else if(is_string($files)) {
			$file = $this->js_path . $files;
			if(is_file($file) && file_exists($file)) {
				$this->js_files[] = $file;
			}
		}
	}

	function loadCss()
	{
		global $site_path;
		if($debug_mode == 1 || !file_exists($site_path . DIRECTORY_SEPARATOR . 'js' .DIRECTORY_SEPARATOR . 'jscript.js')) {
			if(is_array($this->css_files)) {
				foreach($this->css_files as $k => $v) {
					$this->css_code .= @ file_get_content($v);
				}
			}
			// $this->css_code = str_replace(array('\r','\n','\r\n',"\t","\s\s",'  '), array(' '), $this->css_code);
			$this->css_code = preg_replace('/[\r|\n|\t|\s|\r\n]+/', ' ', $this->css_code);
			@ file_put_contents($site_path.'css/cstyle.css', $this->css_code, LOCK_EX);
			@ chmod($site_path.'css/cstyle.css', 0774);
		}
		$this->css_code = '';
		$this->css = '<link rel="stylesheet" href="css/cstyle.css" type="text/css" />';
	}

	function loadJs($async=0, $min=0)
	{
		global $site_path, $debug_mode;
		if($debug_mode == 1 || !file_exists($site_path . DIRECTORY_SEPARATOR . 'js' .DIRECTORY_SEPARATOR . 'jscript.js')) {
			if(is_array($this->js_files)) {
				foreach($this->js_files as $k => $v) {
					$this->js_code .= file_get_contents($v);
				}
			}
			// $this->css_code = str_replace(array('\r','\n','\r\n',"\t","\s\s",'  '), array(' '), $this->css_code);
			// $this->js_code = preg_replace('/[\r|\n|\t|\s|\r\n]+/', ' ', $this->js_code);
			@ file_put_contents($site_path.'js/jscript.js', $this->js_code, LOCK_EX);
			@ chmod($site_path.'js/jscript.js', 0774);
		}
		$this->js_code = '';
		if($async) { $async = 'async="async"'; } else { $async = ''; }
		if($min) { $min = '.min'; } else { $min = ''; }
		echo $this->js = '<script type="text/javascript" src="js/jscript'.$min.'.js" '.$async.' ></script>';
	}

	function __destruct()
	{
		//
	}

}

?>