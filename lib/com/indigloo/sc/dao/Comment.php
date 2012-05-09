<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
	
    class Comment {

		function getOnPostId($postId) {
			$rows = mysql\Comment::getOnPostId($postId);
			return $rows ;
		}
		
		function getOnId($commentId) {
			$rows = mysql\Comment::getOnId($commentId);
			return $rows ;
		}

		function getPaged($paginator,$filter=NULL) {
 
			$limit = $paginator->getPageSize();

			if($paginator->isHome()){
				return $this->getLatest($limit,$filter);
				
			} else {
                $params = $paginator->getDBParams();
				$dbfilter = $this->createDBFilter($filter);
				$start = $params['start'];
				$direction = $params['direction'];

				$rows = mysql\Comment::getPaged($start,$direction,$limit,$dbfilter);
				return $rows ;
			}
		}

		function getLatest($limit,$filter=NULL) {
			$dbfilter = $this->createDBFilter($filter);
			$rows = mysql\Comment::getLatest($limit,$dbfilter);
			return $rows ;
		}
		
		function getTotalCount($filter=NULL) {
			$dbfilter = $this->createDBFilter($filter);
			$row = mysql\Comment::getTotalCount($dbfilter);
            return $row['count'] ;
		}

		function update($commentId,$comment) {
			$loginId = \com\indigloo\sc\auth\Login::tryLoginIdInSession();
			$code = mysql\Comment::update($commentId,$comment,$loginId) ;
			return $code ;
		}
			
        function create($postId, $comment,$loginId) {
            $code = mysql\Comment::create($postId, $comment, $loginId);
            return $code ;
        }

		function delete($commentId){
			$loginId = \com\indigloo\sc\auth\Login::tryLoginIdInSession();
            $code = mysql\Comment::delete($commentId,$loginId);
            return $code ;
        }

    }

}
?>
