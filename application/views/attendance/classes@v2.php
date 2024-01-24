<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			
			<form method="get">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group row">
							<label class="col-form-label col-md-3 text-danger">Class</label>
							<div class="col-md-9">
								<select class="form-control select2" name="class" required>
									<option value="">-</option>
									<?php foreach($class as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['class'])) { if($e['pid'] == $_GET['class']) { echo 'selected'; } } ?>><?php echo $e['title']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-form-label col-md-3">Date</label>
							<div class="col-md-9">
								<input type="month" class="form-control" name="month" value="<?php if(isset($_GET['month'])) { echo $_GET['month']; } else { echo date('Y-m'); } ?>">
							</div>
						</div>
						
						<div class="form-group row">
							<div class="col-md-9 offset-md-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="customCheck1" value="1" name="date_filter" <?php if(isset($_GET['date_filter'])) echo 'checked'; ?>>
									<label class="custom-control-label" for="customCheck1">Only show dates with classes</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group row">
							<div class="col-md-9 offset-md-3">
								<button type="submit" class="btn btn-primary">Search</button>
								<button type="button" onclick="print_normal()" class="btn btn-secondary">Print</button>
								<button type="button" onclick="print_join_date()" class="btn btn-secondary">Print with join date</button>
							</div>
						</div>
					</div>
				</div>
			</form>
            
			<?php if(isset($result2)) {
				?>
				<form method="post">
					<div class="table-responsive">
						<table class="DTableA table table-hover table-bordered" style="width: 200%">
							<thead>
								<th style="width: 10px; height: 30px;">No</th>
								<th Astyle="min-width: 200px">
									Student
									<?php
									$sort = 'desc';
									if(isset($_GET['sort'])) {
										if($_GET['sort'] == 'asc') {
											$sort = 'desc';
										} else {
											$sort = 'asc';
										}
									}
									?>
									<a href="<?php echo base_url('attendance/classes?class='.$_GET['class'].'&month='.$_GET['month']. '&sort='.$sort); ?>" class="float-right sort-link"><i class="fa fa-fw fa-sort-alpha-<?php if(isset($_GET['sort'])) { if($_GET['sort'] == 'asc') { echo 'down'; } else { echo 'up'; } } else { echo 'down'; } ?>"></i></a>
								</th>
								<?php
								if(isset($_GET['month'])) {
									$month = date('m', strtotime($_GET['month']));
									$year = date('Y', strtotime($_GET['month']));
									
									$days_in_month = cal_days_in_month(CAL_GREGORIAN,$month,$year);
									
									for($i=1; $i<=$days_in_month; $i++) {
										echo '<th style="width: 20px; height: 50px;" id="day-'.$i.'">'.$i.'</th>';
									}
								}
								?>
							</thead>
							<tbody> 
								<?php 
								$i=0; $dayList = '';
								foreach($result as $e) {
									if(check_user_availability($e['user'])) { $i++;
										?>
										<tr>
											<td><?php echo $i; ?></td>
											<td>
												<?php echo datalist_Table('tbl_users', 'fullname_en', $e['user']); ?>
												<span class="d-block text-muted small join-on-text">
													Join on
													<?php
													echo $this->log_join_model->list('join_class', branch_now('pid'), [
														
														"class" => $_GET['class'],
														"user" => $e['user'],
														
													])[0]['date'];
													?>
												</span>
											</td>
											
											<?php
											for($j=1; $j<=$days_in_month; $j++) {
												?>
												<td class="font-weight-bold day-<?php echo $j; ?>">
												<?php
												$day = date('N', strtotime($_GET['month'].'-'. $j));
												if(!empty(datalist_Table('tbl_classes', 'dy_' . $day, $_GET['class']))) {
													// $x=time().rand(11,99);
													$dayList .= $j."+";
													if(!empty(
														$this->log_join_model->list('class_attendance', branch_now('pid'), [
														
															"class" => $_GET['class'],
															"user" => $e['user'],
															"date" => $_GET['month']."-".$j
															
														])[0])
													) {
														
														$id = $this->log_join_model->list('class_attendance', branch_now('pid'), [
															"class" => $_GET['class'],
															"user" => $e['user'],
															"date" => $_GET['month']."-".$j
														])[0]['id'];
														
														?>
														<input
														type="checkbox"
														name="old_attendance[]"
														onclick="add_to_removed_list(this)"
														id="<?php echo $e['user']."-".$year."-".$month."-".$j."_".$id; ?>"
														checked
														>
														<?php
													} else {
														?>
														<input
														type="checkbox"
														name="attendance[]"
														id="<?php echo $e['user']."-".$year."-".$month."-".$j; ?>"
														value="<?php echo $e['user'].','.$year."-".$month."-".$j; ?>"
														>
														<?php
													}
												}
												?>
												</td>
												<?php
											}
											?>
										</tr>
										<?php
									}
								}
								?>
							</tbody>
						</table>
						<input type="hidden" name="removed_list">
						<input type="hidden" name="days" value="<?php echo $dayList; ?>"/>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group text-right">
								<button type="submit" name="save" class="btn btn-primary" <?php if ( !check_module('Attendance/Create') ) { echo 'disabled'; } ?>>Submit</button>
							</div>
						</div>
					</div>
				</form>
			<?php } else { echo '<div class="alert alert-warning">No result found</div>'; } ?>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>