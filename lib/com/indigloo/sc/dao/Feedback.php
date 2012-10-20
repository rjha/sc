<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    
    class Feedback {

        function getPaged($paginator) {
 
            $limit = $paginator->getPageSize();

            if($paginator->isHome()){
                return $this->getLatest($limit);
                
            } else {
                $params = $paginator->getDBParams();
                $start = $params['start'];
                $direction = $params['direction'];

                $rows = mysql\Feedback::getPaged($start,$direction,$limit);
                return $rows ;
            }
        }
        
        function getTotalCount() {
            $row = mysql\Feedback::getTotalCount();
            return $row['count'] ;
        }


        function getLatest($limit) {
            $rows = mysql\Feedback::getLatest($limit);
            return $rows ;
        }
        
        function add($name,$email,$phone,$comment) {
            mysql\Feedback::add($name,$email,$phone,$comment);
        }

        function delete($id) {
            mysql\Feedback::delete($id);
        }

        
    }

}
?>
