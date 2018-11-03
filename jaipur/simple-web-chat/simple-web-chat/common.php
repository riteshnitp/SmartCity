<?php
/**
 * common site values
*/
date_default_timezone_set('UTC');
// date_default_timezone_set('Asia/Calcutta');
$address = '127.0.0.1';
$site = (isset($_SERVER['SERVER_NAME']))? $_SERVER['SERVER_NAME'] : $address;
$site = (isset($_SERVER['HTTPS']))? 'https://'.$site : 'http://'.$site;
$site_path = '/var/www/html/local/webchat';
//
if(DIRECTORY_SEPARATOR == '\\') {
   $_SERVER['DOCUMENT_ROOT'] = str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT']);
   $_SERVER['PHP_SELF'] = str_replace('/', '\\', $_SERVER['PHP_SELF']);
}
if(isset($_SERVER['HTTP_HOST'])) {
   $site_uri = $_SERVER['HTTP_HOST'].dirname(str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['PHP_SELF'])).'/'; 	// SERVER_NAME
   $site_url = (isset($_SERVER['HTTPS']))? 'https://'.$site_uri : 'http://'.$site_uri;
   $site_path = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).dirname($_SERVER['PHP_SELF']).DIRECTORY_SEPARATOR;
} else {
   $site_path = dirname(__FILE__).DIRECTORY_SEPARATOR;
   $site_uri = $address.dirname(str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['PHP_SELF'])).'/'; 	// SERVER_NAME
   $site_url = (isset($_SERVER['HTTPS']))? 'https://'.$site_uri : 'http://'.$site_uri;
}
// an array to define text codes for default smileys
$smileys = array('smile.png' => '[:)]', 'wink.png' => '[;)]', 'tongue.png' => '[:p]', 'laugh.png' => '[:d]', 'happy.png' => '[:h]', 'sad.png' => '[:(]', 'oh.png' => '[:o]', 'cry.png' => '[;(]', 'worried.png' => '[:w]', 'speechless.png' => '[:|]', 'blush.png' => '[:b]', 'nerd.png' => '[:n]', 'style.png' => '[:s]');
//
$debug_mode = 0;
$manage_tmp_files = false; 	// make sure files in tmp paths are deleted at appropriate interval
$port = '11171'; 	// web socket server port number
$long_polling_inerval = 20000000;   // 10 seconds
$group_prefix = 'g~';
$name_sep = '_';
$group_sep = '-';
//
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.FSDS.php');
$fsds_obj = new FSDS();
//
?>