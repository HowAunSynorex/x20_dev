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
                    <th>No</th>
                    <th>Title</th>
                    <th>Teacher</th>
                    <th>Course</th>
                    <th>Fee ($)</th>
                    <th>Remark</th>
					<th>Sun</th>
					<th>Mon</th>
					<th>Tue</th>
					<th>Wed</th>
					<th>Thu</th>
					<th>Fri</th>
					<th>Sat</th>
                </thead>
                <tbody>
                    
                    <?php $i=0; foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $e['title']; ?></td>
                        <td><?php echo empty($e['teacher']) ? '-' : datalist_Table('tbl_users', 'fullname_en', $e['teacher']); ?></td>
                        <td><?php echo empty($e['course']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['course']); ?></td>
                        <td><?php echo empty($e['fee']) ? '-' : number_format($e['fee'], 2, '.', ','); ?></td>
                        <td><?php echo empty($e['remark']) ? '-' : $e['remark']; ?></td>
                        <td><?php echo empty($e['dy_7']) ? '-' : str_replace('-', ' - ', $e['dy_7']); ?></td>
                        <td><?php echo empty($e['dy_1']) ? '-' : str_replace('-', ' - ', $e['dy_1']); ?></td>
                        <td><?php echo empty($e['dy_2']) ? '-' : str_replace('-', ' - ', $e['dy_2']); ?></td>
                        <td><?php echo empty($e['dy_3']) ? '-' : str_replace('-', ' - ', $e['dy_3']); ?></td>
                        <td><?php echo empty($e['dy_4']) ? '-' : str_replace('-', ' - ', $e['dy_4']); ?></td>
                        <td><?php echo empty($e['dy_5']) ? '-' : str_replace('-', ' - ', $e['dy_5']); ?></td>
                        <td><?php echo empty($e['dy_6']) ? '-' : str_replace('-', ' - ', $e['dy_6']); ?></td>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>