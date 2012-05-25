<?php
namespace com\indigloo\sc\model {
    
    use \com\indigloo\Util as Util ;

    class Bookmark extends Table {

         const LOGIN_ID = 1;
         const ACTION = 2;
         
         function __construct() {

         }

         public function getColumns() {
             //UI to table columns mapping
             $columns = array(self::LOGIN_ID => "login_id", self::ACTION => 'action');
             return $columns;
         }

         public function getValue($alias,$column,$condition,$value) {
             $column = (is_null($alias)) ? $column : $alias.".".$column ;
             settype($value,"integer");
             $sql = sprintf("%s %s %s ", $column,$condition,$value);
             return $sql ;
         }

    }

}
?>
