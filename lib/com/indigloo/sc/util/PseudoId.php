<?php
namespace com\indigloo\sc\util{

    class PseudoId {

         private static $map = array(
            '0'=>'a','1'=>'b','2'=>'c','3'=>'d','4'=>'e',
            '5'=>'f','6'=>'g','7'=>'h','8'=>'i','9'=>'j',
            'j'=>'9','i'=>'8','h'=>'7','g'=>'6','f'=>'5',
            'e'=>'4','d'=>'3','c'=>'2','b'=>'1','a'=>'0');

        static function decode($esx) {
            $sx = base_convert($esx,10,26);
            $dsx = '';
            for($i = 0 ; $i < strlen($sx); $i++) {
                $dsx .= self::$map[$sx{$i}];
            }

            // base_convert is string operation but our 
            // reverse map can only produce an integer 

            if(!is_numeric($dsx)) return -1 ;

            $dsx = intval($dsx);
            $dsx = $dsx - 271 ;
            return $dsx;
        }

        /**
         * @imp encode should be a monotonically increasing function of x
         * the map used to do encoding should preserve the order of digits
         *
         */
        static function encode($x) {

            if(!is_numeric($x)) {
                trigger_error("bad input: encoder works on numbers only!",E_USER_ERROR);
            }

            //e=2.718...
            $x = $x + 271 ;
            $sx = strval($x);
            $esx = '' ;
            for($i = 0; $i < strlen($sx); $i++) {
                $esx .= self::$map[$sx{$i}];
            }

            //convert to base10 
            return base_convert($esx,26,10);
        }
 
    }

}
?>
