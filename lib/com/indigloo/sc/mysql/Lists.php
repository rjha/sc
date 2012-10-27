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

            $sql = " select id,name from sc_list where login_id = %d " ;
            $sql = sprintf($sql,$loginId);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
            return $rows ;

        }

        static function getTotalOnLoginId($loginId) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId,"int");

            $sql = "select count(id) as count from sc_list where login_id = %d ";
            $sql = sprintf($sql,$loginId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        function getPaged($start,$direction,$limit,$loginId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($start,"integer");
            settype($limit,"integer");
            settype($loginId,"integer");
            $direction = $mysqli->real_escape_string($direction);

            //@todo paged sorting on name!
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

        function getLatest($limit,$loginId) {
            $rows = mysql\Lists::getLatest($limit,$loginId);
            return $rows ;
        }

        static function getOnId($listId) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            settype($listId,"int");
            $sql = " select id,name from sc_list where id = %d " ;
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

        /*
         * updating a list means pushing new items into it
         * deleting items from list is a separate call.
         * we want to 
         *  - push new items into list 
         *  - update dup_bit for existing items
         * 
        */

        static function update($listId,$itemIds) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            // input check 
            settype($loginId,"int");
            if(!is_array($itemIds) || (sizeof($itemIds) <= 0 )) {
                trigger_error("Bad input: items array is empty",E_USER_ERROR);
            } 

            if(empty($listId) || !self::exists($listId)) {
                $message = sprintf("Bad input: listId {%s} does not exists",$listId);
                trigger_error($message,E_USER_ERROR);
            }

            $sql = "insert into sc_list_item(list_id, item_id) values " ;

            // mysql multiple rows insert using values
            // insert size
            $isize = sizeof($itemIds);
            for($index = 0 ; $index < $isize ; $index++ ) {
                //last one?
                $suffix = ($index == ($isize-1)) ? "" : "," ;

                $itemId = $itemIds[$index];
                settype($itemId,"integer");
                $sql .= sprintf(" (%s,%s)%s ",$listId,$itemId,$suffix);
            }

            $sql .= " on duplicate key update dup_bit = 1 " ;
            
            MySQL\Helper::executeSQL($mysqli,$sql);

        }

        static function create($loginId,$name,$hash, $bin_hash, $itemIds) {

            try {

                //input check

                Util::isEmpty("name",$name);
                Util::isEmpty("md5 hash of name",$hash);
                Util::isEmpty("md5 bin hash of name",$bin_hash);

                if(!is_array($itemIds) || (sizeof($itemIds) <= 0 )) {
                    trigger_error("Bad input: items array is empty",E_USER_ERROR);
                } 

                //list
                $sql1 = "insert into sc_list (login_id,name, md5_name, bin_md5_name, created_on) " ;
                $sql1 .= " values (:login_id,:name, :hash, :binhash, now()) " ;
                $flag = true ;

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(":login_id", $loginId);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":hash", $hash);
                $stmt->bindParam(":binhash", $bin_hash);
                
                $flag = $stmt->execute();

                if(!$flag){
                    $dbh->rollBack();
                    $dbh = null;
                    $message = sprintf("DB Error : code is  %s",$stmt->errorCode());
                    throw new DBException($message);
                }

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
                //Tx end
                $dbh->commit();
                $dbh = null;

                return $count ;

            }catch (PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($e->getMessage(),$e->getCode());
            }
            
        }
    }
}
?>
