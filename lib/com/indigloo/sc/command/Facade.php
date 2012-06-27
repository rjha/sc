<?php

namespace com\indigloo\sc\command {

    use \com\indigloo\sc\Logger as Logger ;

    class Facade {
        private $map ;

        function __construct() {
            $this->map = array(
                md5("/qa/ajax/bookmark.php") => "BOOKMARK",
                md5("/qa/ajax/social-graph.php") => "GRAPH" );
        }

        function execute($endPoint, $params) {
            $command = NULL ;
            $hash = md5($endPoint);
            if(!isset($this->map[$hash])) {
                // end point is not mapped
                $message = sprintf("end point [%s] is not mapped for session action",$endPoint);
                Logger::getInstance()->info($message);
                return ;
            }

            //end point is mapped.
            $name = $this->map[$hash];
            switch($name) {
                case "BOOKMARK" :
                    $command = new \com\indigloo\sc\command\Bookmark();
                    break ;
                case "GRAPH" :
                    $command = new \com\indigloo\sc\command\SocialGraph();
                    break ;
                default:
                    //coding error.
                    $message = sprintf("Unknown session action  [%s] for endpoint [%s]",$name,$endPoint);
                    trigger_error($message, E_USER_ERROR);
            }

            $response = $command->execute($params);
            return $response ;

        }
    }
}

?>
