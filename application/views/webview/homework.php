<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid container-wrapper py-3">

	<?php echo alert_get(); ?>
	
	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-1">Upcoming</a>
		</li>
		<li class="nav-item">
			<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-2">Expired</a>
		</li>
	</ul>
	
	<div class="tab-content py-3">
		<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>" id="tab-1">
			<table class="table table-smA table-bordered table-striped">
				<thead>
					<th>Date</th>
					<th style="width:20%">Subject</th>
					<th style="width:10%">Status</th>
					<th>Tutor</th>
					<th>Class</th>
				</thead>
				<tbody>
					<?php
					$i=0;
					foreach($result as $e) {
						$join_class = $this->log_join_model->list('join_class', branch_now('pid'), ["class" => $e['class']]);
						$show = false;
						if(empty($e['student'])) {
							if(count($join_class) > 0) {
								$show = true;
							}
						} else {
							if(count($join_class) > 0 && $e['student'] == $user['pid']) {
								$show = true;
							}
						}
						if($show) {
							$i++;
							?>
							<tr>
								<td><?php echo empty($e['date']) ? '-' : $e['date']; ?></td>
								<td><a href="<?php echo base_url('webview/view_homework/'.$e['pid'].'?token='.$_GET['token']); ?>"><?php echo $e['subject']; ?></a></td>
								<td>
								<?php
									if($e['status'] == 'new') {
										?>
										<span class="badge badge-info"><?php echo ucfirst($e['status']); ?></span>
										<?php 
									} else {
										?>
										<span class="badge badge-success"><?php echo ucfirst($e['status']); ?></span>
										<?php 
									}
								?>
								</td>
								<td><?php echo datalist_Table('tbl_admins', 'nickname', $e['create_by']); ?></td>
								<td><?php echo empty($e['class']) ? '-' : datalist_Table('tbl_classes', 'title', $e['class']); ?></td>
							</tr>
							<?php
						}
					}
					?>
					
					<?php if($i == 0) { ?>
						<tr><td class="text-center" colspan="5">No result found</td></tr></tbody>
					<?php  } ?>
				</tbody>
			</table>
		</div>
		
		<div class="tab-pane fade <?php if( $_GET['tab'] == 2 ) echo 'show active'; ?>" id="tab-2">
			<table class="table table-smA table-bordered table-striped">
				<thead>
					<th>Date</th>
					<th style="width:20%">Subject</th>
					<th style="width:10%">Status</th>
					<th>Tutor</th>
					<th>Class</th>
				</thead>
				<tbody>
					<?php
					$i=0;
					foreach($result_expired as $e) {
						$join_class = $this->log_join_model->list('join_class', branch_now('pid'), ["class" => $e['class']]);
						$show = false;
						if(empty($e['student'])) {
							if(count($join_class) > 0) {
								$show = true;
							}
						} else {
							if(count($join_class) > 0 && $e['student'] == $user['pid']) {
								$show = true;
							}
						}
						if($show) {
							$i++;
							?>
							<tr>
								<td><?php echo empty($e['date']) ? '-' : $e['date']; ?></td>
								<td><a href="<?php echo base_url('webview/view_homework/'.$e['pid'].'?token='.$_GET['token']); ?>"><?php echo $e['subject']; ?></a></td>
								<td>
								<?php
									if($e['status'] == 'new') {
										?>
										<span class="badge badge-info"><?php echo ucfirst($e['status']); ?></span>
										<?php 
									} else {
										?>
										<span class="badge badge-success"><?php echo ucfirst($e['status']); ?></span>
										<?php 
									}
								?>
								</td>
								<td><?php echo datalist_Table('tbl_admins', 'nickname', $e['create_by']); ?></td>
								<td><?php echo empty($e['class']) ? '-' : datalist_Table('tbl_classes', 'title', $e['class']); ?></td>
							</tr>
							<?php
						}
					}
					?>
					
					<?php if($i == 0) { ?>
						<tr><td class="text-center" colspan="5">No result found</td></tr></tbody>
					<?php  } ?>
				</tbody>
			</table>
		</div>
	</div>
	
</div>