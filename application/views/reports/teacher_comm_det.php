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
					<a href="<?php echo base_url($thispage['group'] . '/teacher_comm?month='. $_GET['month']); ?>" class="btn btn-secondary">Close</a>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">
        	<!-- Tan Jing Suan -->
        	<!-- Define abcd error -->
            <?php $a = 0;$b = 0;$c = 0;$d = 0;$f = 0;$g = 0; ?>
            <?php $search_class = array();?>
            <?php $max_student_count = 0;?>
            <?php
				$days = cal_days_in_month(CAL_GREGORIAN,date('m', strtotime($_GET['month'])),date('Y', strtotime($_GET['month'])));
				$weeks = ceil($days / 7);
				for($x=1; $x <= $weeks; $x++) {
					${"bsm_present_student_count_w$x"} = 0;
					${"bsm_student_count_w$x"} = 0;
				}
				for($x=1; $x <= $weeks; $x++) {
					${"bsn_present_student_count_w$x"} = 0;
					${"bsn_student_count_w$x"} = 0;
				}
				for($x=1; $x <= $weeks; $x++) {
					${"psm_present_student_count_w$x"} = 0;
					${"psm_student_count_w$x"} = 0;
				}
				for($x=1; $x <= $weeks; $x++) {
					${"psn_present_student_count_w$x"} = 0;
					${"psn_student_count_w$x"} = 0;
				}
				for($x=1; $x <= $weeks; $x++) {
					${"ssm_present_student_count_w$x"} = 0;
					${"ssm_student_count_w$x"} = 0;
				}
				for($x=1; $x <= $weeks; $x++) {
					${"ssn_present_student_count_w$x"} = 0;
					${"ssn_student_count_w$x"} = 0;
				}
				$bsm_present_student_count = 0;
				$bsn_present_student_count = 0;
				$psm_present_student_count = 0;
				$psn_present_student_count = 0;
				$ssm_present_student_count = 0;
				$ssn_present_student_count = 0;
				$bsm_max_student_count = 0;
				$bsn_max_student_count = 0;
				$psm_max_student_count = 0;
				$psn_max_student_count = 0;
				$ssm_max_student_count = 0;
				$ssn_max_student_count = 0;
				$bsm_payment_student_count = 0;
				$bsn_payment_student_count = 0;
				$psm_payment_student_count = 0;
				$psn_payment_student_count = 0;
				$ssm_payment_student_count = 0;
				$ssn_payment_student_count = 0;
            ?>
            <?php echo alert_get(); ?>
            <!-- Tan Jing Suan -->
			<?php if (count($baby_morning_result) > 0){
				$f = 0; foreach($baby_morning_result as $e) { 
				$time = explode("-", $e['time_range']);
				if(strtotime($time[0]) < strtotime('11:00') && strtotime($time[1]) < strtotime('11:00')){
					$f++;	
				} 
			}
			if ($f > 0){?>
				<h4>幼儿（早班）</h4>
			
				<table class="DTable table">
					<thead>
						<th style="width:10%">No</th>
						<th>Title</th>
						<th>Tutor</th>
						<th>Time</th>
						<?php
						for($i=1; $i <= $weeks; $i++) {
							?>
							<th>Week <?php echo $i; ?></th>
							<?php
						}
						?>
						<th>Total</th>
						<th>Paid</th>
					</thead>
					<tbody>
						
						<?php $i = 1; 
						for($x=1; $x <= $weeks; $x++) {
							${"bsm_present_student_count_w$x"} = 0;
							${"bsm_student_count_w$x"} = 0;
						}
						$bsm_present_student_count = 0;
						$bsm_max_student_count = 0;
						$bsm_payment_student_count = 0;
						foreach($kid_morning_result as $e) { 
							$time = explode("-", $e['time_range']);
							if(strtotime($time[0]) < strtotime('11:00') && strtotime($time[1]) < strtotime('11:00')){?>
							
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $e['class_title']; ?> <?php echo $e['sub_class_title']; ?></td>
								<td><?php echo $e['fullname_en']; ?></td>
								<td><?php echo $e['class_day']; ?> <?php echo $e['time_range']; ?></td>
								<?php for($x=1; $x <= $weeks; $x++) { ?>
									<?php $search_class = search($result, 'sub_class_id', $e['sub_class_id']);  ?>
									<?php $search_week_number = search($search_class, 'week_number', $x) ?>
									<?php ${"bsm_present_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['present_student_count'] : 0 ?>
									<?php ${"bsm_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['student_count'] : 0 ?>
									<?php $max_student_count = count($search_week_number) > 0 ? $search_week_number[0]['max_student_count'] : 0 ?>
									<?php 
										${"bsm_present_student_count_w$x"} = ${"bsm_present_student_count_w$x"} + ${"bsm_present_student_count_$x"};
										${"bsm_student_count_w$x"} = ${"bsm_student_count_w$x"} + ${"bsm_student_count_$x"}; 
									?>
									<td>
										<?php if ($x == 5) { 
												if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
										?>
											<?php echo ${"bsm_present_student_count_$x"} ?> / <?php echo ${"bsm_student_count_$x"} ?></td>
										<?php } else { ?>
												0 / 0
										
										<?php } ?>
										<?php } else { ?>
											<?php echo ${"bsm_present_student_count_$x"} ?> / <?php echo ${"bsm_student_count_$x"} ?></td>
										<?php } ?>
								<?php } ?>
								<td><?php echo array_sum(array_column($search_class, 'present_student_count')) ?> / <?php echo $max_student_count ?></td>
								<td><?php echo array_sum(array_column($search_class, 'payment_student_count')) ?></td>
							</tr>
						
							<?php $i++;
								$bsm_present_student_count = $bsm_present_student_count+array_sum(array_column($search_class, 'present_student_count'));
								$bsm_max_student_count = $bsm_max_student_count+$max_student_count;
								$bsm_payment_student_count = $bsm_payment_student_count+array_sum(array_column($search_class, 'payment_student_count'));
								}
							} ?>
						
					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th>Totals</th>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<th><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"bsm_present_student_count_w$x"} ?> / <?php echo ${"bsm_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"bsm_present_student_count_w$x"} ?> / <?php echo ${"bsm_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<th><?php echo $bsm_present_student_count ?> / <?php echo $bsm_max_student_count ?></th>
							<th><?php echo $bsm_payment_student_count ?></th>
						</tr>
					</tfoot>
					
				</table>
			<?php } ?>

			<?php $g = 0; foreach($kid_morning_result as $e) { 
				$time = explode("-", $e['time_range']);
				if(strtotime($time[0]) > strtotime('11:00') && strtotime($time[1]) > strtotime('11:00')){
					$g++;
				} 
			} 
			if ($g > 0){?>

				<h4 class="mt-4">幼儿（晚班）</h4>
				
				<table class="DTable table">
					<thead>
						<th style="width:30px;">No</th>
						<th>Title</th>
						<th>Tutor</th>
						<th>Time</th>
						<?php
						$days = cal_days_in_month(CAL_GREGORIAN,date('m', strtotime($_GET['month'])),date('Y', strtotime($_GET['month'])));
						$weeks = ceil($days / 7);
						for($i=1; $i <= $weeks; $i++) {
							?>
							<th>Week <?php echo $i; ?></th>
							<?php
						}
						?>
						<th style="width:60px;"> Total</th>
						<th>Paid</th>
					</thead>
					<tbody>
						
						<?php $i = 1; 
						for($x=1; $x <= $weeks; $x++) {
							${"bsn_present_student_count_w$x"} = 0;
							${"bsn_student_count_w$x"} = 0;
						}
						$bsn_present_student_count = 0;
						$bsn_max_student_count = 0;
						$bsn_payment_student_count = 0;
						foreach($kid_morning_result as $e) { 
							$time = explode("-", $e['time_range']);
							if(strtotime($time[0]) > strtotime('11:00') && strtotime($time[1]) > strtotime('11:00')){?>
							
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $e['class_title']; ?> <?php echo $e['sub_class_title']; ?></td>
								<td><?php echo $e['fullname_en']; ?></td>
								<td><?php echo $e['class_day']; ?> <?php echo $e['time_range']; ?></td>
								<?php for($x=1; $x <= $weeks; $x++) { ?>
									<?php $search_class = search($result, 'sub_class_id', $e['sub_class_id']) ?>
									<?php $search_week_number = search($search_class, 'week_number', $x) ?>
									<?php ${"bsn_present_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['present_student_count'] : 0 ?>
									<?php ${"bsn_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['student_count'] : 0 ?>
									<?php $max_student_count = count($search_week_number) > 0 ? $search_week_number[0]['max_student_count'] : 0 ?>
									<?php 
										${"bsn_present_student_count_w$x"} = ${"bsn_present_student_count_w$x"} + ${"bsn_present_student_count_$x"};
										${"bsn_student_count_w$x"} = ${"bsn_student_count_w$x"} + ${"bsn_student_count_$x"}; 
									?>
									<td>
										<?php if ($x == 5) { 
												if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
										?>
											<?php echo ${"bsn_present_student_count_$x"} ?> / <?php echo ${"bsn_student_count_$x"} ?></td>
										<?php } else { ?>
												0 / 0
										
										<?php } ?>
										<?php } else { ?>
											<?php echo ${"bsn_present_student_count_$x"} ?> / <?php echo ${"bsn_student_count_$x"} ?></td>
										<?php } ?>
								<?php } ?>
								<td><?php echo array_sum(array_column($search_class, 'present_student_count')) ?> / <?php echo $max_student_count ?></td>
								<td><?php echo array_sum(array_column($search_class, 'payment_student_count')) ?></td>
							</tr>
						
							<?php $i++;
								$bsn_present_student_count = $bsn_present_student_count+array_sum(array_column($search_class, 'present_student_count'));
								$bsn_max_student_count = $bsn_max_student_count+$max_student_count;
								$bsn_payment_student_count = $bsn_payment_student_count+array_sum(array_column($search_class, 'payment_student_count'));
								
							}
						} ?>
						
					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th>Totals</th>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<th><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"bsn_present_student_count_w$x"} ?> / <?php echo ${"bsn_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"bsn_present_student_count_w$x"} ?> / <?php echo ${"bsn_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<th><?php echo $bsn_present_student_count ?> / <?php echo $bsn_max_student_count ?></th>
							<th><?php echo $bsn_payment_student_count ?></th>
						</tr>
					</tfoot>
				</table>
				<?php } ?>
			<?php } ?>

			<?php if (count($kid_morning_result) > 0){
				$a = 0; foreach($kid_morning_result as $e) { 
				$time = explode("-", $e['time_range']);
				if(strtotime($time[0]) < strtotime('11:00') && strtotime($time[1]) < strtotime('11:00')){
					$a++;	
				} 
			} 
			if ($a > 0){?>
				<h4>小学（早班）</h4>
			
				<table class="DTable table">
					<thead>
						<th style="width:10%">No</th>
						<th>Title</th>
						<th>Tutor</th>
						<th>Time</th>
						<?php
						$days = cal_days_in_month(CAL_GREGORIAN,date('m', strtotime($_GET['month'])),date('Y', strtotime($_GET['month'])));
						$weeks = ceil($days / 7);
						for($i=1; $i <= $weeks; $i++) {
							?>
							<th>Week <?php echo $i; ?></th>
							<?php
						}
						?>
						<th>Total</th>
						<th>Paid</th>
					</thead>
					<tbody>
						
						<?php $i = 1; 
						for($x=1; $x <= $weeks; $x++) {
							${"psm_present_student_count_w$x"} = 0;
							${"psm_student_count_w$x"} = 0;
						}
						$psm_present_student_count = 0;
						$psm_max_student_count = 0;
						$psm_payment_student_count = 0;
						foreach($kid_morning_result as $e) { 
							$time = explode("-", $e['time_range']);
							if(strtotime($time[0]) < strtotime('11:00') && strtotime($time[1]) < strtotime('11:00')){?>
							
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $e['class_title']; ?> <?php echo $e['sub_class_title']; ?></td>
								<td><?php echo $e['fullname_en']; ?></td>
								<td><?php echo $e['class_day']; ?> <?php echo $e['time_range']; ?></td>
								<?php for($x=1; $x <= $weeks; $x++) { ?>
									<?php $search_class = search($result, 'sub_class_id', $e['sub_class_id']);  ?>
									<?php $search_week_number = search($search_class, 'week_number', $x) ?>
									<?php ${"psm_present_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['present_student_count'] : 0 ?>
									<?php ${"psm_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['student_count'] : 0 ?>
									<?php $max_student_count = count($search_week_number) > 0 ? $search_week_number[0]['max_student_count'] : 0 ?>
									<?php 
										${"psm_present_student_count_w$x"} = ${"psm_present_student_count_w$x"} + ${"psm_present_student_count_$x"};
										${"psm_student_count_w$x"} = ${"psm_student_count_w$x"} + ${"psm_student_count_$x"}; 
									?>
									<td>
										<?php if ($x == 5) { 
												if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
										?>
											<?php echo ${"psm_present_student_count_$x"} ?> / <?php echo ${"psm_student_count_$x"} ?></td>
										<?php } else { ?>
												0 / 0
										
										<?php } ?>
										<?php } else { ?>
											<?php echo ${"psm_present_student_count_$x"} ?> / <?php echo ${"psm_student_count_$x"} ?></td>
										<?php } ?>
								<?php } ?>
								<td><?php echo array_sum(array_column($search_class, 'present_student_count')) ?> / <?php echo $max_student_count ?></td>
								<td><?php echo array_sum(array_column($search_class, 'payment_student_count')) ?></td>
							</tr>
						
							<?php $i++;
								$psm_present_student_count = $psm_present_student_count+array_sum(array_column($search_class, 'present_student_count'));
								$psm_max_student_count = $psm_max_student_count+$max_student_count;
								$psm_payment_student_count = $psm_payment_student_count+array_sum(array_column($search_class, 'payment_student_count'));
								}
							} ?>
						
					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th>Totals</th>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<th><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"psm_present_student_count_w$x"} ?> / <?php echo ${"psm_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"psm_present_student_count_w$x"} ?> / <?php echo ${"psm_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<th><?php echo $psm_present_student_count ?> / <?php echo $psm_max_student_count ?></th>
							<th><?php echo $psm_payment_student_count ?></th>
						</tr>
					</tfoot>
					
				</table>
			<?php } ?>
			
			<?php $b = 0; foreach($kid_morning_result as $e) { 
				$time = explode("-", $e['time_range']);
				if(strtotime($time[0]) > strtotime('11:00') && strtotime($time[1]) > strtotime('11:00')){
					$b++;
				} 
			} 
			if ($b > 0){?>
				
				<h4 class="mt-4">小学（晚班）</h4>
				
				<table class="DTable table">
					<thead>
						<th style="width:30px;">No</th>
						<th>Title</th>
						<th>Tutor</th>
						<th>Time</th>
						<?php
						$days = cal_days_in_month(CAL_GREGORIAN,date('m', strtotime($_GET['month'])),date('Y', strtotime($_GET['month'])));
						$weeks = ceil($days / 7);
						for($i=1; $i <= $weeks; $i++) {
							?>
							<th>Week <?php echo $i; ?></th>
							<?php
						}
						?>
						<th style="width:60px;"> Total</th>
						<th>Paid</th>
					</thead>
					<tbody>
						
						<?php $i = 1; 
						for($x=1; $x <= $weeks; $x++) {
							${"psn_present_student_count_w$x"} = 0;
							${"psn_student_count_w$x"} = 0;
						}
						$psn_present_student_count = 0;
						$psn_max_student_count = 0;
						$psn_payment_student_count = 0;
						foreach($kid_morning_result as $e) { 
							$time = explode("-", $e['time_range']);
							if(strtotime($time[0]) > strtotime('11:00') && strtotime($time[1]) > strtotime('11:00')){?>
							
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $e['class_title']; ?> <?php echo $e['sub_class_title']; ?></td>
								<td><?php echo $e['fullname_en']; ?></td>
								<td><?php echo $e['class_day']; ?> <?php echo $e['time_range']; ?></td>
								<?php for($x=1; $x <= $weeks; $x++) { ?>
									<?php $search_class = search($result, 'sub_class_id', $e['sub_class_id']) ?>
									<?php $search_week_number = search($search_class, 'week_number', $x) ?>
									<?php ${"psn_present_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['present_student_count'] : 0 ?>
									<?php ${"psn_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['student_count'] : 0 ?>
									<?php $max_student_count = count($search_week_number) > 0 ? $search_week_number[0]['max_student_count'] : 0 ?>
									<?php 
										${"psn_present_student_count_w$x"} = ${"psn_present_student_count_w$x"} + ${"psn_present_student_count_$x"};
										${"psn_student_count_w$x"} = ${"psn_student_count_w$x"} + ${"psn_student_count_$x"}; 
									?>
									<td>
										<?php if ($x == 5) { 
												if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
										?>
											<?php echo ${"psn_present_student_count_$x"} ?> / <?php echo ${"psn_student_count_$x"} ?></td>
										<?php } else { ?>
												0 / 0
										
										<?php } ?>
										<?php } else { ?>
											<?php echo ${"psn_present_student_count_$x"} ?> / <?php echo ${"psn_student_count_$x"} ?></td>
										<?php } ?>
								<?php } ?>
								<td><?php echo array_sum(array_column($search_class, 'present_student_count')) ?> / <?php echo $max_student_count ?></td>
								<td><?php echo array_sum(array_column($search_class, 'payment_student_count')) ?></td>
							</tr>
						
							<?php $i++;
								$psn_present_student_count = $psn_present_student_count+array_sum(array_column($search_class, 'present_student_count'));
								$psn_max_student_count = $psn_max_student_count+$max_student_count;
								$psn_payment_student_count = $psn_payment_student_count+array_sum(array_column($search_class, 'payment_student_count'));
								
							}
						} ?>
						
					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th>Totals</th>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<th><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"psn_present_student_count_w$x"} ?> / <?php echo ${"psn_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"psn_present_student_count_w$x"} ?> / <?php echo ${"psn_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<th><?php echo $psn_present_student_count ?> / <?php echo $psn_max_student_count ?></th>
							<th><?php echo $psn_payment_student_count ?></th>
						</tr>
					</tfoot>
				</table>
				<?php } ?>
			<?php } ?>
			<?php if (count($mid_morning_result )> 0){
			
				$c = 0; foreach($mid_morning_result as $e) { 
				$time = explode("-", $e['time_range']);
				if(strtotime($time[0]) < strtotime('11:00') && strtotime($time[1]) < strtotime('11:00')){
					$c++;	
				} 
			} 
			if ($c > 0){?>
			
				<h4 class="mt-4" >中学（早班）</h4>
				
				<table class="DTable table">
					<thead>
						<th style="width:10%">No</th>
						<th>Title</th>
						<th>Tutor</th>
						<th>Time</th>
						<?php
						$days = cal_days_in_month(CAL_GREGORIAN,date('m', strtotime($_GET['month'])),date('Y', strtotime($_GET['month'])));
						$weeks = ceil($days / 7);
						for($i=1; $i <= $weeks; $i++) {
							?>
							<th>Week <?php echo $i; ?></th>
							<?php
						}
						?>
						<th>Total</th>
						<th>Paid</th>
					</thead>
					<tbody>
						
						<?php $i = 1; 
							for($x=1; $x <= $weeks; $x++) {
							${"ssm_present_student_count_w$x"} = 0;
							${"ssm_student_count_w$x"} = 0;
							}
							$ssm_present_student_count = 0;
							$ssm_max_student_count = 0;
							$ssm_payment_student_count = 0;
							
							foreach($mid_morning_result as $e) { 
							$time = explode("-", $e['time_range']);
							if(strtotime($time[0]) < strtotime('11:00') && strtotime($time[1]) < strtotime('11:00')){?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $e['class_title']; ?> <?php echo $e['sub_class_title']; ?></td>
								<td><?php echo $e['fullname_en']; ?></td>
								<td><?php echo $e['class_day']; ?> <?php echo $e['time_range']; ?></td>
								<?php for($x=1; $x <= $weeks; $x++) { ?>
									<?php $search_class = search($result, 'sub_class_id', $e['sub_class_id']);  ?>
									<?php $search_week_number = search($search_class, 'week_number', $x);									?>
									<?php ${"ssm_present_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['present_student_count'] : 0 ?>
									<?php ${"ssm_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['student_count'] : 0 ?>
									<?php $max_student_count = count($search_week_number) > 0 ? $search_week_number[0]['max_student_count'] : 0 ?>
									<?php 
										${"ssm_present_student_count_w$x"} = ${"ssm_present_student_count_w$x"} + ${"ssm_present_student_count_$x"};
										${"ssm_student_count_w$x"} = ${"ssm_student_count_w$x"} + ${"ssm_student_count_$x"}; 
									?>
									<td>
										<?php if ($x == 5) { 
												if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
										?>
											<?php echo ${"ssm_present_student_count_$x"} ?> / <?php echo ${"ssm_student_count_$x"} ?></td>
										<?php } else { ?>
												0 / 0
										
										<?php } ?>
										<?php } else { ?>
											<?php echo ${"ssm_present_student_count_$x"} ?> / <?php echo ${"ssm_student_count_$x"} ?></td>
										<?php } ?>
								<?php } ?>
								<td><?php echo array_sum(array_column($search_class, 'present_student_count')) ?> / <?php echo $max_student_count ?></td>
								<td><?php echo array_sum(array_column($search_class, 'payment_student_count')) ?></td>
							</tr>
						
							<?php $i++;
								$ssm_present_student_count = $ssm_present_student_count+array_sum(array_column($search_class, 'present_student_count'));
								$ssm_max_student_count = $ssm_max_student_count+$max_student_count;
								$ssm_payment_student_count = $ssm_payment_student_count+array_sum(array_column($search_class, 'payment_student_count'));
								}
								} ?>
						
					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th>Totals</th>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<th><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"ssm_present_student_count_w$x"} ?> / <?php echo ${"ssm_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"ssm_present_student_count_w$x"} ?> / <?php echo ${"ssm_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<th><?php echo $ssm_present_student_count ?> / <?php echo $ssm_max_student_count ?></th>
							<th><?php echo $ssm_payment_student_count ?></th>
						</tr>
					</tfoot>
				</table>
			<?php } ?>
			
			<?php $d = 0; foreach($mid_morning_result as $e) { 
				$time = explode("-", $e['time_range']);
				if(strtotime($time[0]) > strtotime('11:00') && strtotime($time[1]) > strtotime('11:00')){
					$d++;
				} 
			} 
			if ($d > 0){?>
			
				<h4 class="mt-4" >中学（晚班）</h4>
				
				<table class="DTable table">
					<thead>
						<th style="width:10%">No</th>
						<th>Title</th>
						<th>Tutor</th>
						<th>Time</th>
						<?php
						$days = cal_days_in_month(CAL_GREGORIAN,date('m', strtotime($_GET['month'])),date('Y', strtotime($_GET['month'])));
						$weeks = ceil($days / 7);
						for($i=1; $i <= $weeks; $i++) {
							?>
							<th>Week <?php echo $i; ?></th>
							<?php
						}
						?>
						<th>Total</th>
						<th>Paid</th>
					</thead>
					<tbody>
						
						<?php $i = 1; 
							for($x=1; $x <= $weeks; $x++) {
								${"ssn_present_student_count_w$x"} = 0;
								${"ssn_student_count_w$x"} = 0;
							}
							$ssn_present_student_count = 0;
							$ssn_max_student_count = 0;
							$ssn_payment_student_count = 0;
							foreach($mid_morning_result as $e) { 
							$time = explode("-", $e['time_range']);
							if(strtotime($time[0]) > strtotime('11:00') && strtotime($time[1]) > strtotime('11:00')){?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $e['class_title']; ?> <?php echo $e['sub_class_title']; ?></td>
								<td><?php echo $e['fullname_en']; ?></td>
								<td><?php echo $e['class_day']; ?> <?php echo $e['time_range']; ?></td>
								<?php for($x=1; $x <= $weeks; $x++) { ?>
									<?php $search_class = search($result, 'sub_class_id', $e['sub_class_id']) ?>
									<?php $search_week_number = search($search_class, 'week_number', $x) ?>
									<?php ${"ssn_present_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['present_student_count'] : 0 ?>
									<?php ${"ssn_student_count_$x"} = count($search_week_number) > 0 ? $search_week_number[0]['student_count'] : 0 ?>
									<?php $max_student_count = count($search_week_number) > 0 ? $search_week_number[0]['max_student_count'] : 0 ?>
									<?php 
										${"ssn_present_student_count_w$x"} = ${"ssn_present_student_count_w$x"} + ${"ssn_present_student_count_$x"};
										${"ssn_student_count_w$x"} = ${"ssn_student_count_w$x"} + ${"ssn_student_count_$x"}; 
									?>
									<td>
										<?php if ($x == 5) { 
												if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
										?>
											<?php echo ${"ssn_present_student_count_$x"} ?> / <?php echo ${"ssn_student_count_$x"} ?></td>
										<?php } else { ?>
												0 / 0
										
										<?php } ?>
										<?php } else { ?>
											<?php echo ${"ssn_present_student_count_$x"} ?> / <?php echo ${"ssn_student_count_$x"} ?></td>
										<?php } ?>
								<?php } ?>
								<td><?php echo array_sum(array_column($search_class, 'present_student_count')) ?> / <?php echo $max_student_count ?></td>
								<td><?php echo array_sum(array_column($search_class, 'payment_student_count')) ?></td>
							</tr>
						
							<?php $i++;
								$ssn_present_student_count = $ssn_present_student_count+array_sum(array_column($search_class, 'present_student_count'));
								$ssn_max_student_count = $ssn_max_student_count+$max_student_count;
								$ssn_payment_student_count = $ssn_payment_student_count+array_sum(array_column($search_class, 'payment_student_count'));
								}
							} ?>
						
					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th>Totals</th>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<th><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"ssn_present_student_count_w$x"} ?> / <?php echo ${"ssn_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"ssn_present_student_count_w$x"} ?> / <?php echo ${"ssn_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<th><?php echo $ssn_present_student_count ?> / <?php echo $ssn_max_student_count ?></th>
							<th><?php echo $ssn_payment_student_count ?></th>
						</tr>
					</tfoot>
				</table>
				<?php } ?>
			<?php } ?>
			
			<!-- Tan Jing Suan -->
			<?php if ($a > 0 || $b > 0 || $c > 0 || $d > 0 || $f > 0 || $g > 0){?>
			
			<h4 class="mt-4" >统计</h4>
			
			<table class="DTable table">
					<thead>
						<th style="width:10%">No</th>
						<th>Title</th>
						<?php
						$days = cal_days_in_month(CAL_GREGORIAN,date('m', strtotime($_GET['month'])),date('Y', strtotime($_GET['month'])));
						$weeks = ceil($days / 7);
						for($i=1; $i <= $weeks; $i++) {
							?>
							<th>Week <?php echo $i; ?></th>
							<?php
						}
						?>
						<th>Total</th>
						<th>Paid</th>
					</thead>
					<tbody>
						<!-- Tan Jing Suan -->
						<?php $i = 0; if($f> 0){ $i++?>
						<tr>
							<td><?php echo $i ?></td>
							<td>幼儿（早班）</td>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<td><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"bsm_present_student_count_w$x"} ?> / <?php echo ${"bsm_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"bsm_present_student_count_w$x"} ?> / <?php echo ${"bsm_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<td><?php echo $bsm_present_student_count ?> / <?php echo $bsm_max_student_count ?></td>
							<td><?php echo $bsm_payment_student_count ?></td>
						</tr>
						<?php } ?>
						<?php if($g> 0){ $i++?>
						<tr>
							<td><?php echo $i ?></td>
							<td>幼儿（晚班）</td>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<td><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"bsn_present_student_count_w$x"} ?> / <?php echo ${"bsn_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"bsn_present_student_count_w$x"} ?> / <?php echo ${"bsn_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<td><?php echo $bsn_present_student_count ?> / <?php echo $bsn_max_student_count ?></td>
							<td><?php echo $bsn_payment_student_count ?></td>
						</tr>
						<?php } ?>
						<?php $i = 0; if($a> 0){ $i++?>
						<tr>
							<td><?php echo $i ?></td>
							<td>小学（早班）</td>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<td><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"psm_present_student_count_w$x"} ?> / <?php echo ${"psm_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"psm_present_student_count_w$x"} ?> / <?php echo ${"psm_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<td><?php echo $psm_present_student_count ?> / <?php echo $psm_max_student_count ?></td>
							<td><?php echo $psm_payment_student_count ?></td>
						</tr>
						<?php } ?>
						<?php if($b> 0){ $i++?>
						<tr>
							<td><?php echo $i ?></td>
							<td>小学（晚班）</td>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<td><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"psn_present_student_count_w$x"} ?> / <?php echo ${"psn_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"psn_present_student_count_w$x"} ?> / <?php echo ${"psn_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<td><?php echo $psn_present_student_count ?> / <?php echo $psn_max_student_count ?></td>
							<td><?php echo $psn_payment_student_count ?></td>
						</tr>
						<?php } ?>
						<?php if($c> 0){ $i++?>
						<tr>
							<td><?php echo $i ?></td>
							<td>中学（早班）</td>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<td><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"ssm_present_student_count_w$x"} ?> / <?php echo ${"ssm_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"ssn_present_student_count_w$x"} ?> / <?php echo ${"ssm_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<td><?php echo $ssm_present_student_count ?> / <?php echo $ssm_max_student_count ?></td>
							<td><?php echo $ssm_payment_student_count ?></td>
						</tr>
						<?php } ?>
						<?php if($d> 0){ $i++?>
						<tr>
							<td><?php echo $i ?></td>
							<td>中学（晚班）</td>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<td><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<?php echo ${"ssn_present_student_count_w$x"} ?> / <?php echo ${"ssn_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<?php echo ${"ssn_present_student_count_w$x"} ?> / <?php echo ${"ssn_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<td><?php echo $ssn_present_student_count ?> / <?php echo $ssn_max_student_count ?></td>
							<td><?php echo $ssn_payment_student_count ?></td>
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th>Totals</th>
							<?php for($x=1; $x <= $weeks; $x++) { ?>
								<th><?php if ($x == 5) { 
										if ((array_sum(array_column($search_class, 'student_count')) == $max_student_count)) {
								?>
								<!-- Tan Jing Suan -->
								<!-- <?php echo ${"psm_present_student_count_w$x"} + ${"psn_present_student_count_w$x"} + ${"ssm_present_student_count_w$x"} + ${"ssn_present_student_count_w$x"} ?> -->
								<!-- / <?php echo ${"psm_student_count_w$x"} + ${"psn_student_count_w$x"} + ${"ssm_student_count_w$x"} + ${"ssn_student_count_w$x"} ?></td> -->
								<?php echo ${"bsm_present_student_count_w$x"} + ${"bsn_present_student_count_w$x"} +${"psm_present_student_count_w$x"} + ${"psn_present_student_count_w$x"} + ${"ssm_present_student_count_w$x"} + ${"ssn_present_student_count_w$x"} ?>
								/ <?php echo ${"bsm_student_count_w$x"} + ${"bsn_student_count_w$x"} + ${"psm_student_count_w$x"} + ${"psn_student_count_w$x"} + ${"ssm_student_count_w$x"} + ${"ssn_student_count_w$x"} ?></td>
								<?php } else { ?>
										0 / 0			
								<?php } ?>
								<?php } else { ?>
										<!-- Tan Jing Suan -->
										<!-- <?php echo ${"psm_present_student_count_w$x"} + ${"psn_present_student_count_w$x"} + ${"ssm_present_student_count_w$x"} + ${"ssn_present_student_count_w$x"} ?>  -->
								<!-- / <?php echo ${"psm_student_count_w$x"} + ${"psn_student_count_w$x"} + ${"ssm_student_count_w$x"} + ${"ssn_student_count_w$x"} ?></td> -->
										<?php echo ${"bsm_present_student_count_w$x"} + ${"bsn_present_student_count_w$x"} + ${"psm_present_student_count_w$x"} + ${"psn_present_student_count_w$x"} + ${"ssm_present_student_count_w$x"} + ${"ssn_present_student_count_w$x"} ?> 
								/ <?php echo ${"bsm_student_count_w$x"} + ${"bsn_student_count_w$x"} + ${"psm_student_count_w$x"} + ${"psn_student_count_w$x"} + ${"ssm_student_count_w$x"} + ${"ssn_student_count_w$x"} ?></td>
								<?php } ?>
							<?php } ?>
							<!-- Tan Jing Suan -->
							<!-- <th><?php echo $psm_present_student_count  + $psn_present_student_count +  $ssm_present_student_count + $ssn_present_student_count ?> / <?php echo $psm_max_student_count + $psn_max_student_count + $ssm_max_student_count + $ssn_max_student_count ?></th> -->
							<!-- <th><?php echo $psm_payment_student_count + $psn_payment_student_count + $ssm_payment_student_count + $ssn_payment_student_count ?></th> -->
							<th><?php echo $bsm_present_student_count  + $bsn_present_student_count +  $psm_present_student_count  + $psn_present_student_count +  $ssm_present_student_count + $ssn_present_student_count ?> / <?php echo $psm_max_student_count + $psn_max_student_count + $ssm_max_student_count + $ssn_max_student_count ?></th>
							<th><?php echo $bsm_payment_student_count + $bsn_payment_student_count + $psm_payment_student_count + $psn_payment_student_count + $ssm_payment_student_count + $ssn_payment_student_count ?></th>
						</tr>
					</tfoot>
			</table>
			<?php }else{ ?>
				<h6 class="text-center">Data list Empty</h6>
			<?php } ?>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>