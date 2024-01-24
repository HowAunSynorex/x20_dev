<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="padding: 2px 1rem; box-shadow: none!important; background-color: transparent!important;">
	<ul class="navbar-nav mr-auto">
		<a class="navbar-brand font-weight-boldA" href="https://synorexcloud.com/products?pg=rbc_tuition" target="_blank">
			<?php echo app('title'); ?>
		</a>
	</ul>
	<span class="navbar-text">
		<i class="fa fa-fw fa-building mr-1"></i>
		<?php
		
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		// echo datalist_Table('tbl_branches', 'title', explode("/", $actual_link)[6]); 
		
		echo $branch.' ('.$result_device['title'].')'; 
		?>
    </span>
</nav>

<nav class="navbar navbar-expand-lg navbar-dark fixed-bottom" style="padding: 2px 1rem; box-shadow: none!important; background-color: transparent!important;">
	<span class="navbar-text ml-auto">
		<?php echo date('Y'); ?> &copy; <?php echo app('title'); ?>. All right reserved. Powered by <a href="https://synorexcloud.com/" target="_blank" class="text-white">Synorex</a>
    </span>
</nav>