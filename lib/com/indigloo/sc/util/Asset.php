<?php
namespace com\indigloo\sc\util{

    class Asset {

        static function version($path) {
            $link = '' ;
            //worst case - return incoming path as it is.
            $fname = $path ;
            $parts = pathinfo($path);
            //supplied path is relative to APP_WEB_DIR
            $fullpath = APP_WEB_DIR.$path ;
            $ts = 1 ;
            $slash = "/" ;
            $dot = "." ;

            if(\file_exists($fullpath)) {
                $ts = \filemtime($fullpath);
                $ts = "t".$ts ;
                settype($ts,"string");
                // 10 digit unix timestamp cover from
                // 09 sept. 2001 - 20 Nov. 2286
                // $perl -MPOSIX -le 'print ctime(1000000000)' 
                if(strlen($ts) != 11 ) {
                    trigger_error("asset versioning timestamp is out of range");
                }

                $fname = $parts["dirname"].$slash.$parts["filename"].$dot.$ts.$dot.$parts["extension"];
            }

            if(strcasecmp($parts["extension"],"css") == 0 ) {
                $tmpl = '<link rel="stylesheet" type="text/css" href="{fname}" >' ;
                $link = str_replace("{fname}",$fname,$tmpl);
            }

            if(strcasecmp($parts["extension"],"js") == 0 ) {
                $tmpl = '<script type="text/javascript" src="{fname}"></script>' ;
                $link = str_replace("{fname}",$fname,$tmpl);
            }

            //return css or js link
            return $link ;
        }

    }

}
?>
