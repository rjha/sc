<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
	
    class Comment {

		const LOGIN_ID_COLUMN = "scr1flma";

		function createDBFilter($filter) {
			$map = array(self::LOGIN_ID_COLUMN => mysql\Comment::LOGIN_COLUMN);
			$dbfilter = mysql\Helper::createDBFilter($filter,$map);
			return $dbfilter ;
		}


		function getOnPostId($postId) {
			$rows = mysql\Comment::getOnPostId($postId);
			return $rows ;
		}
		
		function getOnId($commentId) {
			$rows = mysql\Comment::getOnId($commentId);
			return $rows ;
		}

		function getPaged($paginator,$filter=NULL) {
 
			//translate the filter in terms of DB Column
			$params = $paginator->getDBParams();
			$count = $paginator->getPageSize();

			if($paginator->isHome()){
				return $this->getLatest($count,$filter);
				
			} else {
				//convert back to base10
				$dbfilter = $this->createDBFilter($filter);
				$start = $params['start'];
				$direction = $params['direction'];

				if(empty($start) || empty($direction)){
					trigger_error('No start or direction DB params in paginator', E_USER_ERROR);
				}

				$start = base_convert($start,36,10);

				$rows = mysql\Comment::getPaged($start,$direction,$count,$dbfilter);
				return $rows ;
			}
		}

		function getLatest($count,$filter=NULL) {
			$dbfilter = $this->createDBFilter($filter);
			$rows = mysql\Comment::getLatest($count,$dbfilter);
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
