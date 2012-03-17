<?php

namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\util\StringUtil as StringUtil ;
	
    class Group {

		function getLatest($limit) {
			$rows = mysql\Group::getLatest($limit);
            $groups = array();
            foreach($rows as $row) {
                $group = array('token' => $row['token'] ,
                                'name' => StringUtil::convertKeyToName($row['token']));
                array_push($groups,$group);
            }
			return $groups ;
		}

        function getOnLoginId($loginId) {
            $rows = mysql\Group::getOnLoginId($loginId);
            $groups = array();
            foreach($rows as $row) {
                $group = array('token' => $row['token'] ,
                                'name' => StringUtil::convertKeyToName($row['token']));
                array_push($groups,$group);
            }
			return $groups ;
        }

        function getCountOnLoginId($loginId) {
            $count = 0 ;
            $row = mysql\Group::getCountOnLoginId($loginId);
            if(isset($row) && !empty($row)) {
                $count = $row['count'];
            }

            return $count ;
        }

        function getFeatureSlug() {
            $row = mysql\Group::getFeatureSlug();
            $slug = $row['slug'];
            return $slug;
        }

        function setFeatureSlug($slug) {
            $loginId = \com\indigloo\sc\auth\Login::getLoginIdInSession();
            $code = mysql\Group::setFeatureSlug($loginId,$slug);
            return $code ;
        }

        function slugToGroups($slug){
            if(Util::tryEmpty($slug)) { return array(); }

            $groups = array();
            $slugs = explode(Constants::SPACE,$slug);
            foreach($slugs as $slug){
                if(!Util::tryEmpty($slug)) {
                    $group = array('token' => $slug,'name' => StringUtil::convertKeyToName($slug));
                    array_push($groups,$group);
                }
            }
            return $groups;
        }

        /*
         * @param $dbSlug space separated group token stored in DB 
         *
         */
        function slugToNamesArray($dbslug) {
            $names = array();
            if(!Util::tryEmpty($dbslug)) {
                $slugs = explode(Constants::SPACE,$dbslug);

                foreach($slugs as $slug) {
                    if(Util::tryEmpty($slug)) { continue ; }
                    $name = StringUtil::convertKeyToName($slug);
                    array_push($names,$name);
                }
            }
            return $names ;
        }

        /*
         * @param $dbSlug space separated group token stored in DB 
         *
         */

        function slugToName($dbslug){
            $group_names = '' ;
            $names = $this->slugToNamesArray($dbslug);
            if(!empty($names)){
                $group_names = implode(",",$names);
            }
            return $group_names;
        }
        

        /* 
         * we first convert all (new) names to array of slugs. we take this array
         * and implode on space to make slugs to be stored in DB (we need to implode on space to
         * index the field via sphinx.
         *
         * @param group_names - comma separated ucfirst names for display
         *
         */

        function nameToSlug($group_names) {
            $group_slug = '' ;

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
		
    }
}
?>
