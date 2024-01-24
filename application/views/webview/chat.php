<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid container-wrapper p-3">

	<?php echo alert_get(); ?>
	
	<div class="card">
		<h5 class="card-header text-center">John Doe</h5>
		<div class="card-body">
			<div class="text-center mb-2"><?php echo date('d M H:i'); ?></div>
			<?php for($i=0; $i<3; $i++) { ?>
			<div class="sent text-right mb-2">
				<label class="rounded border py-2 px-3 m-0">
					xxxx
				</label>
			</div>
			<?php } ?>
			<?php for($i=0; $i<3; $i++) { ?>
			<div class="received text-left mb-2">
				<label class="bg-primary text-white rounded py-2 px-3 m-0">
					xxxx
				</label>
			</div>
			<?php } ?>
			<div class="sent text-right mb-2">
				<label class="rounded border py-2 px-3 m-0">
					xxxx
				</label>
			</div>
			<div class="received text-left mb-2">
				<label class="bg-primary text-white rounded py-2 px-3 m-0">
					xxxx
				</label>
			</div>
		</div>
		<div class="card-footer">
			<div class="d-flex align-items-center justify-content-between">
				<textarea class="form-control mr-3" rows="1" style="width: auto; flex-grow: 1;"></textarea>
				<div class="bg-primary rounded-circle text-white d-flex justify-content-center align-items-center" style="width: 40px; height: 40px; cursor: pointer;">
					<i class="fa fa-fw fa-paper-plane"></i>
				</div>
			</div>
		</div>
	</div>
	
</div>