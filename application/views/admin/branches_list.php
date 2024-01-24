<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar_admin', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar_admin'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
                </div>
                <div class="col-6 my-auto text-right">
                    <!--<a href="<?php echo base_url($thispage['group'] . '/add/'); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>-->
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			<form url="<?php echo base_url('admin/branches_list'); ?>">
				<input type='hidden' name="search" />
				<div class="form-group">
					<div class="custom-control custom-checkbox d-flex align-items-center">
						<input type="checkbox" class="custom-control-input" id="show-active" name="active" <?php
						if(!isset($_GET['search'])) {
							echo 'checked';
						} else {
							if(isset($_GET['active'])) {
								echo 'checked';
							}
						}
						?>>
						<label class="custom-control-label" for="show-active">Show Active only</label>
					</div>
				</div>
			</form>
            
			<div class="table-responsive">
				<table class="DTable table table-hover table-sm">
					<thead>
						<th>#</th>
						<th>Title</th>
						<th>Status</th>
						<th>Last Online</th>
						<th>Last Online (Cal)</th>
						<th>Student(s)</th>
						<th>Payment(s)</th>
						<th>Admin(s)</th>
						<th>Country</th>
						<th>Currency</th>
						<th>Plan</th>
						<th>Expired Date</th>
						<th>Expired Days</th>
					</thead>
					<tbody>
						<?php
						$i=1;
						
						foreach($result as $e) {
						
							$expired_date_day = empty($e['expired_date']) ? '-' : floor(( strtotime($e['expired_date']) - time() ) / 86400) + 1;
							
							if(!isset($_GET['search']) || isset($_GET['active'])) {
								if($expired_date_day > 0) {
									?>
									<?php $table_bg = '';
										if( $e['is_delete'] == 1 ) { 
											$table_bg = 'table-secondary'; } 
										else if ( $expired_date_day > 0 && $expired_date_day <= 14 ) { 
											$table_bg = 'table-warning'; } 
										else if ( $expired_date_day <= 0 ) { 
											$table_bg = 'table-danger'; 
										}  
									?>
									<tr class="<?php echo $table_bg; ?>">
										<td><?php echo $i; ?></td>
										<td>
											<div class="media">
												<a href="<?php echo base_url($this->group . '/branches_edit/' . $e['pid']); ?>">
													<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="mr-2 my-auto rounded border" style="height: 55px; width: 55px; object-fit: cover;">
												</a>
												<div class="media-body">
													<a href="<?php echo base_url($this->group . '/branches_edit/' . $e['pid']); ?>"><?php echo $e['title']; ?></a>
													<span class="text-muted small d-block">Owner: <?php echo empty($e['owner']) ? '-' : datalist_Table('tbl_admins', 'nickname', $e['owner']); ?></span>
													<span class="text-muted small d-block">Branch ID: <?php echo '#'.$e['pid']; ?></span>
												</div>
											</div>
										</td>
										<td data-order="<?php echo datalist('branch_status_sort')[$table_bg]; ?>">
											<?php
												if($e['is_delete'] == 1) {
													$color = 'secondary';
													$text = 'Deleted';
												} else if ( $expired_date_day <= 0 ) {
													$color = 'danger';
													$text = 'Expired';
												} else if ( $e['active'] == 1 ) {
													$color = 'success';
													$text = 'Active';
												} else if ( $e['active'] == 0 ) {
													$color = 'dark';
													$text = 'Inactive';
												}
											?>
											<span class="badge badge-<?php echo $color; ?>"><?php echo $text; ?></span>
										</td>
										<td><?php echo $e['last_online']; ?></td>
										<td><?php echo time_elapsed_string($e['last_online']); ?></td>
										<td><?php echo count($this->tbl_users_model->list('student', $e['pid'], [ 'active' => 1 ])); ?></td>
										<td><?php echo count($this->tbl_payment_model->list($e['pid'])); ?></td>
										<td><?php echo count($this->log_join_model->list_admin([ 'branch' => $e['pid'], 'type' => 'join_branch' ])); ?></td>
										<td><?php echo empty($e['country']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['country']); ?></td>
										<td><?php echo empty($e['currency']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['currency']); ?></td>
										<td><?php echo empty($e['plan']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['plan']); ?></td>
										<td><?php echo empty($e['expired_date']) ? '-' : $e['expired_date']; ?></td>
										<td><?php echo $expired_date_day; ?></td>
									</tr>
									<?php $i++;
								}
							} else {
								?>
								<?php 
									$table_bg = '';
									if( $e['is_delete'] == 1 ) { 
										$table_bg = 'table-secondary'; 
									} 
									else if ( $expired_date_day > 0 && $expired_date_day <= 14 ) { 
										$table_bg = 'table-warning'; } 
									else if ( $expired_date_day <= 0 ) { 
										$table_bg = 'table-danger'; 
									}  
								?>
								<tr class="<?php echo $table_bg; ?>">
									<td><?php echo $i; ?></td>
									<td>
										<div class="media">
											<a href="<?php echo base_url($this->group . '/branches_edit/' . $e['pid']); ?>">
												<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="mr-2 my-auto rounded border" style="height: 55px; width: 55px; object-fit: cover;">
											</a>
											<div class="media-body">
												<a href="<?php echo base_url($this->group . '/branches_edit/' . $e['pid']); ?>"><?php echo $e['title']; ?></a>
												<span class="text-muted small d-block">Owner: <?php echo empty($e['owner']) ? '-' : datalist_Table('tbl_admins', 'nickname', $e['owner']); ?></span>
												<span class="text-muted small d-block">Branch ID: <?php echo '#'.$e['pid']; ?></span>
											</div>
										</div>
									</td>
									<td data-order="<?php echo datalist('branch_status_sort')[$table_bg]; ?>">
										<?php
										if($e['is_delete'] == 1) {
											$color = 'secondary';
											$text = 'Deleted';
										} else if ( $expired_date_day <= 0 ) {
											$color = 'danger';
											$text = 'Expired';
										} else if ( $e['active'] == 1 ) {
											$color = 'success';
											$text = 'Active';
										} else if ( $e['active'] == 0 ) {
											$color = 'dark';
											$text = 'Inactive';
										}
										?>
										<span class="badge badge-<?php echo $color; ?>"><?php echo $text; ?></span>
									</td>
									<td><?php echo $e['last_online']; ?></td>
									<td><?php echo time_elapsed_string($e['last_online']); ?></td>
									<td><?php echo count($this->tbl_users_model->list('student', $e['pid'], [ 'active' => 1 ])); ?></td>
									<td><?php echo count($this->tbl_payment_model->list($e['pid'])); ?></td>
									<td><?php echo count($this->log_join_model->list_admin([ 'branch' => $e['pid'], 'type' => 'join_branch' ])); ?></td>
									<td><?php echo empty($e['country']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['country']); ?></td>
									<td><?php echo empty($e['currency']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['currency']); ?></td>
									<td><?php echo empty($e['plan']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['plan']); ?></td>
									<td><?php echo empty($e['expired_date']) ? '-' : $e['expired_date']; ?></td>
									<td><?php echo $expired_date_day; ?></td>
								</tr>
								<?php $i++;
							}
							
						}
						?>
					</tbody>

				</table>
			</div>
            
        </div>

        <?php $this->load->view('inc/copyright_admin'); ?>

    </div>

</div>