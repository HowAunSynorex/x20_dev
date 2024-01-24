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

            <div class="table-responsive">
                <table class="DTable table">
                    <thead>
                        <th>No</th>
                        <th>Date / Time</th>
                        <th>Payment No</th>
                        <th>Student</th>
                        <th>Cashier</th>
                        <th>Payment Method</th>
                        <th>Total</th>
						<th>Deleted By</th>
						<th>Deleted Date / Time</th>
                    </thead>
                    <tbody>
                        
                        <?php $i=0; foreach($result as $e) { $i++; ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $e['create_on']; ?></td>
                            <td><?php echo $e['payment_no']; ?></td>
                            <td><?php echo datalist_Table('tbl_users', 'fullname_en', $e['student']); ?></td>
                            <td>
                            <?php echo datalist_Table('tbl_admins', 'nickname', $e['create_by']); ?></td>
                            <td><?php echo empty($e['payment_method']) ? '-' : $e['payment_method'] ; ?></td>
                            <td><?php echo number_format($e['total'], 2, '.', ','); ?></td>
							<td><?php echo datalist_Table('tbl_admins', 'nickname', $e['update_by']); ?></td></td>
							<td><?php echo $e['update_on']; ?></td>
                        </tr>
                        <?php } ?>
                        
                    </tbody>
                </table>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>