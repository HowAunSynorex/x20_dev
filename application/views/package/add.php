<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

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
                            <label class="col-form-label col-md-3 text-danger">Title</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="title" required>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Items</label>
                            <div class="col-md-9">	
                                <?php foreach($items as $e) { ?>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" name="item[]" class="custom-control-input" id="<?php echo $e['pid']; ?>" value="<?php echo $e['pid']; ?>">
										<label class="custom-control-label" for="<?php echo $e['pid']; ?>">
											<?php echo $e['title']; ?>
										</label>
									</div>
								<?php } ?>
                            </div>
                        </div>
						
                    </div>
                    <div class="col-md-6">
					
                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" checked>
                                <label class="custom-control-label" for="checkbox-active">Active</label>
                            </div>
                        </div>
						
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
                            </div>  
                        </div>

                    </div>
                </div>

            </form>

        </div>
        
        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>