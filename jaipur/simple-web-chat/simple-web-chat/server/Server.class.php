<?php
/**
 * websocket server class
 */
class Server
{

	/**
	 * The address of the server
	 * @var String
	 */
	private $address;

	/**
	 * The port for the master socket
	 * @var int
	 */
	private $port;

	/**
	 * The master socket
	 * @var Resource
	 */
	private $master;

	/**
	 * The array of sockets (1 socket = 1 client)
	 * @var Array of resource
	 */
	private $sockets;

	/**
	 * The array of connected clients
	 * @var Array of clients
	 */
	private $clients;

	/**
	 * If true, the server will print messages to the terminal
	 * @var Boolean
	 */
	private $verboseMode;

	/**
	 * Server constructor
	 * @param $address The address IP or hostname of the server (default: 127.0.0.1).
	 * @param $port The port for the master socket (default: 5001)
	 */
	function Server($address = '127.0.0.1', $port = 5001, $verboseMode = false, $rootpath = '')
	{
		global $site_path, $site_url, $smileys, $group_prefix, $name_sep, $manage_tmp_files, $fsds_obj;

		$this->console("Server starting...");
		$this->address = $address;
		$this->port = $port;
		$this->verboseMode = $verboseMode;
		$this->https_on = false;

		// setting config value here
		/*if(!isset($_SERVER['SERVER_NAME']) || trim($_SERVER['SERVER_NAME']) == '') {
			$_SERVER['SERVER_NAME'] = $address;
		}
		if(!isset($_SERVER['DOCUMENT_ROOT']) || trim($_SERVER['DOCUMENT_ROOT']) == '') {
			$_SERVER['DOCUMENT_ROOT'] = $rootpath;
		}*/
		// $site = $_SERVER['SERVER_NAME'];
		// $this->site = (isset($_SERVER['HTTPS']) || $this->https_on)? 'https://'.$site : 'http://'.$site;
		// $site_uri = $_SERVER['SERVER_NAME'].str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname($_SERVER['PHP_SELF'])).DIRECTORY_SEPARATOR;
		$this->site_url = $site_url; 	// (isset($_SERVER['HTTPS']) || $this->https_on)? 'https://'.$site_uri : 'http://'.$site_uri;
		// $this->site_path = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']).DIRECTORY_SEPARATOR;
		$this->site_path = $site_path; 	// dirname($_SERVER['PHP_SELF']).DIRECTORY_SEPARATOR;
		// smileys
		$this->smileys = $smileys; 	// array('smile.png' => '[:)]', 'wink.png' => '[;)]', 'tongue.png' => '[:p]', 'laugh.png' => '[:d]', 'happy.png' => '[:h]', 'sad.png' => '[:(]', 'oh.png' => '[:o]', 'cry.png' => '[;(]', 'worried.png' => '[:w]', 'speechless.png' => '[:|]', 'blush.png' => '[:b]', 'nerd.png' => '[:n]', 'style.png' => '[:s]');
		// group prefix
		$this->group_prefix = $group_prefix; 	// 'g~';
		$this->name_sep = $name_sep;
		$this->manage_tmp_files = $manage_tmp_files;
		// config values defined
		
		// background process generation class object
		$this->pr_obj = new Process();

		// file system data storage
		$this->fsds_obj = $fsds_obj;

		// socket creation
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

		if (!is_resource($socket))
			$this->console("socket_create() failed: ".socket_strerror(socket_last_error()), true);

		if (!socket_bind($socket, $this->address, $this->port))
			$this->console("socket_bind() failed: ".socket_strerror(socket_last_error()), true);

		if(!socket_listen($socket, 20))
			$this->console("socket_listen() failed: ".socket_strerror(socket_last_error()), true);
		$this->master = $socket;
		$this->sockets = array($socket);
		$this->console("Server started on {$this->address}:{$this->port}");
	}

	/**
	 * Create a client object with its associated socket
	 * @param $socket
	 */
	private function connect($socket) {
		$this->console("Creating client...");
		$client = new Client(uniqid(), $socket);
		$this->clients[] = $client;
		$this->sockets[] = $socket;
		$this->console("Client #{$client->getId()} is successfully created!");
	}

