
var uc = 1;
var ms;
var name = '';
var valert = 0;
var pmkc = '';
var flcf = '';
// var senderids = 1;
var plfv = '';
// var urm = 0;
var ip = 0;
var room = '';
var xhr_sm;
var c_xhr;
var notification;
var aj_ip = false;
var onscrapr = false;
var d = new Date();
var gmtoffset = d.getTimezoneOffset();
var usnid = '';
var wo;
// var site_url = window.location.protocol+'//192.168.30.44';
// var port = '11171';
//
$("document").ready(function()
{
	$('.frmreply').css('width', '100%');
	$('#vMessage').css('width', $(document).width() - 59);
	//
	// var imgdivcontent ='<input type="file" id="files" name="files[]"><div id="files_list"></div>';
	$('#start').click(function() {
		if($.trim($('#nam').val()) != '') {
			name = $('#nam').val();
			initchat();
			if (typeof ms != 'undefined' && ms != null) {
				if(typeof ms.mcevs != 'undefined' && ms.mcevs != null && typeof ms.chattype != 'undefined' && ms.chattype != null && ms.chattype != '') {
					$('#identity').hide();
					$('#pro_name').html('@) '+name);
					// room = 'General';
				}
			}
		}
	});
	//
	$('#fp').click(function() {
		var unm = $.trim($('#nam').val());
		var eid = $.trim($('#eid').val());
		if(unm != '' && eid != '') {
			name = '';
			var url = site_url+'cp.php';
			if(!aj_ip) {
				aj_ip = true;
				c_xhr = $.ajax({ url:url, type:'POST', async:false, data:{ 'unm':unm, 'eid':eid }, success:function(resp) {
					if($.trim(resp) == 'wait') {
						alert('A change password link has been mailed to your email-id.');
						name = eid = pass = '';
					} else if($.trim(resp) == 'e-error') {
						alert('Error while processing, please try again later.');
						name = eid = pass = '';
					} else {
						name = eid = pass = '';
						alert('Details are not valid or account is inactive.');
					}
				}, complete: function() { aj_ip = false; }
				});
			}
		}
	});
	//
	$('#srch_frnd').bind('keyup',function(e) {
		if($.trim($(this).val()) == '' && $.trim(flcf)!='clbk') {
			$('.mc_frnd_list > li[class^=groupData]').show();
		} else if($.trim($(this).val()) != '' && $.trim($(this).val()) != $.trim($(this).attr('title'))) {
			$('.mc_frnd_list > li[class^=groupData] > a > label'+':not(:icontains("'+$.trim($(this).val())+'"))').closest('li').hide();
			$('.mc_frnd_list > li[class^=groupData] > a > label'+':icontains("'+$.trim($(this).val())+'")').closest('li').show();
		}
		flcf = '';
	});
	//
	$('.mc_frlist').find('span.icon').live('click', function() {
		$('#srch_frnd').val($.trim($('#srch_frnd').attr('title')));
		if($('.groupData_'+$(this).closest('h4').attr('class')+':last').is(':hidden')) {
			$('.groupData'+"_"+$(this).closest('h4').attr('class')).show();
		} else {
			$('.groupData'+"_"+$(this).closest('h4').attr('class')).hide();
		}
	});
	$('#srch_msgs').live('keyup',function(e) {
		if(($.trim($(this).val()) == '' || $.trim($(this).val()) == 'Filter Messages') && $.trim(flcf)!='clbk') {
			$('.mc_msglist > .msg-row').show();
		} else if($.trim($(this).val()) != '' && $.trim($(this).val()) != 'Filter Messages') {
			$('.mc_msglist > .msg-row'+':not(:icontains("'+$.trim($(this).val())+'"))').hide();
			$('.mc_msglist > .msg-row'+':icontains("'+$.trim($(this).val())+'")').show();
		}
		flcf = '';
	});
	//
	$('#frmreply').bind('submit', function(e) { frmsubmit(e); });
	$("#vMessage").bind("keyup", function(e) {
		if(e.keyCode == 16 || e.keyCode == 17 || e.keyCode == 18) {
			pmkc = 13;
		}
	});
	$("#vMessage").bind("keydown", function(e) {
		if(e.keyCode == 13 && (pmkc == 16 || pmkc == 17 || pmkc == 18)) {
			// e.preventDefault();
			// $('#replay_msg').trigger('click');
			// return false;
			pmkc = e.keyCode;
			return e.keyCode;
		} else if(e.keyCode == 13) {
			e.preventDefault();
			cancelEventBubble(e);
			setTimeout(function() {
				$('#send').trigger('click');
			}, 1);
			return false;
		}
		pmkc = e.keyCode;
		// alert(e.keyCode);
	});
	//
	$('#send').click(function(e) { sendmsgs(e); return false; });
	$('body').click(function() {
		$('title').html($('title').html().replace(' *',''));
	});
	$('body').focus(function() {
		$('title').html($('title').html().replace(' *',''));
	});
	//
	$('#sh_tabs').click(function() {
		if($('.ctabs > span.pointer:visible').not('.tab-active').length > 0) {
			$('.ctabs > span.pointer:visible').not('.tab-active').hide();
		} else {
			$('.ctabs > span.pointer').show();
		}
		$('.ctabs > span.tab-active').show();
	});
	//
	$('#logout').click(function() {
		localStorage.removeItem("nm");
		localStorage.removeItem("usn");
		var url = site_url+'logout.php';
		$.ajax({ url: url, type: 'POST', data:{ 'name':name, 'usnid':usnid }, success:function(resp) { }, complete: function() { window.location.reload(); } });
	});
	//
	$(window).resize(function() {
		if ($('#search_box').is(':visible') || $('#suggest_contacts_box').is(':visible') || $('#contact_box').is(':visible') || $('#group_box').is(':visible')) {
			$('#msgchat').css('width', $(document).width() - 379);
			$('.ctabs').css('width', $(document).width() - 379);
			$('.frmreply').css('width', $(document).width() - 379);
			$('#vMessage').css('width', $('.frmreply').width() - 59);
			if ($('#aboxes').is(':visible')) {
				$('#aboxes').css('max-height', $(document).height());
				/*if ($('.frmreply').width() < 380) {
					$('.abox').css('height', $('#aboxes').outerHeight() + $('#menu').outerHeight());
					$('.ctabs').css('top', $('#aboxes').outerHeight() + $('#menu').outerHeight());
				} else {
					$('.abox').css('height', '1px');
					$('.ctabs').css('top', '30px');
				}*/
			}
		} else {
			// $('#msgchat').css('width', '100%');
			$('.frmreply').css('width', '100%');
			$('#vMessage').css('width', $(document).width() - 59);
			// $('.abox').css('height', '1px');
			// $('.ctabs').css('top', '30px');
		}
		try {
			$.colorbox.resize({ height: "90%", width:"90%" });
		} catch (e) { }
	});
	//
	$('#autoscroll').click(function() {
		if ($('.scrollnew').prop('checked')) {
			$('.scrollnew').prop('checked','');
			// $(this).attr('src','images/imgs/noscroll.png');
			if(!$(this).hasClass('sprite-noscroll')) {
				$(this).addClass('sprite-noscroll');
				$(this).removeClass('sprite-scroll');
			}
		} else {
			$('.scrollnew').prop('checked','checked');
			// $(this).attr('src','images/imgs/scroll.png');
			if(!$(this).hasClass('sprite-scroll')) {
				$(this).addClass('sprite-scroll');
				$(this).removeClass('sprite-noscroll');
			}
		}
	});
	//
	$('#sound').click(function() {
		if ($('.alertsound').prop('checked')) {
			$('.alertsound').prop('checked','');
			// $(this).attr('src','images/imgs/mute.png');
			if(!$(this).hasClass('sprite-mute')) {
				$(this).addClass('sprite-mute');
				$(this).removeClass('sprite-sound');
			}
		} else {
			$('.alertsound').prop('checked','checked');
			// $(this).attr('src','images/imgs/sound.png');
			if(!$(this).hasClass('sprite-sound')) {
				$(this).addClass('sprite-sound');
				$(this).removeClass('sprite-mute');
			}
		}
	});
	//
	$('.clab').click(function() {
		// if($('#group_box').is(':visible')) {
			// $('#groups').trigger('click');
			$('#group_box').hide();
			$('.a2g').hide();
			$('#glist').hide();
			$('#grlist').hide();
		//}
		// if($('#contact_box').is(':visible')) {
			// $('#contact_box').trigger('click');
			$('#contact_box').hide();
		// }
		// if($('#search_box').is(':visible')) {
			// $('#search_box').trigger('click');
			$('#search_box').hide();
		// }
		$('#suggest_contacts_box').hide();
		$('#aboxes').css('display','none');
		$('.abox').css('height', '1px');
		$('.ctabs').css('top', '30px');
		$('#msgchat').css('width', '100%');
		$('.ctabs').css('width', '100%');
		$('.frmreply').css('width', $(document).width());
		$('#vMessage').css('width', $('.frmreply').width() - 59);
	});
	//
	// if(location.hash.length > 0) { 
		initchat(); 
	// }
});
//
function cancelEventBubble(e) {
	var evt = e ? e : window.event;
	if (evt.stopPropagation) { evt.stopPropagation(); }
	if (evt.cancelBubble!=null) { evt.cancelBubble = true; }
	if (evt.stopImmediatePropagation) { evt.stopImmediatePropagation(); }
}
//
function addToContact(jqel) {
	var cnm = jqel.parent().find('.msr').html();
	if ($.trim(cnm) != '') {
		var url = site_url+'newcontact.php';
		if (!aj_ip) {
			aj_ip = true;
			c_xhr = $.ajax({ url: url, type: 'POST', data:{ 'c':cnm, 'name':name, 'usnid':usnid }, success:function(resp) {
				if($.trim(resp) == 'success') {
					jqel.parent().slideUp('slow');
					jqel.parent().remove();
				}
			}, complete: function() { aj_ip = false; }
			});
		}
	}
}
//
function setui() 
{
	$('#search').click(function() {
		if($('#search_box').is(':visible')) {
			$('.clab').trigger('click');
			return true;
		}
		//
		$('.clab').trigger('click');
		if($('#search_box').is(':hidden')) {
			
			/*if($('#group_box').is(':visible')) {
				$('#group_box').trigger('click');
			}
			$('#contact_box').hide();
			$('#suggest_contacts_box').hide();*/
			$('#search_box').show();
			$('#msgchat').css('width', $(document).width() - 379);
			$('.ctabs').css('width', $(document).width() - 379);
			$('.frmreply').css('width', $(document).width() - 379);
			$('#vMessage').css('width', $('.frmreply').width() - 59);
			$('#aboxes').show();
			$('#aboxes').css('display','inline-block');
			$('#aboxes').css('max-height', $(document).height());
			/*if ($('.frmreply').width() < 380) {
				$('.abox').css('height', $('#aboxes').outerHeight() + $('#menu').outerHeight());
				$('.ctabs').css('top', $('#aboxes').outerHeight() + $('#menu').outerHeight());
			}*/
		} /*else {
			$('#search_box').hide();
			if ($('#search_box').is(':visible') || $('#search_box').is(':visible') || $('#contact_box').is(':visible') || $('#group_box').is(':visible')) {
				$('#msgchat').css('width', $(document).width() - 397);
				$('.ctabs').css('width', $(document).width() - 397);
				$('.frmreply').css('width', $(document).width() - 397);
				$('#vMessage').css('width', $('.frmreply').width() - 59);
			} else {
				// $('#aboxes').hide();
				$('#aboxes').css('display','none');
				$('.abox').css('height', '1px');
				$('.ctabs').css('top', '30px');
				$('#msgchat').css('width', '100%');
				$('.ctabs').css('width', '100%');
				$('.frmreply').css('width', $(document).width());
				$('#vMessage').css('width', $('.frmreply').width() - 59);
			}
		}*/
	});
	//
	$('#groups').click(function() {
		if($('#group_box').is(':visible')) {
			$('.clab').trigger('click');
			return true;
		}
		$('.clab').trigger('click');
		if($('#group_box').is(':hidden')) {
			// $('#search_box').hide();
			if($('#contact_box').is(':hidden')) {
				$('#contacts').trigger('click');
			}
			$('.clab').trigger('click');
			$('#group_box').show();
			$('.a2g').show();
			$('#glist').show();
			$('#grlist').show();
			$('#msgchat').css('width', $(document).width() - 379);
			$('.ctabs').css('width', $(document).width() - 379);
			$('.frmreply').css('width', $(document).width() - 379);
			$('#vMessage').css('width', $('.frmreply').width() - 59);
			$('#aboxes').show();
			$('#aboxes').css('display','inline-block');
			$('#aboxes').css('max-height', $(document).height());
			/*if ($('.frmreply').width() < 380) {
				$('.abox').css('height', $('#aboxes').outerHeight() + $('#menu').outerHeight());
				$('.ctabs').css('top', $('#aboxes').outerHeight() + $('#menu').outerHeight());
			}*/
		} else {
			// $('#group_box').hide();
			// $('.a2g').hide();
			// $('#glist').hide();
			// $('#grlist').hide();
			/*if ($('#contact_box').is(':visible') || $('#group_box').is(':visible')) { 	// $('#search_box').is(':visible') || 
				$('#msgchat').css('width', $(document).width() - 397);
				$('.ctabs').css('width', $(document).width() - 397);
				$('.frmreply').css('width', $(document).width() - 397);
				$('#vMessage').css('width', $('.frmreply').width() - 59);
			} else {
				// $('#aboxes').hide();
				$('#aboxes').css('display','none');
				$('.abox').css('height', '1px');
				$('.ctabs').css('top', '30px');
				$('#msgchat').css('width', '100%');
				$('.ctabs').css('width', '100%');
				$('.frmreply').css('width', $(document).width());
				$('#vMessage').css('width', $('.frmreply').width() - 59);
				// $('.clab').trigger('click');
			}*/
		}
	});
	//
	$('#contacts').click(function() {
		console.log('inhere');
		if($('#contact_box').is(':visible')) {
			$('.clab').trigger('click');
			return true;
		}
		url = site_url+'csrch.php';
		// if($('#aboxes').is(':visible')) {
			/*if($('#group_box').is(':visible')) {
				$('#groups').trigger('click');
			}
			if($('#search_box').is(':visible')) {
				$('#search_box').hide();
			}*/
			// $('.clab').trigger('click');
		// } else
		$('.clab').trigger('click');
		console.log('in here');
		if (!aj_ip) { 	// && $('#contact_box').is(':hidden')
			aj_ip = true;
			c_xhr = $.ajax({ url: url, type: 'POST', data:{ 'name':name, 'usnid':usnid }, success:function(resp) {
				var rsp = $.parseJSON(resp);
				var clist = '';
				var a2g = ($('#group_box').is(':hidden'))? 'none' : '';
				var q = '';
				if(rsp != null) {
					for(var e in rsp['con']) {
						var classname = '';
						// console.log(rsp['con'][e]);
						// if(typeof rsp['con'][e]['ol'] != 'undefined' && rsp['con'][e]['ol'] != null) {
						if(typeof rsp['con'][e] == "string") {
							q = (strcasecmp(name, rsp['con'][e]) > 0)? rsp['con'][e]+nsep+name : name+nsep+rsp['con'][e];
							q = site_url+'history.php?name='+name+'&usnid='+usnid+'&q='+q;
							clist = clist + '<div id="msc'+e+'" class="member pointer" title="'+rsp['con'][e]+'" ><label class="cmsr pointer" style="">'+rsp['con'][e]+'</label> <span class="rjct" style="float:right;" title="Remove"> &nbsp; x </span> <a class="hstry" style="float:right; color:#e0e0e0; text-decoration:none;" title="History" href="'+q+'" target="_blank"> &nbsp; h </a> <span class="a2g" title="Add To Group" style="float:right; display:'+a2g+';"> &nbsp; + </span> </div>';
						} else {
							for(var cn in rsp['con'][e]) {
								var lst = rsp['con'][e][cn];
								if(lst.indexOf('ol:') != -1) {
									lst = lst.replace('ol:','');
									classname = 'online-member';
								}
								// set time value as per user timezone
								lst = lst.replace(' AM', '').replace(' PM','');
								var utc_time = strtotime(lst);
								lst = date('Y-m-d h:i:s a', strtotime('+' + (-(gmtoffset)) + ' Minutes', utc_time));
								//
								q = (strcasecmp(name, cn) > 0)? cn+nsep+name : name+nsep+cn;
								q = site_url+'history.php?name='+name+'&usnid='+usnid+'&q='+q;
								clist = clist + '<div id="msc'+e+'" class="member pointer '+classname+'" title="'+cn+'"><label class="cmsr pointer" style="" title="(Last Seen: '+lst+')">'+cn+'</label> <span class="rjct" style="float:right;" title="Remove"> &nbsp; x </span> <a class="hstry" style="float:right; color:#e0e0e0; text-decoration:none;" title="History" href="'+q+'" target="_blank"> &nbsp; h </a> <span class="a2g" title="Add To Group" style="float:right; display:'+a2g+';"> &nbsp; + </span> </div>';
								break;
							}
						}
					}
				}
				var rlist = '';
				if(rsp != null) {
					for(var e in rsp['conr']) {
						rlist = rlist + '<div id="msr'+e+'" class="member pointer" title="'+rsp['conr'][e]+'" ><label class="cmsr pointer">'+rsp['conr'][e]+'</label> <span class="rjct" title="Reject" style="float:right;"> &nbsp; x </span> <span class="acpt" title="Accept" style="float:right;"> &nbsp; + </span></div>';
					}
				}
				var glist = '';
				if(rsp != null) {
					for(var e in rsp['grp']) {
						q = site_url+'history.php?name='+name+'&q='+gprfx+rsp['grp'][e]+nsep+e+nsep; 	// +name
						q = site_url+'history.php?name='+name+'&q='+q;
						glist = glist + '<div id="msg'+e+'" class="group pointer" title="'+rsp['grp'][e]+'" ><label class="cmsr pointer">'+rsp['grp'][e]+'</label> <span class="rjctg" title="Remove" style="float:right;"> &nbsp; x </span> <a class="hstry" style="float:right; color:#e0e0e0; text-decoration:none;" title="History" href="'+q+'" target="_blank"> &nbsp; h </a> <span class="selcg" title="Add To Select" style="float:right;"> &nbsp; + </span> </div>';
					}
				}
				var grlist = '';
				if(rsp != null) {
					for(var e in rsp['grpr']) {
						grlist = grlist + '<div id="msgr'+e+'" class="group pointer" title="'+rsp['grpr'][e]+'" ><label class="cmsr pointer">'+rsp['grpr'][e]+'</label> <span class="rjctg" title="Reject" style="float:right;"> &nbsp; x </span> <span class="acptg" title="Accept" style="float:right;"> &nbsp; + </span></div>';
					}
				}
				if(clist == '') {
					$('#clist').html('No Contacts');
					$('#clist').show();
					$('#search_box').hide();
					$('#suggest_contacts_box').hide();
					$('#contact_box').show();
					$('#msgchat').css('width', $(document).width() - 379);
					$('.ctabs').css('width', $(document).width() - 379);
					$('.frmreply').css('width', $(document).width() - 379);
					$('#vMessage').css('width', $('.frmreply').width() - 59);
					$('#aboxes').show();
					$('#aboxes').css('display','inline-block');
					$('#aboxes').css('max-height', $(document).height());
					/*if ($('.frmreply').width() < 380) {
						$('.abox').css('height', $('#aboxes').outerHeight() + $('#menu').outerHeight());
						$('.ctabs').css('top', $('#aboxes').outerHeight() + $('#menu').outerHeight());
					}*/
				}
				if(clist != '' || rlist != '' || glist != '' || grlist != '') {
					$('#clist').html(clist);
					$('#clist').show();
					$('#rlist').html(rlist);
					$('#rlist').show();
					$('#glist').html(glist);
					// $('#glist').show();
					$('#grlist').html(grlist);
					// $('#grlist').show();
					//
					$('#search_box').hide();
					$('#suggest_contacts_box').hide();
					$('#contact_box').show();
					$('#msgchat').css('width', $(document).width() - 379);
					$('.ctabs').css('width', $(document).width() - 379);
					$('.frmreply').css('width', $(document).width() - 379);
					$('#vMessage').css('width', $('.frmreply').width() - 59);
					$('#aboxes').show();
					$('#aboxes').css('display','inline-block');
					$('#aboxes').css('max-height', $(document).height());
					/*if ($('.frmreply').width() < 380) {
						$('.abox').css('height', $('#aboxes').outerHeight() + $('#menu').outerHeight());
						$('.ctabs').css('top', $('#aboxes').outerHeight() + $('#menu').outerHeight());
					}*/
					//
					biCChat();
					acptCReq();
					rjctCReq();
					biGChat();
					//
					add2Grp();
					selcGrp();
					rjctGrp();
					acptGrp();
					dispHistory();
					//
				} else {
					$('#clist').html('None Found !');
					$('#clist').show();
					$('#msgchat').css('width', $(document).width() - 379);
					$('.ctabs').css('width', $(document).width() - 379);
					$('.frmreply').css('width', $(document).width() - 379);
					$('#vMessage').css('width', $('.frmreply').width() - 59);
					$('#aboxes').show();
					$('#aboxes').css('display','inline-block');
					$('#aboxes').css('max-height', $(document).height());
					/*if ($('.frmreply').width() < 380) {
						$('.abox').css('height', $('#aboxes').outerHeight() + $('#menu').outerHeight());
						$('.ctabs').css('top', $('#aboxes').outerHeight() + $('#menu').outerHeight());
					}*/
				}
			}, complete:function() { aj_ip = false; }
			});
		}
	});
	//
	$('#ssrch').keyup(function(e) {
		if(e.keyCode == 13) {
			url = site_url+'ssrch.php';
			if (!aj_ip) {
				aj_ip = true;
				c_xhr = $.ajax({ url: url, type: 'POST', data:{ 'qk':$('#ssrch').val(), 'name':name, 'usnid':usnid }, success:function(resp) {
					var rsp = $.parseJSON(resp);
					var list = '';
					for(var e in rsp) {
						list = list + '<div id="ms'+e+'" class="member pointer" title="'+rsp[e]+'" ><label class="msr">'+rsp[e]+'</label> <span class="add_con" title="Add As Contact" style="float:right;"> &nbsp; + </span></div>';
					}
					// console.log(list);
					if(list != '') {
						$('#slist').html(list);
						$('#slist').show();
						//
						$('.add_con').click(function() {
							addToContact($(this));
						});
					} else {
						$('#slist').html('None Found !');
						$('#slist').show();
					}
				}, complete:function() { aj_ip = false; }
				});
			}
		}
	});
	//
	$('#csrch').keyup(function(e) {
		if(e.keyCode == 13) {
			var val = $('#csrch').val();
			if (val != '') {
				$('#clist > div.member').hide();
				$('#clist > div.member:contains("'+val+'")').show();
				//
				$('#rlist > div.member').hide();
				$('#rlist > div.member:contains("'+val+'")').show();
			} else {
				$('#clist > div.member').show();
				$('#rlist > div.member').show();
			}
		}
	});
	//
	$('#mgrp').click(function() {
		var grpcon = $('#grpcon').val();
		var grpnms = $('#grpnms').val();
		var url = site_url+'managegroup.php';
		// var jqel = $(this);
		if(!aj_ip) {
			aj_ip = true;
			c_xhr = $.ajax({ url: url, type: 'POST', data:{ 'grpcon':grpcon, 'grpnms':grpnms, 'name':name, 'usnid':usnid }, success: function(resp) {
				if($.trim(resp) == 'success') {
					$('#contacts').trigger('click');
					alert('Group Updated');
				} else {
					alert('Error while processing, verify details before submitting again.');
				}
				aj_ip = false;
			}, complete: function() { aj_ip = false; }
			});
		}
	});
	//
	$('#suggestions').click(function() {
		if($('#suggest_contacts_box').is(':visible')) {
			$('.clab').trigger('click');
			return true;
		}
		$('.clab').trigger('click');
		url = site_url+'suggestcontacts.php';
		if (!aj_ip) {
			aj_ip = true;
			c_xhr = $.ajax({ url: url, type: 'POST', data:{ 'name':name, 'usnid':usnid }, success:function(resp) {
				var rsp = $.parseJSON(resp);
				var list = '';
				for(var e in rsp) {
					list = list + '<div id="ms'+e+'" class="member pointer" title="'+rsp[e]+'" ><label class="msr">'+rsp[e]+'</label> <span class="add_con" title="Add As Contact" style="float:right;"> &nbsp; + </span></div>';
				}
				// console.log(list);
				if(list != '') {
					$('#sglist').html(list);
					$('#sglist').show();
					$('#search_box').hide();
					$('#contact_box').hide();
					$('#suggest_contacts_box').show();
					$('#msgchat').css('width', $(document).width() - 379);
					$('.ctabs').css('width', $(document).width() - 379);
					$('.frmreply').css('width', $(document).width() - 379);
					$('#vMessage').css('width', $('.frmreply').width() - 59);
					$('#aboxes').show();
					$('#aboxes').css('display','inline-block');
					$('#aboxes').css('max-height', $(document).height());
					/*if ($('.frmreply').width() < 380) {
						$('.abox').css('height', $('#aboxes').outerHeight() + $('#menu').outerHeight());
						$('.ctabs').css('top', $('#aboxes').outerHeight() + $('#menu').outerHeight());
					}*/
					//
					$('.add_con').click(function() {
						addToContact($(this));
					});
				} else {
					$('#sglist').html('None Found !');
					$('#sglist').show();
					$('#search_box').hide();
					$('#contact_box').hide();
					$('#suggest_contacts_box').show();
					$('#msgchat').css('width', $(document).width() - 379);
					$('.ctabs').css('width', $(document).width() - 379);
					$('.frmreply').css('width', $(document).width() - 379);
					$('#vMessage').css('width', $('.frmreply').width() - 59);
					$('#aboxes').show();
					$('#aboxes').css('display','inline-block');
					$('#aboxes').css('max-height', $(document).height());
					/*if ($('.frmreply').width() < 380) {
						$('.abox').css('height', $('#aboxes').outerHeight() + $('#menu').outerHeight());
						$('.ctabs').css('top', $('#aboxes').outerHeight() + $('#menu').outerHeight());
					}*/
				}
			}, complete:function() { aj_ip = false; }
			});
		}
	});
	//
	$('#smiley').click(function() {
		if($('#smiley-icons').is(':visible')) {
			$('#smiley-icons').hide();
		} else {
			$('#smiley-icons').show();
		}
	});
	//
	setTimeout(function() {
		$('.smileys').click(function() {
			if(document.activeElement != 'undefined' || document.activeElement != null || document.activeElement.id != 'undefined' || document.activeElement.id != null || document.activeElement.id != 'vMessage') {
				$('#vMessage').focus();
			}
			var altt = $(this).attr('alt');
			if (altt != 'undefined' && altt != null) {
				if (altt == '[:ci]') {
					altt = '<img src="" />';
				}
				insertAtCaret('vMessage', altt);
			}
		});
	}, 1000);
	//
}

