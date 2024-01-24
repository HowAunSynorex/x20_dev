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
					<?php if( check_module('Classes/Create') ) { ?>
						<a href="<?php echo base_url($thispage['group'] . '/add'); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
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
                    <th>Tutor</th>
                    <th>Course</th>
                    <th>Fee ($)</th>
                    <th>Student(s)</th>
                </thead>
                <tbody>
                    
                    <?php $i=0; foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
							<?php if( check_module('Classes/Update') ) { ?>
								<a href="<?php echo base_url($thispage['group'] . '/edit/'.$e['pid']); ?>">
							<?php } ?>
								<?php echo $e['title']; ?>
							<?php if( check_module('Classes/Update') ) { ?>
								</a>
							<?php } ?>
						</td>
                        <td><?php echo badge($e['active']); ?></td>
                        <td><?php echo empty($e['teacher']) ? '-' : datalist_Table('tbl_users', 'fullname_en', $e['teacher']); ?></td>
                        <td><?php echo empty($e['course']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['course']); ?></td>
                        <td><?php echo empty($e['fee']) ? '-' : number_format($e['fee'], 2, '.', ','); ?></td>
                        <td>
							<a href="<?php echo base_url('classes/edit/'.$e['pid'].'?tab=2'); ?>"><?php echo count( $this->log_join_model->list_classes_students( $e['pid'] ) ); ?></a>
						</td>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>