<?php
// include required class
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Process.php');
//
/**
 * This class contains logic to fetch new messages
*/
class Messages
{
	/**
	 * Fetch new messages
	 * @param name name of user
	 * @param type type of request / response (AJAX or SSE)
	*/
	function fetchMessages($name, $type)
	{
		global $site_path, $group_prefix, $name_sep, $long_polling_inerval, $manage_tmp_files, $fsds_obj;

		$dtls = array('gen'=>'');
		$hdtl = array('gen'=>'');
		$fdel = array();
		$time = 0;
		// $umdo = $site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
		$pr_obj = new Process();
		// increase data per one cycle and if data obtained don't go for next cycle && also move delete calls to sep function which will be called only once when loop is completed
		// also change flag file creation from php function to system command
		while(count(array_filter($dtls)) < 1 && $time < $long_polling_inerval) 	// trim($dtls['gen']) == '' ||
		{
			// private messages
			// if(file_exists($umdo)) {
			if($fsds_obj->dirExists('usermap', $name)) {
				// $uflds = scandir($umdo);
				$uflds = $fsds_obj->listing('usermap', $name);
				foreach($uflds as $key => $val) {
					if(strpos($val, $group_prefix) !== false && strpos($val, $group_prefix) === 0) {
						// $ci = substr($val, strpos($val,':')+1, strpos($val,$name_sep)-2);
						$ci = $val;
					} else {
						$ci = trim(str_replace($name,'',$val), $name_sep);
					}
					// $umd = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$val.DIRECTORY_SEPARATOR;
					if(!in_array($val, array('.', '..'))) { 	// && in_array($val, $cids)
						// if(file_exists($umd) && is_dir($umd)) {
						if($fsds_obj->dirExists('usermsg', $val)) {
							$nms = 0;
							$dlp = 0;
							// $ufls = scandir($umd);
							$ufls = $fsds_obj->listing('usermsg', $val);
							$rufls = array_flip($ufls);
							// $rdfls = array();
							// read
							foreach($ufls as $ky => $vl) {
								if(!in_array($vl, array('.', '..')) && strpos($vl, $name_sep) === false) {
									if($manage_tmp_files == true && isset($rufls[$name.$name_sep.$vl])) {
										// if($dlp < 10) {
											// $mtime = @filemtime($umd.$vl);
											$mtime = $fsds_obj->lastModified('usermsg', $val.DIRECTORY_SEPARATOR.$vl);
											$of = false;
											if($mtime && $mtime < strtotime("-1 minutes")) {
												$of = true;
											}
											//
											if($of == true) {
												$fdel[] = $fsds_obj->path('usermsg').$val.DIRECTORY_SEPARATOR.$vl;
												$dlp++;
											}
										// }
									} else if(!isset($rufls[$name.$name_sep.$vl])) { 	// && in_array($vl, $cids)
										// if(is_file($umd.$vl) && file_exists($umd.$vl)) {
											// $cnt = file_get_contents($umd.$vl);
											$cnt = $fsds_obj->get('usermsg', $val.DIRECTORY_SEPARATOR.$vl);
											if(trim($cnt) != '') {
												$dtls[$ci] = (isset($dtls[$ci]))? $dtls[$ci] : ''; 	// [$vl]
												// $tmc = (trim($dtls[$ci]) != '')? "<hr style='border-style:dashed;' />".file_get_contents($umd.$vl) : file_get_contents($umd.$vl);
												// $tmc = file_get_contents($umd.$vl);
												$dtls[$ci] .= $cnt; 	// $tmc; 	// [$vl]
												// if(!in_array($ci.'_'.$vl, $ufls) && !file_exists($umd.$ci.'_'.$vl)) {
												if(!isset($rufls[$ci.$name_sep.$vl])) { 	// && !file_exists($umd.$ci.'_'.$vl)
													$hdtl[$ci] = (isset($hdtl[$ci]))? $hdtl[$ci] : ''; 	// [$vl]
													$hdtl[$ci] .= $cnt; 	// $tmc;
												}
											}
											// don't shift to async call
											// file_put_contents($umd.$name.'_'.$vl,'');
											$fsds_obj->put('usermsg', $val.DIRECTORY_SEPARATOR.$name.$name_sep.$vl, '');
											//@ chmod($umd.$name.'_'.$vl, 0774);
											// $args = array('site_path'=>$site_path, 'class'=>'SetContent', 'function'=>'setContentToFile', 'params' => array($umd.$name.'_'.$vl, ''));
											// $prs = $pr_obj->genProcess($args);
											$nms = $nms+1;
										// }
										// $rdfls[] = $vl;
									}
								}
								// create flag file syncly once reads are completed and before leaving for next loop
								if($nms >= 10000) {
									break;
								}
							}
							// delete, shift code to last
							/* foreach($ufls as $ky => $vl) {
								if(!in_array($vl, array('.', '..')) && strpos($vl,'_') === false && isset($rufls[$name.'_'.$vl])) {
									// delete old files, can be shifted to cron (if using db save data into db before delete)
									$mtime = @filemtime($umd.$vl);
									$of = false;
									if($mtime && $mtime < strtotime("-1 minutes")) {
										$of = true;
									}
									//
									if($of == true) { */
										// shift to async call
										// @ unlink($umd.$vl);
										// if(strpos($vl,'_') === false) {
											/* $fdel = array_merge($umd.$vl); */
											// $args = array('site_path'=>$site_path, 'class'=>'Unlink', 'function'=>'removeFile', 'params' => array($umd.$vl, 1));
											// $prs = $pr_obj->genProcess($args);
										// }
										// @ unlink($site_path.'tmp/mt/'.$name.'_'.$vl);
										// $nms = $nms+1;
									/* }
								}
							} */
							unset($ufls);
							unset($rufls);
						}
						// @ unlink($site_path.'tmp/mt/'.$vl);
					}
				}
				unset($uflds);
			}
			// general messages
			// $fls = scandir($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR);
			$fls = $fsds_obj->listing('genmsg', '');
			$rfls = array_flip($fls);
			// $rdfls = array();
			$nms = 0;
			$dlp = 0;
			// read
			foreach($fls as $ky => $vl) {
				if(!in_array($vl, array('.', '..')) && strpos($vl,$name_sep) === false) {
					if($manage_tmp_files == true && isset($rfls[$name.$name_sep.$vl])) {
						// if($dlp < 10) {
							// $mtime = @filemtime($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
							$mtime = $fsds_obj->lastModified('genmsg', $vl);
							$of = false;
							if($mtime && $mtime < strtotime("-1 minutes")) {
								$of = true;
							}
							//
							if($of == true) {
								// $fdel[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl;
								$fdel[] = $fsds_obj->path('genmsg').$vl;
								$dlp ++;
							}
						// }
					} else if(!isset($rfls[$name.$name_sep.$vl])) {
						// echo $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl;
						// if(is_file($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl) && file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl)) {
							// $cnt = file_get_contents($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
							$cnt = $fsds_obj->get('genmsg', $vl);
							if(trim($cnt) != '') {
								// $tmc = (trim($dtls['gen']) != '')? "<hr style='border-style:dashed;' />".file_get_contents($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl) : file_get_contents($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
								// $tmc = file_get_contents($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
								$dtls['gen'] .= $cnt; 	// $tmc;
								$hdtl['gen'] .= $cnt; 	// $tmc;
							}
							// don't shift to async call
							// file_put_contents($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$name.'_'.$vl, '');
							$fsds_obj->put('genmsg', $name.$name_sep.$vl, '');
							//@ chmod($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$name.'_'.$vl, 0774);
							// $args = array('site_path'=>$site_path, 'class'=>'SetContent', 'function'=>'setContentToFile', 'params' => array($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$name.'_'.$vl, ''));
							// $prs = $pr_obj->genProcess($args);
							$nms = $nms+1;
						// }
					}
					// @ unlink($site_path.'tmp/mt/'.$vl);
					// $rdfls[] = $vl;
				}
				if($nms >= 10000) {
					break;
				}
			}
			// delete, shift code to last
			/* foreach($fls as $ky => $vl) {
				if(!in_array($vl, array('.', '..')) && strpos($vl,'_') === false && isset($rfls[$name.'_'.$vl])) {
					// delete old files, can be shifted to cron (if using db save data into db before delete)
					$mtime = @filemtime($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
					$of = false;
					if($mtime && $mtime < strtotime("-1 minutes")) {
						$of = true;
					}
					//
					if($of == true) { */
						// shift to async call
						// @ unlink($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
						// if(strpos($vl,'_') === false) {
							/* $fdel = array_merge($fdel, $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl); */
							// $args = array('site_path'=>$site_path, 'class'=>'Unlink', 'function'=>'removeFile', 'params' => array($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl, 1));
							// $prs = $pr_obj->genProcess($args);
						// }
						// @ unlink($site_path.'tmp/mt/'.$name.'_'.$vl);
						// $nms = $nms+1;
					/* }
					// @ unlink($site_path.'tmp/mt/'.$vl);
				}
			} */
			unset($fls);
			unset($rfls);
			// unset($rdfls);
			// here make change such that old fles will be moved to new location and than deleted at once ? chk1s x
			if(count(array_filter($dtls)) > 0) { 	// trim($dtls['gen']) != '' ||
				// $dtls = utf8_encode($dtls);
				$dtls = array_map('utf8_encode', $dtls);
				break;
			}
			usleep(500000); 			// 500000
			$time = $time + 500000; 	// 500000
			// break;
		}
		// print_r($fdel);
		// echo "-------";
		// print_r($dtls); exit;
		$dtls = array_filter($dtls);
		$this->sendMessage(time(), $dtls, $type);
		// delete old files, instead of deleting directly trigger a continuous process if not running which will perform this task regularly based on resource usage.
		if($manage_tmp_files == true) {
			$args = array('site_path'=>$site_path, 'class'=>'Unlink', 'function'=>'removeFile', 'params' => array($fdel, 1));
			$prs = $pr_obj->genProcess($args);
		}
		// shift to async call
		// $this->afterMessageFetch($name, $hdtl); 	// $dtls
		$args = array('site_path'=>$site_path, 'class'=>'AfterMsgFetch', 'function'=>'afterMessageFetch', 'params' => array($site_path, $group_prefix, $name_sep, $name, $hdtl));
		$rs = $pr_obj->genProcess($args);
		return true;
	}

