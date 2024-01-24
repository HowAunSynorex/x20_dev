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
                <div class="col-6 my-auto text-right">
					<?php if( check_module('Classes/Create') ) { ?>
						<a href="<?php echo base_url($thispage['group'] . '/add'); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
					<?php } ?>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			
            <table class="DTable table">
                <thead>
                    <th style="width:10%">No</th>
                    <th>Title</th>
                    <th>Tutor</th>
                    <th>Time</th>
					<?php
					$days = cal_days_in_month(CAL_GREGORIAN,date('m', strtotime($_GET['month'])),date('Y', strtotime($_GET['month'])));
					$weeks = ceil($days / 7);
					for($i=1; $i<=$weeks; $i++) {
						?>
						<th>Week <?php echo $i; ?></th>
						<?php
					}
					?>
					<th>Total</th>
                    <th>Paid</th>
                </thead>
                <tbody>
                    
                    <?php
					
					$j=0;
					
					$start_date = date('Y-m-01', strtotime($_GET['month']));
					$end_date = date('Y-m-t', strtotime($_GET['month']));
					
					foreach($result as $e) {
						$j++;
						$total_attd = 0;
						$attd = 0;
						$day = 0;
						?>
						<tr>
							<td><?php echo $j; ?></td>
							<td>
								<?php if( check_module('Classes/Update') ) { ?>
									<a href="<?php echo base_url($thispage['group'] . '/edit/'.$e['pid']); ?>">
								<?php } ?>
									<?php echo $e['title']; ?>
								<?php if( check_module('Classes/Update') ) { ?>
									</a>
								<?php } ?>
							</td>
							<td><?php echo empty($e['teacher']) ? '-' : datalist_Table('tbl_users', 'fullname_en', $e['teacher']); ?></td>
							<td>
								<?php
								foreach(['dy_1', 'dy_2', 'dy_3', 'dy_4', 'dy_5', 'dy_6', 'dy_7'] as $e2) {
									if(!empty($e[$e2])) {
										$day = str_replace('dy_', '', $e2);
										if($day == 7) {
											$day = 0;
										} else {
											$day += 1;
										}
										echo datalist('day_name')[str_replace('dy_', '', $e2)]['name'].': '.$e[$e2].'<br>';
									}
								}
								?>
							</td>
							<?php
							for($i=0; $i<$weeks; $i++) {
								$start = $i == 0 ? $start_date : date('Y-m-d', strtotime('+7 days', strtotime($start)));
								$end = date('Y-m-d', strtotime('+6 days', strtotime($start))) > $end_date ? $end_date : date('Y-m-d', strtotime('+6 days', strtotime($start)));
								
								$sql = '
								
									SELECT * FROM log_join
									WHERE is_delete = 0
									AND type = "join_class"
									AND class = "'.$e['pid'].'"
									GROUP BY user;
								
								';
								$query = $this->db->query($sql)->result_array();
								$std = count($query);
								if(date('Y-m-d', strtotime('+'.($day == 0 ? 0 : $day - 1).' days', strtotime($start))) <= $end_date) {
									$total_attd += $std;
								}
								
								$sql = '
								
									SELECT * FROM log_join
									WHERE is_delete = 0
									AND type = "class_attendance"
									AND class = "'.$e['pid'].'"
									AND date >= "'.$start.'"
									AND date <= "'.$end.'"
									GROUP BY user;
								
								';
								$query = $this->db->query($sql)->result_array();
								$attendance = count($query);
								if(date('Y-m-d', strtotime('+'.($day == 0 ? 0 : $day -1).' days', strtotime($start))) <= $end_date) {
									$attd += $attendance;
								}
								?>
								<td>
									<?php
									if(date('Y-m-d', strtotime('+'.($day == 0 ? 0 : $day -1).' days', strtotime($start))) <= $end_date) {
										echo $attendance .'/'. $std;
									} else {
										echo '-';
									}
									?>
								</td>
								<?php
							}
							?>
							<td><?php echo $attd .'/'. $total_attd; ?></td>
							<td>
								<?php
								$sql = '
								
									SELECT * FROM log_payment
									WHERE is_delete = 0
									AND class = "'.$e['pid'].'"
									AND period = "'.$_GET['month'].'"
									GROUP BY user;
								
								';
								$query = $this->db->query($sql)->result_array();
								echo count($query);
								?>
							</td>
						</tr>
						<?php
					}
					?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>