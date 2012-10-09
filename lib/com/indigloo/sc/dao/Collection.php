<?php

namespace com\indigloo\sc\dao {


    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\sc\ui\Constants as UIConstants ;
    use \com\indigloo\sc\util\PseudoId ;

    /*
     * modeled after redis commands.
     * 
     * 
     */

    class Collection {

        function sadd($key,$member) {
            mysql\Collection::sadd($key,$member);
        }

        function srem($key,$member) {
           mysql\Collection::srem($key,$member);
        }

        function smembers($key) {
           $rows = mysql\Collection::smembers($key);
           return $rows ;
        }

        function uizadd($key,$member) {
            mysql\Collection::uizadd($key,$member);
        }

        function uizrem($key,$member) {
           mysql\Collection::uizrem($key,$member);
        }

        function uizmembers($key) {
           $rows = mysql\Collection::uizmembers($key);
           return $rows ;
        }

        function uizmemberOnSeoKey($key,$seoKey){
            $row = mysql\Collection::uizmemberOnSeoKey($key,$seoKey);
            return $row ;
        }
        
        function uizmembersAsMap($key) {
           $rows = mysql\Collection::uizmembersAsMap($key);
           return $rows ;
        }

    }

}
?>
