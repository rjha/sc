<?php
    header('Content-type: application/json');
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\sc\ui\Constants as UIConstants ;

    set_error_handler('webgloo_ajax_error_handler');

    //use login is required for bookmarking
    if(!Login::hasSession()) {
        $message = array("code" => 401 , "message" => "Authentication failure: You need to login!");
        $html = json_encode($message);
        echo $html;
        exit;
    }

    //parameters
    $login = Login::getLoginInSession();
    $loginId = $login->id ;
    $name = $login->name ;

    $itemId = Util::getArrayKey($_POST, "itemId");
    //action from ajax post can be
    // 1. LIKE 2. SAVE 3. REMOVE
    $action = Util::tryArrayKey($_POST, "action");

    if(empty($action) || empty($itemId)) {
        $message = array("code" => 500 , "message" => "Bad input: missing item or action!");
        $html = json_encode($message);
        echo $html;
        exit;
    }

    $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();
    $postDao = new \com\indigloo\sc\dao\Post();
    $postId = PseudoId::decode($itemId);
    $postDBRow = $postDao->getOnId($postId);
    $title = $postDBRow['title'];
    $ownerId = $postDBRow['login_id'];

    switch($action) {
        case UIConstants::LIKE_POST:
            $bookmarkDao->like($ownerId,$loginId,$name,$itemId,$title);
            break ;
        case UIConstants::SAVE_POST:
            $bookmarkDao->favorite($ownerId,$loginId,$name,$itemId,$title);
            break;
        case UIConstants::REMOVE_POST :
             $bookmarkDao->unfavorite($loginId,$itemId);
             break ;
        default :
            break;
    }
    
    $message = sprintf(" %s for item %s is success!",$action,$title);
    $html = array("code" => 200 , "message" => $message);
    $html = json_encode($html);
    echo $html;
?>
