<?php
namespace com\indigloo\sc\model {

    use \com\indigloo\Util as Util ;

    class Bookmark extends Table {

         const SUBJECT_ID_COLUMN = 1;
         const VERB_COLUMN = 2;

         function __construct() {

         }

         public function getColumns() {
             //UI to table columns mapping
             $columns = array(self::SUBJECT_ID_COLUMN => "subject_id", self::VERB_COLUMN => 'verb');
             return $columns;
         }

         public function getValue($alias,$column,$condition,$value) {
             $column = (is_null($alias)) ? $column : $alias.".".$column ;
             settype($value,"integer");
             $sql = sprintf("%s %s %s ", $column,$condition,$value);
             return $sql ;
         }

    }

}
?>
