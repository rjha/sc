<div id="header">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<div class="pull-left">
					<a class="brand" href="/">3mik.com	<span class="beta">BETA</span> </a>
				</div>
				<div class="pull-left">
					<ul class="nav">
						<li class="active"> <a href="/">Home</a> </li>
						<li class="active"><a href="/share/new.php">Share</a></li>
						<li class="active"><a href="/qa/new.php">Ask</a></li>
						<li class="divider-vertical"></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $gSessionLogin->name; ?><b class="caret"></b> </a>
							<ul class="dropdown-menu">
								<li><a href="/user/dashboard.php">My Account</a></li>
								<li class="divider"></li>
								<li><a href="/user/logout.php">Logout</a></li>
							</ul>
						</li> <!-- dropdown -->
					</ul> <!-- links -->
				</div>
				<div class="pull-left">	
					<form method="GET" action="/search/site">
						<input id="site-search" name="gt" type="text" class="search-query" placeholder="Search">
					</form>
				</div>

		  </div>
		</div>
	</div> <!-- navbar -->
</div> <!-- header -->
