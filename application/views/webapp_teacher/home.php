<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar-webapp_teacher', $thispage); ?>

<div class="container py-3">

	<?php echo alert_get(); ?>

	<form method="get">
		<div class="text-center"> 
			<h3><?php echo datalist_Table('tbl_users', 'fullname_en',  auth_data_teacher('pid')); ?></h3>
		</div>
		<div class="form-group row">
			<label class="col-form-label col-3">Date</label>
			<div class="col-9">
				<input type="date" class="form-control" name="date" value="<?php if(isset($_GET['date'])) echo $_GET['date']; ?>" onchange="window.location.href='<?php echo base_url('webapp_teacher/home'); ?>?date='+this.value">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-form-label col-3">Class</label>
			<div class="col-9">
                <ul class="list-group">
					<?php foreach($class as $e) { ?>
						<a href="<?php echo base_url('webapp_teacher'); ?>?class=<?php echo $e['class_id']; ?>&date=<?php echo $_GET['date']; ?>&search=" class="list-group-item">
							<div class="d-flex  justify-content-between">
								<b class="d-block"><?php echo $e['class_title'] . ' ' . $e['class_subtitle']; ?></b>
								<b class="d-block"><?php echo date('D', strtotime($_GET['date'])) . ' ' . $e['time_range'] ?></b>
							</div>
						</a>
					<?php } ?>
					<?php if(empty($class)) { ?>
						<li class="list-group-item">-</li>
					<?php } ?>
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-form-label col-3">Childcare</label>
			<div class="col-9">
				<?php if(empty($childcares)) { ?>
					<li class="list-group-item">-</li>
				<?php }else{ ?>
					<a href="<?php echo base_url('webapp_teacher/childcare'); ?>?date=<?php echo $_GET['date']; ?>&search=" class="list-group-item">
						<div class="d-flex  justify-content-between">
							<b class="d-block">小学安亲班</b>
							<b class="d-block"><?php echo count($childcares) ?></b>
						</div>
					</a>
				<?php } ?>
			</div>
		</div>
		
		<div class="form-group">
			<button type="submit" name="search" class="btn btn-primary btn-block">Load</button>
		</div>
	</form>
</div>