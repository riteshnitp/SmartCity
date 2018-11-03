<?php
//
include_once('common.php');
$name = (isset($_GET['name']))? $_GET['name'] : '';
$cpcode = (isset($_GET['cpcode']))? $_GET['cpcode'] : '';
if(is_string($name)) { $name = trim($name); } else { $name = ''; }
if(is_string($cpcode)) { $cpcode = trim($cpcode); } else { $cpcode = ''; }
//
$name = (isset($_POST['user']))? $_POST['user'] : $name;
$eml = (isset($_POST['email']))? $_POST['email'] : '';
$phn = (isset($_POST['phone']))? $_POST['phone'] : '';
$ps = (isset($_POST['pass']))? $_POST['pass'] : '';
$ejc = '';
if($name != '' && $eml != '' && $ps != '') { 	// && isset($_POST) && count($_POST) > 0
	$ejc = '<script type="text/javascript"> window.onload = function() { initchat(); }; </script>';
}
// smileys
$emos = '<img class="smileys" src="images/smileys/smile.png" title="smile" alt="[:)]" /><img class="smileys" src="images/smileys/wink.png" title="wink" alt="[;)]" /><img class="smileys" src="images/smileys/ull.png" title="tongue" alt="[:p]" /><img class="smileys" src="images/smileys/laugh.png" title="laugh" alt="[:d]" /><img class="smileys" src="images/smileys/happy.png" title="happy" alt="[:h]" /><img class="smileys" src="images/smileys/sad.png" title="sad" alt="[:(]" /><img class="smileys" src="images/smileys/oh.png" title="oh" alt="[:o]" /><img class="smileys" src="images/smileys/cry.png" title="cry" alt="[;(]" /><img class="smileys" src="images/smileys/worried.png" title="worried" alt="[:w]" /><img class="smileys" src="images/smileys/speechless.png" title="speechless" alt="[:|]" /><img class="smileys" src="images/smileys/blush.png" title="blush" alt="[:b]" /><img class="smileys" src="images/smileys/nerd.png" title="nerd" alt="[:n]" /><img class="smileys" src="images/smileys/style.png" title="style" alt="[:s]" /><img class="smileys" src="images/image.png" title="custom" alt="[:ci]" style="background:#797979; border:1px solid #707070;" />';
//
$cust_imgs = scandir($site_path.'images/custom/');
$anims = '';
foreach($cust_imgs as $k => $v) {
	if(!in_array($v, array('.','..','chompy.gif'))) {
		if(strpos($v,'.') !== false) {
			$vl = substr($v,0, strpos($v,'.'));
		}
		// $anims .= '<img class="smileys" src="images/custom/'.$v.'" title="'.$vl.'" alt="[('.$vl.')]" style="background:#3e3e3e; height:24px;" />';
	}
}
// $emos .= '<hr style="border:0px; border-top:1px solid #303030; height:1px; line-height:1px;"/><div style="background:#3e3e3e;">.'.$anims.'</div>';
//
/*
include_once($site_path . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class.CJLoad.php');
$cjlObj = new CJLoad();
if($debug_mode == 1 || !file_exists($site_path . DIRECTORY_SEPARATOR . 'js' .DIRECTORY_SEPARATOR . 'jscript.js')) {
	$cjlObj->addJs(array('jquery.js','jquery.form.js','socket.io.js','simplewebrtc.js','messagechat.js','jfdatetime.js','chat.js'));
}
*/
//
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Message</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="images/favicon.png" />
<link rel="stylesheet" type="text/css" href="css/colorbox.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script type="text/javascript" src="js/jquery.js"></script>
<!-- <script type="text/javascript" src="js/fastdom.js"></script>
<script type="text/javascript" src="js/jquery-fastdom.js"></script> -->
<script type="text/javascript" src="js/jquery.form.js"></script>
<script type="text/javascript" src="js/socket.io.js"></script>
<script type="text/javascript" src="js/simplewebrtc.js"></script>
<script type="text/javascript" src="js/messagechat.js"></script>
<script type="text/javascript" src="js/jfdatetime.js"></script>
<script type="text/javascript">
	var site = '<?php echo $site; ?>';
	var site_url = window.location.protocol+'//'+'<?php echo $site_uri; ?>';
	var port = '<?php echo $port; ?>';
	var sm_keys = <?php echo json_encode(array_keys($smileys)); ?>;
	var sm_vals = <?php echo json_encode(array_values($smileys)); ?>;
	var gprfx = '<?php echo $group_prefix; ?>';
	var nsep = '<?php echo $name_sep; ?>';
