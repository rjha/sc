<?php
namespace com\indigloo\sc\ui {
    
    class Filter {
        const EQ = '=' ;
        const GT =  '>' ;
        const LT = '<' ;
        const LTE = '<=' ;
        const GTE = '>=' ;

        private $model;
        private $map ;
         
        function __construct($model) {
             $this->map = array();
             $this->map["model"] = $model ;
        }

        function add($name,$condition,$value) {
            $this->map["name"] = $name ;
            $this->map["condition"] = $condition ;
            $this->map["value"] = $value ;
        }

        function __get($name) {
            if(array_key_exists($name,$this->map)) {
                return $this->map[$name] ;
            } else {
                return NULL;
            }
        }
    }
}

?>
