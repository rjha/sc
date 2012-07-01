<?php

    //sc/upload/image.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    require_once(WEBGLOO_LIB_ROOT. '/ext/S3.php');

    set_exception_handler('webgloo_ajax_exception_handler');
    
    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login ;


    //use login is required for bookmarking
    if(!Login::hasSession()) {
        $message = array("code" => 401 , "message" => "Authentication failure: You need to login!");
        $json = json_encode($message); 
        echo $json;
        exit;
    }

    $uploader =  NULL ; 
    $prefix = sprintf("%s/",date('Y/m/d')) ;
    
    //special prefix - test machines 
    if($_SERVER["HTTP_HOST"] == 'mint.3mik.com' || $_SERVER["HTTP_HOST"] == 'mbp13.3mik.com') {
        $prefix = 'test/'.$prefix ;
    }
        
    if (isset($_GET['qqfile'])) {
        $pipe = new \com\indigloo\media\XhrPipe();
        $uploader = new com\indigloo\media\ImageUpload($pipe);
        $uploader->process($prefix,$_GET['qqfile']);
        
    } elseif (isset($_FILES['qqfile'])) {

        $pipe = new \com\indigloo\media\FormPipe();
        $uploader = new com\indigloo\media\ImageUpload($pipe);
        $uploader->process($prefix,"qqfile");
        
    } else {
        trigger_error("file upload is unable to determine pipe", E_USER_ERROR); 
    }

    //first - process the errors
    $errors = $uploader->getErrors() ;

    if (sizeof($errors) > 0 ) {
        $data = array('code' => 500, 'error' => $errors[0]);
        echo json_encode($data);
    
    } else {
        
        $mediaVO = $uploader->getMediaData();
         
        $mediaDao = new com\indigloo\sc\dao\Media();
        $mediaId = $mediaDao->add($mediaVO);
        $mediaVO->id  = $mediaId;
          
        $message = 'file upload done!';
        $data = array('code' => 0, 'mediaVO' => $mediaVO, 'message' => $message,'success' => true);
        echo json_encode($data);
    
    }

?>
