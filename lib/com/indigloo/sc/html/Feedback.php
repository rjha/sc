<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\Util as Util ;
    
    class Feedback {

		static function get($row) {
		    $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/monitor/feedback.tmpl' ;
			$view->description = $row['feedback'];
			$html = Template::render($template,$view);
            return $html ;
		}
    }
}

?>
