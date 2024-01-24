<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar_admin', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar_admin'); ?>

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

            <form method="post" onsubmit="Loading(1)" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Title</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="title" required>
                            </div>
                        </div>

                        <?php
                        switch ($thispage['type']) {
                            case 'country':
                                ?>
                                <div class="form-group row">
                                    <label class="col-form-label col-md-3 text-danger">Country ID</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="country_id" required>
                                    </div>
                                </div>
								
								<div class="form-group row">
                                    <label class="col-form-label col-md-3 text-danger">Phone Code</label>
                                    <div class="col-md-9">
                                        <input type="number" class="form-control" name="phone_code" required>
                                    </div>
                                </div>
								<?php
                                break;

                            case 'currency':
                                ?>
                                <div class="form-group row">
                                    <label class="col-form-label col-md-3 text-danger">Currency ID</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="currency_id" required>
                                    </div>
                                </div>
								<?php
                                break;

                            case 'plan':
                                ?>
                                <div class="form-group row">
                                    <label class="col-form-label col-md-3">Fee</label>
                                    <div class="col-md-9">
										<div class="input-group">
											<div class="input-group-prepend">
												<div class="input-group-text">$</div>
											</div>
											<input type="number" step="0.01" class="form-control" name="fee">
										</div>
                                    </div>
                                </div><?php
                                break;

							case 'payment_method':
								?>
								<div class="form-group row">
									<label class="col-form-label col-md-3 text-danger">Method ID</label>
									<div class="col-md-9">
										<input type="text" class="form-control" name="method_id" required="">
									</div>
								</div>
								<?php 
								break;
							
							case 'receipt': 
								?>
								<div class="form-group row">
									<label class="col-form-label col-md-3">Image</label>
									<div class="col-md-9">
										<img src="<?php echo pointoapi_UploadSource(); ?>" class="border rounded d-block mb-2" style="height: 100px">
										<input type="file" class="form-control" name="image">
									</div>
								</div>
								<?php 
								break;
							
                        }
                        ?>

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
                                <a href="<?php echo base_url($this->group . '/secondary_list/') . $thispage['type']; ?>" class="btn btn-link text-muted">Cancel</a>
                            </div>  
                        </div>

                    </div>
                </div>

            </form>

        </div>
        
        <?php $this->load->view('inc/copyright_admin'); ?>

    </div>

</div>