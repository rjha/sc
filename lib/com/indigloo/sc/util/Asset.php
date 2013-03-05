<?php
namespace com\indigloo\sc\util{

    use \com\indigloo\Configuration as Config ;

    /* 
     * @see also
     * http://php.net/manual/en/language.namespaces.fallback.php
     * when PHP encounters a non-qualified class name - it assumes current namespace
     * when PHP encounters a non-qualified function name - it will fallback to global 
     * definition. 
     * Good practice is to qualify all class/function names.
     * 
     */
    class Asset {

        private static function getTimeStampName($path) {
            
            $fname = $path ;
            $parts = \pathinfo($path);
            //supplied path is relative to APP_WEB_DIR
            $fullpath = APP_WEB_DIR.$path ;
            $ts = 1 ;
            $slash = "/" ;
            $dot = "." ;

            if(\file_exists($fullpath)) {
                $ts = \filemtime($fullpath);
                $ts = "t".$ts ;
                \settype($ts,"string");
                // 10 digit unix timestamp cover from
                // 09 sept. 2001 - 20 Nov. 2286
                // $perl -MPOSIX -le 'print ctime(1000000000)' 

                $length = \strlen($ts);
                if($length != 11 ) {
                    $message = "Asset versioning timestamp is out of range" ;
                    throw new \Exception($message);
                }

                $fname = $parts["dirname"].$slash.$parts["filename"].$dot.$ts.$dot.$parts["extension"];
            }

            return $fname ;
        }

        private static function getCdnName($path) {
            $version = Config::getInstance()->get_value("asset.cdn.version");
            if(empty($version)) {
                $message = "config is missing key asset.cdn.version" ;
                throw new \Exception($message);
            }

            $parts = \pathinfo($path);
            // no directory hierarchy for cdn files
            $fname = \sprintf("http://cdn1.3mik.com/%s-v%s.%s",$parts["filename"],$version,$parts["extension"]);
            return $fname ;
        }

        static function version($path) {
            $link = '' ;
            $fname = $path ;

            // let the error bubble up
            // it is better to die than serve wrong file!
            $scheme = Config::getInstance()->get_value("asset.version.scheme","timestamp");

            if(\strcasecmp($scheme, "timestamp") == 0 ) {
                $fname = self::getTimeStampName($path);
            }

            if(\strcasecmp($scheme, "cdn") == 0 ) {
                $fname = self::getCdnName($path);
            }
            
            $parts = \pathinfo($path);
            if(\strcasecmp($parts["extension"],"css") == 0 ) {
                $tmpl = '<link rel="stylesheet" type="text/css" href="{fname}" >' ;
                $link = \str_replace("{fname}",$fname,$tmpl);
            }

            if(\strcasecmp($parts["extension"],"js") == 0 ) {
                $tmpl = '<script type="text/javascript" src="{fname}"></script>' ;
                $link = \str_replace("{fname}",$fname,$tmpl);
            }

            //return css or js link
            return $link ;
        }

    }

}
?>
