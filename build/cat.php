#!/usr/bin/php
<?php


    error_reporting(-1);

    function make_js_bundle($root3p,$rootsc) {
        //list of 3p files to concatenate
        $files = array();
        $files[] = "jquery/jquery-1.8.2.js" ;
        $files[] = "jquery/jquery.ajaxQueue.js" ;
        $files[] = "jquery/jquery.validate.1.9.0.js" ;
        $files[] = "jquery/isotope/jquery.isotope.js" ;
        $files[] = "jquery/infinite/jquery.infinitescroll.hacked.js" ;
        $files[] = "bootstrap/2.1.1/js/bootstrap.js" ;
        $files[] = "ful/valums/fileuploader.js" ;
        $files[] = "fancybox/jquery.fancybox-1.3.4.js" ;

        $scfiles = array();
        $scfiles[] = "js/sc.js" ;

        //output file name
        $bundle = "bundle-full.js" ;
        $fp = fopen($bundle,"w");


        for($i = 0 ; $i < sizeof($files) ;  $i++ ) {
            $glob = file_get_contents($root3p.$files[$i]);
            fwrite($fp,$glob);
            $separator = sprintf("\n\n /* cat:3p:file:%d:%s */ \n\n",$i+1,$files[$i]);
            fwrite($fp,$separator);
        }

        for($i = 0 ; $i < sizeof($scfiles) ;  $i++ ) {
            $glob = file_get_contents($rootsc.$scfiles[$i]);
            fwrite($fp,$glob);
            $separator = sprintf("\n\n /* cat:sc:file:%d:%s */ \n\n",$i+1,$scfiles[$i]);
            fwrite($fp,$separator);
        }

        fclose($fp);

    }

    function make_css_bundle($root3p,$rootsc) {
        //list of 3p files to concatenate
        $files = array();
        $files[] = "bootstrap/2.1.1/css/bootstrap.css" ;
        $files[] = "fancybox/jquery.fancybox-1.3.4.css";
        $files[] = "zocial/css/zocial.css";
        $files[] = "ful/valums/fileuploader.css" ;

        $scfiles = array();
        $scfiles[] = "css/sc.css" ;

        //output file name
        $bundle = "bundle-full.css" ;
        $fp = fopen($bundle,"w");


        for($i = 0 ; $i < sizeof($files) ;  $i++ ) {
            $glob = file_get_contents($root3p.$files[$i]);
            fwrite($fp,$glob);
            $separator = sprintf("\n\n /* cat:3p:file:%d:%s */ \n\n",$i+1,$files[$i]);
            fwrite($fp,$separator);
        }

        for($i = 0 ; $i < sizeof($scfiles) ;  $i++ ) {
            $glob = file_get_contents($rootsc.$scfiles[$i]);
            fwrite($fp,$glob);
            $separator = sprintf("\n\n /* cat:sc:file:%d:%s */ \n\n",$i+1,$scfiles[$i]);
            fwrite($fp,$separator);
        }

        //reponsive css is last include
        $glob = file_get_contents($root3p.'bootstrap/2.1.1/css/bootstrap-responsive.css');
        fwrite($fp,$glob);

        fclose($fp);

    }

    //root of 3rd party libraries
    $root3p = "/home/rjha/code/github/webgloo/web/3p/" ;
    $rootsc = "/home/rjha/code/github/sc/web/" ;

    make_js_bundle($root3p,$rootsc);
    make_css_bundle($root3p,$rootsc);



?>
