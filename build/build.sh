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

java -jar yuicompressor-2.4.7.jar --nomunge --preserve-semi --disable-optimizations bundle-full.js > bundle.js
java -jar yuicompressor-2.4.7.jar --nomunge --preserve-semi --disable-optimizations bundle-full.css  > bundle.css

#copy
cp ./bundle.js ../web/js/bundle.js
cp ./bundle.css ../web/css/bundle.css

