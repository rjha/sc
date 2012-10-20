<?php
namespace com\indigloo\sc\model {
    
    use \com\indigloo\Util as Util ;

    class Group extends Table {

         const TOKEN = 1;
         const LOGIN_ID = 2;
         
         function __construct() {

         }

         public function getColumns(){
             $columns = array(self::TOKEN => "token", self::LOGIN_ID => "login_id");
             return $columns;
         }

         public function getValue($alias,$column,$condition,$value) {
            //@todo fix expensive-query 
            if(strcmp($column,'token') == 0 ) {
                 $column = (is_null($alias)) ? $column : $alias.".".$column ;
                 $sql = sprintf("%s %s '%s%s' ", $column,$condition,$value,'%%');
                 return $sql ;
            }

            $column = (is_null($alias)) ? $column : $alias.".".$column ;
            $sql = sprintf("%s %s %s ", $column,$condition,$value);
            return $sql ;
         }
         
    }

}
?>
