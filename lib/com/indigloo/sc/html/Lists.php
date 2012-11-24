<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\Url ;

    use \com\indigloo\sc\util\Formatter as Formatter ;
    use \com\indigloo\sc\ui\Constants as UIConstants;

    class Lists {

        static function getSelectPopup($listRows,$itemId,$qUrl) {
            $view = new \stdClass;
            $template =  (sizeof($listRows) > 0) ? "/fragments/lists/popup/select.tmpl" : "/fragments/lists/popup/select-0.tmpl"  ;

            $view->lists = array();
            $view->size = sizeof($listRows) ;
            $view->itemId = $itemId ;
            
            foreach($listRows as $row) {
                $view->lists[$row['id']] = $row['name'] ;
            }
            
            $view->qUrl = $qUrl;
            $view->pageUrl = base64_decode($qUrl);

            $html = Template::render($template,$view);
            return $html ;
        }

        static function getWidget($listDBRow) {
            $view = self::createListView($listDBRow);
            $template = NULL ;

            if($view->hasImage){
                $template =  "/fragments/lists/dash/summary/image.tmpl" ;
            } else {
                $template =  "/fragments/lists/dash/summary/noimage.tmpl" ;
            }

            $html = Template::render($template,$view);
            return $html ;
        }

        static function getPubWidget($row) {
            $view = self::createListView($row);
            $template = NULL ;

            if($view->hasImage){
                $template =  "/fragments/lists/pub/widget/image.tmpl" ;
            } else {
                $template =  "/fragments/lists/pub/widget/noimage.tmpl" ;
            }

            $html = Template::render($template,$view);
            return $html ;
        }

        static function getPubHeader($listDBRow,$userDBRow) {
            $view = self::createListView($listDBRow);

            $view->userName = $userDBRow["name"];
            $view->photoUrl = $userDBRow["photo_url"];
            if(empty($view->photoUrl)) {
                // @hardcoded
                $view->photoUrl = UIConstants::PH2_PIC ;
            }
            
            $encodedId = PseudoId::encode($listDBRow["login_id"]);
            $view->userPubUrl = Url::base()."/pub/user/".$encodedId ;
            $view->createdOn = Formatter::convertDBTime($listDBRow['created_on']);

            $view->description = $listDBRow["description"];
            $view->count = $listDBRow["item_count"];
            
            $template = NULL ;
            $template =  "/fragments/lists/pub/header.tmpl" ;

            $html = Template::render($template,$view);
            return $html ;
        }

        static function createListView($row) {
            $view = new \stdClass ;

            $view->id = $row["id"];
            $view->pseudoId = PseudoId::encode($view->id);
            
            $view->name = $row["name"];
            $view->seoName = $row["seo_name"];
            $view->items = json_decode($row["items_json"]);
            $view->count = ($row["item_count"] == 0 )? "no" : $row["item_count"]  ;

            $view->hasImage = false ;

            if(is_array($view->items)){
                $view->hasImage = true ;
            } else {
                $view->items = array();
            }

            return $view ;

        }
    }

}

?>
