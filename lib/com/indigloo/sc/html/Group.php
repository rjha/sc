<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\Util as Util ;
    use com\indigloo\util\StringUtil as StringUtil ;
    
    class Group {

		static function getLink($group) {
		    $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/group/link.tmpl' ;

            $token = $group["token"];    
            $view->href = "/group/".$token ;
            $view->name = StringUtil::convertKeyToName($token); 
			$html = Template::render($template,$view);
			
            return $html ;

		}
    } 
}
?>
