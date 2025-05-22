<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<!-- Brand Logo -->
	<a href="{{ route('dashboard') }}" class="brand-link">
		<img src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: 0.8" />
		<span class="brand-text font-weight-light">Hotel Sandika</span>
	</a>

	<!-- Sidebar -->
	<div class="sidebar">
		<!-- Sidebar user panel (optional) -->
		<div class="user-panel mt-3 pb-3 mb-3 d-flex">
			<div class="image">
				<img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image" />
			</div>
			<div class="info">
				<a href="#" class="d-block">{{ auth()->user()->name }}</a>
			</div>
		</div>
		<!-- Sidebar Menu -->
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
				<li class="nav-item">
					<a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
						<i class="nav-icon fas fa-hotel"></i> <p>Dashboard</p>
					</a>
				</li>

				@if (auth()->user()->role == "administrator")
					<li class="nav-header">Master</li>
					<li class="nav-item">
						<a href="{{ route('floor.index') }}" class="nav-link {{ request()->routeIs('floor.*') ? 'active' : '' }}">
							<i class="fas fa-dumbbell nav-icon"></i> <p>Lantai</p>
						</a>
					</li>
				@endif
			</ul>
		</nav>
		<!-- /.sidebar-menu -->
	</div>
	<!-- /.sidebar -->
</aside>
