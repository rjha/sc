<?php
namespace com\indigloo\sc\model {
    
    use \com\indigloo\Util as Util ;

    class Group extends Table {

         const TOKEN = 1;
         
         function __construct() {

         }

         public function getColumns(){
             $columns = array(self::TOKEN => "token");
             return $columns;
         }

         public function getValue($alias,$column,$condition,$value) {

             if(strcmp($column,'token') == 0 ) {
                 $column = (is_null($alias)) ? $column : $alias.".".$column ;
                 $sql = sprintf("%s %s '%s' ", $column,$condition,$value);
                 return $sql ;

             }

             $column = (is_null($alias)) ? $column : $alias.".".$column ;
             $sql = sprintf("%s %s %s ", $column,$condition,$value);
             return $sql ;
         }
         
    }

}
?>
