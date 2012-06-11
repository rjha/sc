<?php
namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;

    class Login {

        function getOnId($loginId) {
            $row = mysql\Login::getOnId($loginId);
            return $row ;
        }

        function create($firstName,$lastName,$email,$password){
            $provider = \com\indigloo\sc\auth\Login::MIK ;

            if(Util::tryEmpty($firstName) || Util::tryEmpty($lastName)) {
                throw new UIException(array("User name is missing!"));
            }

            $userName = $firstName. ' '.$lastName ;
            mysql\Login::create($provider,$userName,$firstName,$lastName,$email,$password);

        }

        function getTotalCount($filters=array()) {
            $row = mysql\Login::getTotalCount($filters);
            return $row['count'] ;
        }

        function getLatest($limit) {
            $rows = mysql\Login::getLatest($limit);
            return $rows;
        }

    }
}
?>
