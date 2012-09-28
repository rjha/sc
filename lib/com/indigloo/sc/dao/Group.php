<?php

namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\util\StringUtil as StringUtil ;

    class Group {

        function getOnSearchIds($arrayIds) {
            if(empty($arrayIds)) { return array(); }

            $strIds = implode(",",$arrayIds);
            $rows = mysql\Group::getOnSearchIds($strIds);
            return $rows ;

        }

        function getLatest($limit,$filters=array()) {
            $rows = mysql\Group::getLatest($limit,$filters);
            return $rows ;
        }

        function getRandom($limit) {
            $rows = mysql\Group::getRandom($limit);
            return $rows ;
        }

        function getTotalCount($filters=array()){
            $row = mysql\Group::getTotalCount($filters);
            return $row['count'] ;
        }

        function getPaged($paginator,$filters=array()) {
            $limit = $paginator->getPageSize();

            if($paginator->isHome()){
                return $this->getLatest($limit,$filters);
            } else {

                $params = $paginator->getDBParams();
                $start = $params['start'];
                $direction = $params['direction'];
                $rows = mysql\Group::getPaged($start,$direction,$limit,$filters);
                return $rows ;
            }

        }

        function getLatestUserGroups($limit,$filters=array()) {
            $rows = mysql\Group::getLatestUserGroups($limit,$filters);
            return $rows ;
        }

        function getPagedUserGroups($paginator,$filters=array()) {
            $limit = $paginator->getPageSize();

            if($paginator->isHome()){
                return $this->getLatestUserGroups($limit,$filters);
            } else {

                $params = $paginator->getDBParams();
                $start = $params['start'];
                $direction = $params['direction'];
                $rows = mysql\Group::getPagedUserGroups($start,$direction,$limit,$filters);
                return $rows ;
            }

        }

        function getCountOnLoginId($loginId) {
            $count = 0 ;
            $row = mysql\Group::getCountOnLoginId($loginId);
            if(isset($row) && !empty($row)) {
                $count = $row["count"];
            }

            return $count ;
        }

        function getFeatureSlug() {
            $row = mysql\Group::getFeatureSlug();
            $slug = $row["slug"];
            return $slug;
        }

        function setFeatureSlug($slug) {
            $loginId = \com\indigloo\sc\auth\Login::getLoginIdInSession();
            mysql\Group::setFeatureSlug($loginId,$slug);
             
        }

        function slugToGroupsMap($slug){
            if(Util::tryEmpty($slug)) { return array(); }

            $groups = array();
            $slugs = explode(Constants::SPACE,$slug);

            foreach($slugs as $slug){

                if(!Util::tryEmpty($slug)) {
                    $group = array("token" => $slug,"name" => StringUtil::convertKeyToName($slug));
                    array_push($groups,$group);
                }
            }

            return $groups;
        }

        function tokenizeSlug($dbslug,$separator,$convert=false) {
            $list = array() ;
            $buffer = "" ;

            if(!Util::tryEmpty($dbslug)) {

                $slugs = explode(Constants::SPACE,$dbslug);
                foreach($slugs as $slug) {

                    if(Util::tryEmpty($slug)) { continue ; }
                    $slug = ($convert) ? StringUtil::convertKeyToName($slug) : $slug ;
                    array_push($list,$slug);
                }

            }

            if(!empty($list)) {
                $buffer = implode($separator,$list);
            }

            return $buffer ;
        }

        /*
         * convert the group names from UI (multiple groups separated by comma)
         * into a space separated list of hyphenated words 
         * 
         * @param group_names : what user types on the UI
         * 
         */

        function nameToSlug($group_names) {
            $group_slug = "" ;

            if(!Util::tryEmpty($group_names)) {
                $slugs = array();
                $names = explode(",",$group_names);

                foreach($names as $name) {
                    if(Util::tryEmpty($name)) { continue ; }
                    $slug = \com\indigloo\util\StringUtil::convertNameToKey($name);
                    array_push($slugs,$slug);
                }

                $group_slug = implode(Constants::SPACE,$slugs);
            }

            return $group_slug;
        }

        function process($postId) {
            $postDao = new \com\indigloo\sc\dao\Post();
            $postDBRow = $postDao->getonId($postId);

            $group_slug = $postDBRow['group_slug'];
            $version = $postDBRow['version'];
            $catCode = $postDBRow['cat_code'] ;
            $loginId = $postDBRow['login_id'];

            mysql\Group::process($postId,$loginId,$version,$catCode,$group_slug);

        }

    }
}
?>
