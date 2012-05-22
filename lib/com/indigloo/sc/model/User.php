<?php
namespace com\indigloo\sc\model {
    
    use \com\indigloo\Util as Util ;

    class User extends Table {

         const CREATED_ON = 1;
         
         function __construct() {

         }

         public function getColumns() {
             $columns = array( self::CREATED_ON => "created_on");
             return $columns;
         }

         public function getValue($alias,$column,$condition,$value) {
             if(strcmp($column,"created_on") == 0) {
                 //special case: all processing in this block
                 //value comes in as 24 HOUR / 1 WEEK / 1 MONTH etc.
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
