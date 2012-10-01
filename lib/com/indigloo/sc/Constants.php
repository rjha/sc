<?php
    namespace com\indigloo\sc {

        class Constants {

            //Post Actions
            const FEATURE_POST = 1 ;
            const UNFEATURE_POST = 2 ;

            //Activity feed verbs
            const LIKE_VERB = 1 ;
            const SAVE_VERB = 2 ;
            const FOLLOWING_VERB = 3 ;
            const COMMENT_VERB = 4 ;
            const POST_VERB = 5 ;

            //Activity feed types
            const FOLLOW_FEED = "feed:follow" ;
            const BOOKMARK_FEED = "feed:bookmark" ;
            const POST_FEED = "feed:post" ;
            const COMMENT_FEED = "feed:comment" ;

            //@see http://in2.php.net/strftime
            const TIME_MDYHM = "%b %e %Y, %R" ;
            const TIME_MDY = "%b %e, %Y" ;

            //system and UI collections
            const SET_FEATURED_POST = "set:sys:fposts" ;
            const SET_FEATURED_GROUP = "set:sys:fgroup" ;
            const SET_WEEK_NEWS = "set:sys:wnews" ;
            const UI_ZSET_CATEGORY = "ui:zset:category";

            //set smember sources
            const MEMBER_ITEM = "item" ;
            const MEMBER_GROUP = "group";

            //DB Error codes
            const DUPKEY_ERROR_CODE = 1062 ;

        }
}
?>
