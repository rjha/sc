<div style="background:#FFF;border-radius:5px;">
    <div style="margin-left:20px;">
    <ul class="nav nav-pills">
      <li><a href="/surprise/me">surprise me!</a></li>
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
        </ul>
      </li> <!-- location -->
      <li> <a id="nav-group-open" href="">Browse Groups</a> </li>
     </ul>
    </div>
</div>

<div id="nav-group-browser">
    <div id="groups">
        <div class="cloud">
        <?php 
           $groupDao = new \com\indigloo\sc\dao\Group();
            $navGroups = $groupDao->getLatest(50);
      
            if(isset($navGroups) && !empty($navGroups)) {
                foreach($navGroups as $group) {
                    $num = rand(1,3000);
                    $style = 1 ;
                    if($num > 1000 ) { $style = 2 ; }
                    if($num > 2000 ) { $style = 3 ; }
                    echo \com\indigloo\sc\html\Group::getCloudLink($group,$style); 
                }
            }
        ?>
        </div>

    </div>
    <div class="panel-footer">
        <div class="floatr">
            <a id="nav-group-close" href="">Close&nbsp;<i class="icon-remove"></i></a>
        </div>

    </div>

</div>
