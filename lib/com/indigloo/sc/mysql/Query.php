<?php

namespace com\indigloo\sc\mysql {

	class Query {
        
        private $sql ;
        
        function __construct($sql){
            $this->sql = $sql ;
        }
        
        function filter($filters) {
            if(is_null($filters) || empty($filters)){
                return ;
            }
            $count = 1 ;
            foreach($filters as $filter) {
                $condition = $filter->getSQL();
                if($count == 1 ) {
                    $this->sql .= sprintf(" where %s ", $condition);
                } else {
                    $this->sql .= sprintf(" and %s ", $condition);
                }
                $count++ ;
                
            }
        }
        
        function getSQL() {
            return $this->sql ;
        }
        
	}
}
?>
