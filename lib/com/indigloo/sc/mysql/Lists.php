<?php
namespace com\indigloo\sc\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException;


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

        static function getTotalOnLoginId($loginId) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId,"int");

            $sql = "select list_count as count from sc_user_counter where login_id = %d ";
            $sql = sprintf($sql,$loginId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
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

        static function getTotalItems($filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select count(list.id) as count from sc_list list ";

            $q = new MySQL\Query($mysqli);
            $q->filter($filters);
            $condition = $q->get();
            $sql .= $condition ;

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getLatestItems($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = " select p.* from sc_post p,sc_list_item li" ;
            
            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\ListItem","li");
            //raw condition
            $q->addCondition("p.id = li.item_id");
            $q->filter($filters);
            $condition = $q->get();
            $sql .= $condition;

            $sql .= " order by li.id desc LIMIT %d " ;
            $sql = sprintf($sql,$limit);
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getPagedItems($paginator,$filters) {


            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($start,"integer");
            settype($limit,"integer");
            $direction = $mysqli->real_escape_string($direction);

            $sql = " select p.* from sc_post p,sc_list_item li" ;
            
            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\ListItem","li");
            //raw condition
            $q->addCondition("p.id = li.item_id");
            $q->filter($filters);
            $condition = $q->get();
            $sql .= $condition;
            $sql .= $q->getPagination($start,$direction,"li.id",$limit);
            
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

        static function addItems($listId,$strItemsJson,$itemIds) {

            try{
                // input check 
                settype($loginId,"int");
                if(!is_array($itemIds) || (sizeof($itemIds) <= 0 )) {
                    trigger_error("Bad input: items array is empty",E_USER_ERROR);
                } 

                if(empty($listId) || !self::exists($listId)) {
                    $message = sprintf("Bad input: listId {%s} does not exists",$listId);
                    trigger_error($message,E_USER_ERROR);
                }

                $dbh =  PDOWrapper::getHandle();

                // *** Tx start *** 
                $dbh->beginTransaction();

                // list is changing -
                // some offline processing is needed (set op_bit = 0)
                $sql1 = " update sc_list set version = version + 1 , op_bit = 0 , items_json = :items_json " ;
                $sql1 .= " where id = :list_id";

                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(":list_id", $listId);
                $stmt->bindParam(":items_json", $strItemsJson);
                $stmt->execute();
                $stmt = NULL ;

                $sql2 = "insert into sc_list_item(list_id, item_id) values " ;

                // mysql multiple rows insert using values
                // insert size
                $isize = sizeof($itemIds);
                for($index = 0 ; $index < $isize ; $index++ ) {
                    //last one?
                    $suffix = ($index == ($isize-1)) ? "" : "," ;

                    $itemId = $itemIds[$index];
                    settype($itemId,"integer");
                    $sql2 .= sprintf(" (%s,%s)%s ",$listId,$itemId,$suffix);
                }

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

        static function create($loginId,$name,$hash, $bin_hash,$strItemsJson, $itemIds) {

            try {

                //input check

                Util::isEmpty("name",$name);
                Util::isEmpty("md5 hash of name",$hash);
                Util::isEmpty("md5 bin hash of name",$bin_hash);

                if(!is_array($itemIds) || (sizeof($itemIds) <= 0 )) {
                    trigger_error("Bad input: items array is empty",E_USER_ERROR);
                } 

                //list
                // op_bit is offline_processing bit - set to zero on create
                $sql1 = "insert into sc_list (login_id,name, md5_name, bin_md5_name, " ;
                $sql1 .= "items_json, version, op_bit , created_on) " ;
                $sql1 .= " values (:login_id,:name, :hash, :bin_hash, :items_json,1,0,now()) " ;
                $flag = true ;

                $dbh =  PDOWrapper::getHandle();

                // *** Tx start ***
                $dbh->beginTransaction();
                

                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(":login_id", $loginId);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":hash", $hash);
                $stmt->bindParam(":bin_hash", $bin_hash);
                $stmt->bindParam(":items_json", $strItemsJson);

                $stmt->execute();
                $stmt = NULL ;

                $listId = $dbh->lastInsertId();
                settype($listId, "integer");

                // list:item relationships
                $sql2 = "insert into sc_list_item(list_id, item_id) values " ;

                // mysql multiple rows insert using values
                // insert size
                $isize = sizeof($itemIds);
                for($index = 0 ; $index < $isize ; $index++ ) {
                    //last one?
                    $suffix = ($index == ($isize-1)) ? "" : "," ;

                    //never trust the user input
                    // never ever!
                    $itemId = $itemIds[$index];
                    settype($itemId,"integer");
                    $sql2 .= sprintf(" (%s,%s)%s ",$listId,$itemId,$suffix);
                }

                $count = $dbh->exec($sql2);

                // update item_count of list 
                $sql3 = " update sc_list set item_count = :item_count where id = :list_id " ;
                $stmt3 = $dbh->prepare($sql3);
                $stmt3->bindParam(":list_id", $listId);
                $stmt3->bindParam(":item_count", $isize);
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
    }
}
?>
