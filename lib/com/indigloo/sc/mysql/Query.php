<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\Util as Util;

	class Query {
        
        const SQL_WHERE = "where";
        const SQL_AND = "and";

        private $query ;
        private $amap ;
        private $prefix;
        private $mysqli;
        
        function __construct($mysqli,$prefix=self::SQL_WHERE){
            $this->query = '' ;
            $this->amap = array();
            $this->prefix = $prefix;
            $this->mysqli = $mysqli;
        }

        function setAlias($class,$alias){
            $this->amap[$class] = $alias;
        }
        
        function filter($filters) {
            if(is_null($filters) || empty($filters)){
                return ;
            }

            foreach($filters as $filter) {
                $model = $filter->model;
                $alias = Util::tryArrayKey($this->amap,get_class($model));
                $value = $filter->value ;
                //sanitize input
                $value = $this->mysqli->real_escape_string($value);
                $filter->sanitize($value);
               
                $condition = $model->filter($filter,$alias);
                $this->addCondition($condition);
            }
        }
        
        function addCondition($sql) {
            $this->query = sprintf(" %s %s %s ",$this->query,$this->prefix,$sql);
            $this->prefix = self::SQL_AND ;
        }

        function get() {
            return $this->query;
        }
        
	}
}
?>
