<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\sc\view\Media as MediaView ;
    use com\indigloo\Util as Util ;
    use com\indigloo\Url as Url ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;

    use \com\indigloo\sc\auth\Login as Login ;

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
            $params = array('q' => urlencode(Url::current()));
            $view->passwordUrl = Url::createUrl("/user/account/change-password.php",$params);
            $view->editUrl = Url::createUrl("/user/account/edit.php",$params);

            $html = Template::render($template,$view);
            return $html ;

        }

        static function createUserView($userDBRow) {
            $view = new \stdClass;

            $view->name = $userDBRow['name'];
            $view->createdOn = Util::formatDBTime($userDBRow['created_on']);
            $view->email = $userDBRow['email'];
            $view->aboutMe = $userDBRow['about_me'];
            $view->photoUrl = $userDBRow['photo_url'];
            if(empty($view->photoUrl)) {
                $view->photoUrl = '/css/asset/sc/twitter-icon.png' ;
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
                $view->photoUrl = '/css/asset/sc/twitter-icon.png' ;
            }

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getPublic($userDBRow,$feedDataObj,$total) {

            $html = NULL ;
            $view = new \stdClass;
            $template = '/fragments/user/public.tmpl' ;

            $data = array();

            //what properties are actually set in DB
            $columns = array();
            // labels for properties
            $labels = array('website' => 'Website' ,
                            'blog' => 'Blog' ,
                            'location' => 'Location');

            foreach($labels as $key => $label ) {
                //for label key, the row in DB is set
                if(!empty($userDBRow[$key])) {
                    $value = $userDBRow[$key];
                    //mark column
                    array_push($columns,$key);
                    //push value in data
                    if(strcasecmp($key,'website') == 0 || strcasecmp($key,'blog') == 0 )
                        $data[$key] = '<a href="'.Url::addHttp($value).'" target="_blank">'.$value.'</a>' ;
                    else
                        $data[$key] = $value ;
                }
            }

            $data['name'] = (empty($userDBRow['nick_name'])) ? $userDBRow['name'] : $userDBRow['nick_name'] ;
            $data['about_me'] = $userDBRow['about_me'];
            $data['photo_url'] = $userDBRow['photo_url'];

            if(empty($data['photo_url'])) {
                $data['photo_url'] = '/css/asset/sc/twitter-icon.png' ;
            }

            if($total > 0 ) {
                array_push($columns,"num_posts");
                $data["num_posts"] = $total.' posts &rarr;' ;
                $labels["num_posts"] = "&nbsp;" ;
            }

            $view->createdOn = Util::formatDBTime($userDBRow['created_on']);
            $view->columns = $columns;
            $view->data = $data;
            $view->labels = $labels ;

            //feeds html
            $htmlObj = new \com\indigloo\sc\html\ActivityFeed();
            $feedHtml = $htmlObj->getHtml($feedDataObj);
            $view->feedHtml = empty($feedHtml) ? '' : $feedHtml ;
            $view->total = $total ;

            $view->followingId = $userDBRow["login_id"];
            $encodedId = PseudoId::encode($view->followingId);
            $view->publicUrl = "/pub/user/".$encodedId ;
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

        static function getWidget($row) {
            $row["pubId"] = PseudoId::encode($row["login_id"]) ;
            $format = "%d-%b, %Y / %H:%M" ;
            $row["createdOn"] =  strftime($format, strtotime($row["created_on"]));
            $html = NULL ;
            $template = '/fragments/user/widget.tmpl' ;
            $html = Template::render($template,$row);
            return $html ;
        }

    }

}

?>