</script>
<?php // $cjlObj->loadJs(1,1); ?>
</head>
<body>
<div class="wrapper" id="identity" style="/*padding:5px; margin-bottom:1px;*/">
	<div class="main content clearfix">
		<div class="banner" style="text-align:center;">
			<h1>
				<!-- <img src="images/logo.png" /> -->
				<span class="title-logo">Uni C-AT</span>
			</h1>
		</div>
		<div class="card signin-card clearfix">
			<img class="profile-img" src="images/atom.png" alt="" />
			<input type="text" id="nam" name="nam" title="Username (Case Sensitive)" value="<?php echo $name; ?>" placeholder="Username (Case Sensitive)" />
			<input type="text" id="eid" name="eid" title="Email" placeholder="Email" />
			<input type="text" id="phno" name="phno" title="Phone (Optional)" placeholder="Phone (Optional)" />
			<input type="password" id="pass" name="pass" title="<?php echo ($name != '' && $cpcode != '')? 'New ' : ''; ?>Password" placeholder="<?php echo ($name != '' && $cpcode != '')? 'New ' : ''; ?>Password" />
			<input id="start" name="start" class="rc-button rc-button-submit" value="Sign In / Register" type="button" style="cursor:pointer;" />
			<div style="width: 100%;float: left;clear: both;display: block;padding-top: 10px;text-align: center;padding-bottom:15px;">
				<a id="fp" name="fp" class="pointer">Forgot Password</a>
			</div>
			<span id="cp" style="display:none;">
				<span id="unam"><?php echo $name; ?></span>
				<span id="eml"><?php echo $eml; ?></span>
				<span id="cpc"><?php echo $cpcode; ?></span>
				<span id="ps"><?php echo $ps; ?></span>
				<span id="phn"><?php echo $phn; ?></span>
				<span id="snm"></span>
			</span>
		</div>
	</div>
	<!-- <span style="display:inline-block;">
		<label style="width:90px; display:inline-block;" title="Username (Case Sensitive)">Username:</label>
		<input type="text" id="nam" name="nam" title="Enter your name" value="<?php // echo $name; ?>" style="background:#a0a0a0; color:#fafafa; border:1px solid #aaaaaa;" /> &nbsp;
	</span>
	<span style="display:inline-block;">
		<label style="width:90px; display:inline-block;" title="Email - Id">Email - Id:</label>
		<input type="text" id="eid" name="eid" title="Enter your email-id" style="background:#a0a0a0; color:#fafafa; border:1px solid #aaaaaa;"/> &nbsp;
	</span>
	<span style="display:inline-block;">
		<label style="width:90px; display:inline-block;" title="<?php // echo ($name != '' && $cpcode != '')? 'New' : ''; ?> Password"><?php // echo ($name != '' && $cpcode != '')? 'New' : ''; ?> Password:</label>
		<input type="password" id="pass" name="pass" title="Enter your password" style="background:#a0a0a0; color:#fafafa; border:1px solid #aaaaaa;"/> &nbsp;
	</span>
	<span style="display:inline-block;">
		<label style="width:90px; display:inline-block;" title="Phone (Optional)">Phone:</label>
		<input type="text" id="phno" name="phno" title="Enter your phone number (optional)" style="background:#a0a0a0; color:#fafafa; border:1px solid #aaaaaa;"/> &nbsp;
	</span>
	<span id="cp" style="display:none;">
		<span id="uname"><?php // echo $name; ?></span>
		<span id="cpcode"><?php // echo $cpcode; ?></span>
		<span id="ps"><?php // echo $ps; ?></span>
	</span>
	<span style="">
	<input class="pointer" id="start" name="start" type="button" value="Chat" />
	<input class="pointer" id="fp" name="fp" type="button" value="?" title="Forgot Password" />
	</span> -->
