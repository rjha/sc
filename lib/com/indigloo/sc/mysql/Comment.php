<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\Constants as AppConstants;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;


    class Comment {

        static function getOnPostId($postId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            //sanitize input
            settype($postId,"integer");

            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l " ;
            $sql .= " where l.id = a.login_id and  a.post_id = %d " ;
            $sql = sprintf($sql,$postId);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getOnId($commentId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($commentId,"integer");

            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l ";
            $sql .= " where l.id = a.login_id and a.id = %d " ;
            $sql = sprintf($sql,$commentId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getLatest($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            //sanitize input
            settype($limit,"integer");
            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Comment","a");
            //raw condition
            $q->addCondition("l.id = a.login_id");
            $q->filter($filters);
            $condition = $q->get();

            $sql .= $condition;
            $sql .= " order by id desc LIMIT %d " ;
            $sql = sprintf($sql,$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function getPaged($start,$direction,$limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($start,"integer");
            settype($limit,"integer");
            $direction = $mysqli->real_escape_string($direction);

            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Comment","a");
            //raw condition
            $q->addCondition("l.id = a.login_id");
            $q->filter($filters);

            $sql .= $q->get();
            $sql .= $q->getPagination($start,$direction,"a.id",$limit);

            if(Config::getInstance()->is_debug()) {
                Logger::getInstance()->debug("sql => $sql \n");
            }

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);

            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;

        }

        static function create($loginId,$name,$ownerId,$postId,$title,$comment) {


            $dbh = NULL ;
             
            try {

                // insert into sc_comment, adjust counters via trigger
                $sql1 = " insert into sc_comment(post_id,description,login_id, created_on) " ;
                $sql1 .= " values(:post_id,:comment,:login_id,now()) ";

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":post_id", $postId);
                $stmt1->bindParam(":comment", $comment);
                $stmt1->bindParam(":login_id", $loginId);

                $stmt1->execute();
                $stmt1 = NULL ;
                
                $sql2 = " insert into sc_activity(owner_id,subject_id,subject,object_id, " ;
                $sql2 .= " object,verb, verb_name, op_bit, content,created_on) " ;
                $sql2 .= " values(:owner_id, :subject_id, :subject, :object_id, " ;
                $sql2 .= " :object, :verb, :verb_name, :op_bit, :content,now()) ";
               
                $verb =  AppConstants::COMMENT_VERB ;
                $op_bit = 0 ;
                $verbName = AppConstants::STR_COMMENT ;
                $content = Util::abbreviate($comment,100);

                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":owner_id", $ownerId);
                $stmt2->bindParam(":subject_id", $loginId);
                $stmt2->bindParam(":object_id", $postId);
                $stmt2->bindParam(":subject", $name);
                $stmt2->bindParam(":object", $title);
                $stmt2->bindParam(":verb", $verb);
                $stmt2->bindParam(":verb_name", $verbName);
                $stmt2->bindParam(":op_bit", $op_bit);
                $stmt2->bindParam(":content", $content);

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

        static function update($commentId,$comment,$loginId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "update sc_comment set description = ?, updated_on = now() where id = ? and login_id = ?" ;

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sii",$comment,$commentId,$loginId) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function delete($commentId,$loginId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "delete from sc_comment where id = ? and login_id = ?" ;

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$commentId,$loginId) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }
            
        }
    }
}
?>
