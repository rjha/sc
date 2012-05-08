<?php
namespace com\indigloo\sc\model {
    
    class Post {
         const LOGIN_ID = 1;
         const FEATURED = 2 ;
         private $columns ;
         
         function __construct() {
             $this->columns = array(
                 self::LOGIN_ID => "login_id",
                 self::FEATURED => "is_feature");
         }
         
         function process($key,$value) {
             //@todo - escaping?
             if($key == self::FEATURED) {
                 $value = ($value) ?  "1" : "0" ;
             }
             $sql = sprintf("%s = %s ", $this->columns[$key],$value);
             return $sql ;
            
         }
    }

}
?>
