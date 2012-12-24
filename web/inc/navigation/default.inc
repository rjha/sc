
<div class="row">
    <div class="span8 offset1">
        <div id="top-unit">
            <div class="floatl">
                <h5 class="faded-text">3mik is the place to discover shops and products in India</h5>
            </div>
            <div class="floatr">
                <a id="nav-popup-share" class="btn b">Upload</a>
                &nbsp;&nbsp;
                <a id="nav-popup-join" class="btn btn-red b">Join 3mik</a>
                
            </div>
            <div class="clear"> </div>
        </div>
    </div>

</div>


<div id="site-navigator">
     <div class="row">
        <div class="span12">

            <ul class="nav nav-pills">
                
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        Category
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">

                        <li>
                           <div id="submenu-col2"> 
                                <ul>
                                <?php
                                    $collectionDao = new \com\indigloo\sc\dao\Collection();
                                    $map = $collectionDao->uizmembersAsMap(\com\indigloo\sc\util\Nest::ui_category());
                                    $count = sizeof($map);
                                    $index = 1 ;

                                    foreach($map as $item) {
                                        $index++ ;
                                        if($index > ($count/2)) {
                                            echo "</ul> <ul>";
                                        }
                                        
                                        $strItem = sprintf("<li><a href=\"/category/%s\">%s</a></li>",$item['id'],$item['name']);
                                        echo $strItem ;
                                    }
                                ?>

                                </ul>
                            </div> <!-- 2-col submenu -->
                        </li>

                    </ul>
                </li> <!-- category -->
                <li><a id="nav-popup-group" href="">Featured Groups</a></li>
                
               
            </ul>
        </div>
     </div> <!-- row -->

</div>
