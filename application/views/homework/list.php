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
                    <?php
					if( check_module('Homework/Create') ) {
						?>
						<a href="<?php echo base_url($thispage['group'] . '/add'); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
						<?php
					}
                    ?>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			
            <table class="DTable table">
                <thead>
                    <th style="width:10%">No</th>
					<th style="width:20%">Subject</th>
                    <th style="width:10%">Status</th>
					<th>Date</th>
					<th>Tutor</th>
                    <th>Class</th>
                    <th>Student</th>
                </thead>
                <tbody>
                    <?php
                    $i=0; foreach($result as $e) { $i++;
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
							<td>
								<?php if( check_module('Homework/Update') ) { ?>
									<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
								<?php } ?>
									<?php echo $e['subject']; ?>
								<?php if( check_module('Homework/Update') ) { ?>
									</a>
								<?php } ?>
							</td>
							<td>
								<span class="badge badge-<?php echo datalist('homework_status')[$e['status']]['color']; ?>"><?php echo datalist('homework_status')[$e['status']]['label']; ?></span>	
							</td>
							<td><?php echo empty($e['date']) ? '-' : $e['date']; ?></td>
							<td><?php echo datalist_Table('tbl_admins', 'nickname', $e['create_by']); ?></td>
							<td><a href="<?php echo base_url('classes/edit/'.$e['class']); ?>"><?php echo empty($e['class']) ? '-' : datalist_Table('tbl_classes', 'title', $e['class']); ?></a></td>
							<td>
								<?php
								if(empty($e['student'])) {
									echo '-';
								} else {
									
									if(datalist_Table('tbl_users', 'active', $e['student']) == 1 && datalist_Table('tbl_users', 'is_delete', $e['student']) == 0) {
										?>
										<a href="<?php echo base_url('students/edit/'.$e['student']); ?>">
											<?php echo datalist_Table('tbl_users', 'fullname_en', $e['student']); ?>
										</a>
										<?php
									} else {
										echo datalist_Table('tbl_users', 'fullname_en', $e['student']); 
									}
								}
								?>
								
							</td>
                        </tr><?php
                    }
                    ?>
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>