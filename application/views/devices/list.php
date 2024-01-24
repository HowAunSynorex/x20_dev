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
					<?php if ( check_module('Inventory/Create') ) { ?>
						<a href="<?php echo base_url( $thispage['group'] . '/add'); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
					<?php } ?>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
            <table class="DTable table">
                <thead>
                    <th style="width:10%">No</th>
                    <th style="width:35%">Title</th>
                    <th style="width:10%">Status</th>
                    <th>Type</th>
                    <th>Last Online</th>
                </thead>
                <tbody>
                    
                    <?php $i=0; foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
							<?php //if ( check_module('Inventory/Update') ) { ?>
								<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
							<?php //} ?>
								<?php echo $e['title']; ?>
							<?php //if ( check_module('Inventory/Update') ) { ?>
								</a>
							<?php //} ?>
						</td>
                        <td>
						<?php
						if( $e['active'] == 1 ) {
							?>
							<span class="badge badge-success">Active</span>
							<?php
						} else {
							?>
							<span class="badge badge-dark">Inactive</span>
							<?php
						}/* else if (!empty($e['otp']) && ($e['active'] == 1)) {
							?>
							<span class="badge badge-warning">Pending</span>
							<?php
						}*/
						?>
						</td>
                        <td><?php echo empty($e['type']) ? '-' : datalist('device_type')[$e['type']]['label']; ?></td>
                        <td><?php echo empty($e['last_online']) ? '-' : $e['last_online']; ?></td>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>