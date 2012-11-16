<?php

namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\mysql as mysql;

    use \com\indigloo\Logger as Logger;
    use \com\indigloo\sc\util\PseudoId ;

    class Post {

        function getOnId($postId) {
            $row = mysql\Post::getOnId($postId);
            return $row ;
        }

        function getOnItemId($itemId) {

            $postId = PseudoId::decode($itemId);
            $row = mysql\Post::getOnId($postId);
            return $row ;
        }

        /*
         * @return imgv array containing following keys
         *  - name
            - tname
            - source
            - thumbnail
            - width
            - height 
        * 
        * if no image found then return a placeholder image.
        * 
        */

        function getImageOnId($postId){
             $row = mysql\Post::getOnId($postId);
             $json = $row["images_json"];
             $imgv =  \com\indigloo\sc\html\Post::getImageOrDefault($json);
             return $imgv ;
        }

        /* 
         * @return imgv array containing image details
         * or NULL if no image found 
         *
         */
        function tryImageOnId($postId){
            
            $row = mysql\Post::getOnId($postId);
            $json = $row["images_json"];
            $images = json_decode($json);

            if( !empty($images) && (sizeof($images) > 0)) {
                $image = $images[0] ;
                $imgv = \com\indigloo\sc\html\Post::convertImageJsonObj($image);
                return $imgv ;
            }

            return NULL ;

        }

        /**
         * @error if links json is empty or spaces in DB column
         * @error if links json evaluates to NULL by json_decode
         * @error if links json is valid but not an array
         * @return an array of strings (links)
         *
         */

        function getLinkDataOnId($postId) {
            $row = mysql\Post::getLinkDataOnId($postId);
            $json = $row['json'];
            $links = NULL;

            if(!Util::tryEmpty($json)) {
                $links = json_decode($json);
            }

            if(is_null($links) || !is_array($links)) {
                $message = sprintf("Post %d has Bad json [ %s ] ",$postId,$json);
                Logger::getInstance()->error($message);
                $links = NULL ;
            }

            $data = array('links' => $links, 'version' => $row['version']);
            return $data ;

        }

        function getOnLoginId($loginId,$limit) {
            $rows = mysql\Post::getOnLoginId($loginId,$limit);
            return $rows ;
        }

        function getOnSearchIds($arrayIds) {
            if(empty($arrayIds)) { return array(); }

            $strIds = implode(",",$arrayIds);
            $rows = mysql\Post::getOnSearchIds($strIds);
            return $rows ;
        }

        function getRandom($limit) {
            $rows = mysql\Post::getRandom($limit);
            return $rows ;
        }

        function getPosts($limit,$filters=array()) {
            $rows = mysql\Post::getPosts($limit,$filters);
            return $rows ;
        }

        function getPaged($paginator,$filters=array()) {
            $limit = $paginator->getPageSize();
            
            if($paginator->isHome()){
                return $this->getLatest($limit,$filters);
            } else {

                $params = $paginator->getDBParams();
                $start = $params['start'];
                $direction = $params['direction'];
                $rows = mysql\Post::getPaged($start,$direction,$limit,$filters);
                return $rows ;
            }
        }

        function getLatest($limit,$filters=array()) {
            $rows = mysql\Post::getLatest($limit,$filters);
            return $rows ;
        }
        /*
         * used in monitor posts and random posts controller 
         * use site counter when filters is empty
         * 
         */
        function getTotalCount($filters=array()) {
            $count = 0 ;

            if(empty($filters)) {
                // no filter case
                $row = mysql\Analytic::getSiteCounters();
                if(!empty($row)) {
                    $count = $row["post_count"];
                }

            }else {
                //get from table using where condition
                $row = mysql\Post::getTotalCount($filters);
                $count = $row["count"];
            }
           
            return $count ;
        }

        function create($title,
                        $description,
                        $loginId,
                        $name,
                        $linksJson,
                        $imagesJson,
                        $groupSlug,
                        $categoryCode) {

            $itemId = mysql\Post::create(
                                $title,
                                $description,
                                $loginId,
                                $linksJson,
                                $imagesJson,
                                $groupSlug,
                                $categoryCode);

            //Add to feed
            $feedDao = new \com\indigloo\sc\dao\ActivityFeed();
            $verb = \com\indigloo\sc\Constants::POST_VERB ;
            $image =  \com\indigloo\sc\html\Post::getImageOrDefault($imagesJson);
            $feedDao->addPost($loginId, $name, $itemId, $title,$image,$verb);

            return $itemId ;
        }

        function update($postId,
                        $title,
                        $description,
                        $linksJson,
                        $imagesJson,
                        $groupSlug,
                        $categoryCode) {

            $loginId = NULL ;

            if(\com\indigloo\sc\auth\Login::isAdmin()) {

                //inject right loginId for admins
                $postDBRow = $this->getOnId($postId);
                $loginId = $postDBRow["login_id"];

            } else {
                $loginId = \com\indigloo\sc\auth\Login::getLoginIdInSession();
            }

            mysql\Post::update($postId,
                               $title,
                               $description,
                               $linksJson,
                               $imagesJson,
                               $loginId,
                               $groupSlug,
                               $categoryCode);

        }

        function delete($postId){
            
            $loginId = NULL ;

            if(\com\indigloo\sc\auth\Login::isAdmin()) {
                
                //inject right loginId for admins
                $postDBRow = $this->getOnId($postId);
                $loginId = $postDBRow["login_id"];

            } else {
                $loginId = \com\indigloo\sc\auth\Login::getLoginIdInSession();
            }

            mysql\Post::delete($postId,$loginId);
            

        }
        
        function getLatestOnCategory($code,$limit){
            $rows = mysql\Post::getLatestOnCategory($code,$limit);
            return $rows ;
        }
        
        function getPagedOnCategory($paginator,$code) {
 
            $limit = $paginator->getPageSize();

            if($paginator->isHome()){
                return $this->getLatestOnCategory($code,$limit);
                
            } else {
                $params = $paginator->getDBParams();
                $start = $params["start"];
                $direction = $params["direction"];

                $rows = mysql\Post::getPagedOnCategory($start,$direction,$limit,$code);
                return $rows ;
            }
        }

        function feature ($postId) {
            mysql\Post::set_fp_bit($postId,1);
        }

        function unfeature ($postId) {
            mysql\Post::set_fp_bit($postId,0);
        }
        
    }

}
?>