function acptCReq() {
	$('.acpt').click(function() {
		var c = $(this).parent().attr('title');
		var url = site_url+'acceptcreq.php';
		// var jqel = $(this);
		// aj_ip = true;
		$.ajax({ url: url, type: 'POST', data:{ 'c':c, 'name':name, 'usnid':usnid }, success:function(resp) {
			if($.trim(resp) == 'success') {
				// jqel.remove();
				$('#contacts').trigger('click');
			}
		}, complete: function() { /*aj_ip = false;*/ }
		});
	});
}

function rjctCReq() {
	$('.rjct').click(function() {
		var c = $(this).parent().attr('title');
		var url = site_url+'rejectcreq.php';
		var jqel = $(this);
		// aj_ip = true;
		$.ajax({ url: url, type: 'POST', data:{ 'c':c, 'name':name, 'usnid':usnid }, success: function(resp) {
			if($.trim(resp) == 'success') {
				jqel.parent().remove();
			}
		}, complete: function() { /* aj_ip = false; */ }
		});
	});
}

function add2Grp()
{
	$('.a2g').click(function() {
		if (!$('#grpcon').is('[readonly]')) {
			var c = $(this).parent().attr('title');
			var gm = $('#grpcon').val();
			tmp = gm.replace(/^,|,$/g,'');
			if($.trim(tmp) != '') {
				gm = gm.replace(/,+/g,',');
				if(gm.indexOf(',') === 0) { gm = gm.substring(1, gm.length); }
				if(gm.lastIndexOf(',') === (gm.length-1)) { gm = gm.substring(0, gm.length-1); }
				if(gm.indexOf(c+',') == -1 && gm.indexOf(','+c+',') == -1 && gm.indexOf(','+c) == -1 && $.trim(gm) != c) {
					$('#grpcon').val(gm + ',' + c);
				} else {
					$('#grpcon').val(gm);
				}
			} else {
				$('#grpcon').val(c);
			}
		}
	});
}

