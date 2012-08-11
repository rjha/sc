#!/bin/bash

set -e

if [ "$UID" -eq "0" ]
then
    echo "Do not run this script as root."
    exit 100 ;
fi

#cleanup
rm --force bundle* 

#generate
`dirname $0`/cat.php

#minify

java -jar yuicompressor-2.4.7.jar   bundle-full.js -o bundle.js  --charset utf-8 --disable-optimizations --preserve-semi --line-break 0
java -jar yuicompressor-2.4.7.jar   bundle-full.css -o bundle.css  --charset utf-8 --disable-optimizations --preserve-semi --line-break 0


#copy
cp ./bundle*.js ../web/js/.
cp ./bundle*.css ../web/css/.


#cleanup
rm --force bundle*
