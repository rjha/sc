<?php
namespace com\indigloo\sc\command {

    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\sc\ui\Constants as UIConstants ;
    use \com\indigloo\Util as Util;

    class Comment {

        function execute($params) {
            $action = $params->action;
            $comment = $params->comment ;

            if(empty($action) || empty($comment)) {
                $response = array("code" => 500 , "message" => "Bad input: missing required parameters.");
                return $response ;
            }

            $commentDao = new \com\indigloo\sc\dao\Comment();
            $code = 200 ;

            switch($action) {
                case UIConstants::ADD_COMMENT:

                    $loginId = $params->loginId ;
                    $name = $params->name ;

                    $ownerId = $params->ownerId ;
                    $postId = $params->postId ;
                    $title = $params->title ;
                    $comment = $params->comment ;

                    $commentDao->create($loginId,
                                        $name,
                                        $ownerId,
                                        $postId,
                                        $title,
                                        $comment);

                    $message = sprintf("success. your comment added to item %s ",$title);
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
