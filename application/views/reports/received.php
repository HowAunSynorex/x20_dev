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
			
			<div class="row">
                <div class="col-md-6">
                    <form method="get">
                        
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Start Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="start" value="<?php echo $_GET['start']; ?>" reqyured>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Start Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="end" value="<?php echo $_GET['end']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button class="btn btn-primary" name="search" type="submit">Search</button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Receipt No</th>
                        <th scope="col">Total</th>
                        <th scope="col">Received</th>
                        <th scope="col">Charged</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    foreach( $this->db->query(' SELECT * FROM `tbl_payment` WHERE is_delete=0 AND branch=? AND date>=? AND date<=? ', [ branch_now('pid'), $_GET['start'], $_GET['end'] ])->result_array() as $e ) {
                        
                        ?>
                        <tr>
                            <td><?php echo $e['payment_no']; ?></td>
                            <td><?php echo number_format($e['total'], 2, '.', ',');; ?></td>
                            <td><?php echo number_format($e['receive'], 2, '.', ',');; ?></td>
                            <td><?php echo number_format(($e['receive'] - $e['total']), 2, '.', ','); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>