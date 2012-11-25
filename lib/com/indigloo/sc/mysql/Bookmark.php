<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\Constants as AppConstants ;

    class Bookmark {

        static function find($subjectId,$objectId,$verb) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($subjectId,"integer");
            settype($objectId,"integer");
            settype($verb,"integer");

            $sql = " select count(id) as count from sc_bookmark " ;
            $sql .= " where subject_id = %d and object_id = %d and verb = %d ";
            $sql = sprintf($sql,$subjectId,$objectId,$verb);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        // for historical reasons this method is used to fetch the posts that 
        // have been bookmarked

        static function getLatest($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = " select q.* , l.name as user_name from sc_post q, sc_bookmark a, sc_login l " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Bookmark","a");
            $q->addCondition("q.pseudo_id = a.object_id");
            $q->addCondition("q.login_id = l.id");
            $q->filter($filters);
            $sql .= $q->get();

            $sql .= "order by q.id desc LIMIT %d ";
            $sql = sprintf($sql,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        // This method gets you the latest entries from sc_bookmark table
        // itself - no post information is fetched here
        // @todo expensive-query
        static function getTableLatest($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = " select a.* from  sc_bookmark a " ;
            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Bookmark","a");
            $q->filter($filters);
            $sql .= $q->get();

            $sql .= "order by a.id desc LIMIT %d ";
            $sql = sprintf($sql,$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        // for historical reasons this method is used to fetch the posts that 
        // have been bookmarked
        
        static function getPaged($start,$direction,$limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($start,"integer");
            settype($limit,"integer");
            $direction = $mysqli->real_escape_string($direction);

            $sql = " select q.* , l.name as user_name from sc_post q, sc_bookmark a, sc_login l " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Bookmark","a");
            $q->addCondition("q.pseudo_id = a.object_id ");
            $q->addCondition("q.login_id = l.id");
            $q->filter($filters);
            $sql .= $q->get();

            $sql .= $q->getPagination($start,$direction,"q.id",$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            
            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }

        // This method gets you the latest entries from sc_bookmark table
        // itself - no post information is fetched here
        //@todo expensive-query ?
        static function getTablePaged($start,$direction,$limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($start,"integer");
            settype($limit,"integer");
            $direction = $mysqli->real_escape_string($direction);

            $sql = " select a.* from  sc_bookmark a " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Bookmark","a");
            $q->filter($filters);
            $sql .= $q->get();
            $sql .= $q->getPagination($start,$direction,"a.id",$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);

            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }
        
        static function getOnLoginId($subjectId,$verb) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($subjectId,"integer");
            settype($verb,"integer");

            $sql = " select q.* , l.name as user_name " ;
            $sql .= " from sc_post q, sc_bookmark a , sc_login l " ;
            $sql .= " where q.pseudo_id = a.object_id and q.login_id = l.id ";
            $sql .= " and a.subject_id = %d and a.verb = %d limit 20 ";

            $sql = sprintf($sql,$subjectId,$verb);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function add(
                $ownerId,
                $subjectId,
                $subject,
                $objectId,
                $objectType,
                $title,
                $verb){


            $dbh = NULL ;
             
            try {

                //@todo column object should be renamed to object_type
                //@todo column object_title should be renamed to object 
                // insert into sc_bookmark, adjust counters via trigger
                $sql1 = " insert into sc_bookmark(owner_id,subject_id,subject,object_id, " ;
                $sql1 .= " object, object_title, verb,created_on) " ;
                $sql1 .= " values(:owner_id, :subject_id, :subject, :object_id, :object_type, " ;
                $sql1 .= " :object, :verb, now()) ";
                
                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":owner_id", $ownerId);
                $stmt1->bindParam(":subject_id", $subjectId);
                $stmt1->bindParam(":object_id", $objectId);
                $stmt1->bindParam(":subject", $subject);
                $stmt1->bindParam(":object", $title);
                $stmt1->bindParam(":object_type", $objectType);
                $stmt1->bindParam(":verb", $verb);

                $stmt1->execute();
                $stmt1 = NULL ;
                
                $sql2 = " insert into sc_activity(owner_id,subject_id,subject,object_id, " ;
                $sql2 .= " object,verb, verb_name, op_bit, created_on) " ;
                $sql2 .= " values(:owner_id, :subject_id, :subject, :object_id, " ;
                $sql2 .= " :object, :verb, :verb_name, :op_bit, now()) ";
               
                $verb =  AppConstants::LIKE_VERB ;
                $op_bit = 0 ;
                $verbName = AppConstants::STR_LIKE ;

                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":owner_id", $ownerId);
                $stmt2->bindParam(":subject_id", $subjectId);
                $stmt2->bindParam(":object_id", $objectId);
                $stmt2->bindParam(":subject", $subject);
                $stmt2->bindParam(":object", $title);
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

        static function delete($bookmarkId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "delete from sc_bookmark where id = ? " ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("i",$bookmarkId) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function remove($subjectId,$objectId,$verb) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "delete from sc_bookmark where subject_id = ? and object_id = ? and verb = ? " ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("iii",$subjectId,$objectId,$verb) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }
    }
}
?>
