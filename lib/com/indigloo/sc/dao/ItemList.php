<?php
namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\sc\util\PseudoId ;

    class ItemList {

        function get($loginId) {
            $rows = mysql\ItemList::get($loginId);
            return $rows ;
        }

        function create($loginId,$name,$items) {
            $itemIds = array();
            foreach($items as $item) {
                array_push($itemIds, PseudoId::decode($item)) ;
            }

            $count = mysql\ItemList::create($loginId,$name,$itemIds);
            return $count ;
        }

        function update($listId,$items){
            $itemIds = array();
            foreach($items as $item) {
                array_push($itemIds, PseudoId::decode($item)) ;
            }

            mysql\ItemList::update($listId,$itemIds);
        }

    }
}
?>