</div>
<div id="sc" style="display:none;">
	<div id="menu" style="width:99.7%; /*99.9*/ display:inline-block; vertical-align:top; padding:1px;">
		<div class="mlist pointer" style="text-align:center;">
			<span class="sprite-profile" id="profile" style="display:inline-block;" title="Profile"><!--<img src="images/profile.png" alt="[@]" title="Profile" />--></span> &nbsp; &nbsp; &nbsp;
			<span class="sprite-search" id="search" style="display:inline-block;" title="Search People"><!--<img src="images/search.png" alt="[S]" title="Search People" />--></span> &nbsp; &nbsp; &nbsp;
			<span class="sprite-contacts" id="contacts" style="display:inline-block;" title="Contacts"><!--<img src="images/contacts.png" alt="[C]" title="Contacts" />--></span> &nbsp; &nbsp; &nbsp;
			<span class="sprite-groups" id="groups" style="display:inline-block;" title="Groups"><!--<img src="images/groups.png" alt="[G]" title="Search Groups" />--></span> &nbsp; &nbsp; &nbsp;
			<!--<span id="status" style="display:inline-block; color:#5e5e5e;" title="Status">[M]</span>-->
			<span class="sprite-suggestions" id="suggestions" style="display:inline-block;" title="Contacts Suggestions"><!--<img src="images/suggestions.png" alt="[V]" title="Contacts Suggestions" />--></span> &nbsp; &nbsp; &nbsp;
			<span class="sprite-logout" id="logout" style="display:inline-block; color:#5e5e5e;" title="Logout"><!--<img src="images/logout.png" alt="[O]" title="Logout" />--></span>
		</div>
	</div>
	<div id="profile_box" style="display:none;">
		<span id="pro_name"></span>
	</div>
	<div id="aboxes" style="display:none;">
		<span class="clab sprite-close pointer" title="Close" style="display:inline-block; vertical-align:top;"></span>
		<!--<img class="clab pointer" src="images/imgs/close.png" />-->
		<div id="search_box" style="display:none;">
			<div style="padding:1px;"><label style="width:10px;">Find People:</label> <input type="text" id="ssrch" name="ssrch" title="Find People" style="width:90%; background:#a0a0a0; color:#fafafa; border:1px solid #aaaaaa; vertical-align:top; height:14px;" /></div>
			<div id="slist" style="max-height:100px; overflow:auto; display:none;"></div>
		</div>
		<div id="group_box" style="display:none;">
			<div style="padding:1px;">
				<label style="width:10px;">Add Contacts:</label> <input type="text" id="grpcon" name="grpcon" title="Add Contacts" style="width:70%; background:#a0a0a0; color:#fafafa; border:1px solid #aaaaaa; vertical-align:top; height:14px;" /> <br/>
				<label style="width:10px;">Select Groups:</label> <input type="text" id="grpnms" name="grpnms" title="Select Groups" style="width:70%; background:#a0a0a0; color:#fafafa; border:1px solid #aaaaaa; vertical-align:top; height:14px;" /> <button id="mgrp" name="mgrp">Submit</button>
			</div>
		</div>
		<div id="contact_box" style="display:none;">
			<div style="padding:1px;"><label style="width:87px; display:inline-block;">Filter By :</label> <input type="text" id="csrch" name="csrch" title="Find Contacts" style="width:70%; background:#a0a0a0; color:#fafafa; border:1px solid #aaaaaa; vertical-align:top; height:14px;" /></div>
			<div id="clist" style="max-height:190px; overflow:auto; display:none;"></div>
			<div id="rlist" style="max-height:100px; overflow:auto; display:none;"></div>
			<div id="glist" style="max-height:100px; overflow:auto; display:none;"></div>
			<div id="grlist" style="max-height:100px; overflow:auto; display:none;"></div>
		</div>
		<div id="suggest_contacts_box" style="display:none;">
			<div style="padding:1px;"><label style="width:10px;">People You Might Know:</label></div>
			<div id="sglist" style="max-height:100px; overflow:auto; display:none;"></div>
		</div>
	</div>
	<div class="abox" style="display:block; height:1px;"></div>
	<div id="avc">
		<span class="clavc sprite-close pointer" title="Close Video Chats"></span>
	    <span id="cuvid">
			<span class="clvid sprite-close pointer" title="Close"></span>
			<video id="selfvid" name="selfvid" autoplay controls></video>
		</span>
	    <span id="rvids" style="display:inline-block;"></span>
	</div>
	<div id="msgchat" style="width:100%; display:inline-block;">
		<div id="srch_msgs" style="display:none"></div>
		<div class="mc_frlist" style="display:none"></div>
		<div class="ctabs" style="line-height:1px;">
			<span class="pointer tab-active" title="General" rel=""><label style="display:inline-block; width:100px; overflow:hidden;">General</label> <span></span> <!--(<b class="pointer" style="text-transform:lowercase;">x</b>)--></span>
			<div class="pointer sprite-tabs" id="sh_tabs" title="Show / Hide (Other Tabs)" style="display:inline-block; float:right;"><!--<img class="pointer" src="images/imgs/tabs.png" />--></div>
		</div>
		<div class="mc_msglist">
			<div class="General" style="height:159px; overflow:auto;" rel=""></div>
		</div>
		<div class="frmreply">
			<form name="frmreply" id="frmreply" method="post" style="padding:0px; margin:0px;">
				<input type="hidden" id="name" name="name" style="display:none; visibility:hidden;" />
				<input type="hidden" id="ci" name="ci" style="display:none; visibility:hidden;" />
				<input type="hidden" id="gcid" name="gcid" style="display:none; visibility:hidden;" />
				<input type="hidden" id="usnid" name="usnid" style="display:none; visibility:hidden;" />
				<div class="sendmsg">
					<textarea id="vMessage" name="vMessage" style="" maxlength="10101"></textarea>
					<img id="send" class="pointer" src="images/imgs/send.png" title="Send" style="" />
				</div>
				<div id="smiley-icons" style="border:1px solid #303030; display:none; line-height:1px;">
					<?php /* <!--<img class="smileys" src="images/smileys/smile.png" title="smile" alt="[:)]" />
					<img class="smileys" src="images/smileys/wink.png" title="wink" alt="[;)]" />
					<img class="smileys" src="images/smileys/ull.png" title="tongue" alt="[:p]" />
					<img class="smileys" src="images/smileys/laugh.png" title="laugh" alt="[:d]" />
					<img class="smileys" src="images/smileys/happy.png" title="happy" alt="[:h]" />
					<img class="smileys" src="images/smileys/sad.png" title="sad" alt="[:(]" />
					<img class="smileys" src="images/smileys/oh.png" title="oh" alt="[:o]" />
					<img class="smileys" src="images/smileys/cry.png" title="cry" alt="[;(]" />
					<img class="smileys" src="images/smileys/worried.png" title="worried" alt="[:w]" />
					<img class="smileys" src="images/smileys/speechless.png" title="speechless" alt="[:|]" />
					<img class="smileys" src="images/smileys/blush.png" title="blush" alt="[:b]" />
					<img class="smileys" src="images/smileys/nerd.png" title="nerd" alt="[:n]" />
					<img class="smileys" src="images/smileys/style.png" title="style" alt="[:s]" />
					<img class="smileys" src="images/image.png" title="custom" alt="[:ci]" style="background:#797979; border:1px solid #707070;" />--> */ ?>
					<!--<hr style="border:0px; border-top:1px solid #303030; height:1px; line-height:1px;"/>
					<div style="background:#3e3e3e;"><?php // echo $anims; ?></div>-->
				</div>
				<div class="emos sprite-smileys pointer" id="smiley" title="Smileys" style="float:left; display:inline-block;">
					<!--<img id="smiley" class="pointer" title="Smileys" src="images/imgs/smileys.png" />-->
					<!--<input type="button" id="smiley" class="pointer" title="Smileys" style="background:url('images/imgs/smileys.png') 50% 50%; width:32px; height:17px; border:0px;" />-->
				</div>
				<span id="avchat" class="pointer sprite-av" title="AV - Chat" style="display:inline-block; vertical-align:top;"></span>
				<!--<img id="avchat" class="pointer" src="images/imgs/av.png" title="AV - Chat" style="vertical-align:top;" />-->
				<div style="float:right;">
					<input type="checkbox" checked="checked" class="alertsound" style="display:none;" />
					<input type="checkbox" checked="checked" class="scrollnew" style="display:none;" />
					<img id="autoscroll" class="pointer" src="images/imgs/scroll.png" title="Scroll" />
					<img id="sound" class="pointer" src="images/imgs/sound.png" title="Sound Alerts" />
					<!--<img id="notify" class="pointer" src="images/imgs/notify.png" title="Notifications" />-->
					<span id="notify" class="pointer sprite-notify" title="HTML5 Notifications" style="display:inline-block;"></span>
					<a id="history" class="sprite-history" href="" target="_blank" style="display:inline-block;" title="History"><!--<img title="History" src="images/imgs/history.png" />--></a>
				</div>
				<div id="uploading" class="sprite-attach" title="Attach">
					<input type="file" id="files" name="files[]" multiple="multiple" style="opacity:0;" />
					<div id="files_list"></div>
				</div>
				<!--<input type="button" id="avchat" name="avchat" value="AV-CHAT" />
				<input type="button" id="notify" value="Notifications" style="float:right;" />-->
				<!--<input type="button" id="send" name="send" value="Send" />-->
			</form>
		</div>
		<div class="soundalert" style="position:absolute; left:-1000px;">
			<audio id="soundalert"> 
			  <source src="audio/alert.mp3">
			  <source src="audio/alert.wav">
			</audio>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/colorbox.js"></script>
<script type="text/javascript" async="async"> setTimeout('document.getElementById(\'smiley-icons\').innerHTML = \'<?php echo $emos; ?>\';', 100); // window.onload = function() { }; </script>
<script type="text/javascript" src="js/chat.js" <?php if($ejc == '') { echo 'async="async"'; } ?> ></script>
<?php echo $ejc; ?>
</body>
</html>
<?php
//
?>