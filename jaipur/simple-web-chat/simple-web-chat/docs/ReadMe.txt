
Its a high performance simple and feature rich web based chat application using HTML5 websockets / HTML5 SSE with ajax long polling as fall-back (can work with or without any database server)

It can be used as standalone or as module / plugin in any website. Implemented in core php and js code using jquery. Very simple, feature rich and fully customizable chat system. Auto fall-back from html5 websockets to html5 sse to ajax long polling

Fetaures:
1) Registration, login, forgot password
2) Search and add contacts, manage groups
3) Broadcasting, one to one & group chat
4) Desktop notifications, sound alert, auto scroll to new message
5) File attachments
6) Multiple tabbed chat
7) History of old chat messages
& *Audio-Video chat using WebRTC integrated into code, but not yet tested

All these managed without use of any database server. 
Its fully standalone but can be easily integrated with any database server using simple cron.

Performance: serves 1 lakh messages in avg 30 seconds (tested with apache benchmark utility)

*Note: Performance test is done on 8GB intel i3 processor ubuntu machine with nginx + php-fpm; This test was done after implementing various system (Operating System) level configuration optimizations like sysctl, limits.conf, etc. Several articles on this can be found on internet.