function selcGrp()
{
	$('.selcg').click(function() {
		if (!$('#grpcon').is('[readonly]')) {
			var grpnm = $(this).parent().attr('title');
			/* // to allow multiple groups editing at same time (not tested)
			var grpnms = $('#grpnms').val();
			grpnms = grpnms.replace(/^,|,$/g,'');
			grpnms = grpnms.replace(/,+/g,',');
			if(grpnms.indexOf(',') === 0) { grpnms = grpnms.substring(1, grpnms.length); }
			if(grpnms.lastIndexOf(',') === (grpnms.length-1)) { grpnms = grpnms.substring(0, grpnms.length-1); }
			grpnms = $.trim(grpnms);
			if(grpnms != '') { 	// && grpnms.indexOf(',') == -1
				if(grpnms.indexOf(grpnm+',') == -1 && grpnms.indexOf(','+grpnm+',') == -1 && grpnms.indexOf(','+grpnm) == -1 && $.trim(grpnms) != grpnm) {
					$('#grpnms').val(grpnms + ',' + grpnm);
				} else {
					$('#grpnms').val(grpnms);
				}
			} else if(grpnms == '') {*/
				$('#grpnms').val(grpnm);
				$('#grpcon').val('Loading Group Members ...');
				$('#grpcon').attr('readonly','readonly');
				if(!aj_ip) {
					aj_ip = true;
					var url = site_url+'selcgroup.php';
					c_xhr = $.ajax({ url: url, type: 'POST', data:{ 'grpnm':grpnm, 'name':name, 'usnid':usnid }, success: function(resp) {
						$('#grpcon').val('');
						$('#grpcon').attr('readonly',false);
						if($.trim(resp) != '') {
							$('#grpcon').val(resp);
						}
					}, complete: function() { aj_ip = false; $('#grpcon').attr('readonly',false); }
					});
				}
			// }
		}
	});
}

