<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
     
    /**
     * DAO to read from sc_user table 
     */

    class MikUser {

        function getOnLoginId($loginId) {
            $row = mysql\MikUser::getOnLoginId($loginId);
            return $row ;
        }
        
    }

}
?>
