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
					<?php if ( check_module('Inventory/Create') ) { ?>
						<a href="<?php echo base_url( $thispage['group'] . '/add'); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
					<?php } ?>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
            <table class="DTable table">
                <thead>
                    <th style="width:10%">No</th>
                    <th style="width:20%">Title</th>
                    <th style="width:10%">Status</th>
                    <th>Category</th>
                    <th>Min Price ($)</th>
                    <th>Sale Price ($)</th>
                    <th>Stock On Hand</th>
                </thead>
                <tbody>
                    
                    <?php $i=0; foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
							<?php if ( check_module('Inventory/Update') ) { ?>
								<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
							<?php } ?>
								<?php echo $e['title']; ?>
							<?php if ( check_module('Inventory/Update') ) { ?>
								</a>
							<?php } ?>
						</td>
                        <td><?php echo badge($e['active']); ?></td>
                        <td><?php echo empty($e['category']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['category']); ?></td>
                        <td><?php echo empty($e['price_min']) ? '-' : number_format($e['price_min'], 2); ?></td>
                        <td><?php echo empty($e['price_sale']) ? '-' : number_format($e['price_sale'], 2); ?></td>
                        <td><?php echo $e['stock_ctrl'] == 1 ? number_format(stock_on_hand($e['pid']), 0, '.' ,',') : 'N/A' ; ?></td>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>