function acptGrp()
{
	$('.acptg').click(function() {
		var grpnm = $(this).parent().attr('title');
		var url = site_url+'acceptgreq.php';
		// var jqel = $(this);
		// aj_ip = true;
		$.ajax({ url: url, type: 'POST', data:{ 'grpnm':grpnm, 'name':name, 'usnid':usnid }, success:function(resp) {
			if($.trim(resp) == 'success') {
				// jqel.remove();
				$('#contacts').trigger('click');
			}
		}, complete: function() { /*aj_ip = false;*/ }
		});
	});
}

function rjctGrp()
{
	$('.rjctg').click(function() {
		var grpnm = $(this).parent().attr('title');
		var url = site_url+'rejectgreq.php';
		var jqel = $(this);
		// aj_ip = true;
		$.ajax({ url: url, type: 'POST', data:{ 'grpnm':grpnm, 'name':name, 'usnid':usnid }, success: function(resp) {
			if($.trim(resp) == 'success') {
				var id = jqel.parent().attr('id');
				id = id.replace('msg','');
				jqel.parent().remove();
				$('.ctabs').find('[rel="'+id+'"]').remove();
				$('.mc_msglist').find('[rel="'+id+'"]').remove();
				$('.mc_msglist').find('.General').show();
			}
		}, complete: function() { /* aj_ip = false; */ }
		});
	});
}

