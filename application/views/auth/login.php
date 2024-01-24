<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<form action="" method="post" class="form-signin">

	<div class="text-center mb-4">
		<!-- <h1 class="h3 mb-2 font-weight-bold"><?php echo app('title'); ?></h1> -->
		<?php if(!defined('PRIMARY_BRANCH')) { ?>
			<img src="<?php echo base_url('uploads/site/logo.png?v='.time()); ?>" class="w-50 d-block mx-auto">
    	<?php } else { ?>
			<img src="<?php echo pointoapi_UploadSource(datalist_Table('tbl_branches', 'image', PRIMARY_BRANCH)); ?>" class="w-50 d-block mx-auto">
		<?php } ?>
	</div>
	
	<?php echo alert_Get(); ?>
	
	<div class="card">
		<div class="card-body p-5">
			
			<h5 class="font-weight-bold">Good to see you here!</h5>
			<p>Login and continue to <?php echo app('title'); ?> Admin</p>
			
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
				</div>
				<input type="text" class="form-control form-control-lg py-2" name="username" placeholder="Username" required autofocus>
			</div>

			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
				</div>
				<input type="password" class="form-control form-control-lg py-2" name="password" placeholder="Password" required>
			</div>

			<button class="btn btn-lg btn-primary btn-block" type="submit" name="login">Login</button>
			
			<span class="d-block text-center mt-3 small text-muted"><?php echo date('Y'); ?> &copy; <?php echo app('title'); ?>. All right reserved.</span>
			<span class="d-block text-center mt-1 small text-muted">Powered by <a href="https://synorexcloud.com/" target="_blank">Synorex</a></span>
		
		</div>
	</div>
	
</form>