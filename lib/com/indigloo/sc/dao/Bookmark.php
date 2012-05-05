<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
	
    class Bookmark {

        function add($loginId,$postId,$action) {
            $row = mysql\Bookmark::getRowCount($loginId,$postId,$action);
            $count = $row['count'] ;
            $code = 0 ;
            if($count == 0 ) {
                //actually insert
                $code = mysql\Bookmark::add($loginId,$postId,$action);
                return $code ;
            }

            return $code ;
        }

        function delete($bookmarkId) {
            $code = mysql\Bookmark::delete($bookmarkId);
            return $code ;
        }

        function getOnLoginId($loginId) {
			$rows = mysql\Bookmark::getOnLoginId($loginId);
			return $rows ;
		}
		



    }

}
?>
