<?php
namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\exception\UIException as UIException;

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
            $remoteIp = \com\indigloo\Url::getRemoteIp();
            mysql\Login::create(
                $provider,
                $userName,
                $firstName,
                $lastName,
                $email,
                $password,
                $remoteIp);

        }
        
        function getLatest($limit) {
            $rows = mysql\Login::getLatest($limit);
            return $rows;
        }

        function getAggregate() {
            $rows = mysql\Login::getAggregate();
            return $rows; 
        }

    }
}
?>
