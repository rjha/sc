<?php

namespace com\indigloo\sc\search {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\mysql as MySQL;
    
    class SphinxQL {

		private $connx ;

		function __construct() {
            //create connection
            $user = Config::getInstance()->get_value("mysql.user");
            $password = Config::getInstance()->get_value("mysql.password");
            $connx = new \mysqli("127.0.0.1",$user,$password,"",9306);
            if ($connx->connect_errno) {
                trigger_error($connx->connect_error, E_USER_ERROR);
                exit ;
            }
            $this->connx = $connx ;
          
        }

        function close() {
            $this->connx->close();

        }

        function getPostIdsOnGroup($token) {
            $sql = " select id from groups where match('".$token."') limit 0,50" ;
            $rows = MySQL\Helper::fetchRows($this->connx,$sql);
            $ids = array();

            foreach($rows as $row){
                array_push($ids,$row['id']);
            }

            return $ids ; 
        }

        function getPostIds($token) {
            $sql = " select id from posts where match('".$token."') limit 0,50" ;
            $rows = MySQL\Helper::fetchRows($this->connx,$sql);
            $ids = array();

            foreach($rows as $row){
                array_push($ids,$row['id']);
            }

            return $ids ; 
        }

    }

}
?>
