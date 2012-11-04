<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    class SocialGraph {

        static function find($followerId, $followingId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($followerId,"integer");
            settype($followingId,"integer");

            $sql = " select count(id) as count from sc_follow" ;
            $sql .= " where follower_id = %d and following_id = %d ";
            $sql = sprintf($sql,$followerId, $followingId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }
        
        //@todo - pagination on following/followers queries
        static function getFollowing($loginId,$limit) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
             
            //sanitize input
            settype($loginId,"integer");
            settype($limit,"integer");

            $sql = " select u.name, u.login_id, u.photo_url from sc_denorm_user u, " ;
            $sql .= " sc_follow s  where u.login_id = s.following_id and s.follower_id = %d limit %d " ;
            $sql = sprintf($sql,$loginId,$limit);
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }
        
        static function getFollowers($loginId,$limit) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
             
            //sanitize input
            settype($loginId,"integer");
            settype($limit,"integer");

            $sql = " select u.name, u.login_id, u.photo_url from sc_denorm_user u, " ;
            $sql .= " sc_follow s  where u.login_id = s.follower_id and s.following_id = %d limit %d " ;
            $sql = sprintf($sql,$loginId,$limit);
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }
        
        static function addFollower($followerId, $followingId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_follow(follower_id,following_id,created_on) " ;
            $sql .= " values(?,?,now()) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii", $followerId, $followingId);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle( $mysqli);
            }

        }
        
         static function removeFollower($followerId, $followingId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " delete from sc_follow where follower_id = ? and following_id = ? " ;
            
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii", $followerId, $followingId);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle( $mysqli);
            }

        }
        

    }
}
?>
