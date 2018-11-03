document.addEventListener('DOMContentLoaded', function() {
   var btn = document.getElementById('start');
	btn.addEventListener('click', function() {
		var hst = document.getElementById('hst').value;
		var nam = document.getElementById('nam').value;
		chrome.tabs.create({ url: 'http://'+hst+'?name=' + nam }, function(){ });
		/*chrome.tabs.query({'active': true}, function(tabs) {
			chrome.tabs.update(tabs[0].id, { url: 'http://127.0.0.1/sc/chat/sc.php?name=' + nam });
		});*/
   });
});
