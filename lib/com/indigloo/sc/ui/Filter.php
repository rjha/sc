<?php
namespace com\indigloo\sc\ui {
    
    class Filter {
        private $model;
        private $sql ;
        
        function __construct($model) {
             $this->model = $model ;
             $this->map = array();
             $this->sql = '' ;
        }
        
        function add($key,$value) {
            $this->sql = $this->model->process($key,$value);
        }
        
        function getSQL() {
            return $this->sql;
        }
    }
}

?>
