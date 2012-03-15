
<div class="hero-unit">
    <h1>3mik - share / discover</h1>
</div>
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
    <div class="groups">
    <?php 
       $groupDao = new \com\indigloo\sc\dao\Group();
        $navGroups = $groupDao->getLatest(50);
  
        if(isset($navGroups) && !empty($navGroups)) {
            foreach($navGroups as $group) {
                echo \com\indigloo\sc\html\Group::getLink($group); 
            }
        }
    ?>

    </div>
    <div class="footer">
        <div class="floatr">
            <a id="nav-group-close" href="">Close</a>
        </div>

    </div>

</div>
