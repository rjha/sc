<?php

namespace com\indigloo\sc\ui {

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;

    class Tabs {

        static function getActiveTabClass($tabUrlMap,$css) {
            $requestURI = $_SERVER['REQUEST_URI'] ;
            $pos = strpos($requestURI, '?');

            //remove the part after ? from Url
            if($pos !== false) {
                $requestURI = substr($requestURI,0,$pos);
            }

            $tabs = array_values($tabUrlMap);
            $activeTab = $tabUrlMap[$requestURI];

            $data = array();
            foreach($tabs as $tab) {
                if($tab == $activeTab )
                    $data[$tab] = $css ;
                else
                    $data[$tab] = "" ;
            }

            return $data ;

        }

    }

}

?>
