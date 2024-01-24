<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container container-wrapper d-flex align-items-center justify-content-center">

	<form action="" method="post" style="width: 100%; max-width: 500px; padding: 15px;">
		
		<?php echo alert_get(); ?>
		
		<?php
		
		if(isset($isPaid)) {
			?>
			<div class="card">
				<div class="card-header text-center p-4" style="background: #28a745 linear-gradient(180deg,#48b461,#28a745) repeat-x!important;">
					<div class="p-4">
						<i class="fa fa-check-circle fa-fw text-white" style="font-size: 3.5rem;"></i>
						<h5 class="text-white font-weight-bold pt-4">Payment Succcessfully</h5>
					</div>
				</div>
				<div class="card-body p-4 d-flex flex-column align-items-center">
					<p class="m-0 text-center">We have successfully charged the amount of <b>$<?php echo number_format($payment_callback['amount'], 2, '.', ','); ?></b> you attempted to pay on <?php echo date('M d, Y'); ?>.</p>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="card">
				<div class="card-header text-center p-4" style="background: #D32F2F linear-gradient(180deg,#F44336,#D32F2F) repeat-x!important;">
					<div class="p-4">
						<i class="fa fa-times-circle fa-fw text-white" style="font-size: 3.5rem;"></i>
						<h5 class="text-white font-weight-bold pt-4">Payment Failed</h5>
					</div>
				</div>
				<div class="card-body p-4 d-flex flex-column align-items-center">
					<p class="m-0 text-center">Please contact your tuition centre or you bank to check your transaction status.</p>
				</div>
			</div>
			<?php
		}
		?>
		
	</form>

</div>