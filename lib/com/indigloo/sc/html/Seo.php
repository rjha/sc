<?php
namespace com\indigloo\sc\html{

    class Seo {

        static function getPageTitle($token) {
            $x = "%s - Share and discover %s and other stuff at 3mik.com" ;
            $x = sprintf($x,$token,$token);
            return $x ;
        }

        static function getMetaKeywords($token) {
            $x = "%s, 3mik, share, discover, india, cool shopping items, shopping" ;
            $x = sprintf($x,$token);
            return $x ;
        }

        static function getMetaDescription($token) {
            $x = "%s - share and discover %s and other similar shopping items in India. " ;
            $x .= " %s and other intersting stuff are shared by users of 3mik.com" ;
            $x = sprintf($x,$token,$token,$token);
            return $x ;
        }
        
       static function getHomePageTitle() {
            $x = " Share and discover shopping items in India - 3mik.com" ;
            return $x ;
        }

        static function getHomeMetaKeywords() {
            $x = " 3mik, share, discover, india, cool shopping items, shopping" ;
            return $x ;
        }

        static function getHomeMetaDescription() {
            $x = " share and discover cool shopping items in India. " ;
            $x .= "All intersting stuff are shared by users of 3mik.com" ; 
            return $x ;
        }



    }

}
?>
