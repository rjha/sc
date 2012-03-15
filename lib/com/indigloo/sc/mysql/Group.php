<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Group {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Group';

		static function getLatest($limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select token from sc_user_group order by id desc LIMIT ".$limit ; 
			$rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
		}
	}
}
?>
