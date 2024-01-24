<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar-guest', $thispage); ?>


<div class="container container-wrapper d-flex align-items-center justify-content-center">

	<form method="post" style="width: 100%; max-width: 500px; padding: 15px;">
		
		<?php echo alert_get(); ?>
		
		<div class="card">
					
			<div class="card-body p-4 d-flex flex-column align-items-center">
				
				<h3 class="font-weight-bold mt-3">Enter RFID to check in</h3>
				
				<div class="form-group w-100 mt-3">
					<input type="text" name="rfid_cardid" class="form-control" placeholder="Scan the ID Card" required autofocus>
				</div>
				
				<?php if($result_device['temp_enable'] == 1) { ?>
				<div class="form-group w-100 mb-3">
					<input type="text" name="temperature" class="form-control" placeholder="Temperature (&#8451;)" required>
				</div>
				<?php } ?>

				<div class="form-group w-100 text-right mb-2">
					<button type="submit" name="save" class="btn btn-primary">Submit</button>
				</div>
					
			</div>

		</div>
		
	</form>

</div>

<!--<div id="carouselExampleIndicators" class="carousel slide mb-4" data-ride="carousel">
	<ol class="carousel-indicators">
		<?php $i=0; foreach($slideshow as $e) {  ?>
			<li data-target="#carouselExampleIndicators" data-slide-to="<?php echo $i; ?>" class="<?php if($i == 0) echo 'active'; ?>"></li>
		<?php $i++; } ?>
	</ol>
	<div class="carousel-inner">
		<?php $j=0; foreach($slideshow as $e) {  ?>
			<div class="carousel-item <?php if($j == 0) echo 'active'; ?>">
				<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="d-block w-100 rounded" style="height: 260px; object-fit: cover">
			</div>
		<?php $j++; } ?>
	</div>
	<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
		<span class="carousel-control-prev-icon" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
	</a>
	<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
		<span class="carousel-control-next-icon" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
	</a>
</div>

<div class="container py-3 border-top">
	<div class="row">
		<div class="col-md-6 text-center text-md-left mb-2 mb-md-0">
			<a href="mailto:feedback@synorexcloud.com">Feedback</a>
			<a href="https://help.synorexcloud.com/terms/" target="_blank" class="ml-3">Terms</a>
			<a href="https://help.synorexcloud.com/privacy/" target="_blank" class="ml-3">Privacy</a>
			<a href="https://help.synorexcloud.com/security/" target="_blank" class="ml-3">Security</a>
		</div>
		<div class="col-md-6  text-center text-md-right text-muted">
			<?php echo date('Y'); ?> © <?php echo app('title'); ?>. All right reserved.
		</div>
	</div>
</div>-->