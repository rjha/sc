<?php
namespace com\indigloo\sc\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException;


    class Lists {

        static function get($loginId) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            settype($loginId,"int");
            $sql = " select id,name from sc_list where login_id = %d " ;
            $sql = sprintf($sql,$loginId);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
            return $rows ;

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
                //insert size
                $isize = sizeof($itemIds);
                for($index = 0 ; $index < $isize ; $index++ ) {
                    //last one?
                    $suffix = ($index == ($isize-1)) ? "" : "," ;
                    $sql2 .= sprintf(" (%s,%s)%s ",$listId,$itemIds[$index],$suffix);
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
