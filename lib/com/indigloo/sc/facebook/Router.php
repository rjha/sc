<?php
namespace com\indigloo\sc\facebook{

    
    class Router extends \com\indigloo\core\Router{
        
        function __construct() {
            
        }

        function __destruct() {
        
        }
        
        function initTable() {
            $this->createRule('^/$', 'home');
            $this->createRule('^(?P<token>[-.\w]+)$', 'name');
            $this->createRule('^pages/(?P<token>[-.\w]+)$', 'name');
            $this->createRule('^pages/(?P<token>[-.\w]+)/(?P<id>\d+)$', 'page');
            $this->createRule('^media/set$', 'media');
            $this->createRule('^media/set/$', 'media');
            //keep last - photo.php conflicts with ^/<token>$ path pattern
            $this->createRule('^photo.php$', 'photo');
        }
    }
}
?>
