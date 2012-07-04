<?php
namespace com\indigloo\sc\command {

    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\sc\ui\Constants as UIConstants ;
    use \com\indigloo\Util as Util;

    class SocialGraph {

        function execute($params) {
            $action = $params->action;
            $followerId = $params->followerId ;
            $followingId = $params->followingId ;

            if(empty($followerId) || empty($followingId) || empty($action)) {
                $response = array("code" => 500 , "message" => "Bad input: missing required parameters.");
                return $response;
            }

            $userDao = new \com\indigloo\sc\dao\User();
            $followingDBRow = $userDao->getOnLoginId($followingId);
            $followingName = $followingDBRow['name'];

            $followerDBRow = $userDao->getOnLoginId($followerId);
            $followerName = $followerDBRow['name'];

            $socialGraphDao = new \com\indigloo\sc\dao\SocialGraph();
            $message = "";
            $code = 200 ;

            switch($action) {
                case UIConstants::FOLLOW_USER :
                    $socialGraphDao->follow($followerId,$followerName,$followingId,$followingName);
                    $message = sprintf("Success! You are following %s ",$followingName);
                    break ;
                case UIConstants::UNFOLLOW_USER :
                    $socialGraphDao->unfollow($followerId,$followingId);
                    $message = sprintf("Success! You are no longer following %s ",$followingName);
                    break ;
                default:
                    break;
            }

            $response = array("code" => $code, "message" => $message);
            return $response ;

        }
    }
}


?>