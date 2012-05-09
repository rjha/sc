<?php
namespace com\indigloo\sc\model {
    
    use \com\indigloo\Util as Util ;

    class Group {

         const TOKEN = 1;
         private $columns ;
         
         function __construct() {
             $this->columns = array(self::TOKEN => "token");
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
             $sql = sprintf("%s %s '%s%s' ", $column,$filter->condition,$value,'%%');
             return $sql ;
         }
    }

}
?>
