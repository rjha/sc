<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\sc\view\Media as MediaView ;
    use com\indigloo\Util as Util ;
    use com\indigloo\Url as Url ;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
    use \com\indigloo\sc\ui\Constants as UIConstants ;

    class ActivityFeed {

        static function get($feed) {
            $html = NULL ;
            $view = new \stdClass;
            $template = '/fragments/activity/feed.tmpl' ;

            $view->comment = $commentDBRow['description'];
            $view->createdOn = Util::formatDBTime($commentDBRow['created_on']);
            $view->userName = $commentDBRow['user_name'] ;
            $view->loginId = $commentDBRow['login_id'];
            $view->pubUserId = PseudoId::encode($view->loginId);

            $html = Template::render($template,$view);

            return $html ;

        }

    }

}

?>
