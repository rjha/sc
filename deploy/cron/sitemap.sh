#!/bin/bash

if [ ! "$UID" -eq "0" ]
then
    echo "please run this script as root."
    exit 100 ;
fi

lf=/var/run/sc-sitemap-generator.pid
touch  $lf
read lastPID < $lf

# -n : string is not null : should be quoted
# -e : file exists

if [[ -n "$lastPID"  &&  -e /proc/$lastPID ]]
then
   echo " sitemap generator is running with PID $lastPID"
   exit
else
    # $$ is pid of "this" script 
    echo $$ > $lf
    NOW=`date +%d-%b-%H:%M:%S`
    echo " sitemap generator started with PID  $$ @ $NOW"
    `dirname $0`/sitemap.php
    #copy to website root
    if [[ "$?" -eq "0" ]]
    then
        gzip sitemap*.xml 
        cp sitemap*.xml.gz  /var/www/vhosts/www.3mik.com/htdocs/.
    fi
fi