function dispHistory() {
	$('.hstry').click(function(e) {
		var url = $(this).attr('href');
		$.colorbox({ 
			iframe:true, 
			width:"90%", 
			height:"90%",
			maxWidth:"90%",
			href:url,
			onComplete : function() {
                $(this).colorbox.resize({ height: "90%", width:"90%" });
            }
		});
		e.preventDefault();
		// cancelEventBubble(e);
		return false;
	});
}

function setChatBoxes(vl, gid, bk) {
	if(typeof bk == 'undefined' || bk == null) {
		bk = '';
	}
	if(typeof gid == 'undefined' || gid == null) {
		gid = '';
	}
	if(typeof vl == 'undefined' || vl == null) { return false; }
	var mselc = '.mc_msglist > .'+vl;
	if(gid != '') {
		mselc = '.mc_msglist > .'+vl+'[rel="'+gid+'"]';
	}
	var tbselc = '.ctabs > span[title="'+vl+'"][rel=""]';
	if(gid != '') {
		tbselc = '.ctabs > span[rel="'+gid+'"]';
	} else {
		// tbselc = '.ctabs > span[rel=""]';
	}
	if($(mselc).length == 0) {
		if (bk == '') {
			$('.mc_msglist > div').hide();
			$('.mc_msglist').append('<div class="'+vl+'" rel="'+gid+'" style="height:159px; overflow:auto;"></div>');
		} else {
			$('.mc_msglist').append('<div class="'+vl+'" rel="'+gid+'" style="height:159px; overflow:auto; display:none;"></div>');
		}
	}
	if($(tbselc).length == 0) {
		if (bk == '') {
			$('.ctabs > .tab-active').removeClass('tab-active');
			$('.ctabs').append('<span class="tab-active pointer" title="'+vl+'" rel="'+gid+'"><label style="display:inline-block; width:100px; overflow:hidden;">'+vl+'</label> <span></span> [<b class="pointer" style="text-transform:lowercase;">x</b>]</span>'); 	// text-transform:capitalize;
		} else {
			$('.ctabs').append('<span class="pointer" title="'+vl+'" rel="'+gid+'"><label style="display:inline-block; width:100px; overflow:hidden;">'+vl+'</label> <span></span> [<b class="pointer" style="text-transform:lowercase;">x</b>]</span>'); 	// text-transform:capitalize;
		}
	} else if($(tbselc).length > 0) {
		/*$('.ctabs > .tab-active').removeClass('tab-active');
		$(tbselc).show();*/
		if (bk != '') {
			$(tbselc).show();
		}
	}
	$(tbselc).click(function() {
		var vl = $(this).attr('title');
		if($(mselc).is(':hidden')) {
			$('.mc_msglist > div').hide();
			$(mselc).show();
		}
		$('.ctabs > .tab-active').removeClass('tab-active');
		$(this).addClass('tab-active');
		//
		$(tbselc+' > span').html('');
		var relvl = $(this).attr('rel');
		var q = '';
		if (typeof relvl != 'undefined' && relvl != null && relvl != '') {
			q = gprfx+vl+nsep+gid+nsep+name;
		} else {
			q = (strcasecmp(name, vl) > 0)? vl+nsep+name : name+nsep+vl;
		}
		if (q != '') {
			$('#history').attr('href', site_url+'history.php?name='+name+'&q='+q);
		}
		if(typeof ms.mcevs != 'undefined' && ms.mcevs != null && typeof ms.chattype != 'undefined' && ms.chattype != null && ms.chattype == 'ws') {
			if(typeof relvl != undefined && relvl != null && relvl != '') {
				ms.mcevs.send('scmsg:=:'+gprfx+vl+nsep+relvl);
			} else {
				ms.mcevs.send('scmsg:=:'+vl);
			}
		}
	});
	$(tbselc+' > b').unbind('click').click(function() {
		var vl = $(this).parent().attr('title');
		var gid = $(this).parent().attr('rel');
		var mselc = '.mc_msglist > .'+vl;
		if(gid != '') {
			mselc = '.mc_msglist > .'+vl+'[rel="'+gid+'"]';
		}
		// $(this).parent().remove();
		// $(mselc).remove();
		$(this).parent().hide();
		setTimeout("$('"+mselc+"').hide(); setChatBoxes('General');", 100);
		//
	});
	$(mselc).unbind('click').click(function() {
		var vl = $(this).attr('class');
		$(tbselc+' > span').html('');
	});
	$(mselc).unbind('focus').focus(function() {
		var vl = $(this).attr('class');
		$(tbselc+' > span').html('');
	});
	if($(mselc).is(':hidden') && bk == '') {
		$('.mc_msglist > div').hide();
		$(mselc).show();
		$('.ctabs > .tab-active').removeClass('tab-active');
		$(tbselc).addClass('tab-active');
		$(tbselc).show();
	}
}

setChatBoxes('General');

function setChatUi(vl, gid, bk) {
	var selc = '';
	if(typeof gid != undefined && gid != null) {
		selc = '.mc_msglist > .'+vl+'[rel="'+gid+'"]';
	} else {
		selc = '.mc_msglist > .'+vl;
	}
	if($(selc).length > 0 && (typeof bk == 'undefined' || bk == null)) {
		if($(selc).is(':hidden')) {
			$('.mc_msglist > div').hide();
			// $(selc).show();
			setChatBoxes(vl, gid, bk);
		}
	} else {
		// $('.mc_msglist').append('<div class="'+vl+'" style="height:159px; overflow:auto; border:1px solid #acacac;"></div>');
		setChatBoxes(vl, gid, bk);
	}
	// $('.mc_msglist > '+vl).show();
	if(typeof ms.mcevs != 'undefined' && ms.mcevs != null && typeof ms.chattype != 'undefined' && ms.chattype != null && ms.chattype == 'ws' && (typeof bk == 'undefined' || bk == null)) {
		if(typeof gid != undefined && gid != null) {
			ms.mcevs.send('scmsg:=:'+gprfx+vl+nsep+gid);
		} else {
			ms.mcevs.send('scmsg:=:'+vl);
		}
	}
}

