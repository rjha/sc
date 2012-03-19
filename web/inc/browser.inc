<div style="background:#FFF;border-radius:5px;">
    <div style="margin-left:20px;">
    <ul class="nav nav-pills">

     <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            Categories 
            <b class="caret"></b>
          </a>
        <ul class="dropdown-menu">
            <li><a href="/category/1">Arts/Crafts</a></li>
            <li><a href="/category/2">Baby/Kids</a></li>
            <li><a href="/category/3">Clothes/Accessories</a></li>
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
        </ul>
      </li> <!-- location -->
      <li> <a id="nav-group-open" href="">Popular Groups</a> </li>
     </ul>
    </div>
</div>

<div id="nav-group-browser">
    <div id="groups">
        <?php 
           $groupDao = new \com\indigloo\sc\dao\Group();
           $feature_slug = $groupDao->getFeatureSlug();
           $fgroups = $groupDao->slugToGroups($feature_slug);
           $limit = 0 ;
           $lgroups = array();

           if(sizeof($fgroups) < 50 ) {
               $limit = (50 - sizeof($fgroups)) + 10 ;
               $lgroups = $groupDao->getLatest($limit);
           }

            $navGroups = array_merge($fgroups,$lgroups);
            echo \com\indigloo\sc\html\Group::getCloud($navGroups); 
        ?>
    </div>
    <div class="panel-footer">
        <div class="floatr">
            <a href="/group/cloud/site.php">See More Groups&rarr;</a>
            &nbsp;|&nbsp;
            <a id="nav-group-close" href="">Close&nbsp;<i class="icon-remove"></i></a>
        </div>

    </div>

</div>
