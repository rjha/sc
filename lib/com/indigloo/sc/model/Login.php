<?php
namespace com\indigloo\sc\model {
    
    use \com\indigloo\Util as Util ;

    class Login {

         const CREATED_ON = 1;
         private $columns ;
         
         function __construct() {
             $this->columns = array( self::CREATED_ON => "created_on");
         }
         
         function filter($filter,$alias) {

             $column = Util::tryArrayKey($this->columns,$filter->name);
             if(is_null($column)) {
                 $message = sprintf("No column %s in model",$filter->name);
                 trigger_error($message,E_USER_ERROR); 
             }

             //Add alias to column
             $column = (is_null($alias)) ? $column : $alias.".".$column ;

             $value = $filter->value ;
             if($filter->name == self::CREATED_ON) {
                 //special treatment
                 $sql = " created_on > (now() - interval 24 HOUR) ";
                 return $sql;
             }

             $sql = sprintf("%s %s %s ", $column,$filter->condition,$value);
             return $sql ;
         }
    }

}
?>
