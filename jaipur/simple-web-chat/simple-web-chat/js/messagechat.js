
(function($)
{

	$.fn.msgchat = function (options) {
		return $.msgchat(this, options);
	};
	// $.fn.fnname = fnname;
	$.msgchat = function(el, options)
	{
		var defaults = {
				mc_elslc: ((typeof el[0] == 'undefined' && options.mc_elslc != null)? options.mc_elslc : '#'+el[0].id),
				msg_selc: '', 		// message list selector
				msg_link: '', 		// message server side link
				flt_selc: '',		// friend list selector
				mps_selc: '',		// member setting selector
				call_int: 500, 		// pull/push interval, 1000
				clbk_fnc: '' ,		// message load callback function
				chattype: '', 		// explicit chat type option
				ajaxchat: false, 	// use ajax
				params: '', 		// extra params as string
				on_open: '', 		// callback on connection open
				er_clbk_fn: '', 	// callback function to be called on error
				con_tries: 0, 		// connection tries
				co_num: 0			// used to manage connection
			};
		//
		options = $.extend(defaults, options);
		//console.log(options);

		this.chattype;
		this.getchatmsgs;
		this.msgchatlink = options.msg_link;
		this.mcevs;
		this.ajx_cc = 'y';

		this.chkpg = function() {
			// set user offline
			this.close();
			// alert('exit !');
			return null;
		};
		
		this.reEstablish = function() {
			opts.chattype = 'xhr';
			opts.ajaxchat = true;
			ms = $.fn.msgchat(opts);
		};
		
		this.checkCon = function () {
			if(typeof ms.mcevs != 'undefined' && ms.mcevs != null && typeof ms.chattype != 'undefined' && ms.chattype != null && ms.chattype == 'ws') {
				// return true;
			} else {
				if (options.co_num <= -3) {
					// options.chattype = 'xhr';
					// options.ajaxchat = true;
					options.co_num = 0;
					ms = $.fn.msgchat(options);
				} else {
					options.co_num = options.co_num - 1;
				}
			}
			// console.log(options.co_num);
			setTimeout(function() {
				checkCon();
			}, 30000);
		};
		//
		window.checkCon = this.checkCon;

		this.setchatdtls = function(lid,dtls,opts) {
			opts.co_num = 0;
			if(typeof opts.clbk_fnc != 'undefined' && $.trim(opts.clbk_fnc) != '') {
				var optary = [opts.mc_elslc, opts.msg_selc, opts.flt_selc, opts.mps_selc];
				dtls = (typeof dtls == 'undefined' || dtls == null || dtls == '')? '' : dtls;
				window.requestAnimationFrame(function() { setTimeout(opts.clbk_fnc+"('"+lid+"',"+dtls+",'"+optary+"');", 100) } );
				if(lid == 'xhr') {
					this.ajx_cc = 'y';
					// console.log(options);
					setTimeout(function() { $.fn.msgchat(options); }, options.call_int);
				}
			}
		};

		this.open = function() {
			this.mcevs.open();
		};

		this.close = function() {
			if(!this.mcevs.closed) {
				this.mcevs.close();
			}
			// return 'closed';
		};

		// Initializes the map
		this.initialize = function ()
		{
			// window.onbeforeunload = this.chkpg;
			// window.onbeforeunload = this.chkpg;
			// var chkpg = this.chkpg;
			var setclb_fn = this.setchatdtls;
			// this.msgchatlink = options.msg_link;
			//
			if(!!window.WebSocket && "WebSocket" in window && !options.ajaxchat && (options.chattype == '' || options.chattype == 'ws')) {
				this.chattype = 'ws';
				// var url = options.msg_link.replace('http://','').replace('https://','');
				var url = options.msg_link.replace(window.location.protocol+'//','');
				if(url.indexOf('/') != -1) {
					url = url.substring(0, url.indexOf('/'));
				}
				if(url.indexOf(':') != -1) {
					url = url.substring(0, url.indexOf(':'));
				}
				// var msg_source = new WebSocket("ws://127.0.0.1:11171/");
				var msg_source = new WebSocket("ws://"+url+':'+port+"/"+options.params);
				this.mcevs = msg_source;
				// console.log(connection.extensions); 	# Determining accepted extensions
				msg_source.onmessage = function (e) {
					// var data = e.data; if(!data.charCodeAt(0)) { data = data.substr(1); }
					// console.log(e.stream);
					setclb_fn('0', e.data, options);
				};
				msg_source.onopen = function (e) {
					// connection.send('ping'); // Send the message 'Ping' to the server
					// console.log(e);
					if(typeof options.on_open != 'undefined' && options.on_open != null) {
						if(typeof window[options.on_open] != 'undefined' && window[options.on_open] != null) {
							var fn = window[options.on_open];
							fn();
							fn = null;
						}
					}
				};
				msg_source.onerror = function (error) {
					console.log('WebSocket Error ', error);
				};
				msg_source.onclose = function (e) {
					console.log('WebSocket Closed ', e);
					// options.con_tries = options.con_tries + 1;
					/*if(options.con_tries > 1 && options.chattype != 'sse') {
						options.chattype = 'sse';
						$.fn.msgchat(options);
					}*/
					options.con_tries = options.con_tries + 1;
					if(options.con_tries > 1) {
						options.chattype = 'sse';
						ms = $.fn.msgchat(options);
						//
						if(typeof ms != 'undefined' && ms != null && ms.chattype != null && ms.chattype == 'sse') {
							if(typeof options.on_open != 'undefined' && options.on_open != null) {
								if(typeof window[options.on_open] != 'undefined' && window[options.on_open] != null) {
									var fn = window[options.on_open];
									fn();
									fn = null;
								}
							}
						}
					} else {
						$.fn.msgchat(options);
					}
				};
				//
				// window.onunload = window.onbeforeunload = this.chkpg;
				// console.log(window);
			} else if(!!window.EventSource && !options.ajaxchat && (options.chattype == '' || options.chattype == 'sse')) {
				this.chattype = 'sse';
				options.chattype = 'sse';
				var msg_source = new EventSource(options.msg_link+"?type="+this.chattype+'&param='+options.params);
				this.mcevs = msg_source;
				msg_source.addEventListener('message', function(e) {
					// setTimeout(""+options.clbk_fnc+"("+e.data+");", 100);
					// console.log(e);
					// if (e.origin == site_url) { 	// verify e.origin in your message handler matches your app's origin like 'http://example.com'
					setclb_fn(e.lastEventId, e.data, options);
				}, false);
				msg_source.addEventListener('open', function(e) {
					// Connection was opened.
					// console.log(e);
				}, false);
				msg_source.addEventListener('error', function(error) {
					// console.log(error);
					if (error.eventPhase == EventSource.CLOSED) {
						// Connection was closed.
					}
				}, false);
				// function unloadmevs() { chkpg(); }
				// window.onunload = window.onbeforeunload = this.chkpg;
				// alert(window.onbeforeunload);
			} else {
				// Result to xhr polling :
				this.chattype = 'xhr';
				this.getchatmsgs = function () {
					this.chattype = 'xhr';
					options.chattype = 'xhr';
					if(this.ajx_cc == 'y') {
						this.ajx_cc = 'n';
                        var d = new Date();
						if (typeof this.mcevs != 'undefined' && this.mcevs != null) {
							// reuse xhr obj
						}
						this.mcevs = $.ajax({url:options.msg_link, method:'get', data:{'type':'xhr','param':options.params,'t':d.getTime()}, complete:function(resp, status) {
						    data = '';
						    if(status == 'success') { data = resp.responseText; }
							// setTimeout(function() {
		                        this.ajx_cc = 'y';
							    setclb_fn('xhr', data, options);
							// },700);
						}});
					}
				}
			}
			return this;
		};
		// Initialize
		var to = this.initialize();
		if(this.chattype == 'xhr') {
			this.getchatmsgs();
		}
		return to;
	};

})(jQuery);
