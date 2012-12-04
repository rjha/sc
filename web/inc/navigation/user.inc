<div class="row">
    <div class="span8 offset1">
        <div id="top-unit">
            <div class="floatl">
                <h5 class="faded-text">3mik is the place to discover shops and products in India</h5>
            </div>
            <div class="floatr">
                <a id="nav-popup-share" class="btn b">Upload</a>
                
            </div>
            <div class="clear"> </div>
        </div>
    </div>

</div>



<div class="row">
    <div class="span12">
        <div id="site-navigator">

            <ul class="nav nav-pills">
                <li ><span class="label label-info">Explore</span></li>
                <li><a href="/pub/popular">most popular</a></li>
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

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        City
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/search/location/bangalore">Bangalore</a></li>
                        <li><a href="/search/location/chennai">Chennai</a></li>
                        <li><a href="/search/location/delhi">Delhi</a></li>
                        <li><a href="/search/location/hyderabad">Hyderabad</a></li>
                        <li><a href="/search/location/kolkata">Kolkata</a></li>
                        <li><a href="/search/location/mumbai">Mumbai</a></li>
                        <li><a href="/search/location/pune">Pune</a></li>
                    </ul>
                </li> <!-- location -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        what's new?
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/recent">New items</a></li>
                        <li><a href="/groups/recent">New Groups</a></li>
                        <li><a id="nav-popup-group" href="">Featured Groups</a></li>

                    </ul>
                </li> <!-- whats new -->

            </ul>
        </div> <!-- site:navigator -->
     </div> 

</div>
