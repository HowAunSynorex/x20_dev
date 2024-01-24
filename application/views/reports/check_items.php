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
                    <th style="width:35%">Item</th>
                    <th style="width:10%">Cost Price</th>
                    <th>Min Price</th>
                    <th>Sale Price</th>
                    <th>Stock on Hand</th>
                </thead>
                <tbody>
                    
                    <?php $i=0; foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $e['title']; ?></td>
                        <td><?php echo empty($e['price_cost']) ? '-' : number_format($e['price_cost'], 2, '.', ','); ?></td>
                        <td><?php echo empty($e['price_min']) ? '-' : number_format($e['price_min'], 2, '.', ','); ?></td>
                        <td><?php echo empty($e['price_sale']) ? '-' : number_format($e['price_sale'], 2, '.', ','); ?></td>
                        <td>
                            <?php

                                $qty_in = array();
                                $qty_out = array();
                                $data = $this->log_inventory_model->report_list();

                                foreach($data as $k => $v) {

                                    if($v['item'] == $e['pid']) {

                                        $qty_in[] = $v['qty_in'];
                                        $qty_out[] = $v['qty_out'];

                                    }

                                }

                                echo (array_sum($qty_in) - array_sum($qty_out));
                                
                            ?>
                        </td>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>