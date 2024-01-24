<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="padding: 2px 1rem;">
    <a class="navbar-brand mr-4" href="javascript:void(0)" data-target="#toggle-sidebar"><i class="fa fa-fw fa-bars"></i></a>
    
	<a class="navbar-brand font-weight-boldA" href="<?php echo base_url(); ?>">
		<img src="<?php echo base_url('uploads/site/logo.png?v='.time()); ?>" style="height: 38px;" class="mr-1">
		<span class="small"><?php echo app('title'); ?></span>
	</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#Navbar">
        <i class="fa fa-fw fa-user"></i>
    </button>

    <div class="collapse navbar-collapse" id="Navbar">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item dropdown" data-title="Branches" data-intro="You can switch the system of different branches here">
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown">
                    <i class="fa fa-fw fa-building"></i> <?php echo branch_now('title'); ?>
                </a>
                <div class="dropdown-menu dropdown-menu-left">
                    <h6 class="dropdown-header">RECENT</h6>
					<?php 
					
                    foreach(my_branches() as $e) { 

						if( datalist_Table('tbl_branches', 'active', $e['branch']) == 1 && datalist_Table('tbl_branches', 'is_delete', $e['branch']) == 0 ) {
							
							?><a class="dropdown-item" href="<?php echo base_url('home/branch_access/'.$e['branch']); ?>"><i class="fa fa-fw fa-building mr-1"></i> <?php echo datalist_Table('tbl_branches', 'title', $e['branch']); ?></a><?php 
                    
						}

                    } 
                    ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-primary text-center" style="background-color: inherit !important" href="<?php echo base_url('branches_new/list'); ?>">All Branches</a>
                </div>
            </li>
            
        </ul>
		<ul class="navbar-nav ml-auto">

			<!--<li class="nav-item dropdown my-auto d-none d-md-inline-block" data-title="Synorex ONE" data-intro="Click here to return to the Synorex ONE homepage">
				<a class="nav-link mr-1" href="https://one.synorexcloud.com/client" target="_blank" data-toggle="tooltip" title="Apps"><i style="font-size: 1.25rem" class="fa fa-fw fa-home"></i></a>
			</li>
			
			<li class="nav-item dropdown my-auto d-none d-md-inline-block" data-title="Help & Support" data-intro="Click here to view the system manual and ask for help">
				<a class="nav-link mr-1" href="http://help.synorexcloud.com/" target="_blank" data-toggle="tooltip" title="Help & Support"><i style="font-size: 1.25rem" class="fa fa-fw fa-question-circle"></i></a>
			</li>
		
			<li class="nav-item dropdown">
				<a class="nav-link py-0" href="javascript:void(0)" data-toggle="dropdown"><img src="https://cdn.synorexcloud.com/assets/images/icons/avatar.svg" style="height: 35px"></a>
				<div class="dropdown-menu dropdown-menu-right mt-3" style="min-width: 300px;">
					<div class="container text-center py-3">
						<img src="https://cdn.synorexcloud.com/assets/images/blank/1x1.jpg" style="width: 35%" class="d-block mx-auto mb-3 border rounded-circle">
						<h6 class="mb-1"><?php echo auth_data('nickname'); ?></h6>
						<p class="mb-2 small text-muted"><?php echo auth_data('username'); ?></p>
						<p class="mb-0 small">
							<a href="https://one.synorexcloud.com/client/account?pg=profile" target="_blank" class="btn btn-sm btn-light btn-block">My Account</a>
							<a href="<?php echo base_url('auth/logout'); ?>" class="btn btn-sm btn-light text-danger btn-block">Logout</a>
						</p>
					</div>
				</div>
			</li>-->
			
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown">Hi, <?php echo auth_data('nickname'); ?></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="<?php echo base_url('auth/profile'); ?>"><i class="fa fa-fw mr-2 fa-user"></i> Profile</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo base_url('auth/logout'); ?>"><i class="fa fa-fw mr-2 fa-sign-out-alt"></i> Logout</a>
                </div>
            </li>
            
        </ul>
        <!--<ul class="navbar-nav ml-auto">

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown">Hi, <?php echo auth_data('nickname'); ?></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="<?php echo base_url('auth/profile'); ?>"><i class="fa fa-fw mr-2 fa-user"></i> Profile</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo base_url('auth/logout'); ?>"><i class="fa fa-fw mr-2 fa-sign-out-alt"></i> Logout</a>
                </div>
            </li>
            
        </ul>-->
    </div>
</nav>