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
# bundle-full.js
# bundle-full.css

`dirname $0`/cat.php

# write headers

CDN_VERSION="1.0"
GIT_VERSION=`git rev-parse HEAD`


cp bundle-full.js bundle-full.js.tmp
echo "/*! 3mik:git:${GIT_VERSION} */" > bundle-full.js
echo "/*! 3mik:cdn:${CDN_VERSION} */" >> bundle-full.js
cat bundle-full.js.tmp >> bundle-full.js

cp bundle-full.css bundle-full.css.tmp
echo "/*! 3mik:git:$GIT_VERSION */" > bundle-full.css
echo "/*! 3mik:cdn:$CDN_VERSION */" >> bundle-full.css
cat bundle-full.css.tmp >> bundle-full.css

rm bundle-full.js.tmp
rm bundle-full.css.tmp


#minify

java -jar yuicompressor-2.4.7.jar   bundle-full.js -o bundle.js  --charset utf-8 --disable-optimizations --preserve-semi --line-break 0
java -jar yuicompressor-2.4.7.jar   bundle-full.css -o bundle.css  --charset utf-8 --disable-optimizations --preserve-semi --line-break 0



#copy

cp ./bundle-full.js ../web/js/.
cp ./bundle.js ../web/js/.

cp ./bundle-full.css ../web/css/.
cp ./bundle.css ../web/css/.

#cdn copy
cp ./bundle.js ./bundle-v$CDN_VERSION.js
cp ./bundle.css ./bundle-v$CDN_VERSION.css

# remove app files from build area
rm --force bundle-full.js
rm --force bundle.js
rm --force bundle-full.css
rm --force bundle.css
