<?php
    namespace com\indigloo\sc {

        class Constants {

            //Post Actions
            const FEATURE_POST = 1 ;
            const UNFEATURE_POST = 2 ;

            //Activity feed verbs
            const LIKE_VERB = 1 ;
            const FAVORITE_VERB = 2 ;
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

        }
}
?>
