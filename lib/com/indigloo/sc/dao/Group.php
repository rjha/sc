<?php

namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
	
    class Group {

		function getLatest($limit) {
			$rows = mysql\Group::getLatest($limit);
			return $rows ;
		}

        function getOnLoginId($loginId) {
            $rows = mysql\Group::getOnLoginId($loginId);
            return $rows ;
        }

        function getCountOnLoginId($loginId) {
            $count = 0 ;
            $row = mysql\Group::getCountOnLoginId($loginId);
            if(isset($row) && !empty($row)) {
                $count = $row['count'];
            }

            return $count ;
        }
		
    }
}
?>
