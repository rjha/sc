<?php
namespace com\indigloo\sc\model {

    use \com\indigloo\Util as Util ;

    class Post extends Table {

         const LOGIN_ID = 1;
         const FEATURED = 2 ;
         const CREATED_ON = 3 ;
         const ITEM_ID = 4 ;

         function __construct() {

         }

         public function getColumns() {
             //UI columnn - DB column mapping
             $columns = array(
                 self::LOGIN_ID => "login_id",
                 self::ITEM_ID => "pseudo_id",
                 self::CREATED_ON => "created_on",
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

             if(strcmp($column,"created_on") == 0) {
                 //mysql format for date comparison
                 $column = (is_null($alias)) ? $column : $alias.".".$column ;
                 $sql = sprintf("%s > (now() - interval %s) ",$column,$value);
                 return $sql;
             }

             $column = (is_null($alias)) ? $column : $alias.".".$column ;
             $sql = sprintf("%s %s %s ", $column,$condition,$value);
             return $sql ;
         }

    }

}
?>
