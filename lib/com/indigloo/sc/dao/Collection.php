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

        function sadd($key,$member,$source) {
            mysql\Collection::sadd($key,$member,$source);
        }

        function srem($key,$member) {
           mysql\Collection::srem($key,$member);
        }


    }

}
?>
