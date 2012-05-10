<?php
namespace com\indigloo\sc\model {
    
    use \com\indigloo\Util as Util ;

    class Comment extends Table {

         const LOGIN_ID = 1;
         
         function __construct() {

         }

         public function getColumns() {
             $columns = array(self::LOGIN_ID => "login_id");
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
