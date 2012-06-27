<?php
    //sc/user/form/login.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');


    use com\indigloo\ui\form as Form;
    use com\indigloo\Constants as Constants ;
    use \com\indigloo\exception\UIException as UIException;

    function ajax_post_it($qUrl,$payload){
        //base64_decode payload
        $payload = base64_decode($payload);

        if($payload === FALSE) { return ; }
        //json_decode payload
        $payloadObj = json_decode($payload);
        $endPoint = $payloadObj->endPoint ;
        $paramsObj = $payloadObj->params ;
        // create POST ready parameters now
        $data = http_build_query($paramsObj);
        $keys = get_object_vars($paramsObj);

        // POST params data to endpoint
        // @imp we have no knowledge of what params do
        $post = curl_init();
        curl_setopt($post, CURLOPT_URL, $endPoint);
        curl_setopt($post, CURLOPT_POST, sizeof($keys));
        curl_setopt($post, CURLOPT_POSTFIELDS, $data );
        curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($post);
        //@todo parse response
        curl_close($post);

        //go to interim page
        $gotoUrl = "/site/go-message.php?q=".$qUrl."&message=".base64_encode("test message!");
        header("Location: ".$gotoUrl);
        exit ;

    }

    if (isset($_POST['login']) && ($_POST['login'] == 'Login')) {
        try{
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('email', 'Email', array('required' => 1, 'maxlength' => 64));
            $fhandler->addRule('password', 'Password', array('required' => 1, 'maxlength' => 32));

            $fhandler->addRule('qUrl', 'qUrl', array('required' => 1, 'rawData' =>1));
            $fhandler->addRule('fUrl', 'fUrl', array('required' => 1, 'rawData' =>1));

            $fvalues = $fhandler->getValues();
            $gWeb = \com\indigloo\core\Web::getInstance();

            $qUrl = $fvalues['qUrl'];
            $fUrl = $fvalues['fUrl'];

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            }

            //canonical email - all lower case
            $email = strtolower(trim($fvalues['email']));
            $password = trim($fvalues['password']);
            $flag = \com\indigloo\auth\User::login('sc_user',$email,$password);

            if ($flag < 0 ) {
                $message = "Wrong login or password. Please try again!";
                throw new UIException(array($message),1);
            }

            //success set our own session variables
            //@debug
            //\com\indigloo\sc\auth\Login::startMikSession();

            // session started.
            // resume what was interrupted by login requirement
            // g_ajax_post?
            $gAjaxPost = $fvalues["g_ajax_post"];
            if($gAjaxPost == 1 && isset($fvalues["g_ajax_post_data"])) {
                ajax_post_it($qUrl,$fvalues["g_ajax_post_data"]);
            }

            header("Location: ".$qUrl);
            exit ;

        }catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }

    }
?>
