#!/bin/bash
lf=/var/run/sc-site-worker.pid
touch  $lf
read lastPID < $lf
if [ ! -z `echo $lastPID` -a -d /proc/$lastPID ]; then
   echo " site worker already running with PID $lastPID"
   exit
else
    echo "started site worker | PID in /var/run/sc-site-worker.pid"
    echo $$ > $lf
    `dirname $0`/site-worker.php
fi
