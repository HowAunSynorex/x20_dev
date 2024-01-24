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
                    <a href="<?php echo base_url($thispage['group'] . '/add'); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			
			<div class="table-responsive">
				<table class="DTable table">
					<thead>
						<th>#</th>
						<th>Title</th>
						<th>Status</th>
						<th>Student(s)</th>
						<th>Payment(s)</th>
						<th>Admin(s)</th>
					</thead>
					<tbody>
						<?php
						$i=1;
						foreach($result as $e) {
							?>
							<tr>
								<td><?php echo $i; ?></td>
								<td>
									<div class="media">
										<a href="<?php echo base_url($this->group . '/edit/' . $e['pid']); ?>">
											<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="mr-2 my-auto rounded border" style="height: 55px; width: 55px; object-fit: cover;">
										</a>
										<div class="media-body">
											<a href="<?php echo base_url($this->group . '/edit/' . $e['pid']); ?>"><?php echo $e['title']; ?></a>
										</div>
									</div>
								</td>
								<td><?php echo badge($e['active']); ?></span></td>
								<td><?php echo count($this->tbl_users_model->list('student', $e['pid'], [ 'active' => 1 ])); ?></td>
								<td><?php echo count($this->tbl_payment_model->list($e['pid'])); ?></td>
								<td><?php echo count($this->log_join_model->list_admin([ 'branch' => $e['pid'], 'type' => 'join_branch' ])); ?></td>
							</tr>
							<?php 
							$i++;
							
						}
						?>
					</tbody>
				</table>
			</div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>