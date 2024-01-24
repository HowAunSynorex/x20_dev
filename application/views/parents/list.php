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
                    <?php if( check_module('Parents/Create') ) { ?>
						<a href="<?php echo base_url($thispage['group'] . '/add'); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
					<?php } ?>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
			<div class="table-responsive">
				<table class="DTable table">
					<thead>
						<th style="width:7%">No</th>
						<th style="width:20%">Name</th>
						<th style="width:10%">Status</th>
						<th>Gender</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Child(s)</th>
					</thead>
					<tbody>
						
						<?php
						$this->load->model('tbl_users_model');
						$i=0; foreach($result as $e) { $i++; ?>
						<tr>
							<td><?php echo $i; ?></td>
							<td>
								<div class="media">
									<?php if( check_module('Parents/Update') ) { ?>
										<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
											<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="mr-2 rounded-circle border" style="height: 85px; width: 85px; object-fit: cover">
										</a>
										
										<div class="media-body my-auto">
											<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
												<?php echo $e['fullname_en']; ?>
												<br>
												<?php echo $e['fullname_cn']; ?>
											</a>
										</div>
									<?php } else { ?>
										<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="mr-2 rounded-circle border" style="height: 85px; width: 85px; object-fit: cover">
										
										<div class="media-body my-auto">
											<?php echo $e['fullname_en']; ?>
											<br>
											<?php echo $e['fullname_cn']; ?>
										</div>
									<?php } ?>
									
								</div>
							</td>
							<td><?php echo badge($e['active']); ?></td>
							<td><?php echo ucfirst($e['gender']); ?></td>
							<td><?php echo empty($e['email']) ? '-' : $e['email']; ?></td>
							<td><?php echo empty($e['phone']) ? '-' : $e['phone']; ?></td>
							<td>
								<a href="<?php echo base_url('students/list/?parent='.$e['pid']); ?>">
									<?php echo $e['childs']; ?>
								</a>
							</td>
						</tr>
						<?php } ?>
						
					</tbody>
				</table>
			</div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>