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

    <div class="span5">
        <div class="row">
            <div class="span3"> 
                <div class="floatr">
                 <b> connect &nbsp;</b> <a href="http://www.facebook.com/pages/3mik/332362053467901" target="_blank"><img src="/css/images/fb_logo.png" width="24" height="24" /></a>

                <a href="https://twitter.com/#!/3mikindia" target="_blank"><img src="/css/images/twitter-bird-white-on-blue.png" width="24" height="24" /></a>

                </div>
            </div>
            <div class="span1">
                <!-- Place this tag where you want the +1 button to render -->
                <g:plusone annotation="none"></g:plusone>
            </div>

        </div> <!-- social toolbar row -->

    </div> <!-- span5 -->

</div>

