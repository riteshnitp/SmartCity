<?php
/**
 * This class contains logic for process initialization
*/
class Process
{
	/**
	 * Generate new process
	 * process receiver class will call respective class method
	 * @param args process input parameters array('site_path', 'site_url', 'class_name', 'function_name')
	*/
	
	function __construct()
	{
		global $site_path, $site_url;
		
		$this->pid = 0;
		$this->command = '';
		$this->process_file = 'process.t';
		$this->max_mem = 390;
		$this->max_cpu = 390;
		$this->res_chk = 1;
		$this->site_path = $site_path;
		$this->site_url = $site_url;
		$this->temp_path = 'tmp'.DIRECTORY_SEPARATOR.'t'.DIRECTORY_SEPARATOR;
		$this->process_path = 'tmp'.DIRECTORY_SEPARATOR.'proc'.DIRECTORY_SEPARATOR;
	}
	
	function genProcess($args)
	{
        // global $site_path, $site_url;
		$mem_usg = 0;
		$cpu_usg = 0;
		$rtn = $this->resusage();
		if(is_array($rtn)) {
			$mem_usg = (isset($rtn[0]))? $rtn[0] : 0;
			$cpu_usg = (isset($rtn[1]))? $rtn[1] : 0;
		} else if($rtn == 'er') { return 'er'; }
		if($mem_usg <= $this->max_mem && $cpu_usg <= $this->max_cpu) {
			$prms = $this->site_path.$this->process_path.str_replace('.','',uniqid('', true)).'.pr';
			// $tof = $this->site_path.$this->temp_path.str_replace('.','',uniqid('', true)).'.tp';
			file_put_contents($prms, json_encode($args));
			$op = array();
			// echo "hhvm ".$this->site_path."classes".DIRECTORY_SEPARATOR."class.ProcessReceiver.php $prms > /dev/null 2>&1 & echo $!";
			$rs = exec("php ".$this->site_path."classes".DIRECTORY_SEPARATOR."class.ProcessReceiver.php $prms > /dev/null &", $op);
			return $rs;
		}
		return 'er';
	}
	
	function setconfig($pid='', $command='') {
		if($command != '') {
			$this->command = $command;
			# $this->runCom();
		}
		if(trim($pid) != '') {
			$this->pid = $pid;
		}
	}

	function runCom() {
		$command = 'nohup '.$this->command.' > /dev/null 2>&1 & echo $!';
		exec($command ,$op);
		$this->pid = (int)$op[0];
	}

	function setPid($pid) {
		$this->pid = $pid;
	}

	function getPid() {
		return $this->pid;
	}

	function status($pid='')
	{
		$op = array();
		if(trim($pid)!='') {
			$command = 'ps -F -p '.$pid;
			exec($command, $op, $rtn);
		} else if($this->pid != '') {
			$command = 'ps -F -p '.$this->pid;
			exec($command, $op);
		}
		if (!isset($op[1])) { return false; } else { return true; }
	}

	function start() {
		if ($this->command != '')$this->runCom();
		else return true;
	}

	function kill_process_tree($pid)
	{
		$command = "ps -o pid --ppid $pid";
		exec($command, $op);
		if(count($op) > 1) {
			for($l=1;$l<count($op);$l++) {
				$this->kill_process_tree($op[$l]);
				$this->stop($op[$l]);
			}
		}
		$this->stop($pid);
	}

	function killall_appcron($func, $pid)
	{
		$command = "ps aux | grep '$func'";
		exec($command, $op);
		# pr($op); exit;
		if(count($op) > 1) {
			for($l=0; $l < count($op); $l++) {
				# $this->kill_process_tree($op[$l]);
				$prstats = @ explode(' ', $op[$l]);
				$prstats = array_values(array_filter($prstats));
				if($prstats[1] > $pid && !in_array('grep', $prstats)) {
					$this->stop($prstats[1]);
				}
			}
		}
	}

	function stop($pid) {
		$command = 'kill '.$pid;
		exec($command, $op);
		# pr($op);
		if ($this->status() == false) { return true; }
		else return false;
	}

	function cusage()
	{
		// total cpu load 'cat /proc/loadavg' or 'top -n 1 | grep "Cpu"'
		# sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile)
		# $rs = shell_exec(sprintf("ps %d", $pid));
		# $rs = shell_exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
		file_put_contents($this->temp_path.'usg.t','');
		$rs = exec('cat /proc/loadavg >> '.$this->temp_path.'usg.t 2>&1', $val, $rtn);
		$cnt = file($this->temp_path.'usg.t');
		return $cnt;
	}

	function musage()
	{
		echo $rs = shell_exec(sprintf('top -n 1 | grep "Mem"'), $rtn);
		print_r($rtn);
	}

	# mem: ps auwx | awk '{total += $4} END {print total}'
	# cpu: ps auwx | awk '{total += $3} END {print total}'
	# ps auwx | awk '{m += $4}{c += $3} END {print m,c}'
	function cmusage()
	{
		file_put_contents($this->temp_path.'usg.t','');
		/*$rs = exec('ps aux >> '.$this->config->temp_path.'usg.t 2>&1', $val, $rtn);
		$cnt = file($this->config->temp_path.'usg.t');
		$h = array_filter(explode(' ', $cnt[0]));
		$cmu = array('c'=>0,'m'=>0);
		array_walk($cnt, create_function('$val,$key,&$rtn','$v = array_values(array_filter(explode(" ", $val))); $rtn[0]["c"] += floatval($v["2"]); $rtn[0]["m"] += floatval($v["3"]);'), array(0=>&$cmu));*/
		# pr($cmu);
		# pr($cnt); exit;
		/*$rs = exec('ps auwx | awk \'{total += $4} END {print total}\' >> '.$this->config->temp_path.'usg.t 2>&1', $val, $rtn);
		$m = file_get_contents($this->config->temp_path.'usg.t');
		file_put_contents($this->config->temp_path.'usg.t','');
		$rs = exec('ps auwx | awk \'{total += $4} END {print total}\' >> '.$this->config->temp_path.'usg.t 2>&1', $val, $rtn);
		$m = file_get_contents($this->config->temp_path.'usg.t');*/
		$rs = exec('ps auwx | awk \'{m += $4}{c += $3} END {print m,c}\' >> '.$this->temp_path.'usg.t 2>&1', $val, $rtn);
		$cnt = file_get_contents($this->temp_path.'usg.t');
		$cmu = @ explode(' ', $cnt);
		return $cmu;
	}

