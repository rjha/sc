MYHOME=/home/rjha

#make copy of original nginx.conf and fastcgi_params
cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.orig
cp /etc/nginx/fastcgi_params /etc/nginx/fastcgi_params.orig
#delete sites-available and default file
rm -rf /etc/nginx/sites-available
rm /etc/nginx/sites-enabled/default
#copy our files
cp nginx/nginx.conf /etc/nginx/.
cp nginx/fastcgi_params /etc/nginx/fastcgi_params
cp nginx/www.3mik.com /etc/nginx/sites-enabled/.

#create /var/www/apps and /var/www/htdocs and /var/www/vhosts/www.3mik.com
mkdir -p /var/www/apps
mkdir -p /var/www/htdocs
mkdir -p /var/www/vhosts/www.3mik.com
#make symlink to github dir
ln -nfs $MYHOME/code/github/sc/web /var/www/vhosts/www.3mik.com/htdocs
cp sc/sc-app.inc /var/www/apps/.
cp sc/sc_config.ini  /var/www/apps/.

#copy php-fpm pool and ini files
cp /etc/php5/fpm/pool.d/www.conf /etc/php5/fpm/pool.d/www.conf.orig
cp nginx/www.conf /etc/php5/fpm/pool.d/.
#make backup of original conf files
cp /etc/php5/fpm/php.ini /etc/php5/fpm/php.ini.orig
cp /etc/php5/cgi/php.ini /etc/php5/cgi/php.ini.orig
cp /etc/php5/cli/php.ini /etc/php5/cli/php.ini.orig
#copy our file
cp nginx/php.ini /etc/php5/fpm/. 
cp nginx/php.ini /etc/php5/cgi/. 
cp nginx/php.ini /etc/php5/cli/. 

#create symlinks in web area
mkdir -p $MYHOME/web/log
mkdir -p $MYHOME/web/upload
chown -R www-data:www-data  $MYHOME/web

ln -nfs $MYHOME/code/github/webgloo/web/3p $MYHOME/code/github/sc/web/3p
ln -nfs $MYHOME/web/upload $MYHOME/code/github/sc/web/media
mkdir $MYHOME/code/github/sc/web/compiled
chown -R www-data:www-data  $MYHOME/code/github/sc/web/compiled

#create symlink for log and upload
ln -nfs $MYHOME/web/log /var/www/log
ln -nfs $MYHOME/web/upload /var/www/upload

#copy gitignore
cp github/gitignore $MYHOME/code/github/sc/.gitignore

#copy logrotate script
sudo cp logrotate/webgloo /etc/logrotate.d/webgloo
sudo logrotate --force /etc/logrotate.d/webgloo

echo " ***********************  Pending Tasks ***************** "
echo " 1. Add nginx vhost entry in /etc/hosts file "
echo " 2. create mysql database and load latest data from server "
echo " 3. install sphinx config file and create sphinx indexes"
echo " 4. verify /var/www/apps/sc-app.inc and sc_config.ini files "
echo " 5. install and configure redis server  "
echo " 6. install cron Scripts  "
echo "    REBOOT ..."
echo " ********************************************************** "


