<?php
namespace com\indigloo\sc\model {
    
    use \com\indigloo\Util as Util ;

    class Post extends Table {

         const LOGIN_ID = 1;
         const FEATURED = 2 ;

         function __construct() {

         }

         public function getColumns() {
             //UI columnn - DB column mapping
             $columns = array(
                 self::LOGIN_ID => "login_id",
                 self::FEATURED => "is_feature");

             return $columns;
         }

         public function getValue($alias,$column,$condition,$value) {

             if(strcmp($column,"is_feature") == 0 ) {
                 $column = (is_null($alias)) ? $column : $alias.".".$column ;
                 $value = ($value) ?  "1" : "0" ;
                 $sql = sprintf("%s %s %s ", $column,$condition,$value);
                 return $sql;
             }

             $column = (is_null($alias)) ? $column : $alias.".".$column ;
             $sql = sprintf("%s %s %s ", $column,$condition,$value);
             return $sql ;
         }
         
    }

}
?>
