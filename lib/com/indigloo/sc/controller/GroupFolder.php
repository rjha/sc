<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\html\Seo as SeoData ;
    use \com\indigloo\ui\Filter as Filter;
    use \com\indigloo\ui\Pagination as Pagination;
  
    
    class GroupFolder {

        function __construct() {

        }

        function process($params,$options) {
            $qparams = Url::getRequestQueryParams();
            $filters = array();

            $groupDao = new \com\indigloo\sc\dao\Group();
            $total = $groupDao->getTotalCount($filters);

            $pageSize = 100;
            $paginator = new Pagination($qparams,$total,$pageSize); 
            $groups = $groupDao->getPaged($paginator,$filters);

            $startId = NULL ;
            $endId = NULL ;

            if(sizeof($groups) > 0 ) {
                $startId = $groups[0]['id'] ;
                $endId =   $groups[sizeof($groups)-1]['id'] ;
            }

            $pageBaseUrl = $options["path"];
            $title = "Recent groups";

            $file = APP_WEB_DIR. '/view/group/folder.php' ;
            include ($file);

        }

    }
}
?>
