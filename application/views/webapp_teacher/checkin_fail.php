<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container container-wrapper d-flex align-items-center justify-content-center">

	<form action="" method="post" style="width: 100%; max-width: 500px; padding: 15px;">
		
		<?php echo alert_get(); ?>
		
		<div class="card">
			<div class="card-header text-center p-4" style="background: red repeat-x!important;">
				<div class="p-4">
					<i class="fa fa-times-circle fa-fw text-white" style="font-size: 3.5rem;"></i>
					<h5 class="text-white font-weight-bold pt-4">Checked Failed</h5>
				</div>
			</div>
			<div class="card-body p-4 d-flex flex-column align-items-center">
				<p class="m-0">You have been failed to checked on <span class="font-weight-bold"><?php echo date('Y-m-d H:i:s'); ?></span>
                    due to <span class="font-weight-bold"><?php echo $error; ?></span>.</p>
			</div>
		</div>
		
	</form>

</div>