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
            
            <form method="post">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date" value=<?php echo date('Y-m-d'); ?> required>
                            </div>
                        </div>
                        
                        <!-- <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Reason</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="type" required>
                            </div>
                        </div> -->

                        <div class="form-group row">    
                            <label class="col-form-label col-md-3">Reason</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="title" rows="4">Admin adjust</textarea>
                            </div>
                        </div>

                        <div class="form-group row">    
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
				
				<hr class="mb-4">
				
				<table class="table table-bordered mt-3 mb-4">
                    <thead>
                        <th>Item</th>
                        <th class="text-right" style="width: 10%;">Stock on Hand</th>
                        <th class="text-right" style="width: 10%;">Adjust Stock</th>
                        <th class="text-right" style="width: 10%;">In</th>
                        <th class="text-right" style="width: 10%;">Out</th>
                    </thead>
                    <tbody></tbody>
                </table>
				
				<hr class="mb-4">

                <div class="form-group text-right">
					<a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
					<button type="submit" name="save" class="btn btn-primary">Save</button>
				</div>

            </form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>