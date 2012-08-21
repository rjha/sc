<div id="top-unit">
    <div class="row">
        <div class="span3 offset1">
            <h3> Share and Discover with 3mik </h3>
        </div>
        <div class="span6">
            <a id="nav-open-share" class="btn">Add+&nbsp;/&nbsp;Upload</a>
            &nbsp;&nbsp;
            <a class="btn" href="/user/register.php">Register</a>
            &nbsp;&nbsp;
            <a class="btn" href="/user/login.php">Login&nbsp;&raquo;</a>
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
                        <?php
                            $categoryDao = new \com\indigloo\sc\dao\Category();
                            $map = $categoryDao->getIdNameMap();
                            foreach($map as $item) {
                                $strItem = sprintf("<li><a href=\"/category/%s\">%s</a></li>",$item['id'],$item['name']);
                                echo $strItem ;
                            }
                        ?>
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
                        <li><a id="nav-open-group" href="">New Groups</a></li>

                    </ul>
                </li> <!-- whats new -->

                <li><a href="/surprise/me">Surprise me!</a></li>
                <li><a href="/editor/picks">Editor's pick</a></li>
                <li>  </li>
            </ul>
        </div>
     </div> <!-- row -->

</div>
