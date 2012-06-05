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
            $host = Config::getInstance()->get_value("mysql.host");
            $port = Config::getInstance()->get_value("mysql.sphinx.port");

            $connx = new \mysqli($host,$user,$password,"",$port);
            if ($connx->connect_errno) {
                trigger_error($connx->connect_error, E_USER_ERROR);
                exit ;
            }
            $this->connx = $connx ;
        }

        function escape($token) {
            $from = array ( '\\', '(',')','|','-','!','@','~','"','&', '/', '^', '$', '=', "'",
                "\x00", "\n", "\r", "\x1a" );
            $to   = array ( '\\\\', '\\\(','\\\)','\\\|','\\\-','\\\!','\\\@','\\\~','\\\"', '\\\&', '\\\/',
                 '\\\^', '\\\$', '\\\=', "\\'", "\\x00", "\\n", "\\r", "\\x1a" );
            return str_replace ($from, $to, $token);
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
            if(Util::tryEmpty($token)) { return 0 ; }
            Util::isEmpty('index',$index);
            //escape token
            $token = $this->escape($token);

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
            if(Util::tryEmpty($token)) { return array() ; }
            Util::isEmpty('index',$index);
            //get paginator params
            //escape token
            $token = $this->escape($token);

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
