<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\sc\mysql as mysql;

    class Facebook {
		function getOrCreate($id,$name,$firstName,$lastName,$link,$gender,$email) {
			$loginId = NULL ;

			//is existing record?
            $id = trim($id);
			$row = $this->getOnFacebookId($id); 

			if(empty($row)){
                $message = sprintf("Login:Facebook:create :: id %s, email %s \n",$id,$email);
                Logger::getInstance()->info($message);

				//create login 
				$loginDao = new \com\indigloo\sc\dao\Login();
				$data = $loginDao->create(\com\indigloo\sc\auth\Login::FACEBOOK,$name);
				$loginId = $data['lastInsertId'];
				//create facebook user
				$this->create($id,$name,$firstName,$lastName,$link,$gender,$email,$loginId); 
			} else {
				//found
				$loginId = $row['login_id'];
			}

			return $loginId ;

		}

		function getOnFacebookId($facebookId) {
			$row = mysql\Facebook::getOnFacebookId($facebookId);
			return $row ;
		}

		function create($facebookId,$name,$firstName,$lastName,$link,$gender,$email,$loginId){  
			mysql\Facebook::create($facebookId,$name,$firstName,$lastName,$link,$gender,$email,$loginId); 
		}


	}
}

?>
