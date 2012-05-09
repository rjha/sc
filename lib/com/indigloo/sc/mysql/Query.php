<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\Util as Util;

	class Query {
        
        const SQL_WHERE = "where";
        const SQL_AND = "and";

        private $query ;
        private $amap ;
        private $prefix;
        
        function __construct($prefix=self::SQL_WHERE){
            $this->query = '' ;
            $this->amap = array();
            $this->prefix = $prefix;
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
