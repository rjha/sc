<?php

namespace com\indigloo\sc\mysql {

	class Helper {

		static function createDBFilter($filter,$map) {
			$dbfilter = array();
			if(is_null($filter) || !is_array($filter)) { 
				return $dbfilter ;
			}

			if(!is_array($map)) {
				trigger_error("Wrong DB column map" , E_USER_ERROR);
			}

			foreach($filter as $key => $value) {
				if(array_key_exists($key,$map)) {
					$dbfilter[$map[$key]] = $value ;
				}
			}

			return $dbfilter ;
		}
	}
}
?>
