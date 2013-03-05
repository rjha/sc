<?php

namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\sc\util\Nest ;

    class Preference {

        function get($loginId) {
            $key = Nest::preference("user",$loginId);
            $row = mysql\Collection::glget($key);
            $pData = NULL ;

            
            if(!empty($row)) {
                //json stored in DB for this login
                $strPData = $row["t_value"] ;
                $pData = json_decode($strPData);
            }

            if(is_null($pData)) {
                // if no row found for user 
                // then return default (all true)
                $pData = new \stdClass ; 
                $pData->follow = true ;
                $pData->comment = true ;
                $pData->bookmark = true ;
            }

            return $pData ;
            
        }

        function set($loginId,$data) {
            $key = Nest::preference("user",$loginId);
            mysql\Collection::glset($key,$data);
        }

    }

}
?>
