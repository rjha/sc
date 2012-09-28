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

        // exact match of sc_post.group_slug (internal use only)
        // used on item page (token supplied with OR operator)
        function getPostByGroup($token,$offset,$limit) {
            $ids = $this->getMatch("post_groups",$token,$offset,$limit,false);
            return $ids;
        }

        // used on item page to find related posts via groups and title
        // use quorum operator
        function getRelatedPosts($token,$hits,$offset,$limit) {
            $ids = $this->getQuorum("posts",$token,$hits,$offset,$limit);
            return $ids;
        }

        // used by group controller
        // @todo - first run escape on tokens and then 
        // combine them by pipe
        function getPostCountByGroup($token) {
            $count = $this->getMatchCount("post_groups",$token);
            return $count ;
        }

        function getPagedPostByGroup($token,$paginator) {
            $pageNo = $paginator->getPageNo();
            $limit = $paginator->getPageSize();
            $offset = ($pageNo-1) * $limit ;
            //use the token as it is w/o escaping the pipes
            $ids = $this->getMatch("post_groups",$token,$offset,$limit,false);
            return $ids;
        }

        // used by search controller
        function getPostsCount($token) {
            $count = $this->getMatchCount("posts",$token);
            return $count ;
        }

        function getPosts($token,$offset,$limit) {
            $ids = $this->getMatch("posts",$token,$offset,$limit);
            return $ids;
        }

        function getPagedPosts($token,$paginator) {
            $pageNo = $paginator->getPageNo();
            $limit = $paginator->getPageSize();
            $offset = ($pageNo-1) * $limit ;

            $ids = $this->getMatch("posts",$token,$offset,$limit);
            return $ids;
        }

        function getGroups($token,$offset,$limit) {
            $ids = $this->getMatch("groups",$token,$offset,$limit);
            return $ids;
        }

        function getMatchCount($index,$token,$escape=true) {
            if(Util::tryEmpty($token)) { return 0 ; }
            Util::isEmpty('index',$index);
            //@todo
            //plain wrong! all tokens must be escaped

            $token = ($escape) ? $this->escape($token) : $token;

            $sql = " select id from %s where match('%s') limit 0,1 " ;
            $sql = sprintf($sql,$index,$token);

            //@imp: we need to fire dummy query
            // to retrieve stats from sphinx.
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

        function getMatch($index,$token,$offset,$limit,$escape=true) {
            if(Util::tryEmpty($token)) { return array() ; }
            Util::isEmpty("index",$index);
            
            $token = ($escape) ? $this->escape($token) : $token;

            $sql = " select id from %s where match('%s') " ;
            $sql = sprintf($sql,$index,$token);
            $sql .= sprintf(" limit %d,%d ",$offset,$limit) ;
            
            $rows = MySQL\Helper::fetchRows($this->connx,$sql);
            $ids = array();

            foreach($rows as $row){
                array_push($ids,$row["id"]);
            }

            return $ids ;
        }

        /*
         *
         * Quorum operator needs a syntax like
         * mysql> select id from posts where match('\"zari  patang bag\"\/2') limit 0,10;
         * 
         *
         */
        function getQuorum($index,$token,$hits,$offset,$limit,$escape=true) {
            if(Util::tryEmpty($token)) { return array() ; }
            Util::isEmpty("index",$index);
            
            $token = ($escape) ? $this->escape($token) : $token;

            $sql = sprintf("select id from %s where match('",$index) ;
            $sql .= '\"'.$token.'\"\/'.$hits."')" ;
            $sql .= sprintf(" limit %d,%d ",$offset,$limit) ;

            $rows = MySQL\Helper::fetchRows($this->connx,$sql);
            $ids = array();

            foreach($rows as $row){
                array_push($ids,$row["id"]);
            }

            return $ids ;
        }


    }

}
?>
