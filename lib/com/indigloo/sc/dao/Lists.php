<?php
namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\sc\util\PseudoId ;

    /**
     * According to our naming convention of using singulars : This class
     * should have been called "List" instead of "Lists"
     *
     * Now you are introduced to half-baked namespace support in PHP5.3
     * List is a reserved keyword in PHP. ideally namespace should have taken care 
     * a class named _List_ here but the PHP parser will always interpret *List* as 
     * a reserved word even inside custom namespaces! what a bummer!
     *
     */

    class Lists {

        function get($loginId) {
            $rows = mysql\Lists::get($loginId);
            return $rows ;
        }

        function create($loginId,$name,$items) {
            $itemIds = array();

            //md5 hash as hex string and bytes
            $hash = md5($name);
            $bin_hash = md5($name,TRUE); 

            foreach($items as $item) {
                array_push($itemIds, PseudoId::decode($item)) ;
            }

            $count = mysql\Lists::create($loginId,$name,$hash,$bin_hash,$itemIds);
            return $count ;
        }

        function update($listId,$items){
            $itemIds = array();
            foreach($items as $item) {
                array_push($itemIds, PseudoId::decode($item)) ;
            }

            mysql\Lists::update($listId,$itemIds);
        }

    }
}
?>
