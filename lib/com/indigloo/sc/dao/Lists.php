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

        function getOnLoginId($loginId) {
            $rows = mysql\Lists::getOnLoginId($loginId);
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

        function createNew($loginId,$name,$description) {
            //md5 hash as hex string and bytes
            $hash = md5($name);
            $bin_hash = md5($name,TRUE); 
            $seoName = StringUtil::convertNameToKey($name);
            mysql\Lists::createNew(
                $loginId,
                $name,
                $seoName,
                $hash,
                $bin_hash,
                $description); 
        }

        function create($loginId,$name,$itemId) {

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
            $bin_hash = md5($name,TRUE); 
            $seoName = StringUtil::convertNameToKey($name);
            mysql\Lists::create(
                $loginId,
                $name,
                $seoName,
                $hash,
                $bin_hash,
                $itemsJson,
                $postId);

            return ;
        }

        function addItem($loginId,$listId,$itemId){
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
            $bin_hash = md5($name,TRUE); 
            $seoName = StringUtil::convertNameToKey($name);
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
