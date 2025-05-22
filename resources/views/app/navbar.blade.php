<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
	<!-- Left navbar links -->
	<ul class="navbar-nav">
		<li class="nav-item">
			<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
		</li>
		<li class="nav-item">
			<marquee class="nav-link">Sistem Manajemen Hotel Sandika Jl. Bandung No. 40</marquee>
		</li>
	</ul>

	<!-- Right navbar links -->
	<ul class="navbar-nav ml-auto">
		<!-- Settings Dropdown Menu -->
		<li class="nav-item dropdown">
			<a class="nav-link" data-toggle="dropdown" href="#">
				<i class="fas fa-users-cog"></i>
			</a>
			<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
				<div class="dropdown-divider"></div>
				<a href="#" class="dropdown-item">
					<i class="fas fa-user mr-2"></i> Profil
				</a>
				<div class="dropdown-divider"></div>
				<form action="{{ route('logout') }}" method="post">
					@csrf
					<button type="submit" class="btn btn-link dropdown-item" style="color: black;" ><i class="fas fa-sign-out-alt mr-2"></i> Logout</button> 
				</form>
			</div>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-widget="fullscreen" href="#" role="button">
			<i class="fas fa-expand-arrows-alt"></i>
			</a>
		</li>
		<!-- <li class="nav-item">
			<a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
			<i class="fas fa-th-large"></i>
			</a>
		</li> -->
	</ul>
</nav>
<!-- /.navbar -->