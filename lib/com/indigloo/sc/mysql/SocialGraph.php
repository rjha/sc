<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\Constants as AppConstants;

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
        
        static function addFollower($followerId,$followerName,$followingId,$followingName) {

            $dbh = NULL ;
             
            try {

                // insert into sc_follow, adjust counters via trigger
                $sql1 = " insert into sc_follow(follower_id,following_id,created_on) " ;
                $sql1 .= " values(:follower_id,:following_id,now()) ";

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":follower_id", $followerId);
                $stmt1->bindParam(":following_id", $followingId);
                $stmt1->execute();
                $stmt1 = NULL ;
                

                $sql2 = " insert into sc_activity(owner_id,subject_id,subject,object_id, " ;
                $sql2 .= " object,verb, verb_name, op_bit, created_on) " ;
                $sql2 .= " values(:owner_id, :subject_id, :subject, :object_id, " ;
                $sql2 .= " :object, :verb, :verb_name, :op_bit, now()) ";
               
                $verb =  AppConstants::FOLLOWING_VERB ;
                $op_bit = 0 ;
                $verbName = AppConstants::STR_FOLLOW ;
                $ownerId = -1 ;

                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":owner_id", $ownerId);
                $stmt2->bindParam(":subject_id", $followerId);
                $stmt2->bindParam(":object_id", $followingId);
                $stmt2->bindParam(":subject", $followerName);
                $stmt2->bindParam(":object", $followingName);
                $stmt2->bindParam(":verb", $verb);
                $stmt2->bindParam(":verb_name", $verbName);
                $stmt2->bindParam(":op_bit", $op_bit);


                $stmt2->execute();
                $stmt2 = NULL ;
                

                //Tx end
                $dbh->commit();
                $dbh = null;
                

            }catch (\PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($e->getMessage(),$e->getCode());

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                $message = $ex->getMessage();
                throw new DBException($message);
            }

        }
        
        static function removeFollower($followerId, $followingId) {

             $dbh = NULL ;
             
            try {

                $sql1 = " delete from sc_follow where follower_id = :follower_id " ;
                $sql1 .= " and following_id = :following_id " ;
                
                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":follower_id", $followerId);
                $stmt1->bindParam(":following_id", $followingId);
                $stmt1->execute();
                $stmt1 = NULL ;
                
                $sql2 = " insert into sc_activity(owner_id,subject_id,subject,object_id, " ;
                $sql2 .= " object,verb, verb_name, op_bit, created_on) " ;
                $sql2 .= " values(:owner_id, :subject_id, :subject, :object_id, " ;
                $sql2 .= " :object, :verb, :verb_name, :op_bit, now()) ";
               
                $verb =  AppConstants::UNFOLLOWING_VERB ;
                $op_bit = 0 ;
                $verbName = AppConstants::STR_UNFOLLOW ;
                $ownerId = -1 ; 
                $subject = "_NA_" ;
                $object = "_NA_" ;

                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":owner_id", $ownerId);
                $stmt2->bindParam(":subject_id", $followerId);
                $stmt2->bindParam(":object_id", $followingId);
                $stmt2->bindParam(":subject", $subject);
                $stmt2->bindParam(":object", $object);
                $stmt2->bindParam(":verb", $verb);
                $stmt2->bindParam(":verb_name", $verbName);
                $stmt2->bindParam(":op_bit", $op_bit);

                $stmt2->execute();
                $stmt2 = NULL ;
                
                //Tx end
                $dbh->commit();
                $dbh = null;
                

            }catch (\PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($e->getMessage(),$e->getCode());

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                $message = $ex->getMessage();
                throw new DBException($message);
            }

        }
        

    }
}
?>
