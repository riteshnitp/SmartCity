#!/bin/sh
sudo chown -Rf ubuntu:www-data ./simple-web-chat/
sudo chmod -Rf 750 ./simple-web-chat/
sudo chmod -Rf 740 ./simple-web-chat/.htaccess
sudo chmod -Rf 770 ./simple-web-chat/h/
sudo chmod -Rf 740 ./simple-web-chat/h/.htaccess
sudo chmod -Rf 770 ./simple-web-chat/files/
sudo chmod -Rf 740 ./simple-web-chat/files/.htaccess
sudo chmod -Rf 770 ./simple-web-chat/pub/
sudo chmod -Rf 740 ./simple-web-chat/pub/.htaccess
sudo chmod -Rf 770 ./simple-web-chat/tmp/
sudo chmod -Rf 740 ./simple-web-chat/tmp/.htaccess
