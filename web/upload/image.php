<?php

	//sc/upload/image.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    require_once($_SERVER['WEBGLOO_LIB_ROOT']. '/ext/S3.php');

    set_error_handler('webgloo_ajax_error_handler');
	
    use com\indigloo\Util as Util;

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
		trigger_error("what is this?", E_USER_ERROR); 
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
