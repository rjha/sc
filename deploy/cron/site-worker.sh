#!/bin/bash

if [ ! "$UID" -eq "0" ]
then
    echo "please run this script as root."
    exit 100 ;
fi

lf=/var/run/sc-site-worker.pid
touch  $lf
read lastPID < $lf

# -n : string is not null : should be quoted
# -e : file exists

if [[ -n "$lastPID"  &&  -e /proc/$lastPID ]]
then
   echo " site worker is running with PID $lastPID"
   exit
else
    # $$ is pid of "this" script 
    echo $$ > $lf
    NOW=`date +%d-%b-%H:%M:%S`
    echo " site worker started with PID  $$ @ $NOW"
    `dirname $0`/site-worker.php
fi
