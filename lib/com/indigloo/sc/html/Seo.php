<?php
namespace com\indigloo\sc\html{

    class Seo {

        static function getPageTitle($token) {
            $x = "%s - Share and discover %s and other items at 3mik.com" ;
            $x = sprintf($x,$token,$token);
            return $x ;
        }

        static function getPageTitleWithNumber($gpage,$token) {
            $x = "page %d of %s - Share and discover %s and other items at 3mik.com" ;
            $x = sprintf($x,$gpage,$token,$token);
            return $x ;
        }

        static function getMetaKeywords($token) {
            $x = "%s, 3mik, share, discover, india, cool shopping items, shopping" ;
            $x = sprintf($x,$token);
            return $x ;
        }

        static function getMetaDescription($token) {
            $x = "%s - share and discover %s and other similar shopping items in India. " ;
            $x .= " all interesting items are shared by users of 3mik.com" ;
            $x = sprintf($x,$token,$token);
            return $x ;
        }
        
        static function getMetaDescriptionWithNumber($gpage,$token) {
            $x = " page %d of %s - share and discover %s and other similar shopping items in India. " ;
            $x .= " all interesting items are shared by users of 3mik.com" ;
            $x = sprintf($x,$gpage,$token,$token);
            return $x ;
        }

        static function getHomePageTitle() {
            $x = " Share and discover shopping items in India - 3mik.com" ;
            return $x ;
        }

        static function getHomePageTitleWithNumber($gpage) {
            $x = " page %d of Share and discover shopping items in India - 3mik.com" ;
            $x = sprintf($x,$gpage);
            return $x ;
        }

        static function getHomeMetaKeywords() {
            $x = " 3mik, share, discover, india, cool shopping items, shopping" ;
            return $x ;
        }

        static function getHomeMetaDescription() {
            $x = " share and discover cool shopping items in India. " ;
            $x .= " all interesting items are shared by users of 3mik.com" ; 
            return $x ;
        }

        static function getHomeMetaDescriptionWithNumber($gpage) {
            $x = " page %d of share and discover cool shopping items in India. " ;
            $x .= " all interesting items are shared by users of 3mik.com" ; 
            $x = sprintf($x,$gpage);
            return $x ;
        }

        static function thisOrHomeDescription($value) {
            if(!empty($value)) { return $value ; }
            else { return self::getHomeMetaDescription(); }

        }


    }

}
?>
