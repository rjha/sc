<?php
	//sc/ajax/url/extract.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');

	set_error_handler('webgloo_ajax_error_handler');

	use \com\indigloo\text\UrlParser as UrlParser;

	//Get URL from ajax request 
	$url = $_POST['q'];
	$parser = new UrlParser();

	$data = $parser->extract($url);
	$payload = array('code' => 1 , 'data' => $data , 'message' => 'success');
	echo json_encode($payload);
	
?>
