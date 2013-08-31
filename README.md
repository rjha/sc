sc
==

This is the repository that used to power our website www.3mik.com. 3mik is a shopping curation site 
and a social network. To setup a copy of this application on your server please do the following

+ Get a linux VM. The instructions are for a Debian based system. We were using Ubuntu 12.04 LTS. 
It should work for other distributions as well but you have to translate the instructions.


+ Git clone this repo on your machine. we are assuming that you clone this repo in 
$HOME/code/github/sc folder.

+ Install the webgloo repository. We use webgloo library as an MVC framework.

$cd $HOME/code/github
$git clone git://github.com/rjha/webgloo.git

Now you should have

```
 /home/foo/code/github
    |_ sc/
    |_ webgloo/
    
```

+ Install required packages on your VM


 $ cd $HOME/code/github/sc/deploy/linux/packages
 $ sudo aptitude install $(< precise.server.packages)

 The only interaction is to provide root password for mysql.
 provide any password you like. save the mysql root password for later use.

+ Add mint.3mik.com as host in /etc/hosts file

```
  $sudo vim /etc/hosts
  Add an entry for mint.3mik.com 127.0.0.1
```

    
+ Run debian-7.vm.sh script

```
cd $HOME/code/github/sc/deploy
open debian-7.vm.sh script
@imp: set your home dir path on top
save and quit file.

$chmod +x ./debian-7.vm.sh
$sudo ./debian-7.vm.sh
```

@imp: If there are any other complaints then the script is failing 
and script should be fixed first.
moving ahead will only cause agony.

    
    
+ Verify PHP install

``` 


$sudo service nginx stop
$sudo service php5-fpm stop
$sudo service nginx start
$sudo service php5-fpm start

@imp
    - verify that /etc/nginx/sites-enabled/www.3mik.com vhost file has the right domain
    - verify /var/www/vhosts/www.3mik.com/htdocs location
```


open a browser and type :- http://mint.3mik.com/echo.php

@imp: This should show your path and loaded modules etc.  
If things work then we are ready to move onto next step, else we need to fix things here first.


+ create mysql database

```
$cd $HOME/code/github/sc/deploy/mysql/
make changes to mysql.newdb.sql file to reflect your user/db name
load mysql.newdb.sql 
$mysql -u root -p < mysql.newdb.sql
```

    
+ copy the my.cnf file 

```
    sudo cp  /etc/mysql/my.cnf /etc/mysql/my.cnf.orig
    sudo cp  my-5.5.cnf /etc/mysql/my.cnf
    restart mysql
    
```

+ copy sc_config.ini and sc-app.inc file 

```
    $sudo cp sc-app.inc /var/www/apps/.
    $sudo cp sc_config.ini /var/www/apps/.
```

+ Make changes to sc_config.ini

    - node.name - in quotes, your machine name (error reports will include this name)
    -  node.type (leave as development)
    - www.host.name (leave as mint.3mik.com)
    - send.error.mail (if you want to send error reports via email - not useful for dev machines)
      should be set to 0 
    - change mysql DB/user/password
    - mysql.sphinx.port=9306
    - session.backend="mysql"
    - verify: log location  
    - verify: file.store is local
    - verify: session.backend to mysql
    - verify: redis host and port
    - verify: sphinx port
    - sensitive information (like sendgrid password, FB App Id etc. is removed)
    - client  ID and client secret for social logins


+ install and configure sphinx. 

+ install logrotate script

```

cd deploy/logrotate/webgloo  
$ sudo cp webgloo /etc/logrotate.d/webgloo
$ sudo logrotate --force /etc/logrotate.d/webgloo 
```

+ install cron scripts
 
```
$mkdir -p $HOME/cron
$copy site-worker.sh and site-worker.php file
$chmod +x site-worker.sh
$chmod +x site-worker.php
$sudo crontab -l
$sudo crontab -e
49  * * * * /usr/bin/indexer --quiet --all --rotate >> /var/log/sphinxsearch/cron.log 2>&1
*/17 * * * * /home/rjha/cron/site-worker.sh >> /var/www/log/cron.log 2>&1
@imp
fix send_activity_mail method on DEV machines
patch it to return w/o sending mails
run ./site-worker.sh from command line to test things!
```


16) Reboot your machine.

Now access http://mint.3mik.com

