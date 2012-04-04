<?php

namespace com\indigloo\sc\facebook {

    use \com\indigloo\Logger as Logger ;

    class Util {

        static function getObjectIdInSet($set) {
            $fbId = NULL ;

            //get what is after last dot in set
            $pos = \strrpos($set,".");
            if($pos !== false) {
                $fbId = substr($set,$pos+1);
            }

            return $fbId ;
        }

	}
}

?>
