<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid container-wrapper py-3">

	<?php echo alert_get(); ?>
	
	
	<form method="get">
		<div class="row">
			<div class="col-md-6">
				
				<?php if($user['type'] == 'parent') { ?>
					<div class="form-group row">
						<label class="col-md-3 col-form-label">Child</label>
						<div class="col-md-9">
							<select class="form-group select2" name="user">
								<option value="">-</option>
								<?php foreach($child as $e) { ?>
									<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['user'])) if($_GET['user'] == $e['pid']) echo 'selected'; ?>><?php echo $e['fullname_en']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				
				<div class="form-group row">
					<label class="col-form-label col-md-3">Month</label>
					<div class="col-md-9">
						<input type="month" class="form-control" name="month" value="<?php if(isset($_GET['month'])) { echo $_GET['month']; } else { echo date('Y-m'); } ?>">
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col-md-9 offset-md-3">
						<input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
						<button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
					</div>
				</div>
				
			</div>
		</div>
	</form>
	
	<div class="table-responsive">
		<table class="DTable2 table table-hover table-bordered" style="width: 200%">
			<thead>
				<th>No</th>
				<th>Class</th>
				<?php
				
				$date;
				
				if(isset($_GET['month'])) {
					
					$month = date('m', strtotime($_GET['month']));
					$year = date('Y', strtotime($_GET['month']));
					$date = $_GET['month'];
					
				} else {
					
					$month = date('m');
					$year = date('Y');
					$date = date('Y-m');
					
				}
				
				$days_in_month = cal_days_in_month(CAL_GREGORIAN,$month,$year);
				
				for($i=1; $i<=$days_in_month; $i++) {
					echo '<th id="day-'.$i.'">'.$i.' <small class="text-muted">'.datetoname($_GET['month'].'-'.$i).'</small></th>';
				}
					
				?>
			</thead>
			<tbody> 
				<?php 
				
				$no=0;
				$existing_user = [];
										
				if(isset($result)) {
					foreach($result as $e)  { $no++;
					    ?>
						<tr>
							<td><?php echo $no; ?></td>
							<td><?php echo datalist_Table('tbl_classes', 'title', $e['class']); ?></td>
							<?php
            				
            				$date;
            				
            				if(isset($_GET['month'])) {
            					
            					$month = date('m', strtotime($_GET['month']));
            					$year = date('Y', strtotime($_GET['month']));
            					$date = $_GET['month'];
            					
            				} else {
            					
            					$month = date('m');
            					$year = date('Y');
            					$date = date('Y-m');
            					
            				}
            				
            				$days_in_month = cal_days_in_month(CAL_GREGORIAN,$month,$year);
            				
            				for($i=1; $i<=$days_in_month; $i++) {
            				    ?>
            				    <td>
            				        <?php
            				        
            				        if($i<10) $i='0'.$i;
            				        
            				        $day = date('N', strtotime($_GET['month'].'-'.$i));
            				        
            				        if(!empty(datalist_Table('tbl_classes', 'dy_'.$day, $e['class']))) {
            				            
                				        $check = $this->log_join_model->list_all([ 'type' => 'class_attendance', 'class' => $e['class'], 'active' => 1, 'date' => $_GET['month'].'-'.$i ]);
                				        if(count($check) == 0) {
                				            echo '<span class="text-danger font-weight-bold text-center d-block">&#215;</span>';
                				        } else {
                				            echo '<span class="text-success font-weight-bold text-center d-block">&#10003;</span>';
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
					
				} else {
					?>
					<tr>
						<td class="text-center" colspan="<?php echo $days_in_month + 1; ?>">No result found</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	
</div>