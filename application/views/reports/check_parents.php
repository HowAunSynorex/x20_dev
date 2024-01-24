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
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
            <table class="DTable table">
                <thead>
                    <th style="width:10%">No</th>
                    <th style="width:35%">Parent</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Child(s)</th>
                    <th>Portal Access</th>
                </thead>
                <tbody>
                    
                    <?php $i=0; foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $e['fullname_en']; ?></td>
                        <td><?php echo empty($e['phone']) ? '-' : $e['phone']; ?></td>
                        <td><?php echo empty($e['email']) ? '-' : $e['email']; ?></td>
                        <td><?php echo empty($e['address']) ? '-' :$e['address']; ?></td>
                        <td><?php echo count($this->tbl_users_model->total_children($e['pid'])); ?></td>
                        <?php echo empty($e['username'] && $e['password']) ? '<td class="text-success"><b>Enable</b></td>' : '<td class="text-danger"><b>Disable</b></td>'; ?>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>