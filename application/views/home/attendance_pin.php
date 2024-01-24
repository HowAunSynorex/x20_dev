<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar-guest', $thispage); ?>

<div class="container container-wrapper d-flex align-items-center justify-content-center">

	<form action="" method="post" style="width: 100%; max-width: 500px; padding: 15px;">
		
		<?php echo alert_get(); ?>
		
		<div class="card">

			<div class="card-body p-4 d-flex flex-column align-items-center">

				<h1 class="font-weight-bold p-5">9999</h1>
				<h3>Scan to check in</h3>
				<div class="progress w-75 my-3">
					<input type="hidden" name="stopper" value="15">
					<div class="progress-bar text-right bg-secondary" role="progressbar" style="width: 100%;">
						<span class="timer pr-2">0:15</span>
					</div>
				</div>
				<h6 class="text-muted text-center">Tip: Please use Robocube Tuition to submit your attendance</h6>
			</div>
		</div>
		
	</form>

</div>