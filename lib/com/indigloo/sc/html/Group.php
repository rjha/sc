<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\Util as Util ;
    use com\indigloo\util\StringUtil as StringUtil ;

    class Group {

        static function getCloud($groups) {
            
            $view = new \stdClass;
            $view->groups = $groups ;

            printf("<div class=\"cloud\">");
            foreach($groups as $group) {
                $num = rand(1,3000);
                $style = 1 ;
                if($num > 1000 ) { $style = 2 ; }
                if($num > 2000 ) { $style = 3 ; }
                $href = "/group/".$group['token'];
                $color = 'tag-color'.$style ;
                $size = 'tag-size'.$style ;

                printf(" <a href=\"%s\" class=\"cloud-item %s\"> ",$href,$color);
                printf(" <span class=\"%s\">%s</span> </a>",$size,$group['name']);
            }

            printf("</div>");

        }

        /*
         * @param card #1 is blue card, #2 is white card 
         *
         */

        static function getCard($groups,$card) {
            $html = NULL ;
            $view = new \stdClass;
            $view->groups = $groups ;
            $view->class = ($card == 1 ) ? "card-blue" : "card-white" ;
            $template = '/fragments/group/card.tmpl' ;
            $html = Template::render($template,$view);
            return $html;

        }

         static function getTile($groups) {
            $html = NULL ;
            $view = new \stdClass;
            $view->groups = $groups ;
            $template = '/fragments/group/tile.tmpl' ;
            $html = Template::render($template,$view);
            return $html;

         }


    }
}
?>
