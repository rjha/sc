<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
  
	
    class Random {
        
        function process($params,$options) {
            
			$questionDao = new \com\indigloo\sc\dao\Question();
			$total = $questionDao->getTotalCount();

            $rows1 = $questionDao->getRandom(25);

            $ids = array();
            for($i = 1; $i <= 25;$i++) {
                $ids[] = mt_rand(1,$total-1);
            }

            $rows2 = $questionDao->getOnSearchIds($ids);
            $questionDBRows = array_merge($rows1,$rows2);
            
            $pageHeader = "Random picks";
            $file = $_SERVER['APP_WEB_DIR']. '/view/tiles.php' ;
            include ($file);
		
        }


    }
}
?>