	function process_prms_file($filename, $params)
	{
		$flnm = $this->process_path.$filename.'.pr';
		$rs = file_put_contents($flnm, json_encode($params));
		return $rs;
	}

	function process_prms_rfile($filename)
	{
		$flnm = $this->process_path.$filename.'.pr';
		$rs = file_get_contents($flnm);
		return $rs;
	}

	function process_rsp_file($file, $resp)
	{
		//
	}

	function process_progress_file_write($pid, $file)
	{
		// $this->plugin('encrypt');
		$pf = $this->process_path.$this->process_file;
		$processes = file_get_contents($pf);
		if(trim($processes) != '') {
			// $processes = $this->encrypt->decrypt($processes);
			$processes = @ json_decode($processes,1);
		} else {
			$processes = array();
		}
		if($pid != '') {
			$processes[$pid] = $file;
		} else {
			$processes[] = $file;
			$fp = array_flip($processes);
			$max = max($fp);
			$pid = ($max + 1);
		}
		$processes = @ json_encode($processes);
		// $processes = $this->encrypt->encrypt($processes);
		file_put_contents($pf, $processes);
		return $pid;
	}

	function process_progress_file_read($pid='')
	{
		$pf = $this->process_path.$this->process_file;
		$processes = file_get_contents($pf);
		if(trim($processes) != '') {
			// $this->plugin('encrypt');
			// $processes = $this->encrypt->decrypt($processes);
			$processes = @ json_decode($processes,1);
		}
		if(trim($pid)!='') {
			return $processes[$pid];
		}
		return $processes;
	}

	function resusage()
	{
		$cmu = $this->cmusage();
		if(isset($cmu[0]) && isset($cmu[1])) {
			$mem_usg = floatval($cmu[0]);
			$cpu_usg = floatval($cmu[1]);
			if($mem_usg >= $this->max_mem || $cpu_usg >= $this->max_cpu) { return 'er'; }
			return array($mem_usg, $cpu_usg);
		}
		return '';
	}

	function call_func_in_bkg_process($func, $rsp_file, $prms='', $hup='')
	{
		$rtn = $this->resusage();
		if($rtn == 'er') { return 'er'; }
		if($mem_usg <= $this->max_mem && $cpu_usg <= $this->max_cpu) {
			if(trim($rsp_file) == '') { $file = '/dev/null'; /*'nohup.out';*/ /*'/dev/null';*/ }
			else { $file = $this->process_path.$rsp_file.'.rs'; }
			if($hup == 'n') { $hup = 'nohup'; } else { $hup = ''; }
			# pr($_SERVER); exit;
			# php /var/www/folder/AC_Main.php /var/www events/events/test
			# echo "$hup php ".$this->site_path."AC_Main.php ".$_SERVER['DOCUMENT_ROOT']." $func:=:$prms >> $file 2>&1 & echo $!"; exit;
			$pid = shell_exec(sprintf("$hup php ".$this->site_path."AC_Main.php ".$_SERVER['DOCUMENT_ROOT']." $func:=:$prms >> $file 2>&1 & echo $!",""));
			if(trim($rsp_file) != '') {
				$pid = $this->process_progress_file_write($pid, $rsp_file);
			}
			return $pid;
		}
		return 'er';
	}

	function run_background_process($cmd, $prms)
	{
		//
	}

	public function progress($pid)
	{
		$file = $this->process_progress_file_read($pid);
		$file = $this->process_path.$file.'.rs';
		$cnt = file_get_contents($file);
		$cnt = substr($cnt, strrpos('>>> process progress : '));
		$cnt = substr($cnt, 0, strpos('<<<'));
		return $cnt;
	}

	public function appcron($func, $typ='')
	{
		// $this->model('tools/settings');
		$dtls = $this->settings->get_setting_var("vName = '$typ'");
		$pid = (isset($dtls[0]['vSourceValue']))? $dtls[0]['vSourceValue'] : '0';
		$cmd = (isset($dtls[0]['vValue']))? $dtls[0]['vValue'] : '';
		// $func = $this->app_cron[$typ]['func'];
		# echo $func; exit;
		if(!$this->status($pid) && $func != '') {
			$psrch = "php ".$this->site_path."AC_Main.php ".$_SERVER['DOCUMENT_ROOT']." ".$func;
			$this->killall_appcron($psrch, $pid);
			$pid = 0;
			$pid = $this->call_func_in_bkg_process($func,'','','n');
			if(trim($pid) != 'er' && intval($pid) > 0) {
				// $rs = $this->settings->updateValues(array('vSourceValue'=>$pid), $typ);
			}
		}
	}

	public function cleanup($filename)
	{
		@ unlink($this->process_path.$filename.'.pr');
		@ unlink($this->process_path.$filename.'.rs');
	}

}
?>