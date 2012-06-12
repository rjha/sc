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

        function update($loginId,$firstName,$lastName,$nickName,$email,
                                $website,$blog,$location,$age,$photoUrl,$aboutMe) {

            mysql\User::update($loginId,$firstName,$lastName,$nickName,$email,
                                $website,$blog,$location,$age,$photoUrl,$aboutMe);


        }

        function getLatest($limit,$filters=array()) {
            $rows = mysql\User::getLatest($limit,$filters);
            return $rows ;
        }

        function getTotal($filters=array()) {
            $row = mysql\User::getTotal($filters);
            return $row['count'] ;
        }

    }

}
?>
