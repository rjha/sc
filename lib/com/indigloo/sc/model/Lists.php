<?php
namespace com\indigloo\sc\model {

    use \com\indigloo\Util as Util ;

    class Lists extends Table {

        
        const LIST_ID = 1 ;
        const LOGIN_ID = 2;
        const DL_BIT = 3 ;

        function __construct() {

        }

        public function getColumns() {
            //UI columnn - DB column mapping
            $columns = array(
                self::LOGIN_ID => "login_id",
                self::LIST_ID => "id",
                self::DL_BIT => "dl_bit");

            return $columns;
        }

        public function getValue($alias,$column,$condition,$value) {
            $column = (is_null($alias)) ? $column : $alias.".".$column ;
            $sql = sprintf("%s %s %s ", $column,$condition,$value);
            return $sql ;
        }

    }

}
?>
