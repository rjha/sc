<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');

    use \com\indigloo\Url as Url ;
    use \com\indigloo\ui\Pagination as Pagination;

    $ft  = Url::tryQueryParam("ft");
    if(empty($ft)) {
        $ft = 'a' ;
    }

    $groupDao = new \com\indigloo\sc\dao\Group();
    $filter = array($groupDao::TOKEN_COLUMN => $ft);

    $total = $groupDao->getTotalCount($filter);
    
    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    $pageSize =	50;
    $paginator = new Pagination($qparams,$total,$pageSize);	
    $groups = $groupDao->getPaged($paginator,$filter);

    $startId = NULL ;
    $endId = NULL ;

    if(sizeof($groups) > 0 ) {
        $startId = $groups[0]['id'] ;
        $endId =   $groups[sizeof($groups)-1]['id'] ;
    }

    $pageBaseUrl = "/group/alpha.php" ;
    include($_SERVER['APP_WEB_DIR'].'/group/inc/body.inc');

?>
            <div class="btn-toolbar">
                <div class="btn-group">
                    <button class="btn"><a href="/group/alpha.php?ft=a">A</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=b">B</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=c">C</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=d">D</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=e">E</a></button>
                </div>
               <div class="btn-group">
                    <button class="btn"><a href="/group/alpha.php?ft=f">F</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=g">G</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=h">H</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=i">I</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=j">J</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=k">K</a></button>
                </div>
                <div class="btn-group">
                    <button class="btn"><a href="/group/alpha.php?ft=l">L</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=m">M</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=n">N</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=o">O</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=p">P</a></button>
                </div>

                <div class="btn-group">
                    <button class="btn"><a href="/group/alpha.php?ft=q">Q</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=r">R</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=s">S</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=t">T</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=u">U</a></button>
                </div>
                
                 <div class="btn-group">
                    <button class="btn"><a href="/group/alpha.php?ft=v">V</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=w">W</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=x">X</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=y">Y</a></button>
                    <button class="btn"><a href="/group/alpha.php?ft=z">Z</a></button>
                </div>
 
            </div> 

           </div> <!-- container -->
            <hr>
            <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>

        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



