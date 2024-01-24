<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar-webapp_teacher', $thispage); ?>

<div class="container py-3">

	<?php echo alert_get(); ?>

	<form method="get">
		<div class="form-group row">
			<label class="col-form-label col-3">Date</label>
			<div class="col-9">
				<input type="date" class="form-control" name="date" value="<?php if(isset($_GET['date'])) echo $_GET['date']; ?>" onchange="window.location.href='<?php echo base_url('webapp_teacher'); ?>?date='+this.value+'&class=<?php echo $_GET['class'] ?>&search=<?php echo $_GET['search']?>'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-form-label col-3">Class</label>
			<div class="col-9">
				<select class="form-control select2" name="class">
					<?php foreach($class as $e) { ?>
						<option value="<?php echo $e['class_id']; ?>" <?php if(isset($_GET['class']) && $_GET['class'] == $e['class_id']) echo 'selected'; ?>><?php echo date('D', strtotime($_GET['date'])) . ' ' . $e['time_range'] . ' ' . $e['class_title'] . ' ' . $e['class_subtitle']; ?></option>
					<?php }
					if(empty($class)) {
						?>
						<option>-</option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<button type="submit" name="search" class="btn btn-primary btn-block">Load</button>
		</div>
	</form>

    <div class="form-group row">
        <div class="col-12 text-center">
            <img src="<?=$class_qr?>">
            </select>
        </div>
    </div>
	
	<form method="post" class="mb-5 pb-5">
		<div class="form-group">
			<input type="text" class="form-control" id="search" onclick="$('#modal-search').modal('show');" placeholder="Search by Student Name or Code" readonly>
		</div>
		
		<?php
		
		$none_count = $present_count = $absent_count = 0;
				
		foreach($students as $e) {
			
			$is_none = $is_present = $is_absent = false;
			$bg_section_color = '#eee';
			
			$check_attd_sql = '
				SELECT * FROM log_join
				WHERE is_delete = 0
				AND type = "class_attendance"
				AND sub_class = "'.$_GET['class'].'"
				AND user = "'.$e['pid'].'"
				AND date = "'.$_GET['date'].'"
			';
			
			$check_attd = $this->db->query($check_attd_sql)->result_array();
			
			//

			if(count($check_attd) == 0) {
				$is_none = true;
				$none_count++;
			} else {
				if($check_attd[0]['active']) {
					$is_present = true;
					$present_count++;
					$bg_section_color = '#54c67e';
				} else {
					$is_absent = true;
					$absent_count++;
					$bg_section_color = '#ec5b78';
				}
			}
			
			//
			
			$check_first = $this->db->query(' SELECT * FROM `log_join` WHERE `type` LIKE ? AND user=? AND class=? AND sub_class=? AND active=1 AND is_delete=0 ', [ 
			    'class_attendance', 
			    $e['pid'],
			    $thispage['class_id'],
			    $_GET['class'],
		    ])->result_array();
			
// 			$check_abs = $this->db->query(' SELECT * FROM `log_join` WHERE `type` LIKE ? AND user=? AND class=? AND sub_class=? AND active=1 AND is_delete=0 AND date LIKE ? ', [ 
// 			    'class_attendance', 
// 			    $e['pid'],
// 			    $thispage['class_id'],
// 			    $_GET['class'],
// 			    '%'.date('Y-m', strtotime($_GET['date'])).'%',
// 		    ])->result_array();
		    
		    $check_abs = $this->db->query(' SELECT * FROM log_join WHERE is_delete=0 AND user=? AND class=? AND type=? AND date LIKE ? AND active=0 ', [ $e['pid'], $thispage['class_id'], 'class_attendance', '%'.date('Y-m', strtotime($_GET['date'])).'%' ])->result_array();
			$full_bg = '';
			
			if(count($check_abs) == 1) {
			    $full_bg = '#FEFE9A';
			} elseif(count($check_abs) == 2) {
			    $full_bg = '#F5C966';
			} elseif(count($check_abs) == 3) {
			    $full_bg = '#FF957F';
			} elseif(count($check_first) == 0) {
			    $full_bg = '#BEFFBF';
			}
			
			?>
			<!-- Tan Jing Suan -->
			<!-- <div class="card mb-3 student-card" style="background-color: <?php echo $full_bg; ?>"> -->
			<div class="card mb-3 student-card" id="full_bg_<?php echo $e['pid']; ?>" style="background-color: <?php echo $full_bg; ?>">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<?php
						
							$count = $this->db->query(' SELECT log_join.id, tbl_classes.title AS class_title,
							teachers.fullname_en AS teacher_name,
							timetables.title AS class_subtitle, timetables.time_range, 
							DAYNAME(CONCAT("1970-09-2", timetables.qty)) AS class_day
							FROM `log_join` 
							JOIN tbl_classes ON tbl_classes.pid = log_join.class
							JOIN tbl_users teachers ON teachers.pid = tbl_classes.teacher
							LEFT JOIN log_join timetables ON timetables.id = log_join.sub_class AND timetables.is_delete = 0
							AND timetables.type = "class_timetable" 
							WHERE log_join.type LIKE ? AND log_join.user = ? AND log_join.is_delete = 0', [ 'join_class', $e['pid'] ])->result_array();
						?>
						<a data-json='<?php echo json_encode($count); ?>' class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; cursor: pointer;" href="javascript:;" data-toggle="modal" data-target="#modal-class" data-value="<?php echo $e['pid']; ?>">
							<?php echo count( $count ); ?>
						</a>
						<div class="px-3" style="flex: 1 1 auto;">
							<div class="mb-1 student-info">
								<?php
									echo $e['fullname_en'] . ' ' . $e['fullname_cn'];
									echo empty($e['code']) ? '' : ' (' . $e['code'] . ')';
								?>
							</div>
							<!-- Tan Jing Suan -->
							<!-- <div class="bg-section row m-0 rounded border" style="background-color: <?php echo $bg_section_color ?>;"> -->
							<div class="bg-section row m-0 rounded border" id="color_<?php echo $e['pid']; ?>" style="background-color: <?php echo $bg_section_color ?>;">
								<div class="col-4 p-1">
									<label class="checkbox-container checkbox-none">
										<!-- Tan Jing Suan -->
										<!-- <input type="radio" name="attd[<?php echo $e['pid']; ?>]" value="" <?php if($is_none) echo 'checked'; ?>> -->
										<input type="radio" name="attd[<?php echo $e['pid']; ?>]" value="" <?php if($is_none) echo 'checked'; ?> onchange="save_attendance(<?php echo $e['pid']; ?>,'<?=$_GET['class'];?>','<?=$_GET['date'];?>','')" >
										<span class="checkmark p-1 rounded">None</span>
									</label>
								</div>
								<div class="col-4 p-1">
									<label class="checkbox-container checkbox-present">
										<!-- Tan Jing Suan -->
										<!-- <input type="radio" name="attd[<?php echo $e['pid']; ?>]" value="1" <?php if($is_present) echo 'checked'; ?>> -->
										<input type="radio" name="attd[<?php echo $e['pid']; ?>]" value="1" <?php if($is_present) echo 'checked'; ?> onchange="save_attendance(<?php echo $e['pid']; ?>,'<?=$_GET['class'];?>','<?=$_GET['date'];?>','1')" >
										<span class="checkmark p-1 rounded">Present</span>
									</label>
								</div>
								<div class="col-4 p-1">
									<label class="checkbox-container checkbox-absent">
										<!-- Tan Jing Suan -->
										<!-- <input type="radio" name="attd[<?php echo $e['pid']; ?>]" value="2" <?php if($is_absent) echo 'checked'; ?>> -->
										<input type="radio" name="attd[<?php echo $e['pid']; ?>]" value="2" <?php if($is_absent) echo 'checked'; ?> onchange="save_attendance(<?php echo $e['pid']; ?>,'<?=$_GET['class'];?>','<?=$_GET['date'];?>','0')" >
										<span class="checkmark p-1 rounded">Absent</span>
									</label>
								</div>
							</div>
							<div class="my-3 reason-div <?php if(!$is_absent) echo 'd-none'; ?>">
								<label class="d-block mb-0">Reason:</label>
								<!-- Tan Jing Suan -->
								<!-- <select class="form-control select2" name="reason[<?php echo $e['pid']; ?>]">
									<option value="">-</option>
									<?php 
									$reason = $this->tbl_secondary_model->list('reason', $e['branch'], [ 'active' => 1 ]);
									foreach($reason as $e2) {
										?>
										<option value="<?php echo $e2['pid']; ?>" <?php if(isset($check_attd[0]) && $check_attd[0]['remark'] == $e2['pid']) echo 'selected'; ?>><?php echo $e2['title']; ?></option>
										<?php
									}
									?>
								</select> -->
								<select class="form-control select2" id="remark_<?php echo $e['pid']; ?>" name="reason[<?php echo $e['pid']; ?>]" onchange="save_attendance(<?php echo $e['pid']; ?>,'<?=$_GET['class'];?>','<?=$_GET['date'];?>','0')" >
									<option value="">-</option>
									<?php 
									$reason = $this->tbl_secondary_model->list('reason', $e['branch'], [ 'active' => 1 ]);
									foreach($reason as $e2) {
										?>
										<option value="<?php echo $e2['pid']; ?>" <?php if(isset($check_attd[0]) && $check_attd[0]['remark'] == $e2['pid']) echo 'selected'; ?>><?php echo $e2['title']; ?></option>
										<?php
									}
									?>
								</select>
							</div>
							<?php
                            $each_week = getDatesByDayOfWeek(
                                getDayOfWeek($_GET['date']), 
                                $_GET['date'], 								
                            );
                            
                            // output date
                            foreach ($each_week as $date) {
                                
                                unset($text);
                                
                                // check this std and class join date
                                $check_std_join_date = $this->db->query(' SELECT * FROM log_join WHERE is_delete=0 AND user=? AND class=? AND type=? AND active=1 ', [ $e['pid'], $thispage['class_id'], 'join_class' ])->result_array();
                                if(count($check_std_join_date)>0) {
                                    $check_std_join_date = $check_std_join_date[0];
                                    $join_date = $check_std_join_date['date'];
                                } else {
                                    $join_date = '';
                                }
                                
                                // xx
                                $bg_section_color = '#eee';
                                $check_class_date_status = $this->db->query(' SELECT * FROM log_join WHERE is_delete=0 AND user=? AND class=? AND type=? AND date=? ', [ $e['pid'], $thispage['class_id'], 'class_attendance', $date ])->result_array();
                                if(count($check_class_date_status)>0) {
                                    $check_class_date_status = $check_class_date_status[0];
                                    // 如果有点名记录
                                    if($check_class_date_status['active'] == 1) {
                                        $bg_section_color = 'rgb(84, 198, 126)';
                                    } else {
                                        $bg_section_color = '#dc3545';
                                    }
                                } else {
                                    // 如果没有点名记录
                                    // echo $join_date.'/'.date('d', strtotime($date)).'<br>';
                                    if( $join_date > $date ) {
                                        $bg_section_color = '#eee';
                                        $text = '-';
                                    } else {
                                        $bg_section_color = '#eee';
                                    }
                                }
                                
                                // if( 
                                //     date('d', strtotime($date)) <= date('d', strtotime($_GET['date']))    
                                // ) {
                                    
                                    
                                    
                                // } else {
                                //     // 如果是未来的日期
                                //     $bg_section_color = '#eee';
                                //     $future = 1;
                                // }
                                
                                // // 如果是未来的日期
                                // if($future == 1) {
                                    
                                // }    
                                if (date('d', strtotime($date)) != date('d', strtotime($_GET['date']))){
                                ?>
								
                                <div class="bg-section border p-2 my-2 rounded mr-2 text-center d-inline-block <?php if(date('d', strtotime($date))==date('d', strtotime($_GET['date']))) echo 'blink'; ?>" style="height: 40px; width: 40px; background-color: <?php echo $bg_section_color; ?>;">
    							    <?php echo isset($text) ? $text : date('d', strtotime($date)); ?>
    							</div>
                                <?php
								}
                            }

							?>
							<div class="d-flex">
								<?php 
								
								// output unpaid
								
                                
								// echo count($check_month_unpaid);
								
								for($i=-3; $i<=0; $i++) { 
								    $check_month_unpaid = $this->db->query(' SELECT * FROM log_payment WHERE is_delete=0 AND user=? AND class=? AND period=? ', [ $e['pid'], $thispage['class_id'], date('Y-m', strtotime($i.' months')) ])->result_array();
    								?><i class="fa fa-money-bill pr-2 text-<?php echo count($check_month_unpaid)>0 ? 'success' : 'danger' ; ?>" style="font-size: 200%;"></i><?php 
								} 
								?>
							</div>
							<!-- Tan Jing Suan -->
							<div class="d-flex" id="alert_<?php echo $e['pid']; ?>" style="display: none;" >
								<div class="alert alert-success small" id="alertmsg_<?php echo $e['pid']; ?>" style="display: none;"></div>
							</div>
						</div>
						<div class="d-flex align-items-center justify-content-center">
							<a href="javascript:;" data-toggle="modal" data-target="#modal-student" data-value="<?php echo $e['pid']; ?>">
								<i class="fa fa-fw fa-chevron-right	text-danger" style="font-size: 150%;"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		<?php }
		if(empty($students)) {
			?>
			<p class="text-center text-muted">No result found</p>
			<?php
		}
		?>
		
		<div class="fixed-bottom">
			<div class="bg-white border-top d-flex align-items-center justify-content-between p-3">
				<div class="d-flex align-items-center">
					<?php
					$arr = [
						'none' => [
							'count' => $none_count,
							'color' => 'white',
						],
						'present' => [
							'count' => $present_count,
							'color' => '#54c67e',
						],
						'absent' => [
							'count' => $absent_count,
							'color' => '#ec5b78',
						],
					];
					
					foreach($arr as $k => $e) {
						?>
						<div class="d-flex align-items-center justify-content-center border rounded mr-2 <?php echo $k; ?>-count" style="height: 40px; width: 40px; background-color: <?php echo $e['color']; ?>;">
							<?php echo $e['count']; ?>
						</div>
						<?php
					}
					?>
					<!-- Tan Jing Suan -->
					<!-- <button type="submit" name="save" class="btn btn-primary btn-lg">Submit</button> -->
				</div>
			</div>
		</div>
		
	</form>
    
</div>

<div class="modal fade" id="modal-student" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Student Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table">
					<tr>
						<td class="font-weight-bold">Name</td>
						<td class="name">Name</td>
					</tr>
					<tr>
						<td class="font-weight-bold">Code</td>
						<td class="code">code</td>
					</tr>
					<tr>
						<td class="font-weight-bold">School</td>
						<td class="school">school</td>
					</tr>
					<tr>
						<td class="font-weight-bold">Form</td>
						<td class="form">form</td>
					</tr>
					<tr>
						<td></td>
						<td class="extra-function"></td>
					</tr>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-class" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Class Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table">
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modal-search" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Search</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			
			    <div class="input-group mb-3">
                    <input type="text" class="form-control" onchange="search(this.value)" placeholder="Search by Student Name or Code" />
                    <!--<div class="input-group-append">-->
                    <!--    <button class="btn btn-outline-secondary" type="button" id="button-addon2">Button</button>-->
                    <!--</div>-->
                </div>
                
                <ul class="list-group" id="append">
                    
                </ul>

			</div>
		</div>
	</div>
</div>

<?php if(isset($class_dropdown)) { ?>
    <script>
        var class_dropdown = <?php echo json_encode( $class_dropdown ); ?>;
    </script>
<?php } ?>

<script>
    var class_id = <?php echo $thispage['class_id']; ?>;
</script>