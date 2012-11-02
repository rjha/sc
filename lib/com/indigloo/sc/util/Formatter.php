<?php
namespace com\indigloo\sc\util{

    use \com\indigloo\Util as Util ;

    class Formatter {

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
