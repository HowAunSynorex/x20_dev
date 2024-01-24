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
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
            <table class="DTable table table-hover">
                <thead>
                    <th style="width:10%">No</th>
                    <th>Name</th>
                    <th style="width:10%">Status</th>
                    <th style="width:10%">Whitelabel</th>
                    <th>Branch(s)</th>
                    <th>Access Branch(s)</th>
                    <th>Email</th>
                    <th>Last Sync</th>
					<th>Last Online</th>
					<th>Last Online (Cal)</th>
                </thead>
                <tbody>
                    
                    <?php $i=0; foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
							<a href="<?php echo base_url('admin/admins_edit/'.$e['pid']); ?>">
								<?php echo $e['nickname']; ?>
							</a>
						</td>
                        <td><?php echo badge($e['active']); ?></td>
                        <td><?php echo !empty($e['password']) ? '<i class="fa fa-fw fa-check-circle text-success"></i>' : '<i class="fa fa-fw fa-times-circle text-danger"></i>' ; ?></td>
                        <td><?php echo count($this->tbl_branches_model->list([ 'create_by' => $e['pid'] ])); ?></td>
						<td>
							<?php
							
							$j = 0;
							$query = $this->log_join_model->list_admin([ 'admin' => $e['pid'], 'type' => 'join_branch' ]);
							foreach($query as $q) {
								if(datalist_Table('tbl_branches', 'is_delete', $q['branch']) == 0) {
									$j++;
								}
							}
							echo $j;
							
							?>
						</td>
                        <td><?php echo $e['username']; ?></td>
                        <td><?php echo $e['update_on']; ?></td>
						<td><?php echo $e['last_online']; ?></td>
						<td><?php echo time_elapsed_string($e['last_online']); ?></td>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright_admin'); ?>

    </div>

</div>