function biCChat() {
	$('#clist > .member > .cmsr').click(function() {
		var ci = $(this).html();
		var url = site_url+'biCChat.php';
		if (!aj_ip) {
			aj_ip = true;
			c_xhr = $.ajax({ url: url, type: 'POST', data:{ 'ci':ci, 'name':name, 'usnid':usnid }, success: function(resp) {
				aj_ip = false;
				resp = $.trim(resp);
				if(resp.indexOf('success:') != -1 && resp.indexOf('success:') === 0) {
					setChatUi(ci);
					var q = $.trim(resp.replace('success:',''));
					room = (q != 'gen' && q.toLowerCase() != 'general' && $.trim(q) != '')? q : '';
					$('#history').attr('href', site_url+'history.php?name='+name+'&q='+q);
				} else if(resp == -1) {
					alert('Friend request not yet accepted');
				} else if(resp == 0) {
					alert('Not a friend or not in contacts list');
				} else if(resp == 1) {
					alert('Your friend request does not seem to exist. Please remove this request and send new request');
				} else {
					alert('Error while proceeding, please try again later');
				}
			}, complete: function() { aj_ip = false; }
			});
		} else {
			alert('please try again after some time');
		}
	});
}

function biGChat() {
	$('#glist > .group > .cmsr').click(function() {
		var gi = $(this).html();
		var gid = $(this).parent().attr('id');
		if(typeof gid != undefined && gid != null) {
			gid = gid.replace('msg','');
		}
		var url = site_url+'biGChat.php';
		if (!aj_ip) {
			aj_ip = true;
			c_xhr = $.ajax({ url: url, type: 'POST', data:{ 'gi':gi, 'gid':gid, 'name':name, 'usnid':usnid }, success: function(resp) {
				resp = $.trim(resp);
				if(resp.indexOf('success:') != -1 && resp.indexOf('success:') === 0) {
					setChatUi(gi, gid);
					var q = $.trim(resp.replace('success:',''));
					room = (q != 'gen' && q.toLowerCase() != 'general' && $.trim(q) != '')? q : '';
					// if(q.indexOf('g:') != -1 && q.indexOf('g:') === 0) {
						q = q+nsep+name;
					// }
					$('#history').attr('href', site_url+'history.php?name='+name+'&q='+q);
				} else if(resp == -1) {
					alert('Request not yet accepted');
				} else if(resp == 0) {
					alert('Request not yet accepted');
				} else if(resp == 1) {
					alert('Your request does not seem to exist at other end. Please remove this request and send new request');
				} else {
					alert('Error while proceeding, please try again later');
				}
			}, complete: function() { aj_ip = false; }
			});
		} else {
			alert('please try again after some time');
		}
	});
}

function initchat() {
	var unm = $.trim($('#nam').val());
	var eid = $.trim($('#eid').val());
	var phno = $.trim($('#phno').val());
	var pass = $.trim($('#pass').val());
	var cpc = $.trim($('#cpc').html());
	if(unm == '') { unm = $('#unam').html(); }
	if(eid == '') { eid = $('#eml').html(); }
	if(phno == '') { phno = $('#phn').html(); }
	if(pass == '') { pass = $('#ps').html(); }
	if($.trim(unm) == '') {
		if (window["localStorage"] && localStorage.length > 0) {
			unm = localStorage["nm"];
			usnid = localStorage["usn"];
			if(typeof unm == 'undefined' || unm == null) { unm = ''; }
			if(typeof usnid == 'undefined' || usnid == null) { usnid = ''; }
		}
	} /*else if(usnid == '') {
		usnid = $('#snm').html();
	}*/
	//
	if(unm != '' && ((eid != '' && pass != '') || usnid != '')) {
		name = '';
		var url = site_url+'chkusr.php';
		if(!aj_ip) {
			aj_ip = true;
			c_xhr = $.ajax({ url:url, type:'POST', async:false, data:{ 'unm':unm, 'eid':eid, 'pass':pass, 'cp':cpc, 'usn':usnid, 'phno':phno }, success:function(resp) {
				if(resp.indexOf('success:') != -1 && resp.indexOf('success:') === 0) {
					name_val = $.trim(resp.replace('success:',''));
					name = name_val.substring(0, name_val.indexOf(':'));
					usnid = $.trim(name_val.replace(name+':',''));
					if (window["localStorage"]) {
						localStorage["nm"] = name;
						localStorage["usn"] = usnid;
					}
					// window.location.href = window.location.href.toString() + "#in";
					var stateData = {
						    "location": site_url
						};
					//
					try {
						window.history.pushState(null, null, site_url + "#in");
						// window.history.replaceState(stateData, null, window.location.href.toString() + "#in");
					} catch (e) { }
					//
					$('#identity').hide();
					$('#menu').css('position','fixed');
					$('#pro_name').html('@) '+name);
					$('#snm').html(usnid);
					//
				} else if($.trim(resp) == 'wait') {
					alert('A verification link has been mailed to your email-id.');
					name = eid = pass = '';
				} else if($.trim(resp) == 'e-error') {
					alert('Error while registration, please try again later.');
					name = eid = pass = '';
				} else {
					if(eid != '' && pass != '') {
						name = eid = pass = '';
						alert('Details are not valid or account is inactive.');
					}
					name = eid = pass = '';
				}
			}, complete: function() { aj_ip = false; }
			});
		}
		//
		if(name != '') {
			$('#sc').show();
			$('.ctabs').css('position', 'fixed');
			var mcslink = site_url+'nchat.php';
			ms = new $('#msgchat').msgchat({ msg_selc:'.mc_msglist', flt_selc:'.mc_frlist', msg_link:mcslink, clbk_fnc:'setchatdtls', on_open:'joinedchat', params:name+'|'+usnid }); // user this 	, ajaxchat:true
			$('#history').attr('href', site_url+'history.php?name='+name+'&q=General_'+name);
			if(typeof ms != 'undefined' && ms != null && typeof ms.mcevs != 'undefined' && ms.mcevs != null && typeof ms.chattype != 'undefined' && ms.chattype != null && ms.chattype != '') { 	// && ms.chattype != 'ws'
				// setTimeout(function() { sendmsgs('', "* joined the chat"); }, 300);
				// check permission for notification
				$('#notify').click(function() {
					if (window.webkitNotifications) {
						if (window.webkitNotifications.checkPermission() != 0) { window.webkitNotifications.requestPermission(); }
					} else if (window.Notification) {
						window.Notification.requestPermission(function (perm) { });
					} else {
						alert("Your browser doesn't support HTML5 notifications!");
					}
				});
				$('#profile').click(function() {
					if($('#profile_box').is(':hidden')) {
						$('#profile_box').show();
					} else {
						$('#profile_box').hide();
					}
				});
				setui();
				$('#avchat').click(function() {
					initav();
				});
			}
			// check for connection
			checkCon();
			//
		} else {
			//
		}
	}
	// console.log(ms);
	// console.log(ms.close());
}

