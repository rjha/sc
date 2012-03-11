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
            $this->createRule('^search/site$','com\indigloo\sc\controller\Search');
            $this->createRule('^group/(?P<name>[-\w]+)$','com\indigloo\sc\controller\Group');
            $this->createRule('^pub/user/(?P<login_id>\d+)$','com\indigloo\sc\controller\User');
            $this->createRule('^search/location/(?P<location>\w+)$','com\indigloo\sc\controller\Location');
            $this->createRule('^surprise/me$','com\indigloo\sc\controller\Random');
        }
    }
}
?>
