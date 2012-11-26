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
            const UNFOLLOWING_VERB = 6 ;

            const STR_LIKE = "like" ;
            const STR_SAVE = "save" ;
            const STR_FOLLOW = "follow" ;
            const STR_UNFOLLOW = "unfollow";
            const STR_COMMENT = "comment";
            const STR_POST = "post" ;

            const REDIS_MAGIC_COOKIE = "__4D41474943_B00B5__";

            //Activity feed types
            const FOLLOW_FEED = "feed:follow" ;
            const BOOKMARK_FEED = "feed:bookmark" ;
            const POST_FEED = "feed:post" ;
            const COMMENT_FEED = "feed:comment" ;
            
            //DB Error codes
            const DUPKEY_ERROR_CODE = 1062 ;

            //mail types
            const RESET_PASSWORD_MAIL = 1 ;
            const NEW_ACCOUNT_MAIL = 2 ;

        }
}
?>
