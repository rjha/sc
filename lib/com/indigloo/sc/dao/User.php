<?php

namespace com\indigloo\sc\dao {


    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;

    class User {

        function getOnEmail($email) {
            $row = mysql\User::getOnEmail($email);
            return $row ;
        }

        /*
         * function to return sc_denorm_data on user.login_id
         * There is a need to maintain common data or the data that can be changed
         * via a form on our website in a common table. since we accept data from
         * different user sources like 3mik, facebook, twitter etc., the common
         * lookup parameter for us is login_id created by us and not the email of the user.
         *
         */
        function getOnLoginId($loginId) {
            $row = mysql\User::getOnLoginId($loginId);
            return $row ;
        }

        function update(
            $loginId,
            $firstName,
            $lastName,
            $nickName,
            $email,
            $website,
            $blog,
            $location,
            $age,
            $photoUrl,
            $aboutMe) {

            mysql\User::update(
                $loginId,
                $firstName,
                $lastName,
                $nickName,
                $email,
                $website,
                $blog,
                $location,
                $age,
                $photoUrl,
                $aboutMe);
        }

        function getTotal($filters=array()) {
            if(empty($filters)) {
                // no filters?
                // get from site counter
                $row = Analytic::getSiteUserCounter();
            }else {
                //get from user table using where condition
                $row = mysql\User::getTotal($filters);
            }
             
            return $row["count"] ;
        }

        function getLatest($limit,$filters=array()) {
            $rows = mysql\User::getLatest($limit,$filters);
            return $rows ;
        }

        function getPaged($paginator,$filters=array()) {

            $limit = $paginator->getPageSize();

            if($paginator->isHome()){
                return $this->getLatest($limit,$filters);

            } else {
                $params = $paginator->getDBParams();
                $start = $params["start"];
                $direction = $params["direction"];
                $rows = mysql\User::getPaged($start,$direction,$limit,$filters);
                return $rows ;
            }
        }

        function ban ($loginId) {
            // session Id for this user?
            $loginRow = mysql\Login::getOnId($loginId);
            $sessionId = $loginRow["session_id"];
            mysql\User::set_bu_bit($loginId,1,$sessionId);
        }

        function unban ($loginId) {
            mysql\User::set_bu_bit($loginId,0,NULL);
        }

        function taint ($userId) {
            mysql\User::set_tu_bit($userId,1);
        }


    }

}
?>
