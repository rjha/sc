<?php
    header('Content-type: application/json');
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\util\PseudoId ;

    set_error_handler('webgloo_ajax_error_handler');

    //use login is required for bookmarking
    if(!Login::hasSession()) {
        $message = array("code" => 401 , "message" => "Authentication failure: You need to login!");
        $html = json_encode($message);
        echo $html;
        exit;
    }

    
    //parameters
    $loginId = Login::getLoginIdInSession();
    $itemId = Util::getArrayKey($_POST, "itemId");
    $action = Util::getArrayKey($_POST, "action");

    $map = array();
    $map["LIKE"] = 1 ;
    $map["FAVORITE"] = 2 ;
    $map["REMOVE"] = 32 ;

    $code = $map[$action];

    $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();

    switch($code) {
        case 1:
        case 2:
            $bookmarkDao->add($loginId,$itemId,$code);
            break;
        case 32 :
             $bookmarkDao->unfavorite($loginId,$itemId);
             break ;
        default :
            break;
    }

    $postDao = new \com\indigloo\sc\dao\Post();
    $postId = PseudoId::decode($itemId);
    $postDBRow = $postDao->getOnId($postId);
    $postName = $postDBRow['title'];

    $message = sprintf(" %s action for item %s is success!",$action,$postName);
    $html = array("code" => 200 , "message" => $message);
    $html = json_encode($html);
    echo $html;
?>
