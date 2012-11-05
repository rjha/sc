<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\html\Seo as SeoData ;

    use \com\indigloo\ui\Filter as Filter;
    use \com\indigloo\ui\Pagination as Pagination;
    use \com\indigloo\sc\util\Nest as Nest ;
    
    class GroupsMap {

        function __construct() {

        }

        function process($params,$options) {
            
            $path = $options["path"];

            if(!empty($path) && (strlen($path) > 8)) {
                $name = substr($path,8);
                if(strcmp($name,"featured") == 0 ) {
                    $this->spewFeatured($params,$options);
                } else {
                    $this->spewRecent($params,$options);
                }
            }

        }


        function spewRecent($params,$options) {

            $qparams = Url::getRequestQueryParams();
            $filters = array();

            $groupDao = new \com\indigloo\sc\dao\Group();
            $pageSize = 100;
            $paginator = new Pagination($qparams,$pageSize); 
            $groups = $groupDao->getPaged($paginator,$filters);

            $startId = NULL ;
            $endId = NULL ;

            if(sizeof($groups) > 0 ) {
                $startId = $groups[0]["id"] ;
                $endId =   $groups[sizeof($groups)-1]["id"] ;
            }

            $pageBaseUrl = $options["path"];
            $title = "Recent groups";

            $file = APP_WEB_DIR. "/view/group/cards-page.php" ;
            include ($file);

        }

        function spewFeatured($params,$options) {
            
          
            $collectionDao = new \com\indigloo\sc\dao\Collection();
            $groupDao = new \com\indigloo\sc\dao\Group();
            $row = $collectionDao->glget(Nest::fgroups());
            $feature_slug = empty($row) ? "" : $row["t_value"] ;

            $groups = $groupDao->slugToGroupsMap($feature_slug);
            $title = "Featured groups";
            $file = APP_WEB_DIR. "/view/group/cards.php" ;
            include ($file);
        }

    }
}
?>
