<?php
namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\sc\util\PseudoId ;

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

        function getTotalOnLoginId($loginId) {
            $row = mysql\Lists::getTotalOnLoginId($loginId);
            return $row["count"] ;
        }

        function getPaged($paginator,$loginId) {

            $limit = $paginator->getPageSize();
            
            if($paginator->isHome()){
                return $this->getLatest($limit,$loginId);
            } else {

                $params = $paginator->getDBParams();
                $start = $params["start"];
                $direction = $params["direction"];
                $rows = mysql\Lists::getPaged($start,$direction,$limit,$loginId);
                return $rows ;
            }
        }

        function getLatest($limit,$loginId) {
            $rows = mysql\Lists::getLatest($limit,$loginId);
            return $rows ;
        }
        
        function getOnId($listId) {
            $row = mysql\Lists::getonId($listId);
            return $row ;
        }

        function exists($listId) {
            return mysql\Lists::exists($listId);
        }

        private function convertItemIds($items) {
            $itemIds = array();
            foreach($items as $item) {
                if(ctype_digit($item)) {
                    array_push($itemIds, PseudoId::decode($item)) ;
                }
            }

            return $itemIds;
        }

        function create($loginId,$name,$items) {
            $itemIds = $this->convertItemIds($items);
            if(empty($itemIds)) {
                $message = "List create received no items!";
                throw new UIException(array($message));
            }

            //md5 hash as hex string and bytes
            $hash = md5($name);
            $bin_hash = md5($name,TRUE); 

            $count = mysql\Lists::create($loginId,$name,$hash,$bin_hash,$itemIds);
            return $count ;
        }

        function addItems($listId,$items){
            $itemIds = $this->convertItemIds($items);
            if(empty($itemIds)) {
                $message = "List create received no items!";
                throw new UIException(array($message));
            }

            mysql\Lists::addItems($listId,$itemIds);
        }

    }
}
?>
