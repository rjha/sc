<?php
namespace com\indigloo\sc\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException;


    class ItemList {

        static function get($loginId) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            settype($loginId,"int");
            $sql = " select id,name from sc_list where login_id = %d " ;
            $sql = sprintf($sql,$loginId);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
            return $rows ;

        }

        static function create($loginId,$name,$itemIds) {
            try {

                //list
                $sql1 = "insert into sc_list (login_id,name,created_on) " ;
                $sql1 .= " values (:login_id,:name, now()) " ;
                $flag = true ;

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(":login_id", $loginId);
                $stmt->bindParam(":name", $name);
                
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