function initav()
{
	if ($.trim(room) != '') {
		// console.log(wo);
		if (navigator.appCodeName == "Mozilla" && navigator.appVersion.indexOf('WebKit') == -1) {
			$('video').css({'width':'320px', 'height':'180px'});
		}
		$('.clavc').unbind('click').bind('click',function() {
			$('#rvids').html('');
			$('#avc').hide();
			$('#msgchat').css('padding-top','0px');
			$('.ctabs').offset({top:30});
			wo.stopLocalVideo();
			wo.leaveRoom();
		});
		$('.clovid').unbind('click').bind('click', function() {
			$(this).parent().remove();
		});
		if (wo == null || typeof wo != 'object') {
			wo = new SimpleWebRTC({
				localVideoEl: 'selfvid',
				remoteVideosEl: 'rvids',
				autoRequestMedia: true,
				log: false,
				media: {
					video: {
						"mandatory": {
							maxHeight: 180,
							maxWidth: 320
						}
					},
					audio:true
				}
			});
			console.log("for wo in here");
			wo.startLocalVideo();
			$('#avc').show();
			$('#msgchat').css('padding-top','179px');
			$('.ctabs').offset({top:211});
		} else {
			wo.startLocalVideo();
			$('#avc').show();
			$('#msgchat').css('padding-top','179px');
			$('.ctabs').offset({top:211});
		}
		wo.on('readyToCall', function () {
			if(room) {
				$('#avc').show();
				wo.joinRoom(room);
				$('#msgchat').css('padding-top','179px');
				$('.ctabs').offset({top:211});
			}
		});
		//
		$('.clvid').click(function() {
			if(room) {
				wo.stopLocalVideo();
				wo.leaveRoom();
			}
			$('#avc').hide();
			$('#msgchat').css('padding-top','0px');
			$('.ctabs').offset({top:30});
			// $('#cuvid').html('<span class="clvid sprite-close pointer" title="Close"></span><video id="selfvid" name="selfvid" autoplay controls></video>');
		});
	}
}

function strip_tags (input, allowed) {
	// making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
	allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
	var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
		return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
	});
}

function frmsubmit(e, vl)
{
	if(ms.chattype == 'ws') { return false; }
	try {
		e.preventDefault();
	} catch (ex) { }
	// var URL = site_url+"app/mcchat/controller/insertmessage.php";
	var url = site_url+"reply.php";
	// $('#name').val(name);
	if((typeof vl == 'undefined' || vl == null || $.trim(vl) == '' || vl.length < 1)
		&& ($('#files').val() == 'undefined' || $('#files').val() == null || $.trim($('#files').val()) == '' || $('#files').val().length < 1)
	) {
		$('#vMessage').val('');
		$("#files").val('');
		// $('.soundalert').html('.');
		return false;
	}
	//
	if(typeof vl != 'undefined' && vl != null && $.trim(vl) != '' && vl.length > 0) {
		$('#vMessage').val(vl);
	}
	//
	var ci = $('.mc_msglist > div:visible').attr('class');
	var gcid = $('.mc_msglist > div:visible').attr('rel');
	if(typeof ci == 'undefined' || ci == null) { ci = ''; }
	if(typeof gcid == 'undefined' || gcid == null) { gcid = ''; }
	if(ci != '' && name != '') {
		$('#name').val(name);
		$('#ci').val(ci);
		$('#gcid').val(gcid);
		$('#usnid').val(usnid);
		$('#frmreply').ajaxSubmit({
			forceSync: true,
			url: url,
			type: 'post',
			success: function(resp, status, xhr, frm) {
				// $('.soundalert').html('.');
				$("#vMessage").val('');
				$("#files").val('');
				// $("#uploading").html(imgdivcontent);
				// $("#uploading").css('display','none');
			}
		});
	}
}

function sendmsgs(ev, vl)
{
	if(typeof ms != 'undefined' && ms != null) {
		if(typeof vl == 'undefined' || vl == null) {
			vl = $('#vMessage').val();
		}
		vl = $.trim(vl);
		// console.log(ms.chattype);
		if(ms.chattype == 'ws') {
			// ms.mcevs.binaryType = 'arraybuffer';
			ms.mcevs.send(vl);
			$('#vMessage').val('');
			// file upload
			var files = document.querySelector('#files').files;
			var msg = "";
			var ci = $('.mc_msglist > div:visible').attr('class');
			var gcid = $('.mc_msglist > div:visible').attr('rel');
			if(typeof ci == 'undefined' || ci == null) { ci = ''; }
			if(typeof gcid == 'undefined' || gcid == null) { gcid = ''; }
			if(ci != '' && name != '') {
				if(files.length > 0 && ip == 0) {
					for(var l=0; l<files.length; l++) {
						if(files[l].size > 0) {
							if(! (xhr_sm instanceof XMLHttpRequest)) {
								xhr_sm = new XMLHttpRequest();
							}
							var url = window.location.toString();
							url = url.substring(0, url.indexOf('sc.php'))+"filehandler.php";
							xhr_sm.open('POST', url, true);
							//
							xhr_sm.upload.onprogress = function(e) {
								if (e.lengthComputable) {
								  var percentComplete = (e.loaded / e.total) * 100;
								  console.log(percentComplete + '% uploaded');
								}
							};
							xhr_sm.onload = function() {
								if (this.status == 200) {
									ip = ip + 1;
									var resp = JSON.parse(this.response);
									if (typeof resp != 'undefined' && resp != null && typeof resp.txt != 'undefined' && resp.txt != null && resp.txt != '') {
										if(msg == "") { msg = "File(s) : "; }
										msg = msg + "<br />" + resp.txt;
										// ms.mcevs.send(resp.txt);
										// console.log('response: ', resp);
										// var image = document.createElement('img');
										// image.src = resp.dataUrl;
										// document.body.appendChild(image);
										// if(ip == -1) {
										if(ip == files.length) {
											if (msg != '') {  console.log(msg); ms.mcevs.send(msg); }
											ip = 0;
											msg = '';
											setTimeout(function() { $('#files').val(''); }, 300);
										}
									}
									// console.log(ip);
								};
							};
							var fd = new FormData();
							fd.append("file", files[l]);
							fd.append("name", name);
							fd.append("ci", ci);
							fd.append("gcid", gcid);
							fd.append("usnid", usnid);
							// ip = 1;
							xhr_sm.send(fd);
							// console.log(l);
							// console.log(files.length);
							if(l == (files.length-1)) {
								//ip = -1;
								// $('#files').val('');
							}
						}
					}
				}
			}
		} else {
			// $('#frmreply').submit();
			frmsubmit('', vl);
		}
		// console.log(ms.mcevs);
	} else {
		alert('no connection');
	}
	return false;
}

function joinedchat() {
	// setTimeout(function() { sendmsgs('', ""); }, 300); 	// * joined the chat
}

