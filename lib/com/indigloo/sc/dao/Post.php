<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\mysql as mysql;
    
    class Post {

		const LOGIN_ID_COLUMN  = "oowyh1vm";
		const FEATURE_COLUMN  = "dwndk1vo";

		function createDBFilter($filter) {
			$map = array(self::LOGIN_ID_COLUMN => mysql\Post::LOGIN_COLUMN,
                            self::FEATURE_COLUMN => mysql\Post::FEATURE_COLUMN);
			$dbfilter = mysql\Helper::createDBFilter($filter,$map);
			return $dbfilter ;
		}

		function getOnId($postId) {
			$row = mysql\Post::getOnId($postId);
			return $row ;
		}

		function getOnLoginId($loginId,$limit) {
			$rows = mysql\Post::getOnLoginId($loginId,$limit);
			return $rows ;
		}

		function getOnSearchIds($arrayIds) {
            if(empty($arrayIds)) { return array(); }

			$strIds = implode(",",$arrayIds);
			$rows = mysql\Post::getOnSearchIds($strIds);
			return $rows ;
		}
		
        function getRandom($limit) {
			$rows = mysql\Post::getRandom($limit);
			return $rows ;
		}

        function getPosts($filter,$limit) {
            $dbfilter = $this->createDBFilter($filter);
			$rows = mysql\Post::getPosts($dbfilter,$limit);
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

				$rows = mysql\Post::getPaged($start,$direction,$count,$dbfilter);
				return $rows ;
			}
		}

		function getLatest($count,$filter=NULL) {
			$dbfilter = $this->createDBFilter($filter);
			$rows = mysql\Post::getLatest($count,$dbfilter);
			return $rows ;
		}
		
		function getTotalCount($filter=NULL) {
			$dbfilter = $this->createDBFilter($filter);
			$row = mysql\Post::getTotalCount($dbfilter);
            return $row['count'] ;
		}

        function create($title,
						$description,
						$location,
						$tags,
						$loginId,
						$linksJson,
                        $imagesJson,
                        $groupSlug) {
			
            $data = mysql\Post::create(
								$title,
								$description,
								$location,
								$tags,
								$loginId,
								$linksJson,
                                $imagesJson,
                                $groupSlug);
			
            return $data ;
        }
		
		
		function update($postId,
						$title,
						$description,
						$location,
						$tags,
						$linksJson,
                        $imagesJson,
                        $groupSlug) {
			
			$loginId = \com\indigloo\sc\auth\Login::getLoginIdInSession();
            $code = mysql\Post::update($postId,
						       $title,
                               $description,
                               $location,
                               $tags,
                               $linksJson,
							   $imagesJson,
                               $loginId,
                               $groupSlug);
            return $code ;
        }

		function delete($postId){
			$loginId = \com\indigloo\sc\auth\Login::getLoginIdInSession();
            $code = mysql\Post::delete($postId,$loginId);
            return $code ;
        }

        function doAdminAction($strIds,$action){
            $loginId = \com\indigloo\sc\auth\Login::getLoginIdInSession();
            $data = array('code' => 0);

            switch($action) {
            case 'delete' :
                //do nothing till we fix the interface
                break;
            case 'add-feature' :
                $code = mysql\Post::setFeature($loginId,$strIds,1);
                $data["code"] = $code ;
                break;
            case 'remove-feature' :
                $code = mysql\Post::setFeature($loginId,$strIds,0);
                $data["code"] = $code ;
                break;
            default:
                break;
            }

            return $data ;
        }


    }

}
?>
