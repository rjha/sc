<?php

namespace com\indigloo\sc\dao {


    use \com\indigloo\sc\mysql as mysql;

    
    class Analytic {
        
        function currentSessions() {
            $rows = mysql\Analytic::currentSessions();
            return $rows ;
        }

        function getSiteCounters() {
            $row = mysql\Analytic::getSiteCounters();
            return $row;
        }

        static function getUserCounters($loginId) {
            $row = mysql\Analytic::getUserCounters($loginId);
            return $row;
        }

    }

}
?>