	/**
	 * Send messages to user
	 * @param lid latest unique identification token
	 * @param dtls message text
	 * @param typ type of request / response (AJAX or SSE)
	*/
	function sendMessage($lid, $dtls, $typ='ajx')  //
	{
		$retry = 500; 	// 1000
		if($typ == 'sse') {
			ob_clean();
			header("Connection: keep-alive");
			header('Content-Type: text/event-stream');
			// recommended to prevent caching of event data.
			header("pragma: no-cache,no-store");
			header('Cache-Control: no-cache,no-store,must-revalidate,max-age=0,max-stale=0');
			header("Expires: Sun, 31 Jan 2010 10:10:10 GMT");
			//
			echo "retry: ".$retry . "\r\n";
			echo "id: $lid" . "\r\n";
			echo "data: " . json_encode($dtls) . "\r\n";
			echo "\r\n";
			//
			ob_flush();
			flush();
		} else {
			ob_clean();
			echo json_encode(array('lastEventId' => $lid, 'data' => $dtls)); 	// JSON_FORCE_OBJECT
			//
			ob_flush();
			flush();
		}
		// return '';
	}

	/**
	 * Not used any more
	 * Some extra processing after fetching messages
	 * not used any more, instead async call is made to a class with similar name
	 * @param name name of user
	 * @param dtls message text
	*/
	function afterMessageFetch($name, &$dtls)
	{
		// shift to async call
		global $site_path, $group_prefix, $name_sep;

		$umdo = $site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
		// clean old history files
		$tm = $name.'-'.gmdate('Y-m', strtotime('-2 months'));
		if(is_file($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html') && file_exists($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html')) {
			$mtime = @filemtime($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html');
			@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
			@ unlink($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html');
			/*if($mtime && (strtotime('+1 minutes') - $mtime) > strtotime('-7 days')) {
				@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
				@ file_put_contents($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', '');
				@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
			}*/
		}
		// removing old history files
		if(file_exists($umdo)) {
			$uflds = scandir($umdo);
			foreach($uflds as $key => $val) {
				/*if(!file_exists($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val)) {
					if(!is_dir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val)) {
						@ mkdir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val, 0774, true);
					}
				}*/
				$tm = $val.'-'.gmdate('Y-m', strtotime('-2 months'));
				if(!in_array($val, array('.', '..'))) { 	// && in_array($val, $cids)
					if(is_file($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.DIRECTORY_SEPARATOR.$tm.'.html')) { 	// && file_exists($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.DIRECTORY_SEPARATOR.$tm.'.html')
						@ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
						@ unlink($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.DIRECTORY_SEPARATOR.$tm.'.html');
						/*$mtime = @filemtime($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html');
						if($mtime && (strtotime('+1 minutes') - $mtime) > strtotime('-7 days')) {
							@ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html', 0774);
							@ file_put_contents($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html', '');
							@ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html', 0774);
						}*/
					}
					//
				}
			}
		}
		$tm = $name.'-'.gmdate('Y-m');
		//write to history file
		if(trim($dtls['gen']) != '') { 	// && $fl = @fopen($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', "a+")
			if(!file_exists($site_path."h".DIRECTORY_SEPARATOR.$name)) {
				if(!is_dir($site_path."h".DIRECTORY_SEPARATOR.$name)) {
					@ mkdir($site_path."h".DIRECTORY_SEPARATOR.$name, 0774, true);
					@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name, 0774);
				}
			}
			//
			@ file_put_contents($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', $dtls['gen'], FILE_APPEND); 	// | LOCK_EX
			/*@ fwrite($fl, $dtls['gen']); 	// ."<hr style='border-style:dashed;' />" 	// ." <i style='float:right;'>(".$date.")</i><hr style='border-style:dashed;' />"
			@ fclose($fl);*/
			@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
		}
		if(is_array($dtls)) {
			foreach ($dtls as $key => $value) {
				if(strpos($key, $group_prefix) !== false && strpos($key, $group_prefix) === 0) {
					$fldnm = $key.$name_sep.$name;
				} else {
					$fldnm = (strcasecmp($name, $key) > 0)? $key.$name_sep.$name : $name.$name_sep.$key;
				}
				/*if(!file_exists($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm)) {
					if(!is_dir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm)) {
						@ mkdir($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm, 0774, true);
					}
				}*/
				$tm = $fldnm.'-'.gmdate('Y-m');
				if($key != 'gen' && trim($value) != '') { 	// && $fl = @fopen($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$tm.'.html', "a+")
					// $vl = $key;
					@ file_put_contents($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$tm.'.html', $value, FILE_APPEND); 	// | LOCK_EX
					/*@ fwrite($fl, $value); 	// ."<hr style='border-style:dashed;' />" 	// ." <i style='float:right;'>(".$date.")</i><hr style='border-style:dashed;' />"
					@ fclose($fl);*/
					@ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR.$tm.'.html', 0774);
				}
			}
		}
		// reg
		if(file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u') && is_file($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u')) {
			$udtl = file_get_contents($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', json_encode(array('')));
			$udtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				$udtl = '';
				if(!is_array($udtls)) { $udtls = array(); }
			}
			$udtls['lst'] = gmdate('Y-m-d h:i:s A');
			file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', json_encode($udtls));
			@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', 0774);
		} else {
			$udtls['lst'] = gmdate('Y-m-d h:i:s A');
			file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', json_encode($udtls));
			@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', 0774);
		}
		// online
		$mtime = filemtime($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u');
		if(! file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u') || ($mtime && $mtime < strtotime("-30 seconds"))) {
			file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u', json_encode(array('lastseen'=>gmdate('Y-m-d H:i:s'))));
			@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u', 0774);
		}
		// remove offline
		$udrs = scandir($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR);
		foreach($udrs as $ky => $vl) {
			if(!in_array($vl, array('.', '..'))) {		// delete old files, can be shifted to cron
				$mtime = @ filemtime($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$vl);
				if($mtime && $mtime < strtotime("-1 minutes")) {
					@ unlink($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$vl);
				}
			}
		}
	}
}
?>