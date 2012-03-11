<?php

namespace com\indigloo\sc\dao {

    
    use com\indigloo\Util as Util ;
    use com\indigloo\sc\mysql as mysql;
    
    class Media {

        function add($mediaVO) {
            $mediaId = mysql\Media::add($mediaVO);
            if(empty($mediaId)) {
                trigger_error("No Media ID in DAO :: Error adding media", E_USER_ERROR);
            }
            
            return $mediaId ;
        }
        
        function getMediaOnPostId($postId) {
             $rows = mysql\Media::getMediaOnPostId($postId);
             return $rows;
        }
        
        function deleteOnId($mediaId) {
            mysql\Media::deleteOnId($mediaId);
            
        }
    }

}

?>
