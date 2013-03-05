<?php 
    
    // normal HTML output
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url ;

    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\ui\Constants as UIConstants;

    set_exception_handler('webgloo_ajax_exception_handler');

    $hkey = Util::tryArrayKey($_GET, "hkey");
    if(empty($hkey)) {
        echo "No help key supplied" ;
        exit ;
    }

    $html = \com\indigloo\sc\html\Site::getHelp($hkey);
    echo $html ;

?>
