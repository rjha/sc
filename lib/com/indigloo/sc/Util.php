<?php
    namespace com\indigloo\sc {

        class Util {

            static function convertDBTime($original) {

                if (Util::tryEmpty($original)) {
                    return "" ;
                }

                $format = "%e %b, %Y" ;
                $dt = strftime($format, strtotime($original));
                return $dt;
            }
        }
}
?>
