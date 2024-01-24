<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="padding: 2px 1rem;">
    
	<a class="navbar-brand font-weight-boldA" href="<?php echo base_url(); ?>">
		<?php if(!defined('PRIMARY_BRANCH')) { ?>
			<img src="<?php echo base_url('uploads/site/logo.png?v='.time()); ?>" style="height: 38px;" class="mr-1">
		<?php } else { ?>
			<img src="<?php echo pointoapi_UploadSource(datalist_Table('tbl_branches', 'image', PRIMARY_BRANCH)); ?>" style="height: 38px; width: 38px; object-fit: cover;" class="mr-1">
		<?php } ?>
		<span class="small"><?php echo app('title'); ?></span>
	</a>

    <div class="collapse navbar-collapse" id="Navbar">
        <ul class="navbar-nav ml-auto">
		
			<li class="nav-item dropdown">
				<a class="nav-link py-0" href="javascript:void(0)" data-toggle="dropdown"><img src="https://cdn.synorexcloud.com/assets/images/icons/avatar.svg" style="height: 35px"></a>
				<div class="dropdown-menu dropdown-menu-right mt-3" style="min-width: 300px;">
					<div class="container text-center py-3">
						<img src="https://cdn.synorexcloud.com/assets/images/blank/1x1.jpg" style="width: 35%" class="d-block mx-auto mb-3 border rounded-circle">
						<h6 class="mb-1"><?php echo auth_data('nickname'); ?></h6>
						<p class="mb-2 small text-muted"><?php echo auth_data('username'); ?></p>
						<p class="mb-0 small">
							<?php if(!defined('WHITELABEL')) { ?>
								<a href="https://one.synorexcloud.com/client/account?pg=profile" target="_blank" class="btn btn-sm btn-light btn-block">My Account</a>
							<?php } ?>
							<a href="<?php echo base_url('auth/logout'); ?>" class="btn btn-sm btn-light text-danger btn-block">Logout</a>
						</p>
					</div>
				</div>
			</li>
			
        </ul>
    </div>
</nav>