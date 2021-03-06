<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;

    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\util\StringUtil as StringUtil ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;

    use \com\indigloo\sc\Constants as AppConstants ;
    use \com\indigloo\sc\ui\Constants as UIConstants ;

    class Site {

        static function getOverlay($message) {
            if(empty($message)) { return NULL ; }
            $html = NULL ;
            $view = new \stdClass;
            $template = "/fragments/site/overlay.tmpl" ;
            $view->message = $message ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function pageHeader($content) {
            if(!Util::tryEmpty($content)) { 
                return  '<div class="page-header"><h2>'.$content.'</h2></div>' ;
            }
            
        }

        static function formMessage() {
            
            $html = NULL ;
            $template = '/fragments/ui/form-message.tmpl' ;
            $view = new \stdClass;

            $gWeb = \com\indigloo\core\Web::getInstance();
            $messages = $gWeb->find(Constants::FORM_MESSAGES,true);
            $errors = $gWeb->find(Constants::FORM_ERRORS,true);

            $view->messages = (is_null($messages)) ? array() : $messages ;
            $view->errors = (is_null($errors)) ? array() : $errors ;
            
            $html = Template::render($template,$view);
            return $html ;
        }
        
        /*
         * #1)
         * if we are on page > 2 then user knows how to create 
         * entity. in that case our pagination did not bring any 
         * results. in that case we just want to show "no more results"
         * 
         * #2) 
         * 
         * if we get a help key 
         * if we are on page #1 - and there is no content then 
         * we would like to show the users help about how to 
         * create entity. 
         * 
         * 
         * 
         */


        static function getNoResult($message,$options=NULL) {

            $defaults = array(
                "hkey" => NULL ,
                "form" => "vanilla");

            $settings = Util::getSettings($options,$defaults);
            
            //get qparams from Url
            $qparams = \com\indigloo\Url::getRequestQueryParams();
            $gpage = -1 ;

            //hkey supplied - means show help on page #1.
            if(!empty($qparams) && (isset($qparams["gpage"]))) {  
                $gpage = $qparams["gpage"];
                $gpage = intval($gpage);
            }else {
                $gpage = 1 ;
            }

            $html = NULL ;
            $help_key = $settings["hkey"] ;

            if(($gpage <= 1) && !is_null($help_key)) {

                try{
                    $html = self::getHelp($help_key);
                    return $html ;
                } catch(\Exception $ex) {
                    $html = NULL ;
                    $errorMsg = $ex->getMessage();
                    Logger::getInstance()->error($errorMsg);
                    Logger::getInstance()->error($ex->getMessage());
                }

            }

            $view = new \stdClass;            
            $template = NULL ;
            $form = $settings["form"] ;

            switch($form) {
                case "tile" :
                    $template = "/fragments/site/noresult/tile.tmpl" ;
                    break ;
                default :
                    $template = "/fragments/site/noresult/vanilla.tmpl"  ;
                    break ;
            }
            
            $view->message = $message ;
            $html = Template::render($template,$view);
            return $html ;

        }

        static function getHelp($key) {
            
            $pos = \strpos($key,"/");

            //bad key
            if($pos !== false) {
                $message = \sprintf("wrong format for help file key: {%s} ",$key) ;
                throw new \Exception($message);
            }

            $name = \str_replace(".","/",$key);
            $path = \sprintf("%s/site/help/%s.html",APP_WEB_DIR,$name) ;

            if(!\file_exists($path)) {
                $message = sprintf("unable to locate help file {%s}",$path);
                throw new \Exception($message);
            }

            //get buffered output

            \ob_start();
            include ($path);
            $buffer = \ob_get_contents();
            \ob_end_clean();

            return $buffer;

        }
        
        static function renderAddBox() {
            $html = NULL ;
            $view = new \stdClass;
            $template = "/fragments/ui/add-box.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function renderSlugPanel($dbslug) {

            $records = array();
            //explode DB slug on space
            $slugs = explode(Constants::SPACE,$dbslug);
            //sort slugs on alpha
            sort($slugs);

            foreach($slugs as $slug) {
                if(Util::tryEmpty($slug)) { continue ; }
                $slug = trim($slug);
                $name =  StringUtil::convertKeyToName($slug);
                $records[] = array("slug" => $slug, "name" => $name ,"checked" => "checked") ;

            }

            $view = new \stdClass;
            $template = "/fragments/ui/slug-panel.tmpl" ;

            $view->records  = $records ;
            $view->total = sizeof($records);

            $html = Template::render($template,$view);
            return $html ;
        }

        static function getBookmarkTable($rows) {
            
            for($i = 0 ; $i < count($rows); $i++) {
                $rows[$i]["objectUrl"]  = "/item/".$rows[$i]["object_id"];
                $rows[$i]["subjectUrl"]  = "/pub/user/".PseudoId::encode($rows[$i]["subject_id"]);
                $verb = $rows[$i]["verb"] ;
                switch($verb) {
                    case AppConstants::LIKE_VERB :
                        $rows[$i]["action"] = UIConstants::LIKE_POST ;
                        break ;
                    
                    default :
                        break ;
                }
                
            }

            $html = NULL ;
            $template = '/fragments/site/bookmark/table.tmpl' ;
            $view = new \stdClass;
            $view->rows = $rows ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getSessionTable($rows) {
            
            for($i = 0 ; $i < count($rows); $i++) {
                $rows[$i]["pubUrl"]  = "/pub/user/".PseudoId::encode($rows[$i]["login_id"]);
            }

            $html = NULL ;
            $template = '/fragments/site/analytic/session.tmpl' ;
            $view = new \stdClass;
            $view->rows = $rows ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getDashItemHelp($count) {

            if($count > 0 ) { return "" ; }

            $html = NULL ;
            $view = new \stdClass ;

            $template = '/fragments/dash/item-help.tmpl' ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getInvitationMessage() {
            $html = NULL ;
            $view = new \stdClass ;

            $template = '/fragments/site/invitation.tmpl' ;
            $html = Template::render($template,$view);
            return $html ;
        }

    }

}

?>
