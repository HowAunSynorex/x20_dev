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
				<?php if(branch_now('version') == 'faire') { ?>
					<div class="col-6 text-right">
						<a href="<?php echo base_url($thispage['group'] . '/check_students_export'); ?>" target="_blank" class="btn btn-primary"><i class="fa fa-fw fa-file-export"></i> Export to Excel</a>
					</div>
				<?php } ?>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
            <table class="DTable table">
                <thead>
                    <th style="width:10%">No</th>
                    <th style="width:25%">Student</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Parent</th>
                    <th>Parent Phone</th>
                    <th>School</th>
                    <th>Portal Access</th>
                </thead>
                <tbody>
                    
                    <?php $i=0; foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $e['fullname_cn']; ?> <?php echo ($e['fullname_en'] !='')?'</br>'.$e['fullname_en']:''; ?></td>
                        <td><?php echo empty($e['phone']) ? '-' : $e['phone']; ?></td>
                        <td><?php echo empty($e['email']) ? '-' : $e['email']; ?></td>
                        <td><?php echo empty($e['address']) ? '-' :$e['address']; ?></td>
                        <td><?php echo empty($e['parent']) ? '-' : datalist_Table('tbl_users', 'fullname_en', $e['parent']); ?></td>
                        <td><?php echo empty($e['parent']) ? '-' : empty(datalist_Table('tbl_users', 'phone', $e['parent'])) ? '-' : datalist_Table('tbl_users', 'phone', $e['parent']); ?></td>
                        <td><?php echo empty($e['school']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['school']); ?></td>
                        <?php echo empty($e['username'] && $e['password']) ? '<td class="text-success"><b>Enable</b></td>' : '<td class="text-danger"><b>Disable</b></td>'; ?>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>