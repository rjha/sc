#!/bin/bash

status_nginx()
{
	ps -ef | grep -v grep | grep nginx > /dev/null 
	# if not found then 1 else 0 (success)
	if [ $? -eq 1 ]
	then
		echo "nginx is not running ..." 
		return 1 
	else
		n_pid=`cat /usr/local/var/run/nginx.pid`
		echo "nginx is running with pid $n_pid"
		return 0
	fi
}


start_nginx() 
{
	status_nginx 
	#status will return 1 when nginx not found
	if [ $? -eq 1 ]
	then
		/usr/local/sbin/nginx
		echo "started nginx ..."
	fi

}

stop_nginx()
{
	
	status_nginx
	# if not found - equals to 1, start it
	if [ $? -eq 0 ]
	then
		/usr/local/sbin/nginx -s stop
		echo "stopped nginx ..."
	fi
}

status_php()
{
	ps -ef | grep -v grep | grep php > /dev/null
	# if not found - equals to 1
	if [ $? -eq 1 ]
	then
		echo "php fastcgi process manager (fpm) is not running ...."
		return 1 
	else
		n_pid=`cat /usr/local/var/run/php-fpm.pid`
		echo "php fastcgi process manager (fpm) is running with pid $n_pid ...."
		return 0
	fi


}


start_php() 
{
	status_php
	#status will return 1 when php fpm not running 
	if [ $? -eq 1 ]
	then
		/usr/local/sbin/php-fpm.dSYM
		echo "started php-fpm listeners... "
	fi
}

stop_php()
{
	status_php
	# if found - equals to 0
	if [ $? -eq 0 ]
	then
		n_pid=`cat /usr/local/var/run/php-fpm.pid`
		kill $n_pid 
		echo "stopped php-fpm listeners ... "
	fi
}

status_mysql ()
{

	ps -ef | grep -v grep | grep mysql > /dev/null
	# if not found - equals to 1
	if [ $? -eq 1 ]
	then
		echo "mysql is not running..."
		return 1 
	else
		n_pid=`cat /usr/local/mysql/data/rjha-mbp13h.local.pid`
		echo "mysql  is running with pid $n_pid"
		return 0 
	fi
}


start_mysql ()
{
	
	status_mysql
	# mysql not found - 1 
	if [ $? -eq 1 ] 
	then
		echo "starting mysql database... "
		/usr/local/mysql/bin/mysqld_safe
	fi

}

stop_mysql()
{
	status_mysql
	# if found - equals to 0
	if [ $? -eq 0 ]
	then
		mysqladmin shutdown 
		echo "stopped  mysql database ..."
	fi
}



func_start() 
{
	start_nginx;
	sleep 2 ;
	start_php;
	sleep 2 ;
	start_mysql;
	sleep 2 ;
}

func_stop()
{
	read -p  "Are you sure? (y/n):" choice
	
	case "$choice" in 
		"Y" | "y" )
			stop_mysql;
			sleep 2 ;
			stop_php;
			sleep 2 ;
			stop_nginx;
			sleep 2 ;
		;;
		* )
			echo
			echo " GoodBye! "
			echo 
		;;
	esac	

}

func_restart()
{
	func_stop ;
	func_start ;
	
}

func_status ()
{
	status_mysql ;
	status_php;
	status_nginx ;

}


if [ "$UID" -eq "0" ]
then
	echo " running as root..." 
else
	echo "please run the script as root..."
	exit 101 
fi


#read user input 

case "$1" in
	"start" )
		func_start 
	
	;;
	"stop" )
		func_stop
	;;
	"status" )
		func_status
	;;
	"restart" )
		func_restart
	;;
	* )
		echo
		echo " == welcome to npm.sh (script to start/stop nginx, php-fpm and mysql == "
		echo " Usage : sudo npm.sh (status|start|stop|restart) "
	;;
esac





