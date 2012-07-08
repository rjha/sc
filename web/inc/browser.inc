<div class="row">
    <div class="span7">
        <div style="background:#FFF;border-radius:5px;">
            <div style="margin-left:20px;">
            <ul class="nav nav-pills">

             <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    Categories
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
                    Location
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
              <li> <a id="nav-group-open" href="">Popular Groups</a> </li>
              <li> <a href="/site/contact.php">Contact</a> </li>
             </ul>
            </div>
        </div>

        <div id="block-spinner"> </div>
        <div id="simple-popup">
            <div id="content"> </div>
            <div class="panel-footer">
                <div class="floatr">press Esc or&nbsp;<a id="simple-popup-close" href="">close&nbsp;<i class="icon-remove"></i></a> </div>
            </div>
        </div> <!-- simple popup -->
    </div> <!-- span7 -->

    <div class="span4">
        <div style="float: right; padding-bottom: 15px;">
            <span style="font-size: 16px; padding-right: 5px;">&nbsp;</span>
            <a href="http://twitter.com/3mikindia"><img src="/css/images/ico_twitter.png" width="32" height="32" style="vertical-align: middle; padding-left: 2px;" alt="Twitter"></a>
            <a href="http://www.facebook.com/3mikindia"><img src="/css/images/ico_facebook.png" width="32" height="32" style="vertical-align: middle; padding-left: 2px;" alt="Facebook"></a>
            <a href="http://plus.google.com/108668124722035129135"><img src="/css/images/ico_googleplus.png" width="32" height="32" style="vertical-align: middle; padding-left: 2px;" alt="Google+"></a>
      </div>

    </div> <!-- span4 -->
    <div class="span1"> </div>

