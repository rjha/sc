<?php
namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\exception\UIException as UIException;

    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\util\StringUtil as StringUtil ;


    /**
     * 
     * @imp According to our naming convention of using singulars : This class
     * should have been called "List" instead of "Lists"
     *
     * Now you are introduced to half-baked namespace support in PHP5.3
     * List is a reserved keyword in PHP. ideally namespace should have taken care 
     * a class named _List_ here but the PHP parser will always interpret *List* as 
     * a reserved word even inside custom namespaces! what a bummer!
     *
     */

    class Lists {

        private  $defaults ;
        private $defaultKeys ;

        function __construct() {
            // since we push this list onto db list rows 
            // the elements will appear in reverse order on UI
            $this->defaults = array("Wishlist" => "-1002", "Favorites" => "-1001");
            $this->defaultKeys = array("wishlist", "favorites");
        }

        private function createListRow($name,$loginId) {
            $row = array();

            $row["name"] = $name ;
            $row["version"] = -1 ;
            $row["login_id"] = $loginId ;
            $row["id"] = $this->defaults[$name] ;

            return $row ;

        }

        private function filterDefault($dlrows) {
            
            $dbKeys = array();
            $names = array();

            foreach($dlrows as $row) {
                array_push($dbKeys,$row["seo_name"]);
            }

            foreach($this->defaults as $default => $id) {
                $seoKey = StringUtil::convertNameToKey($default);

                if(!in_array($seoKey,$dbKeys)){
                    array_push($names,$default);
                }
            }

            return $names ;
        }

        function getOnLoginId($loginId) {

            // all Rows
            $rows = mysql\Lists::getOnLoginId($loginId);
            $dlrows = mysql\Lists::getDefaultOnLoginId($loginId);
            $defaults = $this->filterDefault($dlrows);

            // extra rows in front
            foreach($defaults as $default) {
                array_unshift($rows,$this->createListRow($default,$loginId));
            }

            return $rows ;
        }

        function getOnId($listId) {
            $row = mysql\Lists::getonId($listId);
            return $row ;
        }

        function exists($listId) {
            return mysql\Lists::exists($listId);
        }

        function isOwner($loginId,$listId) {
            $row = $this->getOnId($listId);
            $loginIdInDB = $row["login_id"];
            settype($loginIdInDB, "integer");

            if($loginIdInDB != $loginId) {
                $error = "List ownership is in dispute!" ;
                throw new UIException(array($error));
            }

        }

        function getPagedOnLoginId($paginator,$loginId) {

            $limit = $paginator->getPageSize();
            $pageNo = $paginator->getPageNo();
            $offset = ($pageNo-1) * $limit ; 

            $rows = mysql\Lists::getPagedOnLoginId($loginId,$offset,$limit);
            return $rows ;
        }
        
        function getLatestItems($limit,$filters) {
            $rows = mysql\Lists::getLatestItems($limit,$filters);
            return $rows ;
        }

        function getPagedItems($paginator,$filters) {
            $limit = $paginator->getPageSize();
            
            if($paginator->isHome()){
                return $this->getLatestItems($limit,$filters);

            } else {
                
                $params = $paginator->getDBParams();
                $start = $params["start"];
                $direction = $params["direction"];
                $rows = mysql\Lists::getPagedItems($start,$direction,$limit,$filters);
                return $rows ;
            }
        }

        // create a new list w/o items
        function createNew($loginId,$name,$description,$dl_bit=0) {

            $seoName = StringUtil::convertNameToKey($name);
            if( ($dl_bit == 0) && in_array($seoName,$this->defaultKeys)) {
                $errorMsg = sprintf("Error: list name _%s_ is already in use!",$name);
                throw new UIException(array($errorMsg));
            }

            //md5 hash as hex string and bytes
            $hash = md5($name);
            //unique name constraint is on seo_name
            // that is used in pub URL
            $bin_hash = md5($seoName,TRUE); 

            mysql\Lists::createNew(
                $loginId,
                $name,
                $seoName,
                $hash,
                $bin_hash,
                $description); 
        }

        // create a new list with items
        function create($loginId,$name,$itemId,$dl_bit = 0) {

            $seoName = StringUtil::convertNameToKey($name);

            if(($dl_bit == 0) && in_array($seoName,$this->defaultKeys)) {
                $errorMsg = sprintf("Error: list name _%s_ is already in use!",$name);
                throw new UIException(array($errorMsg));
            }

            //get item row
            $postId = PseudoId::decode($itemId);
            $postDao = new \com\indigloo\sc\dao\Post();
            $imgv = $postDao->tryImageOnId($postId);
            $items = array();

            if(!is_null($imgv)) {
                $json = new \stdClass ;
                $json->id = $itemId ;
                $json->thumbnail = $imgv["thumbnail"];
                array_push($items,$json);
            }

            $itemsJson = json_encode($items);
            $itemsJson = Util::formSafeJson($itemsJson);

            //md5 hash as hex string and bytes
            $hash = md5($name);
            $bin_hash = md5($seoName,TRUE); 

            $listId = mysql\Lists::create(
                $loginId,
                $name,
                $seoName,
                $hash,
                $bin_hash,
                $itemsJson,
                $postId,
                $dl_bit);

            return $listId;
        }

        function addItem($loginId,$listId,$itemId){

            // transpose of defaults list
            // 1 => favorites, 2 => wishlist etc.
            $transpose = array_flip($this->defaults);

            if(array_key_exists($listId, $transpose)) {

                //create new list with dl_bit set to 1
                $name = $transpose[$listId];
                $this->create($loginId,$name,$itemId,1);
                return ;
            }

            // list ownership check is required
            // when we do not pass the loginId to backend
            // someone assuming a "fake" loginId is a problem
            // that data layer cannot solve!

            $this->isOwner($loginId,$listId);

            $postId = PseudoId::decode($itemId);
            $row = $this->getOnId($listId);
            $dbItemsJson = $row["items_json"];

            $dbItems = json_decode($dbItemsJson);
            $dbItemIds = array();

            foreach($dbItems as $dbItem) {
                array_push($dbItemIds,$dbItem->id);
            }

            // update items_json summary only if
            // #1 - the number of items < 4 
            // #2 - we have not seen this item earlier

            if( (sizeof($dbItemIds) < 4) && (!in_array($itemId,$dbItemIds))) {
                //get item row
                $postDao = new \com\indigloo\sc\dao\Post();
                $imgv = $postDao->tryImageOnId($postId);
                $items = array();

                if(!is_null($imgv)) {
                    $json = new \stdClass ;
                    $json->id = $itemId ;
                    $json->thumbnail = $imgv["thumbnail"];
                    array_push($dbItems,$json);
                }

            }

            $itemsJson = json_encode($dbItems);
            $itemsJson = Util::formSafeJson($itemsJson);
            mysql\Lists::addItem($listId,$itemsJson,$postId);

        }

        function deleteItems($loginId,$listId,$itemsJson) {

            $items = json_decode($itemsJson);
            
            //get all the itemIds
            $itemIds = array();
            foreach($items as $item) {
                $itemId = PseudoId::decode($item);
                array_push($itemIds,$itemId);
            }

            if(empty($itemIds)) {
                //@todo - throw error?
                return ;
            }

            mysql\Lists::deleteItems($loginId,$listId,$itemIds);
            
        }

        function edit($loginId,$listId,$name,$description) {

            //md5 hash as hex string and bytes
            $hash = md5($name);
            $seoName = StringUtil::convertNameToKey($name);
            $bin_hash = md5($seoName,TRUE); 

            mysql\Lists::edit(
                $loginId,
                $listId,
                $name,
                $seoName,
                $hash,
                $bin_hash,
                $description); 
        }

        function delete($loginId,$listId) {
            mysql\Lists::delete($loginId,$listId);
        }

        function addItemLink($loginId,$listId,$itemLink) {
            // get item id from link

        }

        


    }
}
?>
