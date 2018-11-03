#!/usr/bin/php -q

<?php
error_reporting(E_ALL);

include_once 'common.php';
include_once 'classes/class.Process.php';
require_once 'server/Server.class.php';
require_once 'server/Client.class.php';

set_time_limit(0);
// date_default_timezone_set('Asia/Calcutta');

// variables
// $address = '127.0.0.1';
$verboseMode = true;
// $rootpath = '/var/www';
$server = new Server($address, $port, $verboseMode);
$server->run();
// command to start server: sudo -u www-data php -q chat.php
?>