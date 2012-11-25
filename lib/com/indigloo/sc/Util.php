<?php
    namespace com\indigloo\sc {

        use \com\indigloo\Util as CoreUtil ;

        class Util {

            static function convertDBTime($original) {

                if (CoreUtil::tryEmpty($original)) {
                    return "" ;
                }

                $format = "%e %b, %Y" ;
                $dt = strftime($format, strtotime($original));
                return $dt;
            }

            static function getItemIdInUrl($link){
                if(empty($link)) { return NULL ; }
                
                $link = trim($link);
                $scheme = \parse_url($link,PHP_URL_SCHEME);
                
                $link = (empty($scheme)) ? "http://".$link : $link ;
                $info = parse_url($link);
                $path = (isset($info["path"])) ? $info["path"] : NULL ;

                if(empty($path)) { return NULL ; }
                //remove leading and trailing slash
                $path = trim($path,"/");
                if(empty($path)) { return NULL ; }

                
                $pattern = '^item/(?P<item_id>\d+)$' ;
                //@see http://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
                $pattern = '{'.$pattern.'}u' ;

                $itemId = NULL ; 
                //match path against our pattern
                $count = preg_match($pattern,$path,$matches);
                if($count !== FALSE && ($count != 0 )) {
                    if(isset($matches["item_id"])) {
                        $itemId = $matches["item_id"];
                    }
                }

                if(!empty($itemId)) {
                    $itemId = trim($itemId);
                    //@imp do not use is_int() as it checks the var type
                    // for strings it will always return false.
                    settype($itemId, "integer");
                }
                
                $itemId = (!empty($itemId)) ? $itemId : NULL;
                return $itemId ;

            }
        }
}
?>
