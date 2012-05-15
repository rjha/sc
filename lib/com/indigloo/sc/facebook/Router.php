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
            $this->createRule('^people/(?P<token>[-.\w]+)/(?P<id>\d+)$', 'people');
            $this->createRule('^media/set$', 'media');
            $this->createRule('^media/set/$', 'media');
            $this->createRule('^(?P<token>[-.\w]+)/info$', 'name');
            //php scripts
            $this->createRule('^(?P<name>[\w+]+)\.php$', 'script');
            //keep last - special php scripts 
            $this->createRule('^photo.php$', 'photo');
            $this->createRule('^profile.php$', 'profile');
        }
    }
}
?>
