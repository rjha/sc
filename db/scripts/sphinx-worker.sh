#!/bin/bash
lf=/var/run/sc-sphinx-worker.pid
touch  $lf
read lastPID < $lf
if [ ! -z `echo $lastPID` -a -d /proc/$lastPID ]; then
   echo " sphinx worker already running with PID $lastPID"
   exit
else

    TIME=`date`
    echo " sphinx worker @ $TIME : PID /var/run/sc-sphinx-worker.pid"
    echo $$ > $lf
    #copy query log here
    cp /usr/local/sphinx/var/log/query.log /home/rjha/cron/query.log
    chmod 755 /home/rjha/cron/query.log
    #process query log
    `dirname $0`/sphinx-worker.php
fi
