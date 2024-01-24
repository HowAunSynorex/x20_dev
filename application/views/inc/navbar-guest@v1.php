<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="padding: 2px 1rem;">
	<ul class="navbar-nav mr-auto">
		<a class="navbar-brand font-weight-boldA" href="<?php echo base_url(); ?>" target="_blank">
			<img src="https://cdn.synorex.link/assets/images/robocube/tuition.png" style="height: 38px;" class="mr-1">
			<span class="small"><?php echo app('title'); ?></span>
		</a>
	</ul>
	<span class="navbar-text">
		<i class="fa fa-fw fa-building mr-1"></i>
		<?php
		
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		echo datalist_Table('tbl_branches', 'title', explode("/", $actual_link)[6]); 
		
		?>
    </span>
</nav>