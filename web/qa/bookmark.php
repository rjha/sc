<?php 
    header('Content-type: application/json'); 
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    //including as it is will cause parsing errors
    // the 302 goes back to script
    //include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
     
    set_error_handler('webgloo_ajax_error_handler');
	use \com\indigloo\sc\auth\Login as Login ;
    //post_id /user_id
    $loginId = Login::getLoginIdInSession();
    $postId = $_POST['postId'];  
    $data = array('login_id' => $loginId, 'post_id' => $postId);

    $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();
    $bookmarkDao->add($loginId,$postId);
    $html = array("code" => 200 , "message" => "success");
    $html = json_encode($html); 
    echo $html;
?>
