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
          
        function getGroupsCount($token) {
            $count = $this->getDocumentsCount('groups',$token);
            return $count ;
        }

        function getGroups($token,$offset,$limit) {
            $ids = $this->getDocuments('groups',$token,$offset,$limit);
            return $ids;
        }

        function getPagedGroups($token,$paginator) {
            $pageNo = $paginator->getPageNo();
            $limit = $paginator->getPageSize();
            $offset = ($pageNo-1) * $limit ;

            $ids = $this->getDocuments('groups',$token,$offset,$limit);
            return $ids;
        }
 
        function getPostsCount($token) {
            $count = $this->getDocumentsCount('posts',$token);
            return $count ;
        }

        function getPosts($token,$offset,$limit) {
            $ids = $this->getDocuments('posts',$token,$offset,$limit);
            return $ids;
        }

        function getPagedPosts($token,$paginator) {
            $pageNo = $paginator->getPageNo();
            $limit = $paginator->getPageSize();
            $offset = ($pageNo-1) * $limit ;

            $ids = $this->getDocuments('posts',$token,$offset,$limit);
            return $ids;
        }

        function getDocumentsCount($index,$token) {
            Util::isEmpty('index',$index);
            Util::isEmpty('token',$token);

            $sql = " select id from %s where match('%s') limit 0,1" ;
            $sql = sprintf($sql,$index,$token);
            $rows = MySQL\Helper::fetchRows($this->connx,$sql);
            // get meta data about this token
            $sql = " show meta " ;
            $stats = MySQL\Helper::fetchRows($this->connx,$sql);

            $count = 0;
            foreach($stats as $stat){
                if($stat['Variable_name'] == 'total') {
                    $count = $stat['Value'] ;
                }
            }

            return $count ;
        }

        function getDocuments($index,$token,$offset,$limit) {
            Util::isEmpty('index',$index);
            Util::isEmpty('token',$token);
            //get paginator params
            
            $sql = " select id from %s where match('%s') limit %d,%d" ;
            $sql = sprintf($sql,$index,$token,$offset,$limit);

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
