<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException;

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

        //@see http://www.warpconduit.net/2011/03/23/selecting-a-random-record-using-mysql-benchmark-results/
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
            $sql = sprintf($sql,$limit);

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
            $sql .= " ORDER BY q.id desc" ;

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

            $sql .= " order by q.id desc LIMIT ".$limit ;

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
                               $linksJson,
                               $imagesJson,
                               $groupSlug,
                               $categoryCode) {


            try {
                $sql1 = " insert into sc_post(title,description,login_id,links_json, " ;
                $sql1 .= "images_json,group_slug,cat_code,created_on) ";
                $sql1 .= " values(?,?,?,?,?,?,?,now()) ";

                $flag = true ;
                $dbh = PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                //insert post
                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(1, $title);
                $stmt->bindParam(2, $description);
                $stmt->bindParam(3, $loginId);
                $stmt->bindParam(4, $linksJson);
                $stmt->bindParam(5, $imagesJson);
                $stmt->bindParam(6, $groupSlug);
                $stmt->bindParam(7, $categoryCode);

                $flag = $stmt->execute();

                if(!$flag){
                    $dbh->rollBack();
                    $dbh = null;
                    $message = sprintf("DB Error : code is  %s",$stmt->errorCode());
                    throw new DBException($message);
                }

                $postId = $dbh->lastInsertId();
                settype($postId, "integer");
                $itemId = PseudoId::encode($postId);

                if(strlen($itemId) > 32 ) {
                    throw new DBException("exceeds pseudo_id column size of 32");
                }

                $sql2 = "update sc_post set pseudo_id = :item_id where id = :post_id " ;
                $stmt = $dbh->prepare($sql2);
                $stmt->bindParam(":item_id", $itemId);
                $stmt->bindParam(":post_id", $postId);
                $flag = $stmt->execute();

                if(!$flag){
                    $dbh->rollBack();
                    $dbh = null;
                    $message = sprintf("DB  Error : code is  %s",$stmt->errorCode());
                    throw new DBException($message);
                }

                //Tx end
                $dbh->commit();
                $dbh = null;

                return $itemId;

            } catch (PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                $message = sprintf("PDO error :: code %d message %s ",$e->getCode(),$e->getMessage());
                throw new DBException($message);
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

        static function setFeature($loginId,$postId,$value){
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($loginId,"integer");
            settype($postId,"integer");
            settype($value,"integer");

            //operation needs admin privileges
            $mikUserRow = \com\indigloo\sc\mysql\MikUser::getOnLoginId($loginId);
            if($mikUserRow['is_admin'] != 1 ){
                trigger_error("User does not have admin rights", E_USER_ERROR);
            }

            $sql = " update sc_post set is_feature = %d where ID = %d " ;
            $sql = sprintf($sql,$value,$postId);
            MySQL\Helper::executeSQL($mysqli,$sql);
        }

    }
}
?>
