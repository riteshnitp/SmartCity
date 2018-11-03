<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Message</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="images/favicon.png" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<style type="text/css">
body { overflow:hidden; }
#chat-box { background:#eeeeee; margin:0px; width:390px; color:#505050; }
.title { font-weight:bold; display:inline-block; }
.close { float:right; font-weight:bold; display:inline-block; }
.shtg { float:right; font-weight:bold; display:inline-block; }
.pointer { cursor:pointer; }
</style>
<script type="text/javascript">
$(document).ready(function() {
	$(function() {
		// $("#chat-box").disableSelection();
		$("#chat-box").draggable();
		$('.shtg').click(function() {
			if($('.chat-zone').is(':visible')) {
				$('.chat-zone').hide();
			} else {
				$('.chat-zone').show();
			}
		});
	});
	$('window, body').keypress(function(e) {
		// console.log(e);
		if (e.ctrlKey && e.shiftKey && (e.charCode == 41 || e.keyCode == 41)) {
			$('#chat-box').css('left',300);
			$('#chat-box').css('top',100);
		} else if (e.ctrlKey && e.shiftKey && (e.charCode == 95 || e.keyCode == 95)) {
			$('.shtg').trigger('click');
		}
	});
});
</script>
</head>
<body>
<div id="chat-box" class="ui-widget-content">
	<div style="padding:1px;">
		<span class="title pointer"> &nbsp; WC &nbsp; </span>
		<!--<span class="close pointer"> &nbsp; X &nbsp;</span>-->
		<span class="shtg pointer"> &nbsp; - &nbsp;</span>
	</div>
	<div class="chat-zone">
		<iframe src="http://localhost/simple-web-chat/" style="overflow:hidden; height:550px; width:390px; border:0px;" />
	</div>
</div>
</body>
</html>
