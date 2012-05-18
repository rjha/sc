#make copy of original nginx.conf and fastcgi_params
cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.orig
cp /etc/nginx/fastcgi_params /etc/nginx/fastcgi_params.orig
#delete sites-available and default file
rm -rf /etc/nginx/sites-available
rm /etc/nginx/sites-enabled/default
#copy our files
cp nginx/nginx.conf /etc/nginx/.
cp nginx/fastcgi_params /etc/nginx/fastcgi_params
cp nginx/mint.3mik.com /etc/nginx/sites-enabled/.

#create /var/www/apps and /var/www/htdocs and /var/www/vhosts/mint.3mik.com
mkdir -p /var/www/apps
mkdir -p /var/www/htdocs
mkdir -p /var/www/vhosts/mint.3mik.com
#make symlink to github dir
ln -nfs /home/rjha/code/github/sc/web /var/www/vhosts/mint.3mik.com/htdocs
cp sc/sc-app.inc /var/www/apps/.

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
mkdir -p /home/rjha/web/log
mkdir -p /home/rjha/web/upload
chown -R www-data:www-data  /home/rjha/web

ln -nfs /home/rjha/code/github/webgloo/web/3p /home/rjha/code/github/sc/web/3p
ln -nfs /home/rjha/web/upload /home/rjha/code/github/sc/web/media
mkdir /home/rjha/code/github/sc/web/compiled
chown -R www-data:www-data  /home/rjha/code/github/sc/web/compiled

#copy gitignore
cp github/gitignore /home/rjha/code/github/sc/.gitignore

echo " ******  Pending Tasks ******* "
echo " 1. Sphinx Installation "
echo " 2. MySQL  Installation "
echo " 3. Cron Scripts?  "
echo " 4. copy sc_config.ini from server - change upload to local,session backend "
echo " 5. Add mint.3mik.com entry in /etc/hosts file "
echo " 6. Restart Nginx, php-fpm,mysqld, searchd services "
echo " ****************************** "


