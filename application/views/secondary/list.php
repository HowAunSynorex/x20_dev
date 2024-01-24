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
					<?php if( check_module('Secondary/Create') ) { ?>
						<a href="<?php echo base_url($thispage['group'] . '/add/' . $thispage['type']); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
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
                    <?php
					switch ($thispage['type']) {
						case 'school':
							echo '<th>Student(s)</th>';
							break;

						case 'course':
							echo '<th>Class(s)</th>';
							break;
							
						case 'item_cat':
							echo '<th>Item(s)</th>';
							break;
							
						case 'childcare':
						case 'transport':
							echo '<th>Price</th>';
							break;

						case 'payment_method':
							echo '<th>Method ID</th>';
							break;
					}
                    ?>
                    <th>Remark</th>
                </thead>
                <tbody>
					<?php $i=0; foreach($null as $e) { $i++; ?>
					<tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $e['title']; ?></td>
						<td>
							<label class="switch default-item">
								<input type="checkbox" <?php if(!empty($this->log_join_model->list('secondary', branch_now('pid'), ['secondary' => $e['pid']]))) { if($this->log_join_model->list('secondary', branch_now('pid'), ['secondary' => $e['pid']])[0]['active'] == 1) { echo 'checked'; }} ?> data-id="<?php echo $e['pid']; ?>" data-type="<?php echo datalist('secondary_type')[$thispage['type']]['single']; ?>" <?php if( !check_module('Secondary/Update') ) { echo 'disabled'; } ?>>
								<span class="slider round"></span>
							</label>
						</td>
						<?php
                        switch ($thispage['type']) {
                         
							case 'payment_method': 
                                ?>
                                <td><?php echo $e['method_id']; ?></td><?php
                                break;

							case 'childcare':  
							case 'transport':  
                                ?>
								<td>
									<?php echo $e['price']; ?>
								</td><?php
                                break;
								
                        }
                        ?>
                        <td><?php echo empty($e['remark']) ? '-' : $e['remark'] ; ?></td>
                    </tr>
                    <?php } ?>
					
                    <?php foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
							<?php if( check_module('Secondary/Update') ) { ?>
								<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
							<?php } ?>
								<?php echo $e['title']; ?>
							<?php if( check_module('Secondary/Update') ) { ?>
								</a>
							<?php } ?>
						</td>
						<?php
						switch ($thispage['type']) {
                            case 'payment_method':  
                                ?>
								<td>
									<label class="switch branch-item">
										<input type="checkbox" <?php if($e['active'] == 1) echo 'checked'; ?> data-id="<?php echo $e['pid']; ?>" data-type="<?php echo datalist('secondary_type')[$thispage['type']]['single']; ?>" <?php if( !check_module('Secondary/Update') ) { echo 'disabled'; } ?>>
										<span class="slider round"></span>
									</label>
								</td><?php
                                break;
							
							case 'item_cat':  
                                ?>
								<td>
									<label class="switch branch-item">
										<input type="checkbox" <?php if($e['active'] == 1) echo 'checked'; ?> data-id="<?php echo $e['pid']; ?>" data-type="<?php echo datalist('secondary_type')[$thispage['type']]['single']; ?>" <?php if( !check_module('Secondary/Update') ) { echo 'disabled'; } ?>>
										<span class="slider round"></span>
									</label>
								</td><?php
                                break;
								
							case 'bank':  
                                ?>
								<td>
									<label class="switch branch-item">
										<input type="checkbox" <?php if($e['active'] == 1) echo 'checked'; ?> data-id="<?php echo $e['pid']; ?>" data-type="<?php echo datalist('secondary_type')[$thispage['type']]['single']; ?>" <?php if( !check_module('Secondary/Update') ) { echo 'disabled'; } ?>>
										<span class="slider round"></span>
									</label>
								</td><?php
                                break;
								
                            default:
                                ?><td><?php echo badge($e['active']); ?></td><?php
                                break;
						}
						?>
                        <?php
                        switch ($thispage['type']) {
                            case 'school':  
                                ?>
                                <td><?php echo count($this->tbl_users_model->total_student($e['pid'], branch_now('pid'))); ?></td><?php
                                break;

							case 'childcare':  
							case 'transport':  
                                ?>
								<td>
									<?php echo $e['price']; ?>
								</td><?php
                                break;
								
                            case 'course':
                                ?>
                                <td><?php echo count($this->tbl_classes_model->total_class($e['pid'], branch_now('pid'))); ?></td><?php
                                break;
								
							case 'item_cat':
                                ?>
                                <td><?php echo count($this->tbl_inventory_model->list(branch_now('pid'), 'item', ['category' => $e['pid']])); ?></td><?php
                                break;
							
							case 'bank': 
                                break;

							case 'payment_method': 
                                ?>
                                <td><?php echo $e['method_id']; ?></td><?php
                                break;

                        }
                        ?>
                        <td><?php echo empty($e['remark']) ? '-' : $e['remark'] ; ?></td>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>