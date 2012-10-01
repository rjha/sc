<?php

namespace com\indigloo\sc\search {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\mysql as MySQL;
   use \com\indigloo\Constants ;

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
        function getPostByGroup($dbslug,$offset,$limit) {

            $bucket = array();
            /* limit for groups #1,2 and 3 */
            
            $limitMap = array( 0 => 8, 1 => 4 , 2 => 2);

            if(!Util::tryEmpty($dbslug)) {

                $slugs = explode(Constants::SPACE,$dbslug);
                $count = 0 ;

                foreach($slugs as $slug) {
                    if(Util::tryEmpty($slug)) { continue ; }

                    /* fetch only one post beyond group #3 */
                    $limit = ($count <=2 ) ? $limitMap[$count] : 1 ;
                    $ids = $this->getMatch("post_groups",$slug,$offset,$limit);

                    $unique = array_diff($bucket,$ids);
                    $bucket = array_merge($bucket,$unique);
                    $count++ ;
                }
            }

            return $bucket;
        }

        // used on item page to find related posts via groups and title
        // use quorum operator
        function getRelatedPosts($line,$hits,$offset,$limit) {
            
            $line = $this->escape($line);
            $ids = $this->getQuorum("posts",$line,$hits,$offset,$limit);
            return $ids;
        }

        // used by group controller
        // single token
        function getPostCountByGroup($token) {
            $token = $this->escape($token);
            $count = $this->getMatchCount("post_groups",$token);
            return $count ;
        }

        function getPagedPostByGroup($token,$paginator) {
            $token = $this->escape($token);
            $pageNo = $paginator->getPageNo();
            $limit = $paginator->getPageSize();
            $offset = ($pageNo-1) * $limit ;
            //use the token as it is w/o escaping the pipes
            $ids = $this->getMatch("post_groups",$token,$offset,$limit);
            return $ids;
        }

        // used by search controller
        function getPostsCount($token) {
            $token = $this->escape($token);
            $count = $this->getMatchCount("posts",$token);
            return $count ;
        }

        function getPosts($token,$offset,$limit) {
            $token = $this->escape($token);
            $ids = $this->getMatch("posts",$token,$offset,$limit);
            return $ids;
        }

        function getPagedPosts($token,$paginator) {
            $token = $this->escape($token);
            $pageNo = $paginator->getPageNo();
            $limit = $paginator->getPageSize();
            $offset = ($pageNo-1) * $limit ;

            $ids = $this->getMatch("posts",$token,$offset,$limit);
            return $ids;
        }

        function getGroups($token,$offset,$limit) {
            $token = $this->escape($token);
            $ids = $this->getMatch("groups",$token,$offset,$limit);
            return $ids;
        }

        function getMatchCount($index,$token) {
            if(Util::tryEmpty($token)) { return 0 ; }
            Util::isEmpty('index',$index);
            
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

        function getMatch($index,$token,$offset,$limit) {
            if(Util::tryEmpty($token)) { return array() ; }
            Util::isEmpty("index",$index);
            
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
        function getQuorum($index,$token,$hits,$offset,$limit) {
            if(Util::tryEmpty($token)) { return array() ; }
            Util::isEmpty("index",$index);
            
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
