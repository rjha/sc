<?php
namespace com\indigloo\sc\model {
    
    use \com\indigloo\Util as Util ;

    class Post {

         const LOGIN_ID = 1;
         const FEATURED = 2 ;
         private $columns ;
         
         function __construct() {
             $this->columns = array(
                 self::LOGIN_ID => "login_id",
                 self::FEATURED => "is_feature");
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
             if($filter->name == self::FEATURED) {
                 $value = ($value) ?  "1" : "0" ;
             }

             $sql = sprintf("%s %s %s ", $column,$filter->condition,$value);
             return $sql ;
         }
    }

}
?>