function afterMsgSet(options, msgs, cid) {
	// set scroll
	if($(options[0]).find('.scrollnew:checked').length > 0) {
		try {
			// $(options[0]).find(options[1]+' > '+cb_selc).scrollTop(parseInt($(options[0]).find(options[1]+' > '+cb_selc)[0].scrollHeight, 10));
			$(window).scrollTop(parseInt($(document).height(), 10));
		} catch (e) {
			$(window).scrollTop(parseInt($(document).height(), 10));
		}
	}
	// sound alert
	if($(options[0]).find('.alertsound:checked').length > 0) {
		setTimeout(function(){ msg_alert_sound(); }, 1);
	}
	// notification alert
	if (window.webkitNotifications) {
		if (window.webkitNotifications.checkPermission() == 0) {
			try {
				notification.cancel(); 	// notification.close();
			} catch(e) { }
			try {
				notification.close();
			} catch(e) { }
			notification = null;
			if (isNaN(msgs)) {
				var notify_msg = msgs;
				notify_msg = notify_msg.substring(0, notify_msg.indexOf('</i>'));
				// notify_msg = strip_tags(notify_msg,'').substring(0,100) + ' ...';
				notify_msg = strip_tags(notify_msg,'');
				notification = window.webkitNotifications.createNotification('', 'Message ('+cid+')', notify_msg);
				if(typeof wrapObj != 'undefined' && wrapObj != null && typeof wrapObj.notify != 'undefined' && wrapObj.notify != null) {
					wrapObj.notify(''+cid+': ' + notify_msg);
				}
			} else if(msgs != null) {
				notification = window.webkitNotifications.createNotification('', 'New Messages', 'You have ' + msgs + ' new unread messages');
				if(typeof wrapObj != 'undefined' && wrapObj != null && typeof wrapObj.notify != 'undefined' && wrapObj.notify != null) {
					wrapObj.notify('NMC:- ' + msgs);
				}
			}
			if (notification != null) {
				try {
					notification.onclick = function() { window.focus(); notification.cancel(); }
				} catch(e) {  }
				try {
					notification.show();
				} catch(e) {  }
				// setTimeout('try { notification.cancel(); } catch(er) {  }', 10000);
			}
		}
	} else if (window.Notification) {
		try {
			notification.cancel(); 	notification.close();
		} catch(e) { }
		try {
			notification.close();
		} catch(e) { }
		notification = null;
		if (isNaN(msgs)) {
			var notify_msg = msgs;
			/*for(var si in sm_vals) {
				var t = sm_keys[si].substring(0, sm_keys[si].indexOf('.'));
				var r = new RegExp('<img title="'+t+'" src="images/smileys/'+sm_keys[si]+'" />', 'g');
				notify_msg = notify_msg.replace(r, sm_vals[si]);
			}*/
			notify_msg = notify_msg.substring(0, notify_msg.indexOf('</i>'));
			notify_msg = strip_tags(notify_msg, '');
			notification = new Notification('Message ('+cid+')', { body: notify_msg });
		} else if(msgs != null) {
			notification = new Notification('New Message(s)', { body: 'You got new unread message(s)' });
		}
		if (notification != null) {
			try {
				notification.onclick = function() { try { window.focus(); notification.cancel(); } catch(er) {  } }
			} catch(e) {  }
			try {
				notification.show();
			} catch(e) {  }
			// setTimeout('try { notification.cancel(); } catch(er) {  }', 10000);
		}
	}
	//
	if($('body').height() > $(window).height() && onscrapr == false) {
		$(window).trigger('resize');
		onscrapr = true;
	}
	//
	$('.uflnk').mouseover(function() {
		var link = $(this).attr('href');
		if (link.indexOf('/download.php?fl=') != -1) {
			link = link.replace('/download.php?fl=','/download.php?'+'name='+name+'|'+usnid+'&fl=');
			$(this).attr('href', link);
		}
	});
	// set time value as per user timezone
	$('.msg > i[title^="UTC-Time"]').each(function() {
		var utc_time = $(this).attr('title').replace('UTC-Time:','');
		// console.log(utc_time);
		var utc_val = $(this).html();
		$(this).attr('title', 'UTC : ' + utc_val);
		// utc_time = utc_time.replace(' AM', '').replace(' PM','');
		utc_time = strtotime(utc_time);
		utc_time = date('Y-m-d h:i:s a', strtotime('+' + (-(gmtoffset)) + ' Minutes', utc_time));
		utc_time = ' &nbsp; (' + utc_time + ') &nbsp; ';
		$(this).html(utc_time);
	});
	//
}

function setchatdtls(lid, dtls, opts)
{
	var options = opts.split(',');
	// console.log(dtls);
	// console.log(options);
	var msgs = '';
	var frnds = '';
	var cid = 'General';
	if($.trim(dtls) != '') {
		if(lid == 'xhr' && typeof dtls['data'] != 'undefined' && dtls['data'] != null) {
			dtls = dtls['data'];
		}
	}
	var mn = 0;
	if(typeof dtls != 'undefined' && dtls != null) {
		for(var v in dtls) {
			var el_selc = 'title="'+cid+'"';
			var gid = '';
			var cb_selc = '.'+cid; 	// .toLowerCase();
			if(typeof dtls[v] != 'undefined' && dtls[v] != null) {
				msgs = dtls[v];
				cid = (v != 'gen')? v : 'General';
				if(cid.indexOf(gprfx) != -1 && cid.indexOf(gprfx) === 0) {
					cid = cid.substring(2, cid.length);
					gid = cid.substring(cid.indexOf(nsep)+1);
					cid = cid.substring(0, cid.indexOf(nsep));
					el_selc = 'rel="'+gid+'"';
					cb_selc = '[rel="'+gid+'"]';
				}
			}
			el_selc = 'title="'+cid+'"';
			cb_selc = '.'+cid;
			//
			if($.trim(msgs) != '') {
				mn = mn + 1;
				$('.nomsg').hide();
				if(uc != 0) {
					if($('.ctabs > span['+el_selc+'] > span').length == 0 || $('.ctabs > span['+el_selc+'] > span').is(':hidden')) {
						setChatUi(cid, gid, '1');
					}
					$(options[0]).find(options[1]+' > '+cb_selc).append(msgs); 	// +"<br/><hr style='border-style:dashed;' />"
					$('.msg>b:contains("'+name+'")').parent().not('.user-text').addClass('user-text');
					var urm = 0;
					if($('.ctabs > span['+el_selc+'] > span').length > 0) {
						urm = $('.ctabs > span['+el_selc+'] > span').html().replace('(','').replace(')','');
					} else {
						// $('msg_recv').html('');
					}
					if(isNaN(parseInt(urm))) { urm = 0; }
					urm = parseInt(urm) + 1;
					$('title').html($('title').html().replace(' *','') + ' *');
					$('.ctabs > span['+el_selc+'] > span').html('('+urm+')');
				} else {
					uc = 1;
				}
			}
		}
		//
		if(mn > 1) {
			afterMsgSet(options, mn, cid);
		} else if(mn > 0 && $.trim(msgs) != '') {
			afterMsgSet(options, msgs, cid);
		}
		//
	}
}

function msg_alert_sound() {
	// if(valert == 0) { return false; }
	// if($('#soundalert').length > 0) {
		try {
			$('#soundalert').get(0).play();
		} catch (e) {
			console.log("Can't play audio !");
		}
	// }
	/*if($('.soundalert').html()=='.') {
		$('.soundalert').html('');
	} else {
		$('.soundalert').html('<embed height="1px" width="1px" src="audio/alert.mp3" autostart="true" volume="100" loop="false" />'); 	// hidden="true"
		$('.soundalert').html('');
		$('.soundalert').html('<object width="1" height="1"><param name="src" value="audio/alert.mp3"><param name="autoplay" value="true"><param name="controller" value="true"><param name="bgcolor" value="#ffffff"><embed type="audio/mpeg" src="audio/alert.mp3" autostart="true" loop="false" width="1" height="1" controller="true" bgcolor="#ffffff"></embed></object>');
	}*/
}

function strcasecmp (f_string1, f_string2) {
	var string1 = (f_string1 + '').toLowerCase();
	var string2 = (f_string2 + '').toLowerCase();
	if (string1 > string2) {
		return 1;
	} else if (string1 == string2) {
		return 0;
	}
	return -1;
}

function insertAtCaret(areaId, text) {
	var txtarea = document.getElementById(areaId);
	var scrollPos = txtarea.scrollTop;
	var strPos = 0;
	var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? "ff" : (document.selection ? "ie" : false ));
	if (br == "ie") {
		txtarea.focus();
		var range = document.selection.createRange();
		range.moveStart ('character', -txtarea.value.length);
		strPos = range.text.length;
	} else if(br == "ff") {
		strPos = txtarea.selectionStart;
	}
	var front = (txtarea.value).substring(0,strPos);
	var back = (txtarea.value).substring(strPos,txtarea.value.length);
	txtarea.value=front+text+back;
	strPos = strPos + text.length;
	if (br == "ie") {
		txtarea.focus();
		var range = document.selection.createRange();
		range.moveStart ('character', -txtarea.value.length);
		range.moveStart ('character', strPos);
		range.moveEnd ('character', 0);
		range.select();
	} else if (br == "ff") {
		txtarea.selectionStart = strPos;
		txtarea.selectionEnd = strPos;
		txtarea.focus();
	}
	txtarea.scrollTop = scrollPos;
}

function sleep(milliseconds) {
	var start = new Date().getTime();
	for (var i = 0; i < 1e7; i++) {
		if ((new Date().getTime() - start) > milliseconds) {
			break;
		}
	}
}
