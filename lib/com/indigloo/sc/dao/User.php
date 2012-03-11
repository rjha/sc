<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
     
    class User {

		function getOnId($userId) {
			$row = mysql\User::getOnId($userId);
			return $row ;
		}

		function getOnLoginId($loginId) {
			//figure out the provider 
			$loginDao = new \com\indigloo\sc\dao\Login();
			$loginRow = $loginDao->getonId($loginId);

			$provider = $loginRow['provider'];
			$row = NULL ;

			switch($provider) {
				case \com\indigloo\sc\auth\Login::MIK :
					$row = mysql\User::getOnLoginId($loginId);
					//@todo - get rid of kludge
					$row["name"] = $row["user_name"];
					break;
				case \com\indigloo\sc\auth\Login::FACEBOOK :
					$row = mysql\Facebook::getOnLoginId($loginId);
					break;
				case \com\indigloo\sc\auth\Login::TWITTER :
					$row = mysql\Twitter::getOnLoginId($loginId);
					break;
				default:
					trigger_error("Unknown user provider",E_USER_ERROR);
			}

			//Add provider information
			if(!empty($row)){
				$row['provider'] = $provider ;
			}

			return $row ;
		}
		
        function update($userId,$firstName,$lastName) {
            $code = mysql\User::update($userId,$firstName,$lastName);
            return $code ;
        }

		function addFeedback($feedback) {
            $code = mysql\User::addFeedback($feedback);
            return $code ;
        }

		function getGroups($loginId) {

            $groups = array();
            $rows = mysql\User::getGroups($loginId);
            foreach($rows as $row) {
                array_push($groups,$row['token']);
            }
            
            return $groups ;
        }
		
        
    }

}
?>
