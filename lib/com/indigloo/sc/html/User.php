<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\sc\view\Media as MediaView ;
    use \com\indigloo\Util as Util ;
    
    use \com\indigloo\Url as Url ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;
    use \com\indigloo\sc\auth\Login as Login ;

    use \com\indigloo\sc\util\Formatter as Formatter ;
    use \com\indigloo\sc\ui\Constants as UIConstants ;

    class User {

        /**
         *
         * @param gSessionLogin $login data stored in session
         * @param userDBRow - DB row for this login_id from sc_denorm_user table
         *
         */
        static function getProfile($gSessionLogin,$userDBRow) {
            if(is_null($gSessionLogin)) {
                return '' ;
            }

            if(!is_null($gSessionLogin) && ($gSessionLogin->id != $userDBRow['login_id'] )) {
                return '' ;

            }

            $html = NULL ;
            $template = '/fragments/user/profile/private.tmpl' ;

            $view = self::createUserView($userDBRow);
            
            $view->passwordUrl = "/user/account/change-password.php" ;
            $view->editUrl = "/user/account/edit.php" ;

            $html = Template::render($template,$view);
            return $html ;

        }

        static function createUserView($userDBRow) {
            $view = new \stdClass;

            $view->name = (empty($userDBRow['nick_name'])) ? $userDBRow['name'] : $userDBRow['nick_name'] ;
            $view->createdOn = Formatter::convertDBTime($userDBRow['created_on']);
            $view->email = $userDBRow['email'];
            $view->aboutMe = $userDBRow['about_me'];
            $view->photoUrl = $userDBRow['photo_url'];
            if(empty($view->photoUrl)) {
                $view->photoUrl =  UIConstants::PH2_PIC ;
            }

            $loginId = $userDBRow['login_id'];
            $encodedId = PseudoId::encode($loginId);
            $view->publicUrl = "/pub/user/".$encodedId ;

            $view->website = $userDBRow['website'];
            $view->blog = $userDBRow['blog'];
            $view->location = $userDBRow['location'];
            $view->nickName = $userDBRow['nick_name'];
            $view->age = $userDBRow['age'];
            $view->aboutMe = $userDBRow['about_me'];

            return $view ;
        }

        static function getPhoto($name,$photoUrl) {
            $html = NULL ;
            $template = '/fragments/user/profile/photo.tmpl' ;

            $view = new \stdClass;
            $view->name = $name;
            $view->photoUrl = $photoUrl;
            if(empty($view->photoUrl)) {
                $view->photoUrl = UIConstants::PH2_PIC ;
            }

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getGrid($rows) {
            $html = NULL ;
            $view = new \stdClass;
            $view->users = array();

            $template = '/fragments/user/grid/small.tmpl' ;

            foreach($rows as $row){
                $user = self::createUserView($row);
                array_push($view->users,$user);
            }

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getPublic($userDBRow) {

            $html = NULL ;
            $view = new \stdClass;
            $template = '/fragments/user/public.tmpl' ;

            $view = self::createUserView($userDBRow);
            $view->followingId = $userDBRow["login_id"];
              
            //userId in session is follower
            $loginId = Login::tryLoginIdInSession();
            $view->followerId = (empty($loginId)) ? "{loginId}" : $loginId ;

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getTable($rows) {
            //Add user profile to rows
            for($i = 0 ; $i < count($rows); $i++) {
                $rows[$i]["pubUrl"]  = "/pub/user/".PseudoId::encode($rows[$i]["login_id"]);
            }

            $html = NULL ;
            $template = '/fragments/user/table.tmpl' ;
            $view = new \stdClass;
            $view->rows = $rows ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getAdminWidget($row) {
            $view = new \stdClass ;
            
            //db fields
            $view->id = $row["id"];
            $view->loginId = $row["login_id"];
            $view->provider = $row["provider"];
            $view->email = $row["email"];
            $view->website = $row["website"];
            $view->name = $row["name"];
            $view->location = $row["location"];

            //display fields
            $view->pubId = PseudoId::encode($row["login_id"]) ;
            $view->createdOn =  Formatter::convertDBTime($row["created_on"]);
            $ts = Util::secondsInDBTimeFromNow($row["created_on"]);
            
            $span = 24*3600 ;
            $view->last24hr = ($ts < $span) ? true : false ;

            $view->ban = ($row["bu_bit"] == 0 ) ? true : false ;
            $view->unban = ($row["bu_bit"] == 1 ) ? true : false ;
            $view->taint = ($row["tu_bit"] == 0 ) ? true : false ;

            $html = NULL ;
            $template = "/fragments/user/admin/widget.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getCounters($data) {
            $view = new \stdClass ;
            $zero = '-:-' ;

            $view->posts =   $data["post_count"];
            $view->comments =  $data["comment_count"];
            $view->lists = $data["list_count"];
            $view->likes =  $data["like_count"];
            $view->followers = $data["follower_count"];
            $view->following = $data["following_count"];

            $variables = get_object_vars($view);

            foreach($variables as $prop => $value) {
                if(intval($view->{$prop})  ==  0 ) {
                    $view->{$prop} = $zero ;
                }
            }

            $html = NULL ;
            $template = '/fragments/user/counter/private.tmpl' ;
            $html = Template::render($template,$view);
            return $html ;
            
        }

    }

}

?>
