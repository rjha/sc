<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class SocialGraph {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\SocialGraph';

        static function checkFollower($followerId, $followingId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            settype($followerId,"integer");
            settype($followingId,"integer");
            
            $sql = " select count(id) as count from sc_follow" ;
            $sql .= " where follower_id = %d and following_id = %d ";
            $sql = sprintf($sql,$followerId, $followingId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

       
        static function addFollower($followerId, $followingId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_follow(follower_id,following_id,created_on) " ;
            $sql .= " values(?,?,now()) ";

            $code = MySQL\Connection::ACK_OK;
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("ii", $followerId, $followingId);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    $code = MySQL\Error::handle(self::MODULE_NAME, $stmt);
                }
                $stmt->close();
            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
            
            return $code ;
        }
    
        
    }
}
?>
