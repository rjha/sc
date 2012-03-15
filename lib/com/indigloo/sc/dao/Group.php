<?php

namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
	
    class Group {

		function getLatest($limit) {
			$rows = mysql\Group::getLatest($limit);
			return $rows ;
		}
		
    }
}
?>
