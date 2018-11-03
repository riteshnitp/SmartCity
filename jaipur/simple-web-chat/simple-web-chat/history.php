<!DOCTYPE HTML>
<html>
<head>
<title>History</title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<style type="text/css">
/*body { background:#3e3e3e; color:#e0e0e0; }
a { color:#e0e0e0; text-decoration:none; }
a.visited { color:#a0a0a0; text-decoration:none; }*/
</style>
<script type="text/javascript" src="js/jquery.js"></script>
</head>
<body>
<?php
include_once('common.php');
$name = (isset($_GET['name']))? $_GET['name'] : '';
$usnid = (isset($_GET['usnid']))? $_GET['usnid'] : '';
$q = (isset($_GET['q']))? $_GET['q'] : '';
$t = (isset($_GET['t']))? $_GET['t'] : '';
if(is_string($name)) { $name = trim($name); } else { $name = ''; }
if(is_string($q)) { $q = trim($q); } else { $q = ''; }
if(is_string($t)) { $t = trim($t); } else { $t = 'cm'; }
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$name = trim($name);
// $q = preg_replace('/[^A-Za-z0-9:-]/', '', $q);
if(strpos($q, $group_prefix) === 0) {
	$q = preg_replace('/[^A-Za-z0-9'.$name_sep.''.$group_sep.''.$group_prefix.']/', '', $q);
} else {
	$q = preg_replace('/[^A-Za-z0-9'.$name_sep.']/', '', $q);
}
$q = trim($q);
$eauth = false;
if($name != '' && $usnid != '' && $q != '' && $t == '') {
	echo '<div style="padding:5px;">';
	echo '<div>History</div>';
	echo '<hr/> <br/>';
	echo '&bull; &nbsp; <a href="'.$site_url.'history.php?name='.$name.'&usnid='.$usnid.'&q='.$q.'&t=lm"><i>Last Month</i></a>';
	echo '<br/> <br/>';
	echo '&bull; &nbsp; <a href="'.$site_url.'history.php?name='.$name.'&usnid='.$usnid.'&q='.$q.'&t=cm"><i>Current Month</i></a>';
	echo '</div>';
} else if($name != '' && $usnid != '' && $q != '' && $t != '') {
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.CheckUserSession.php');
	$cus_obj = new CheckUserSession();
	$vf = $cus_obj->checkSession($name, $usnid);
	if(!$vf) { echo 'Can\'t access history !'; exit; }
	//
	if($t == '' || ($t != 'cm' && $t != 'lm')) { $t = 'cm'; }
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.ChatHistory.php');
	$h_obj = new ChatHistory();
	$eauth = $h_obj->getHistory($name, $q, $t);
	if($name != '' && $eauth === true) { ?>
		<script type="text/javascript">
			$('.msg>b:contains("<?php echo $name; ?>")').parent().addClass('user-text');
			$('.uflnk').mouseover(function() {
				var link = $(this).attr('href');
				console.log(link);
				if (link.indexOf('/download.php?fl=') != -1) {
					link = link.replace('/download.php?fl=', '/download.php?name=<?php echo $name; ?>|<?php echo $usnid ?>&fl=');
					$(this).attr('href', link);
				}
			});
		</script>
	<?php } else if($name != '' && $eauth === false) { ?>
		<div style="padding:5px;">
			<div>History</div>
			<hr /> <br/>
			Hi, <em><?php echo $name; ?> </em>!
			<br />
			Enter your password to view chat history :
			<form name="frm" id="frm" action="" method="post">
				<input type="password" name="pass" id="pass" value="" />
				<input type="submit" name="submit" id="submit" value="Submit" />
			</form>
		</div>
	<?php }
} else { 
	echo 'Can\'t access history !'; exit; 
} ?>
</body>
</html>
<?php
exit;
/*
function rt($x) {
	$z = 1;
	$pa = array();
	// for ($i = 0; $i < 1000; $i++) {
	echo "2rt(x) : loop z = z - ((z*z - x) / (2 * z)) till z value repeats <br/>";
	echo "3rt(x) : loop z = z - ((z*z*z - x) / (3 * z*z)) till z value repeats <br/>";
	while(1) {
		echo "$z -= ($z*$z*$z - $x) / (3 * $z*$z)";
		$z -= ($z*$z*$z - $x) / (3 * $z*$z);
		echo " = $z <br/>";
		if(in_array($z, $pa)) { break; }
		$pa[] = $z;
	}
	echo $z;
}
rt(27);
exit;
*/
?>
