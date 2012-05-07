<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
	
    class Bookmark {

        function add($loginId,$itemId,$action) {
            $row = mysql\Bookmark::find($loginId,$itemId,$action);
            $count = $row['count'] ;
            $code = 0 ;
            if($count == 0 ) {
                //actually insert
                $code = mysql\Bookmark::add($loginId,$itemId,$action);
                return $code ;
            }

            return $code ;
        }

        function delete($bookmarkId) {
            $code = mysql\Bookmark::delete($bookmarkId);
            return $code ;
        }

        function unfavorite($loginId,$itemId) {
            $code = mysql\Bookmark::unfavorite($loginId,$itemId);
            return $code ;
        }
        
        function getOnLoginId($loginId,$action) {
			$rows = mysql\Bookmark::getOnLoginId($loginId,$action);
			return $rows ;
		}
		



    }

}
?>
