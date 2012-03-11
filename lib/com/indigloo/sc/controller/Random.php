<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
  
	
    class Random {
        
        function process($params,$options) {
            
            //following variables will be visible in $file as well
			$questionDao = new \com\indigloo\sc\dao\Question();
			$total = $questionDao->getTotalCount();

            //lets mix it a little

            $indexes = array();

            $random = mt_rand(1,$total/3);
            array_push($indexes,$random);

            $random = mt_rand($total/3,2*$total/3);
            array_push($indexes,$random);

            $random = mt_rand(2*total/3,$total-1);
            array_push($indexes,$random);

            $questionDBRows = array();

            foreach($indexes as $index){
                $rows = $questionDao->getRandom($index,20);
                $questionDBRows = array_merge($rows,$questionDBRows);
            }

            //shuffle
            shuffle($questionDBRows);

            $file = $_SERVER['APP_WEB_DIR']. '/search/results.php' ;
            include ($file);
		
        }
    }
}
?>
