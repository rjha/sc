<?php
namespace com\indigloo\sc\command {

    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\sc\ui\Constants as UIConstants ;
    use \com\indigloo\Util as Util;

    class Bookmark {

        function execute($params) {
            $action = $params->action;
            $itemId = $params->itemId ;
            $loginId = $params->loginId ;
            $name = $params->name ;

            if(empty($action) || empty($itemId)) {
                $response = array("code" => 500 , "message" => "Bad input: missing required parameters.");
                return $response ;
            }

            $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();
            $postDao = new \com\indigloo\sc\dao\Post();
            $postId = PseudoId::decode($itemId);
            $postDBRow = $postDao->getOnId($postId);
            $title = $postDBRow["title"];
            $ownerId = $postDBRow["login_id"];
            $code = 200 ;

            switch($action) {
                case UIConstants::LIKE_POST:
                    $bookmarkDao->like($ownerId,$loginId,$name,$itemId,$title);
                    $message = sprintf(" Success! Like for item %s done.",$title);
                    break ;
                case UIConstants::SAVE_POST:
                    $bookmarkDao->favorite($ownerId,$loginId,$name,$itemId,$title);
                    $message = sprintf("Success! Item %s added to favorites",$title);
                    break;
                case UIConstants::REMOVE_POST :
                    $bookmarkDao->unfavorite($loginId,$itemId);
                    $message = sprintf("Success! Item %s removed from favorites",$title);
                    break ;
                default :
                    break;
            }

            $response = array("code" => $code, "message" => $message);
            return $response ;

        }
    }
}


?>
