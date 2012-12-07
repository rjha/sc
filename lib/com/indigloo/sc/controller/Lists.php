<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\sc\util\PseudoId as PseudoId ;
    use \com\indigloo\sc\html\Seo as SeoData ;
    use \com\indigloo\ui\Filter as Filter;


    class Lists {

        function process($params,$options) {

            if(is_null($params) || empty($params)){
                $controller = new \com\indigloo\sc\controller\Http400();
                $controller->process();
                exit;
            }

            $plistId = Util::getArrayKey($params,"list_id");
            $listId = PseudoId::decode($plistId);
            $qparams = Url::getRequestQueryParams();

            $gpage = Url::tryQueryParam("gpage");
            $gpage = empty($gpage) ? "1" : $gpage ;

            //@todo input check 
            // people can type all sort of input garbage
            settype($listId,"int");

            $listDao = new \com\indigloo\sc\dao\Lists(); 
            $listDBRow = $listDao->getOnId($listId);
            
            if(empty($listDBRow)) {
                //not found
                $controller = new \com\indigloo\sc\controller\Http404();
                $controller->process();
                exit;
            }

            $listName = $listDBRow["name"];
            $listPubUrl = sprintf("%s/pub/list/%d/%s",Url::base(),$plistId,$listDBRow["seo_name"]);

            //get items from sc_list_item table
            $model = new \com\indigloo\sc\model\Lists();
            $filter = new Filter($model);
            $filter->add($model::LIST_ID,Filter::EQ,$listId);
            
            $pageSize = Config::getInstance()->get_value("user.page.items");

            $filters = array();
            array_push($filters,$filter);
            
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $itemDBRows = $listDao->getPagedItems($paginator,$filters);

            $loginId = $listDBRow["login_id"];
            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);


            $template = APP_WEB_DIR. '/view/list/pub.php';

            //page variables
            $pageBaseUrl = $listPubUrl ;
            $pageTitle = $listDBRow["name"];
            
            $description = Util::abbreviate($listDBRow["description"],160);
            $metaDescription = SeoData::thisOrHomeDescription($description);
            $metaKeywords = SeoData::getHomeMetaKeywords();
            
            include($template);

        }
    }
}
?>
