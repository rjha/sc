<?php

namespace com\indigloo\sc\dao {


    use \com\indigloo\sc\mysql as mysql;

    
    class Analytic {
        
        function currentSessions() {
            $rows = mysql\Analytic::currentSessions();
            return $rows ;
        }

    }

}
?>
