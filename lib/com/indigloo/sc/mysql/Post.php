<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;
    use \com\indigloo\mysql\PDOWrapper;

    use \com\indigloo\exception\DBException;
    use \com\indigloo\sc\Constants as AppConstants;

    class Post {

        static function getOnId($postId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($postId,"integer");

            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l " ;
            $sql .= " where l.id = q.login_id and q.id = %d " ;
            $sql = sprintf($sql,$postId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getLinkDataOnId($postId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($postId,"integer");

            $sql = "select version,links_json as json from sc_post where id = %d ";
            $sql = sprintf($sql,$postId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

        }

        // @todo fix expensive-query
        // @see http://www.warpconduit.net/2011/03/23/selecting-a-random-record-using-mysql-benchmark-results/
        // @examined This query is used on thanks page after logout 
        // and Random posts controller.
        static function getRandom($limit) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = " SELECT q.*,l.name as user_name FROM sc_post q,sc_login l WHERE q.login_id = l.id " ;
            $sql .=" and RAND()<(SELECT ((%d/COUNT(*))*4) FROM sc_post q2) ";
            $sql .= " ORDER BY RAND() LIMIT %d";
            $sql = sprintf($sql,$limit,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

         static function getPosts($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = "select q.*,l.name as user_name  from sc_post q, sc_login l ";

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Post","q");
            //raw condition
            $q->addCondition("l.id = q.login_id");
            $q->filter($filters);
            $condition = $q->get();

            $sql .= $condition;
            $sql .= " order by id desc limit %d " ;
            $sql = sprintf($sql,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function getOnLoginId($loginId,$limit) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");
            settype($loginId,"integer");

            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l where q.login_id = l.id " ;
            $sql .= " and  q.login_id = %d order by id desc limit %d " ;
            $sql = sprintf($sql,$loginId,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }


        /*
         *
         * 1. we need to fetch rows from mysql doing a range scan on ids
         * returned by sphinx.
         *
         * 2. To preserve the order of ids returned by sphinx you need to create a
         * sort field like
         * $sql .= " ORDER BY FIELD(q.id,".$strIds. ") " ;
         * @see http://sphinxsearch.com/info/faq/
         *
         * 3. we want sorting to be done on our DB created_on column (our choice)
         *
         */
        static function getOnSearchIds($strIds) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $strIds = $mysqli->real_escape_string($strIds);

            $sql = " select q.*,l.name as user_name from sc_post q, sc_login l " ;
            $sql .= " where l.id = q.login_id and q.id in (".$strIds. ") " ;
            $sql .= " ORDER BY FIELD(q.id,".$strIds. ") " ;
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getLatest($limit,$filters) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l" ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Post","q");
            //raw condition
            $q->addCondition("l.id = q.login_id");
            $q->filter($filters);
            $condition = $q->get();
            $sql .= $condition;

            $sql .= " order by q.id desc LIMIT %d " ;
            $sql = sprintf($sql,$limit);
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function getTotalCount($filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select count(id) as count from sc_post ";

            $q = new MySQL\Query($mysqli);
            $q->filter($filters);
            $condition = $q->get();
            $sql .= $condition ;

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

        }

        static function getPaged($start,$direction,$limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($start,"integer");
            settype($limit,"integer");
            $direction = $mysqli->real_escape_string($direction);

            // primary key id is an excellent proxy for created_on column
            // latest posts has max(id) and appears on top
            // so AFTER (NEXT) means id < latest post id

            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Post","q");
            //raw condition
            $q->addCondition("l.id = q.login_id");
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

        static function update($postId,
                               $title,
                               $description,
                               $linksJson,
                               $imagesJson,
                               $loginId,
                               $groupSlug,
                               $categoryCode) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "update sc_post set title=?,description=?,links_json =?,images_json=?,version=version +1,";
            $sql .= "group_slug = ? , updated_on = now(),cat_code = ? where id = ? and login_id = ?" ;

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssssssii",
                        $title,
                        $description,
                        $linksJson,
                        $imagesJson,
                        $groupSlug,
                        $categoryCode,
                        $postId,
                        $loginId);


                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function create($title,
                                $description,
                                $loginId,
                                $name,
                                $linksJson,
                                $imagesJson,
                                $groupSlug,
                                $categoryCode) {


            $dbh = NULL ;

            try {
                $sql1 = " insert into sc_post(title,description,login_id,links_json, " ;
                $sql1 .= " images_json,group_slug,cat_code, pseudo_id,created_on) ";
                $sql1 .= " values (:title,:description,:login_id,:links_json,:images_json, " ;
                $sql1 .= " :group_slug, :cat_code, :pseudo_id, now()) ";

                $dbh = PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                //insert into sc_post, change counters via trigger
                $stmt1 = $dbh->prepare($sql1);

                $stmt1->bindParam(":title", $title);
                $stmt1->bindParam(":description", $description);
                $stmt1->bindParam(":login_id", $loginId);
                $stmt1->bindParam(":links_json", $linksJson);
                $stmt1->bindParam(":images_json", $imagesJson);
                $stmt1->bindParam(":group_slug", $groupSlug);
                $stmt1->bindParam("cat_code", $categoryCode);

                // @see http://drupal.org/node/1369332
                // pseudo_id is part of a UNIQUE index and mysql has to lock
                // the index attached to pseudo_id if we do not insert anything
                // NULL not being comparable to anything, it doesn't participate 
                // in uniqueness constraints and MySQL doesn't have to lock the index.
                // $pseudoId = NULL ;
                // $stmt1->bindParam(":pseudo_id", $pseudoId);
                $stmt1->bindValue(":pseudo_id", null,\PDO::PARAM_STR);

                $stmt1->execute();
                $stmt1 = NULL ;

                $postId = $dbh->lastInsertId();
                settype($postId, "integer");
                $itemId = PseudoId::encode($postId);

                if(strlen($itemId) > 32 ) {
                    throw new DBException("exceeds pseudo_id column size of 32");
                }

                $sql2 = "update sc_post set pseudo_id = :item_id where id = :post_id " ;
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":item_id", $itemId);
                $stmt2->bindParam(":post_id", $postId);
                $stmt2->execute();
                $stmt2 = NULL ;

                $sql3 = " insert into sc_activity(owner_id,subject_id,subject,object_id, " ;
                $sql3 .= " object,verb, verb_name, op_bit, created_on) " ;
                $sql3 .= " values(:owner_id, :subject_id, :subject, :object_id, " ;
                $sql3 .= " :object, :verb, :verb_name, :op_bit, now()) ";
               
                $verb =  AppConstants::POST_VERB ;
                $op_bit = 0 ;
                $verbName = AppConstants::STR_POST ;

                $stmt3 = $dbh->prepare($sql3);
                $stmt3->bindParam(":owner_id", $loginId);
                $stmt3->bindParam(":subject_id", $loginId);
                $stmt3->bindParam(":object_id", $itemId);
                $stmt3->bindParam(":subject", $name);
                $stmt3->bindParam(":object", $title);
                $stmt3->bindParam(":verb", $verb);
                $stmt3->bindParam(":verb_name", $verbName);
                $stmt3->bindParam(":op_bit", $op_bit);

                $stmt3->execute();
                $stmt3 = NULL ;
                

                //Tx end
                $dbh->commit();
                $dbh = null;

                return $itemId;

            } catch (\PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($e->getMessage(),$e->getCode());
                 

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($ex->getMessage());
                 
            }

        }

        static function delete($postId,$loginId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " delete from sc_post where id = ? and login_id = ?" ;

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$postId,$loginId) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }  

        static function getLatestOnCategory($code,$limit) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $code = $mysqli->real_escape_string($code);
            settype($limit,"integer");

            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l " ;
            $sql .= " where l.id=q.login_id  and q.cat_code = '%s' order by q.id desc LIMIT %d ";
            $sql = sprintf($sql,$code,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function getPagedOnCategory($start,$direction,$limit,$code) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $code = $mysqli->real_escape_string($code);
            $direction = $mysqli->real_escape_string($direction);
            settype($start,"integer");
            settype($limit,"integer");

            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l ";
            $codeCondition = sprintf("cat_code = '%s'",$code);

            $q = new MySQL\Query($mysqli);
            $q->addCondition("l.id = q.login_id");
            $q->addCondition($codeCondition);

            $sql .= $q->get();
            $sql .= $q->getPagination($start,$direction, "q.id",$limit);


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

        static function set_fp_bit($postId,$value) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "update sc_post set updated_on = now() ,fp_bit = ? where id = ?" ;
            
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$value,$postId) ;
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }
        }

    }
}
?>
