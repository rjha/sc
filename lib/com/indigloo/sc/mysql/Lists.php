<?php
namespace com\indigloo\sc\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException;
    use \com\indigloo\sc\util\PseudoId ;


    class Lists {

        static function getOnLoginId($loginId) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            //input
            settype($loginId,"int");

            $sql = " select list.*, login.name as user_name" ;
            $sql .= " from sc_list list, sc_login login  where list.login_id = login.id  " ;
            $sql .= " and  list.login_id = %d" ;
            $sql = sprintf($sql,$loginId);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
            return $rows ;

        }

        /**
         * @imp we do not expext a huge (# of lists/user). There should be a cap of
         * 50 or 100 lists/user. The pagination on login_id is sorted on name
         * and we assume that LIMIT OFFSET,N  kind of queries work fine in this 
         * case.
         *
         * if that is not the case and if large (#of lists/user) is a common scenario
         * then you need to tune the pagination query. LIMIT offset,size kind of queries
         * is bad for performance.
         *
         */
        static function getPagedOnLoginId($loginId,$offset,$limit) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($loginId,"integer");
            settype($offset,"integer");
            settype($limit,"integer");

            $sql = " select * from sc_list where login_id = %d order by id desc limit %d,%d " ;
            $sql = sprintf($sql,$loginId,$offset,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function getOnId($listId) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            settype($listId,"int");
            $sql = " select * from sc_list where id = %d " ;
            $sql = sprintf($sql,$listId);

            $row = MySQL\Helper::fetchRow($mysqli,$sql);
            return $row ;

        }

        static function exists($listId) {
            settype($listId,"int");
            $row = self::getOnId($listId);
            $flag = (!is_null($row) && !empty($row["name"])) ? true : false ;
            return $flag ;

        }

        static function getLatestItems($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = "select login.name as user_name, post.* ".
            " from sc_post post, sc_login login , ".
            " sc_list list, sc_list_item li ".
            " where list.login_id = login.id ".
            " and li.list_id = list.id ".
            " and li.item_id = post.id " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Lists","list");
            //start filter conditions using AND operator
            $q->setPrefixAnd();

            $q->filter($filters);
            $condition = $q->get();
            $sql .= $condition;

            $sql .= " order by post.id desc LIMIT %d " ;
            $sql = sprintf($sql,$limit);
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getPagedItems($start,$direction,$limit,$filters) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($start,"integer");
            settype($limit,"integer");
            $direction = $mysqli->real_escape_string($direction);

            $sql = "select login.name as user_name, post.* ".
            " from sc_post post, sc_login login , ".
            " sc_list list, sc_list_item li ".
            " where list.login_id = login.id ".
            " and li.list_id = list.id ".
            " and li.item_id = post.id " ;
               
            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Lists","list");
            //start filter conditions using AND operator
            $q->setPrefixAnd();

            $q->filter($filters);
            $condition = $q->get();
            $sql .= $condition;
            $sql .= $q->getPagination($start,$direction,"post.id",$limit);
            

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            
            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }
         

        /*
         * Add more items to an existing list!
         * deleting items from list is a separate call.
         * we want to 
         *  - push new items into list 
         *  - update dup_bit for existing items
         * 
        */

        static function addItem($listId,$strItemsJson,$postId) {

            try{
                // input check 
                settype($listId,"integer"); 
                settype($postId,"integer"); 

                if(empty($listId) || !self::exists($listId)) {
                    $message = sprintf("Bad input: listId {%s} does not exists",$listId);
                    trigger_error($message,E_USER_ERROR);
                }

                $dbh =  PDOWrapper::getHandle();

                // *** Tx start *** 
                $dbh->beginTransaction();

                // list is changing -
                // some offline processing is needed (set op_bit = 0)
                $sql1 = " update sc_list set version = version + 1 , op_bit = 0 ," ;
                $sql1 .= " items_json = :items_json where id = :list_id";

                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(":list_id", $listId);
                $stmt->bindParam(":items_json", $strItemsJson);
                $stmt->execute();
                $stmt = NULL ;

                // @imp sc_list.item_count is updated via insert trigger
                $sql2 = "insert into sc_list_item(list_id, item_id) values (%d,%d) " ;
                $sql2 = sprintf($sql2,$listId,$postId);
                $sql2 .= " on duplicate key update dup_bit = 1 " ;

                $dbh->exec($sql2);

                // *** Tx end *** 
                $dbh->commit();
                $dbh = null;

            }catch (PDOException $e) {
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

        static function edit( 
                $loginId,
                $listId,
                $name,
                $seoName,
                $hash,
                $bin_hash,
                $description){

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " update sc_list set name = ?, seo_name = ?, description=?,md5_name = ?, " ;
            $sql .= " bin_md5_name = ? , version=version +1 , updated_on = now() ";
            $sql .= " where id = ? and login_id = ?" ;

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sssssii",
                        $name,
                        $seoName,
                        $description,
                        $hash,
                        $bin_hash,
                        $listId,
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

        static function create(
            $loginId,
            $name,
            $seoName,
            $hash, 
            $bin_hash,
            $strItemsJson,
            $postId) {

            try {

                //input check

                settype($loginId,"integer"); 
                settype($postId,"integer"); 

                Util::isEmpty("name",$name);
                Util::isEmpty("md5 hash of name",$hash);
                Util::isEmpty("md5 bin hash of name",$bin_hash);

                //list
                // op_bit is offline_processing bit - set to zero on create
                $sql1 = "insert into sc_list (login_id,name, seo_name,md5_name, bin_md5_name, " ;
                $sql1 .= "items_json, version, op_bit , created_on) " ;
                $sql1 .= " values(:login_id,:name,:seo_name,:hash,:bin_hash,:items_json,1,0,now()) " ;
                $flag = true ;

                $dbh =  PDOWrapper::getHandle();

                // *** Tx start ***
                $dbh->beginTransaction();
                

                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(":login_id", $loginId);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":seo_name", $seoName);
                $stmt->bindParam(":hash", $hash);
                $stmt->bindParam(":bin_hash", $bin_hash);
                $stmt->bindParam(":items_json", $strItemsJson);

                $stmt->execute();
                $stmt = NULL ;

                $listId = $dbh->lastInsertId();
                settype($listId, "integer");

                // list:item relationships
                $sql2 = "insert into sc_list_item(list_id, item_id) values (%d,%d)" ;
                $sql2 = sprintf($sql2,$listId,$postId);

                $count = $dbh->exec($sql2);

                // update item_count + pseudo_id of list
                $pseudoId =  PseudoId::encode($listId);
                $sql3 = " update sc_list set item_count = 1, pseudo_id = :pseudo_id " ;
                $sql3 .= " where id = :list_id " ;

                $stmt3 = $dbh->prepare($sql3);
                $stmt3->bindParam(":list_id", $listId);
                $stmt3->bindParam(":pseudo_id", $pseudoId);
                $stmt3->execute();
                $stmt3 = NULL ;


                // *** Tx end ***
                $dbh->commit();
                $dbh = null;

                return $count ;

            }catch (PDOException $e) {
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

        static function delete($loginId,$listId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " delete from sc_list where id = ? and login_id = ?" ;

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$listId,$loginId) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }
    }
}
?>
