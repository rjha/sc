<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
	use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;

 
	
    class Post {
        
        function process($params,$options) {
            $file = $_SERVER['APP_WEB_DIR']. '/view/item.php' ;
            
            if(is_null($params) || empty($params))
                trigger_error("Required params is null or empty", E_USER_ERROR);

			$itemId = Util::getArrayKey($params,"item_id");

            if($itemId < 1200) {
                //Add permanent redirect
                $redirectUrl = "/item/".PseudoId::encode($itemId) ;
                header( "HTTP/1.1 301 Moved Permanently" ); 
                header( "Location: ".$redirectUrl );   
                exit ;
            }

			$questionDao = new \com\indigloo\sc\dao\Question();
            $questionId = PseudoId::decode($itemId);
			$questionDBRow = $questionDao->getOnId($questionId);

			$imagesJson = $questionDBRow['images_json'];
			$images = json_decode($imagesJson);
			
			$linksJson = $questionDBRow['links_json'];
			$links = json_decode($linksJson);

			$answerDao = new \com\indigloo\sc\dao\Answer();
			$answerDBRows = $answerDao->getOnQuestionId($questionId);
			
			$gWeb = \com\indigloo\core\Web::getInstance();
			$sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
			$loginId = NULL ;
            $gSessionLogin = NULL ;

			if(is_null($gSessionLogin)) {
				$login = \com\indigloo\sc\auth\Login::tryLoginInSession();
				if(!is_null($login)) {
					$loginId = $login->id ;
				}
			}

            $xids = array();
            $xrows = array();
            $group_slug = $questionDBRow['group_slug'];

            if(!Util::tryEmpty($group_slug)) {

                $groups = explode(Constants::SPACE,$group_slug); 
                $sphinx = new \com\indigloo\sc\search\SphinxQL();

                foreach($groups as $group) {
                    $ids = $sphinx->getGroups($group,0,5);  
                    foreach($ids as $id){
                        if(!in_array($id,$xids) && ($id != $questionId)) {
                            array_push($xids,$id);
                        }
                    }
                }

                //get posts on groups
                if(!empty($xids)) {
                    $xrows = $questionDao->getOnSearchIds($xids);
                }
            }

            //get posts for same user
            $userRows = $questionDao->getOnLoginId($questionDBRow['login_id'],10);
            foreach($userRows as $userRow) {
                if(!in_array($userRow['id'],$xids) && ($userRow['id'] != $questionId)) {
                    array_push($xrows,$userRow);
                }
            }
            
            if(sizeof($xrows) < 12 ) {
                //how many?
                $limit = 12 - (sizeof($xrows)) ;
                $randomRows = $questionDao->getRandom($limit);
                $xrows = array_merge($xrows,$randomRows);
            }

			$loginUrl = "/user/login.php?q=".$_SERVER['REQUEST_URI'];
			$formErrors = FormMessage::render(); 

			$pageTitle = Util::abbreviate($questionDBRow['title'],70);
			$pageMetaDescription = Util::abbreviate($questionDBRow['description'],160);
            $pageUrl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			
			include($file);
        }
    }
}
?>
