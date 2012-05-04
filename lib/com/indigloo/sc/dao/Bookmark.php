<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
	
    class Bookmark {


        function add($loginId,$postId) {
            $code = mysql\Bookmark::add($loginId,$postId);
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
