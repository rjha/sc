<?php
namespace com\indigloo\sc\router{

    
    class Router extends \com\indigloo\core\Router{
        
        function __construct() {
            
        }

        function __destruct() {
        
        }
        
        function initTable() {
            $this->createRule('^/$', 'com\indigloo\sc\controller\Home');
            $this->createRule('^item/(?P<item_id>\d+)$','com\indigloo\sc\controller\Post');
            //rule for item folder. @imp do not use /item/ on our site
            $this->createRule('^recent$','com\indigloo\sc\controller\ItemFolder');
            $this->createRule('^category/(?P<category_id>\d+)$','com\indigloo\sc\controller\Category');
            $this->createRule('^search/site$','com\indigloo\sc\controller\Search');
            //rule for group folder. @imp do not use /group/ on our site
            $this->createRule('^groups/recent$','com\indigloo\sc\controller\GroupFolder');
            $this->createRule('^group/(?P<name>[-\w]+)$','com\indigloo\sc\controller\Group');
            $this->createRule('^pub/user/(?P<login_id>\d+)$','com\indigloo\sc\controller\User');
            $this->createRule('^search/location/(?P<location>\w+)$','com\indigloo\sc\controller\Location');
            $this->createRule('^surprise/me$','com\indigloo\sc\controller\Random');
            $this->createRule('^editor/picks$','com\indigloo\sc\controller\Editor');
            $this->createRule('^site/(?P<site_id>\d+)$','com\indigloo\sc\controller\Site');
        }
    }
}
?>
