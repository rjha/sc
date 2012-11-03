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

        function getTotalOnLoginId($loginId) {
            $row = mysql\Lists::getTotalOnLoginId($loginId);
            return $row["count"] ;
        }

        function getPagedOnLoginId($paginator,$loginId) {

            $limit = $paginator->getPageSize();
            $pageNo = $paginator->getPageNo();
            $offset = ($pageNo-1) * $limit ; 

            $rows = mysql\Lists::getPagedOnLoginId($loginId,$offset,$limit);
            return $rows ;
        }

        function getTotalItems($filters) {
            $row = mysql\Lists::getTotalItems($filters);
            return $row['count'] ;
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
                $rows = mysql\Post::getPagedItems($start,$direction,$limit,$filters);
                return $rows ;
            }
        }

        private function getIdMergeItems($dbItemsJson, $frmItemsJson) {
            $data = $this->getIdAndItems($frmItemsJson);
            $ids = $data["ids"];
            $items = $data["items"];

            $numImages = 5  - (sizeof($items));
            $count = 0 ;

            if($numImages > 0 ) { 
                $dbItems = json_decode($dbItemsJson);
                foreach($dbItems as $dbItem) {
                    if($count >= $numImages) break ;
                    if(property_exists($dbItem,"thumbnail")) { 
                        array_push($items,$dbItem); $count++ ;
                    }
                }
            }

            //reassign
            $data["items"] = $items ;
            return $data;
        }

        private function getIdAndItems($frmItemsJson) {
            $frmItems = json_decode($frmItemsJson);

            $ids = array();
            $bucket = array();

            $count = 0 ;
            $numImages = 5 ;

            foreach($frmItems as $item) {
                if(ctype_digit($item->id)) {
                    //all items
                    array_push($ids, PseudoId::decode($item->id)) ;
                    // numImages images
                    if(($count < $numImages) && property_exists($item,"thumbnail")) { 
                        array_push($bucket, $item) ; $count++ ;
                    }
                }
            }

            $data = array("ids" => $ids , "items" => $bucket );
            return $data;
        }

        /**
         *
         * @param frmItemsJson is string representation of an array of json 
         * objects. each object has attribute
         *  - id
         *  - thumbnail 
         *
         */
        function create($loginId,$name,$frmItemsJson) {

            $data = $this->getIdAndItems($frmItemsJson);
            $itemIds = $data["ids"];
            $items = $data["items"];
            $itemsJson = json_encode($items);
            $itemsJson = Util::formSafeJson($itemsJson);

            if(empty($itemIds)) {
                $message = " Not able to create List without items!";
                throw new UIException(array($message));
            }

            //md5 hash as hex string and bytes
            $hash = md5($name);
            $bin_hash = md5($name,TRUE); 
            $seoName = StringUtil::convertNameToKey($name);
            $count = mysql\Lists::create(
                $loginId,
                $name,
                $seoName,
                $hash,
                $bin_hash,
                $itemsJson,
                $itemIds);

            return $count ;
        }

        //@todo - add @param loginId
        function addItems($listId,$frmItemsJson){
            $row = $this->getOnId($listId);
            $dbItemsJson = $row["items_json"];

            $data = $this->getIdMergeItems($dbItemsJson,$frmItemsJson);
            $itemIds = $data["ids"];
            $items = $data["items"];
            $itemsJson = json_encode($items);
            $itemsJson = Util::formSafeJson($itemsJson);

            if(empty($itemIds)) {
                $message = " Not able to create List without items!";
                throw new UIException(array($message));
            }

            mysql\Lists::addItems($listId,$itemsJson,$itemIds);
        }

        function deleteItems($loginId,$listId,$frmItemsJson) {
            // @todo - look @ list.items_json
            // if it contains items that are being deleted then
            // we need to update list.items_json as well 
            trigger_error("Not implemented",E_USER_ERROR);
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
