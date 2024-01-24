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
					<?php if( check_module('Settings/Create') ) { ?>
						<a href="<?php echo base_url($thispage['group'] . '/add_whitelabel'); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
					<?php } ?>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
            <table class="DTable table">
                <thead>
                    <th style="width:10%">No</th>
                    <th style="width:35%">Name</th>
                    <th>Status</th>
                    <th>Permission(s)</th>
                    <th>Joined Date</th>
					<?php if( check_module('Settings/Delete') ) { ?>
						<th></th>
					<?php } ?>
                </thead>
                <tbody>
                    
                    <?php 
					$i=0; foreach($result as $e) { $i++; 
					
						$e['permission'] = json_decode($e['permission'], true);
						if(!is_array($e['permission'])) $e['permission'] = [];
						
						if( $e['status'] != 'rejected' ) {
							
							?>
							<tr>
								<td><?php echo $i; ?></td>
								<td>
									<?php
									
									if($e['status'] == 'pending') {
										
										echo $e['email'];
										
									} else {
										
										if( check_module('Settings/Update') ) { 
											?>
											<a href="<?php echo base_url($thispage['group'] . '/edit/' .$e['id']); ?>"><?php
										}
										
										echo datalist_Table('tbl_admins', 'nickname', $e['admin']);
											
										if( check_module('Settings/Update') ) {
											?>
											</a><?php
										}
										
									}
									?>
								</td>
								<td>
									<?php
									
									if($e['status'] == 'pending') {
										
										echo '<span class="badge badge-warning">'.ucfirst($e['status']).'</span>';
										
									} else {
										
										echo '<span class="badge badge-success">Accepted</span>';
										
									}
									?>
								</td>
								<td><?php echo branch_now('owner') == $e['admin'] ? 'Owner' : 'Total '.count( $e['permission'] ).' permission(s)' ; ?></td>
								<td><?php echo $e['create_on']; ?></td>
								<?php if( check_module('Settings/Delete') ) { ?>
									<td>
										<?php if( $e['admin'] != branch_now('owner') ) { ?>
											<a class="btn btn-sm btn-danger" href="javascript:;" onclick="del_ask(<?php echo $e['id']; ?>)" data-toggle="tooltip" title="Remove"><i class="fa fa-fw fa-times py-1"></i></a>
										<?php } ?>
									</td>
								<?php } ?>
							</tr>
							<?php 
							
						}
						
					} 
					?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>