	/**
	 * Do the handshaking between client and server
	 * @param $client
	 * @param $headers
	 */
	private function handshake($client, $headers) {
		$this->console("Getting client WebSocket version...");
		if(preg_match("/Sec-WebSocket-Version: (.*)\r\n/", $headers, $match))
			$version = $match[1];
		else {
			$this->console("The client doesn't support WebSocket");
			return false;
		}

		$this->console("Client WebSocket version is {$version}, (required: 13)");
		if($version == 13) {
			// Extract header variables
			$this->console("Getting headers...");
			if(preg_match("/GET (.*) HTTP/", $headers, $match))
				$root = $match[1];
			if(preg_match("/Host: (.*)\r\n/", $headers, $match))
				$host = $match[1];
			if(preg_match("/Origin: (.*)\r\n/", $headers, $match))
				$origin = $match[1];
			if(preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $match))
				$key = $match[1];

			$this->console("Client headers are:");
			$this->console("\t- Root: ".$root);
			$this->console("\t- Host: ".$host);
			$this->console("\t- Origin: ".$origin);
			$this->console("\t- Sec-WebSocket-Key: ".$key);

			$this->console("Generating Sec-WebSocket-Accept key...");
			$acceptKey = $key.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
			$acceptKey = base64_encode(sha1($acceptKey, true));

			$upgrade = "HTTP/1.1 101 Switching Protocols\r\n".
					   "Upgrade: websocket\r\n".
					   "Connection: Upgrade\r\n".
					   "Sec-WebSocket-Accept: $acceptKey".
					   "\r\n\r\n";

			$this->console("Sending this response to the client #{$client->getId()}:\r\n".$upgrade);
			socket_write($client->getSocket(), $upgrade);
			$client->setHandshake(true);
			$this->console("Handshake is successfully done!");
			return true;
		}
		else {
			$this->console("WebSocket version 13 required (the client supports version {$version})");
			return false;
		}
	}

	/**
	 * Disconnect a client and close the connection
	 * @param $socket
	 */
	private function disconnect($client) {
		$this->console("Disconnecting client #{$client->getId()}");
		$i = array_search($client, $this->clients);
		$j = array_search($client->getSocket(), $this->sockets);

		if($j >= 0) {
			array_splice($this->sockets, $j, 1);
			socket_shutdown($client->getSocket(), 2);
			socket_close($client->getSocket());
			$this->console("Socket closed");
		}

		if($i >= 0)
			array_splice($this->clients, $i, 1);
		$this->console("Client #{$client->getId()} disconnected");

		//
		$this->console("Killing a child process");
		posix_kill($client->getPid(), SIGTERM); 	// 9
		// system('kill -9 '. $client->getPid());
		$this->console("Process {$client->getPid()} is killed!");
	}

	/**
	 * Get the client associated with the socket
	 * @param $socket
	 * @return A client object if found, if not false
	 */
	private function getClientBySocket($socket) {
		foreach($this->clients as $client)
			if($client->getSocket() == $socket) {
				$this->console("Client found");
				return $client;
			}
		return false;
	}

	/**
	 * Do an action
	 * @param $client
	 * @param $action
	 */
	private function action($client, $action) {
		// $action = $this->unmask($action);
		// $this->console("Performing action: ".$action);
		if(trim($action) == '') { return false; }
		$flnm = uniqid(true).'.mt';
		$sm_vals = array_values($this->smileys);
		$sm_keys = array_keys($this->smileys);
		$sm_keys = array_map(function($val) { return $val = '<img title="'.(substr($val,0,strpos($val,'.'))).'" src="images/smileys/'.$val.'" />'; }, $sm_keys);
		$action = str_replace($sm_vals, $sm_keys, $action);
		//
		$cust_imgs = scandir($this->site_path.'images/custom/');
		$custom_ikeys = $custom_ivals = array();
		foreach($cust_imgs as $k => $v) {
			if(!in_array($v, array('.','..','chompy.gif'))) {
				if(strpos($v,'.') !== false) {
					$vl = substr($v,0, strpos($v,'.'));
				}
				$custom_ikeys[] = "[($vl)]";
				$custom_ivals[] = '<img class="smileys" src="images/custom/'.$v.'" title="'.$vl.'" alt="[('.$vl.')]" style="background:#a0a0a0;" />';
			}
		}
		$action = str_replace($custom_ikeys, $custom_ivals, $action);
		//
		$txt = str_ireplace(array("<br />\n","<br />\r","<br />\r\n", "`", "<script", "</script", "</ script", "<iframe", '<img src="" />'), array("<br />","<br />","<br />", "'", "&lt;script", "&lt;/script", "&lt;/script", "&lt;iframe",''), nl2br($action));
		$utc_time = gmdate('Y-m-d H:i:s');
		$date = gmdate('Y-m-d h:i:s a', strtotime($utc_time));
		$txt = '<div class="msg"><b>'.$client->getName().'</b> '.' <i title="UTC-Time:'.$utc_time.'">('.$date.')</i> '.'<div>'.$action.'</div></div>'; 	// $client->getName().": ".$txt;
		if($client->getOClient() == 'gen') {
			// don't shift to async call
			// file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$client->getName().'_'.$flnm, "");
			$this->fsds_obj->put('genmsg', $client->getName().$this->name_sep.$flnm, '');
			/* $args = array('site_path'=>$this->site_path, 'class'=>'SetContent', 'function'=>'setContentToFile',
							'params' => array($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.'_'.$client->getName().'_'.$flnm, "")
						);
			$prs = $this->pr_obj->genProcess($args);*/
		}
		//
		if($client->getOClient() == 'gen' && is_array($this->clients) && count($this->clients) > 0) {
			foreach($this->clients as $oclient) {
				// $this->console(json_decode(json_encode($action), 1));
				// if(trim($txt) != '' && $action != json_decode(json_encode($action), 1)) { }
				if(trim($action) != '') { 	// trim($txt) != ''
					// don't shift to async call
					// file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$oclient->getName().'_'.$flnm, "");
					$this->fsds_obj->put('genmsg', $oclient->getName().$this->name_sep.$flnm, '');
					/* $args = array('site_path'=>$this->site_path, 'class'=>'SetContent', 'function'=>'setContentToFile',
								'params' => array($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.'_'.$oclient->getName().'_'.$flnm, "")
							);
					$prs = $this->pr_obj->genProcess($args);*/
					// send data to other client
					// $this->console(trim($txt));
					// $this->send($client, $oclient, $action." <i style='float:right;'>(".$date.")</i>"); 	// $txt
					$this->send($client, $oclient, '<div class="msg"><b>'.$client->getName().'</b> '.' <i title="UTC-Time:'.$utc_time.'">('.$date.')</i> '.'<div>'.$action.'</div></div>');
				}
			}
		} else {
			// $oclient = null;
			$cname = $client->getName();
			$coname = $client->getOClient();
			$grpc = false;
			if(strpos($coname, $this->group_prefix) !== false && strpos($coname, $this->group_prefix) === 0) {
				$grpc = true;
			}
			if($grpc && strpos($coname, $this->name_sep) !== false) {
				$coname = substr($coname, 0, strpos($coname, $this->name_sep));
			}
			if(strpos($coname, $this->group_prefix) !== false && strpos($coname, $this->group_prefix) === 0) {
				$fldnm = $coname;
			} else {
				$fldnm = (strcasecmp($cname, $coname) > 0)? $coname.$this->name_sep.$cname : $cname.$this->name_sep.$coname;
			}
			$msf = false;
			// print_r($this->clients);
			foreach($this->clients as $othclient) {
				$othcnm = $othclient->getName();
				// $umd = $this->site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$othcnm.DIRECTORY_SEPARATOR;
				// if($othcnm === $coname || ($grpc && file_exists($umd.$coname) && $othcnm != $cname)) {
				if($othcnm === $coname || ($grpc && $this->fsds_obj->dirExists('usermap', $othcnm.DIRECTORY_SEPARATOR.$coname) && $othcnm != $cname)) {
					$oclient = $othclient;
					if(trim($action) != '') { 	// trim($txt) != ''
						// $this->console(trim($txt));
						if($oclient != null) {
							if(is_object($oclient)) {
								$ocnm = $oclient->getName();
							} else { $ocnm = $oclient; }
							// don't shift to async call
							// file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$cname.'_'.$flnm, "");
							$this->fsds_obj->put('usermsg', $fldnm.DIRECTORY_SEPARATOR.$cname.$this->name_sep.$flnm, '');
							/*$args = array('site_path'=>$this->site_path, 'class'=>'SetContent', 'function'=>'setContentToFile',
											'params' => array($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.'_'.$cname.'_'.$flnm, "")
										);
							$prs = $this->pr_obj->genProcess($args);*/
							//file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$coname.'_'.$flnm, "");
							// don't shift to async call
							// file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$ocnm.'_'.$flnm, "");
							$this->fsds_obj->put('usermsg', $fldnm.DIRECTORY_SEPARATOR.$ocnm.$this->name_sep.$flnm, '');
							/*$args = array('site_path'=>$this->site_path, 'class'=>'SetContent', 'function'=>'setContentToFile',
											'params' => array($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.'_'.$ocnm.'_'.$flnm, "")
										);
							$prs = $this->pr_obj->genProcess($args);*/
							//
							$this->send($client, $oclient, '<div class="msg"><b>'.$client->getName().'</b> '.' <i title="UTC-Time:'.$utc_time.'">('.$date.')</i> '.'<div>'.$action.'</div></div>', 'y'); 	// $txt
							$msf = true;
						} else {
							// $oclient = $client->getOClient();
						}
						// $this->send($oclient, $client, $client->getName().": ".$action." <i style='float:right;'>(".$date.")</i>", 'y'); 	// $txt
					}
					// file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$cname.'_'.$flnm, "");
					// if(is_object($oclient)) {
						// file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$coname.'_'.$flnm, "");
					// }
					// file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$flnm, $txt." <i style='float:right;'>(".$date.")</i><br />");
					break;
				}
			}
			// if message sent to others send to self
			if($coname != '') {
				$this->send($client, $client, '<div class="msg"><b>'.$client->getName().'</b> '.' <i title="UTC-Time:'.$utc_time.'">('.$date.')</i> '.'<div>'.$action.'</div></div>', 'y'); 	// $txt
			}
			// shift to async call
			// file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$flnm, $txt);
			if(!$msf) {
				// file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$cname.'_'.$flnm, "");
				$this->fsds_obj->put('usermsg', $fldnm.DIRECTORY_SEPARATOR.$cname.$this->name_sep.$flnm, '');
			}
			$args = array('site_path'=>$this->site_path, 'class'=>'SetContent', 'function'=>'setContentToFile',
							'params' => array($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$flnm, $txt)
						);
			$prs = $this->pr_obj->genProcess($args);
			//
		}
		//
		if(trim($txt) != '' && $client->getOClient() == 'gen') {
			// shift to async call
			// file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$flnm, $txt);
			$args = array('site_path'=>$this->site_path, 'class'=>'SetContent', 'function'=>'setContentToFile',
							'params' => array($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$flnm, $txt)
						);
			$prs = $this->pr_obj->genProcess($args);
		}
		/*if($action == "exit" || $action == "quit") {
			$this->console("Killing a child process");
			posix_kill($client->getPid(), SIGTERM);
			$this->console("Process {$client->getPid()} is killed!");
		} */
	}

	/**
	 * Run the server
	 */
	public function run() {
		$this->console("Start running...");
		while(true) {
			$changed_sockets = $this->sockets;
			@socket_select($changed_sockets, $write = NULL, $except = NULL, 1);
			foreach($changed_sockets as $socket) {
				if($socket == $this->master) {
					if(($acceptedSocket = socket_accept($this->master)) < 0) {
						$this->console("Socket error: ".socket_strerror(socket_last_error($acceptedSocket)));
					} else {
						// $this->console($acceptedSocket);
						$this->connect($acceptedSocket);
					}
				} else {
					$this->console("Finding the socket that associated to the client...");
					$client = $this->getClientBySocket($socket);
					if($client) {
						$this->console("Receiving data from the client");
						$bytes = @ socket_recv($socket, $data, 4096, MSG_DONTWAIT);
						if(!$client->getHandshake()) {
							$this->console("Doing the handshake");
							if($this->handshake($client, $data)) {
								$root = "";
								$params = array();
								if(preg_match("/GET (.*) HTTP/", $data, $match)) {
									$root = $match[1];
									if(trim($root) != '') {
										$params = @ explode('/', $root);
										$params = array_values(array_filter($params));
										$param_vals = @ explode('%', $params[0]);
										$client->setName($param_vals[0]);
										$client->setUsnid($param_vals[1]);
									}
								}
								$this->startProcess($client);
							}
						} else if($bytes === 0) {
							$this->disconnect($client);
						} else if($bytes !== false) {
							$data = $this->unmask($data);
							$data = $this->read($client, $socket, $data, 4096);
							$this->console($data);
							if(strpos($data, 'scmsg:=:') !== false && strpos($data, 'scmsg:=:') === 0) {
								$data = str_replace('scmsg:=:', '', $data);
								if(strpos($data, $this->group_prefix) !== false && strpos($data, $this->group_prefix) === 0) {
									$data = $data; 	// .$this->name_sep.$client->getName();
								}
								$client->setOClient($data);
								if($client->getOClient() == 'general' || $client->getOClient() == 'General') { $client->setOClient('gen'); }
								// create folder and files as per reply.php
							} else {
								// When received data from client
								if(trim($data) != '') {
									$this->action($client, $data);
								}
							}
						}
					}
				}
			}
			// read files
			if(isset($client)) { 	// && is_array($this->clients) && count($this->clients) > 0
				foreach($this->clients as $cky => $lclient) {
					// need to read and send msgs in parallel
					$dtls = "";
					$name = $lclient->getName(); 	// $oclient->getName();
					// one to one files
					// $umdo = $this->site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
					// if(file_exists($umdo)) {
					if($this->fsds_obj->dirExists('usermap', $name)) {
						// $uflds = scandir($umdo);
						$uflds = $this->fsds_obj->listing('usermap', $name);
						foreach($uflds as $key => $val) {
							$dtls = "";
							if(strpos($val, $this->group_prefix) !== false && strpos($val, $this->group_prefix) === 0) {
								$ci = $val;
							} else {
								$ci = trim(str_replace($name,'',$val), $this->name_sep);
							}
							// $umd = $this->site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$val.DIRECTORY_SEPARATOR;
							if(!in_array($val, array('.', '..'))) { 	// && in_array($val, $cids)
								// if(file_exists($umd) && is_dir($umd)) {
								if($this->fsds_obj->dirExists('usermsg', $val)) {
									$nms = 0;
									// $ufls = scandir($umd);
									$ufls = $this->fsds_obj->listing('usermsg', $val);
									$rufls = array_flip($ufls);
									//read
									// $ufls = glob($umd.'[!_]*', GLOB_NOSORT);
									foreach($ufls as $ky => $vl) {
										if(!in_array($vl, array('.', '..')) && strpos($vl, $this->name_sep) === false) {
											if(!isset($rufls[$name.$this->name_sep.$vl])) { 	// && !in_array($name.'_'.$vl, $ufls) 	// && strpos($vl, '_') === false  	// && in_array($vl, $cids)
												// if(is_file($umd.$vl) && file_exists($umd.$vl)) {
												// if(is_file($vl) && file_exists($vl) && !file_exists($umd.'_'.$name.'_'.basename($vl))) {
													// $cnt = file_get_contents($umd.$vl);
													$cnt = $this->fsds_obj->get('usermsg', $val.DIRECTORY_SEPARATOR.$vl);
													// $cnt = file_get_contents($vl);
													if(trim($cnt) != '') {
														// $dtls .= (trim($dtls) != '')? "<hr style='border-style:dashed;' />".file_get_contents($umd.$vl) : file_get_contents($umd.$vl); 	// [$vl]
														$dtls .= $cnt; 	// file_get_contents($umd.$vl); 	// [$vl]
													}
													// don't shift to async call
													// file_put_contents($umd.$name.'_'.basename($vl),'');
													$this->fsds_obj->put('usermsg', $val.DIRECTORY_SEPARATOR.$name.$this->name_sep.basename($vl), '');
													/*$args = array('site_path'=>$this->site_path, 'class'=>'SetContent', 'function'=>'setContentToFile',
																	'params' => array($umd.$name.'_'.basename($vl), '')
																);
													$prs = $this->pr_obj->genProcess($args);*/
													// @ chmod($umd.$name.'_'.basename($vl), 0774);
													$this->fsds_obj->chperm('usermsg', $val.DIRECTORY_SEPARATOR.$name.$this->name_sep.basename($vl), 0774);
													$nms = $nms + 1;
												// }
											}
											//
										}
										if($nms >= 10) {
											break;
										}
									}
									// delete
									if($this->manage_tmp_files == true) {
										foreach($ufls as $ky => $vl) {
											if(!in_array($vl, array('.', '..')) && strpos($vl, $this->name_sep) === false && isset($rufls[$name.$this->name_sep.$vl])) {
												// delete old files, can be shifted to cron (if using db save data into db before delete)
												// $mtime = @filemtime($umd.$vl);
												$mtime = $this->fsds_obj->lastModofied('usermsg', $val.DIRECTORY_SEPARATOR.$vl);
												$of = false;
												if($mtime && $mtime < strtotime("-1 minutes")) {
													$of = true;
												}
												//
												if($of == true) {
													// shift to async call
													// @ unlink($umd.$vl);
													// if(strpos($vl,'_') === false) {
														$args = array('site_path'=>$this->site_path, 'class'=>'Unlink', 'function'=>'removeFile', 'params' => array($this->fsds_obj->path('usermsg').$val.DIRECTORY_SEPARATOR.$vl, 1));
														$prs = $this->pr_obj->genProcess($args);
													// }
													// @ unlink($site_path.'tmp/mt/'.$name.'_'.$vl);
													// $nms = $nms+1;
												}
											}
										}
									}
									unset($ufls);
									unset($rufls);
								}
								// @ unlink($this->site_path.'tmp/mt/'.$vl);
							}
							if(trim($dtls) != '') {
								$this->send($ci, $lclient, $dtls, 'y');
								$dtls = "";
							}
						}
						unset($uflds);
					}
					// gen files
					$nms = 0;
					$dtls = "";
					$fls = $this->fsds_obj->listing('genmsg', ''); 	// scandir($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR);
					// $fls = glob($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.'[!_]*', GLOB_NOSORT);
					$rfls = array_flip($fls);
					// read
					foreach($fls as $ky => $vl) {
						if(!in_array($vl, array('.', '..')) && strpos($vl, $this->name_sep) === false) {
							// if(!$of && !in_array($name.'_'.$vl, $fls) && strpos($vl, '_') === false) {
							// if(!file_exists($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.'_'.$name.'_'.basename($vl)) && strpos(basename($vl), '_') === false) {
							if(!isset($rfls[$name.$this->name_sep.$vl])) {
								// $dtls .= (trim($dtls) != '')? "<br/>" . file_get_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl) : file_get_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
								$dtls .= (trim($dtls) != '')? "<br/>" . $this->fsds_obj->get('genmsg', $vl) : $this->fsds_obj->get('genmsg', $vl);
								// $dtls .= (trim($dtls) != '')? "<br/>".file_get_contents($vl) : file_get_contents($vl);
								// don't shift to async call
								// file_put_contents($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$name.'_'.basename($vl),'');
								$this->fsds_obj->put('genmsg', $name.$this->name_sep.basename($vl),'');
								/*$args = array('site_path'=>$this->site_path, 'class'=>'SetContent', 'function'=>'setContentToFile',
												'params' => array($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.'_'.$name.'_'.basename($vl), '')
											);
								$prs = $this->pr_obj->genProcess($args);*/
								// @ chmod($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$name.'_'.basename($vl), 0774);
								$this->fsds_obj->chperm('genmsg', $name.$this->name_sep.basename($vl), 0774);
								$nms = $nms + 1;
							}
							// @ unlink($this->site_path.'tmp/mt/'.$vl);
						}
						if($nms >= 10) {
							break;
						}
					}
					//delete
					$ftd = array();
					if($this->manage_tmp_files == true) {
						foreach($fls as $ky => $vl) {
							if(!in_array($vl, array('.', '..')) && strpos($vl, $this->name_sep) === false && isset($rfls[$name.$this->name_sep.$vl])) {
								// delete old files, can be shifted to cron (if using db save data into db before delete)
								// $mtime = @ filemtime($this->site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
								$mtime = $this->fsds_obj->lastModofied('genmsg', $vl);
								$of = false;
								if($mtime && $mtime < strtotime("-1 minutes")) {
									$of = true;
								}
								//
								if($of == true) {
									// shift to async call
									// @ unlink($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
									// if(strpos($vl,'_') === false) {
										$ftd[] = $this->fsds_obj->path('genmsg') . $vl;
										/*$args = array('site_path'=>$this->site_path, 'class'=>'Unlink', 'function'=>'removeFile', 'params' => array($this->fsds_obj->path('genmsg').$vl, 1));
										$prs = $this->pr_obj->genProcess($args);*/
									// }
									// @ unlink($site_path.'tmp/mt/'.$name.'_'.$vl);
									// $nms = $nms+1;
								}
								// @ unlink($site_path.'tmp/mt/'.$vl);
							}
						}
					}
					if(count($ftd) > 0) {
						$args = array('site_path'=>$this->site_path, 'class'=>'Unlink', 'function'=>'removeFile', 'params' => array($ftd, 1));
						$prs = $this->pr_obj->genProcess($args);
					}
					unset($fls);
					unset($rfls);
					unset($ftd);
					//
					if(trim($dtls) != '') {
						// $this->send($client, $client, $dtls, 'y');
						$this->send('gen', $lclient, $dtls, 'y');
					}
				// }
					// reg
					// if(! file_exists($this->site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$lclient->getName().'.u')) {
						// file_put_contents($this->site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$lclient->getName().'.u', json_encode(array('')));
					// }
					// online
					// shift to async call
					/*
					$mtime = @ filemtime($this->site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$lclient->getName().'.u');
					if(! file_exists($this->site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$lclient->getName().'.u') || ($mtime && $mtime < strtotime("-30 seconds"))) {
						file_put_contents($this->site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$lclient->getName().'.u', json_encode(array('lastseen'=>gmdate('Y-m-d H:i:s'))));
						@ chmod($this->site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$lclient->getName().'.u', 0774);
					}
					*/
					// $mtime = @ filemtime($this->site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$lclient->getName().'.u');
					// if(! file_exists($this->site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$lclient->getName().'.u') || ($mtime && $mtime < strtotime("-30 seconds"))) {
					if((date('i') % 2) == ($cky % 2)) {
						$args = array('site_path'=>$this->site_path, 'class'=>'Online', 'function'=>'setOnline', 'params' => array($this->fsds_obj->path('onlineusers').$lclient->getName().'.u'));
						$prs = $this->pr_obj->genProcess($args);
					}
					// }
					//
				}
				// remove offline
				/* not required here, it may slow down socket response
				$udrs = scandir($this->site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR);
				foreach($udrs as $ky => $vl) {
					if(!in_array($vl, array('.', '..'))) {
						// delete old files
						$mtime = @ filemtime($this->site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$vl);
						if($mtime && $mtime < strtotime("-1 minutes")) {
							@ unlink($this->site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$vl);
						}
					}
				}*/
				//
			}
			//
		}
	}

	/**
	* read data from socket
	*/
	function read($client, $socket, $read, $length = 4096)
	{
		// $this->method = "read";
		if(!$client) {
			$this->console("No valid socket descriptor !\n");
			return false;
		}
		// $read = '';
        // while(($flag = socket_recv($socket, $buf, $length, 0)) > 0)
        while($flag = @socket_recv($socket, $buf, $length, MSG_DONTWAIT))
		{
            // $asc = ord(substr($buf, -1));
            $buf = $this->unmask($buf);
			$read .= $buf;
			/*if ($asc==0) {
				$read .= substr($buf,0,-1);
				break;
			} else {
				$read .= $buf;
			}*/
        }
		if($flag === false) {
			return $read;
		} else if ($flag < 0) {
            // error
            return false;
        } else if ($flag === 0) {
            // Client disconnected
            return 0;
        } else {
            return $read;
        }
    }

	/**
	 * Start a child process for pushing data
	 * @param unknown_type $client
	 */
	private function startProcess($client) {
		$this->console("Start a client process");
		$pid = pcntl_fork();
		if($pid == -1) {
			die('could not fork');
		}
		elseif($pid) { // process
			$client->setPid($pid);
		}
		else {
			// we are the child
			while(true) {
				// push something to the client
				$seconds = rand(2, 5);
				// $this->send($client, json_encode("I am waiting {$seconds} seconds"));
				sleep($seconds);
			}
		}
	}

	/**
	 * Send a text to client
	 * @param $client
	 * @param $text
	 */
	private function send($client, $oclient, $text, $fo='') 
	{
		$clientname = (is_object($client))? $client->getName() : $client;
		$oclientname = (is_object($client))? $client->getOClient() : '-';
		if(is_object($client) && is_object($oclient) && $client->getName() === $oclient->getName()) {
			$t = $clientname;
			$clientname = $oclientname;
			$oclientname = $t;
		}
		if(strpos($oclientname, $this->group_prefix) !== false && strpos($oclientname, $this->group_prefix) === 0) {
			if(strpos($oclientname, $this->name_sep) !== false) {
				$oclientname = substr($oclientname, 0, strpos($oclientname, $this->name_sep));
			}
		}
		$txt = str_ireplace(array("<br />\n","<br />\r","<br />\r\n", "`", "<script", "</script", "</ script", "<iframe", '<img src="" />'), array("<br />","<br />","<br />", "'", "&lt;script", "&lt;/script", "&lt;/script", "&lt;iframe",''), nl2br($text));
		/*if($fo == '') {
			$txt = utf8_encode($clientname.": ".$txt);
		} else {*/
			$txt = utf8_encode($txt);
		// }
		//
		// $this->console("Send '".$txt."' to client #{$oclient->getId()}");
		// $jtkey = ($client->oclient == 'gen')? 'gen' : $client->oclient; 	// $client->getName()
		$op_clientname = $clientname;
		if($oclientname == 'gen') { $clientname = 'gen'; }
		else if(strpos($oclientname, $this->group_prefix) !== false && strpos($oclientname, $this->group_prefix) === 0) { $clientname = $oclientname; }
		$txt = json_encode(array($clientname => $txt));
		$text = $this->encode($txt);
		//
		if(socket_write($oclient->getSocket(), $text, strlen($text)) !== false) {
			// if msg was send do post processing like updating history
			$clientname = $op_clientname;
			if($oclientname == 'gen') {
				// clean old history
				/*if(!file_exists($this->site_path."h".DIRECTORY_SEPARATOR.$clientname)) {
					if(!is_dir($this->site_path."h".DIRECTORY_SEPARATOR.$clientname)) {
						@ mkdir($this->site_path."h".DIRECTORY_SEPARATOR.$clientname, 0774, true);
					}
				}*/
				$tm = $clientname.'-'.gmdate('Y-m', strtotime('-2 months'));
				// if(is_file($this->site_path."h".DIRECTORY_SEPARATOR.$clientname.DIRECTORY_SEPARATOR.$tm.'.html') && file_exists($this->site_path."h".DIRECTORY_SEPARATOR.$clientname.DIRECTORY_SEPARATOR.$tm.'.html')) {
				if($this->fsds_obj->exists('history', $clientname.DIRECTORY_SEPARATOR.$tm.'.html')) {
					// shift to async call
					// @ chmod($this->site_path."h".DIRECTORY_SEPARATOR.$clientname.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
					// @ unlink($this->site_path."h".DIRECTORY_SEPARATOR.$clientname.DIRECTORY_SEPARATOR.$tm.'.html');
					$args = array('site_path'=>$this->site_path, 'class'=>'Unlink', 'function'=>'removeFile',
									'params' => array($this->fsds_obj->path('history').$clientname.DIRECTORY_SEPARATOR.$tm.'.html', 0)
								);
					$prs = $this->pr_obj->genProcess($args);
					/*$mtime = @ filemtime($this->site_path."h".DIRECTORY_SEPARATOR.$clientname.'.html');
					if($mtime && (strtotime('+1 minutes') - $mtime) > strtotime('-7 days')) {
						@ chmod($this->site_path."h".DIRECTORY_SEPARATOR.$clientname.'.html', 0774);
						@ file_put_contents($this->site_path."h".DIRECTORY_SEPARATOR.$clientname.'.html', '');
						@ chmod($this->site_path."h".DIRECTORY_SEPARATOR.$clientname.'.html', 0774);
					}*/
				}
				// write to history
				$tm = $clientname.'-'.gmdate('Y-m');
				// shift to async call
				// @ file_put_contents($this->site_path."h".DIRECTORY_SEPARATOR.$clientname.DIRECTORY_SEPARATOR.$tm.'.html', $txt, FILE_APPEND); 	// | LOCK_EX 	// ."<hr style='border-style:dashed;' />"
				$args = array('site_path'=>$this->site_path, 'class'=>'SetContent', 'function'=>'appendContentToFile',
								'params' => array($this->fsds_obj->path('history').$clientname.DIRECTORY_SEPARATOR.$tm.'.html', $txt)
							);
				$prs = $this->pr_obj->genProcess($args);
				/*$fl = fopen($this->site_path."h".DIRECTORY_SEPARATOR.$clientname.DIRECTORY_SEPARATOR.$tm.'.html', "a+");
				fwrite($fl, $txt); 	// ."<hr style='border-style:dashed;' />"
				fclose($fl);*/
			} else {
				if(strpos($oclientname, $this->group_prefix) !== false && strpos($oclientname, $this->group_prefix) === 0) {
					$fldnm = $oclientname; 	// .$this->name_sep.$clientname;
				} else {
					$fldnm = (strcasecmp($clientname, $oclientname) > 0)? $oclientname.$this->name_sep.$clientname : $clientname.$this->name_sep.$oclientname;
				}
				/*if(!file_exists($this->site_path."h".DIRECTORY_SEPARATOR.'uh'.DIRECTORY_SEPARATOR.$fldnm)) {
					if(!is_dir($this->site_path."h".DIRECTORY_SEPARATOR.'uh'.DIRECTORY_SEPARATOR.$fldnm)) {
						@ mkdir($this->site_path."h".DIRECTORY_SEPARATOR.'uh'.DIRECTORY_SEPARATOR.$fldnm, 0774, true);
					}
				}*/
				// clean old history
				$tm = $fldnm.'-'.gmdate('Y-m', strtotime('-2 months'));
				// if(is_file($this->site_path."h".DIRECTORY_SEPARATOR.'uh'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$tm.'.html') && file_exists($this->site_path."h".DIRECTORY_SEPARATOR.'uh'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$tm.'.html')) {
				if($this->fsds_obj->exists('userhistory', $fldnm.DIRECTORY_SEPARATOR.$tm.'.html')) {
					// shift to async call
					// @ chmod($this->site_path."h".DIRECTORY_SEPARATOR.'uh'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
					// @ unlink($this->site_path."h".DIRECTORY_SEPARATOR.'uh'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$tm.'.html');
					$args = array('site_path'=>$this->site_path, 'class'=>'Unlink', 'function'=>'removeFile',
									'params' => array($this->fsds_obj->path('userhistory').$fldnm.DIRECTORY_SEPARATOR.$tm.'.html', 0)
								);
					$prs = $this->pr_obj->genProcess($args);
					/*$mtime = @ filemtime($this->site_path."h".DIRECTORY_SEPARATOR.$fldnm.'.html');
					if($mtime && (strtotime('+1 minutes') - $mtime) > strtotime('-7 days')) {
						@ chmod($this->site_path."h".DIRECTORY_SEPARATOR.$fldnm.'.html', 0774);
						@ file_put_contents($this->site_path."h".DIRECTORY_SEPARATOR.$fldnm.'.html', '');
						@ chmod($this->site_path."h".DIRECTORY_SEPARATOR.$fldnm.'.html', 0774);
					}*/
				}
				// write to history
				$tm = $fldnm.'-'.gmdate('Y-m');
				// shift to async call
				// @ file_put_contents($this->site_path."h".DIRECTORY_SEPARATOR.'uh'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$tm.'.html', $txt, FILE_APPEND); 	// | LOCK_EX 	// ."<hr style='border-style:dashed;' />"
				$args = array('site_path'=>$this->site_path, 'class'=>'SetContent', 'function'=>'appendContentToFile',
								'params' => array($this->fsds_obj->path('userhistory').$fldnm.DIRECTORY_SEPARATOR.$tm.'.html', $txt)
							);
				$prs = $this->pr_obj->genProcess($args);
			}
		} else {
			$this->console("Unable to write to client #{$oclient->getId()}'s socket");
			$this->disconnect($oclient);
		}
	}

	/**
	*
	* get 64 bit representation of integer.
	* @param integer $upper upper 32 bit of 64 bit
	* @param integer $lower lower 32 bit of 64 bit
	* @param integer $value integer to convert
	* @return Websocket
	*/
	protected final function get64Bit(&$upper, &$lower, $value)
	{
		$BIGINT_DIVIDER = 0x7fffffff + 1;
		$lower = intval($value % $BIGINT_DIVIDER);
		$upper = intval(($value - $lower) / $BIGINT_DIVIDER);
		return $this;
	}

	/**
	 * Encode a text for sending to clients via ws://
	 * @param $text
	 */
	private function encode($text)
	{
		// 0x1 text frame (FIN + opcode)
		$b1 = 0x80 | (0x1 & 0x0f);
		$length = strlen($text);
		// $this->console($length);

		if($length <= 125) {
			/// $this->console(125);
			$header = pack('CC', $b1, $length);
		} else if($length > 125 && $length < 65536) {
			// $this->console('125'.'-'.'65536');
			$header = pack('CCn', $b1, 126, $length);
		} elseif($length >= 65536 && $length <= pow(2, 63)) {
			// some code for 64 bit byte integer
			$upper = 0;
			$lower = 0;
			$this->get64Bit($upper, $lower, $length);
			$header = pack('CCNN', $b1, 127, $upper, $lower);
		} else if($length >= 65536) {
			// $this->console(65536);
			$header = pack('CCN', $b1, 127, $length);
		}

		return $header.$text;
	}

	/**
	* Encode a text for sending to clients via ws://
	* @param $text
	* @param $messageType
	*/
	function _encode($message, $messageType='text')
	{
		switch ($messageType) {
			case 'continuous':
				$b1 = 0;
				break;
			case 'text':
				$b1 = 1;
				break;
			case 'binary':
				$b1 = 2;
				break;
			case 'close':
				$b1 = 8;
				break;
			case 'ping':
				$b1 = 9;
				break;
			case 'pong':
				$b1 = 10;
				break;
		}
		$b1 += 128;
		$length = strlen($message);
		$lengthField = "";
		if ($length < 126) {
			$b2 = $length;
		} elseif ($length <= 65536) {
			$b2 = 126;
			$hexLength = dechex($length);
			//$this->stdout("Hex Length: $hexLength");
			if (strlen($hexLength)%2 == 1) {
				$hexLength = '0' . $hexLength;
			}
			$n = strlen($hexLength) - 2;
			for ($i = $n; $i >= 0; $i=$i-2) {
				$lengthField = chr(hexdec(substr($hexLength, $i, 2))) . $lengthField;
			}
			while (strlen($lengthField) < 2) {
				$lengthField = chr(0) . $lengthField;
			}
		} else {
			$b2 = 127;
			$hexLength = dechex($length);
			if (strlen($hexLength)%2 == 1) {
				$hexLength = '0' . $hexLength;
			}
			$n = strlen($hexLength) - 2;
			for ($i = $n; $i >= 0; $i=$i-2) {
				$lengthField = chr(hexdec(substr($hexLength, $i, 2))) . $lengthField;
			}
			while (strlen($lengthField) < 8) {
				$lengthField = chr(0) . $lengthField;
			}
		}
		return chr($b1) . chr($b2) . $lengthField . $message;
	}

	/**
	 * Unmask a received payload
	 * @param $buffer
	 */
	private function unmask($payload) {
		$length = ord($payload[1]) & 127;

		if($length == 126) {
			$masks = substr($payload, 4, 4);
			$data = substr($payload, 8);
		}
		elseif($length == 127) {
			$masks = substr($payload, 10, 4);
			$data = substr($payload, 14);
		}
		else {
			$masks = substr($payload, 2, 4);
			$data = substr($payload, 6);
		}

		$text = '';
		for ($i = 0; $i < strlen($data); ++$i) {
			$text .= $data[$i] ^ $masks[$i%4];
		}
		return $text;
	}

	/**
	 * Print a text to the terminal
	 * @param $text the text to display
	 * @param $exit if true, the process will exit
	 */
	private function console($text, $exit = false) {
		$text = gmdate('[Y-m-d H:i:s] ').$text."\r\n";
		if($exit)
			die($text);
		if($this->verboseMode)
			echo $text;
	}

	/*
	* XSS filter
	*
	* This was built from numerous sources
	* (thanks all, sorry I didn't track to credit you)
	*
	* It was tested against *most* exploits here: http://ha.ckers.org/xss.html
	* WARNING: Some weren't tested!!!
	* Those include the Actionscript and SSI samples, or any newer than Jan 2011
	*
	*
	* TO-DO: compare to SymphonyCMS filter:
	* https://github.com/symphonycms/xssfilter/blob/master/extension.driver.php
	* (Symphony's is probably faster than my hack)
	*/

	function xss_clean($data)
	{
		// Fix &entity\n;
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

		do
		{
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		while ($old_data !== $data);

		// we are done...
		return $data;
	}

}

?>