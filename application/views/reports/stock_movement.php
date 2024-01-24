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

            <form method="get" class="mb-3">

                <div class="row">
                    <div class="col-md-6">
					
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Start Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="start_date" value="<?php if(isset($_GET['start_date'])) { echo $_GET['start_date']; } else {
                                    echo date('Y-m-d'); } ?>">
                            </div>
                        </div>
						
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">End Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="end_date" value="<?php if(isset($_GET['end_date'])) { echo $_GET['end_date']; } else {
                                    echo date('Y-m-d'); } ?>">
                            </div>
                        </div>
						
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
								<button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
						
                    </div>
                </div>
            </form>
            
            <table class="DTable2 table">
                <thead>
                    <th style="width:10%">No</th>
                    <th style="width:35%">Item</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Balance</th>
                </thead>
                <tbody>
                    
                    <?php $i=0; foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $e['title']; ?></td>
                        <?php

                            $qty_in = array();
                            $qty_out = array();

                            if(isset($_GET['start_date'])) { 
                                $start_date = $_GET['start_date']; 
                            } else {
                                $start_date = date('Y-m-d'); 
                            }

                            if(isset($_GET['end_date'])) { 
                                $end_date = $_GET['end_date']; 
                            } else {
                                $end_date = date('Y-m-d'); 
                            }

                            $data = $this->log_inventory_model->stock_list($start_date, $end_date);

                            foreach($data as $k => $v) {

                                if($v['item'] == $e['pid']) {

                                    $qty_in[] = $v['qty_in'];
                                    $qty_out[] = $v['qty_out'];

                                }

                            }

                            echo '<td>'.number_format(array_sum($qty_in), 0, '.' ,',').'</td>';
                            echo '<td>'.number_format(array_sum($qty_out), 0, '.' ,',').'</td>';
                            echo '<td>'.number_format((array_sum($qty_in) - array_sum($qty_out)), 0, '.', ',').'</td>';
                            
                        ?>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>