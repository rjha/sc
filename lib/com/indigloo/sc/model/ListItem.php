<?php
namespace com\indigloo\sc\model {

    use \com\indigloo\Util as Util ;

    class ListItem extends Table {

        
        const LIST_ID = 1 ;
        
        function __construct() {

        }

        public function getColumns() {
            //UI columnn - DB column mapping
            $columns = array(self::LIST_ID => "list_id");
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
