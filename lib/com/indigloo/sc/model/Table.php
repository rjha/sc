<?php
namespace com\indigloo\sc\model {
    
    use \com\indigloo\Util as Util ;

    abstract class Table {

         abstract protected function getColumns();
         abstract protected function getValue($alias,$column,$condition,$value);

         function __construct() {

         }
         
         function filter($filter,$alias) {
             $columns = $this->getColumns(); 
             $column = Util::tryArrayKey($columns,$filter->name);
             if(is_null($column)) {
                 $message = sprintf("No column %s in model",$filter->name);
                 trigger_error($message,E_USER_ERROR); 
             }

             //find condition for this column using model
             $sql = $this->getValue($alias,$column,$filter->condition,$filter->value);
             return $sql ;
         }
    }

}
?>
