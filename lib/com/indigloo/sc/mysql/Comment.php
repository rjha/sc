<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Comment {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Comment';

		static function getOnPostId($postId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($postId,"integer");

            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l " ;
            $sql .= " where l.id = a.login_id and  a.post_id = %d " ;
            $sql = sprintf($sql,$postId);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
		}
		
		static function getOnId($commentId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();

            settype($commentId,"integer");
			
            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l ";
            $sql .= " where l.id = a.login_id and a.id = %d " ;
            $sql = sprintf($sql,$commentId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}
		
		static function getLatest($limit,$filters) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($limit,"integer");
            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l " ;

            $q = new Query();
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
		
		static function getTotalCount($filters) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " select count(id) as count from sc_comment ";

            $q = new Query();
            $q->filter($filters);
            $condition = $q->get();

            $sql .= $condition ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}

		static function getPaged($start,$direction,$limit,$filters) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();

            settype($start,"integer");
            settype($limit,"integer");

            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l " ;

            $q = new Query();
            $q->setAlias("com\indigloo\sc\model\Comment","a");
            //raw condition
            $q->addCondition("l.id = a.login_id");
            $q->filter($filters);
            $condition = $q->get();

            $sql .= $condition;

            if($direction == 'after') {
                $sql .= " and a.id < %d order by a.id DESC LIMIT %d " ;
            } else if($direction == 'before'){
                $sql .= " and a.id > %d  order by a.id ASC LIMIT % d ";
            } else {
                trigger_error("Unknow sort direction in query", E_USER_ERROR);
            }
            
            $sql = sprintf($sql,$start,$limit); 
            
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

        static function create($postId, $comment, $loginId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_comment(post_id,description,login_id, created_on) " ;
            $sql .= " values(?,?,?,now()) ";

            $code = MySQL\Connection::ACK_OK;
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("isi",$postId,$comment,$loginId);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    $code = MySQL\Error::handle(self::MODULE_NAME, $stmt);
                }
                $stmt->close();
            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
			
			return $code ;
        }
		
		static function update($commentId,$comment,$loginId) {
			
			$code = MySQL\Connection::ACK_OK ;
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$sql = "update sc_comment set description = ?, updated_on = now() where id = ? and login_id = ?" ;
			
			$stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("sii",$comment,$commentId,$loginId) ;
                $stmt->execute();
                $stmt->close();
				
            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
			
			return $code ;
			
		}

		static function delete($commentId,$loginId) {

			$code = MySQL\Connection::ACK_OK ;
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$sql = "delete from sc_comment where id = ? and login_id = ?" ;

			$stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$commentId,$loginId) ;
                $stmt->execute();
                $stmt->close();
				
            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
			
			return $code ;
		}
	}
}
?>
