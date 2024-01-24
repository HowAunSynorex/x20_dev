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
					<?php if( check_module('Inventory/Delete') ) { ?>
						<div class="dropdown">
							<button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
							<div class="dropdown-menu dropdown-menu-right">
							</div>
						</div>
					<?php } ?>
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
                                <input type="text" class="form-control" name="title" value="<?php echo $result['title']; ?>" required>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Category</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="category">
									<option value="">-</option>
									<?php foreach ($branch_item_cat as $e) { ?>
										<option value="<?php echo $e['secondary']; ?>" <?php if($e['pid'] == $result['category']) echo 'selected'; ?>><?php echo datalist_Table('tbl_secondary', 'title', $e['secondary']); ?></option>
									<?php } ?>
									<?php foreach ($item_cat as $e2) { ?>
										<option value="<?php echo $e2['pid']; ?>" <?php if($e2['pid'] == $result['category']) echo 'selected'; ?>><?php echo $e2['title']; ?></option>
									<?php } ?>
								</select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">SKU</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="sku" value="<?php echo $result['sku']; ?>">
                            </div>
                        </div>

                        <div class="form-group row">    
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"><?php echo $result['remark']; ?></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1 custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" <?php echo ($result['active'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="checkbox-active">Active</label>
                            </div>
                            <div class="custom-control custom-checkbox mt-1 custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="checkbox-stock" name="stock_ctrl" <?php echo ($result['stock_ctrl'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="checkbox-stock">Stock Control</label>
                            </div>
                        </div>
						
						<div class="form-group row mb-3 pb-1">
                            <label class="col-md-3 col-form-label">Type</label>
                            <div class="col-md-9 my-auto">
                                
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radio-type-product" name="item_type" class="custom-control-input" value="product" <?php if($result['item_type'] == 'product') echo 'checked'; ?>>
                                    <label class="custom-control-label" for="radio-type-product">Product</label>
                                </div>
								
								<div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radio-type-service" name="item_type" class="custom-control-input" value="service" <?php if($result['item_type'] == 'service') echo 'checked'; ?>>
                                    <label class="custom-control-label" for="radio-type-service">Service</label>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
						    <label class="col-form-label col-md-3">Cost Price</label>
							<div class="input-group col-md-9">
								<div class="input-group-prepend">
									<div class="input-group-text">$</div>
								</div>
								<input type="number" step="0.01" class="form-control" name="price_cost" value="<?php echo $result['price_cost']; ?>">
							</div>
                        </div>
						
						<div class="form-group row">
						    <label class="col-form-label col-md-3">Min Price</label>
							<div class="input-group col-md-9">
								<div class="input-group-prepend">
									<div class="input-group-text">$</div>
								</div>
								<input type="number" step="0.01" class="form-control" name="price_min" value="<?php echo $result['price_min']; ?>">
							</div>
                        </div>
						
						<div class="form-group row">
						    <label class="col-form-label col-md-3">Sale Price</label>
							<div class="col-md-9">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">$</div>
									</div>
									<input type="number" step="0.01" class="form-control" name="price_sale" value="<?php echo $result['price_sale']; ?>">
								</div>
								<span class="form-text text-muted small">This will be the default price</span>
							</div>
                        </div>

                    </div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
								<?php if( check_module('Inventory/Delete') ) { ?>
									<button type="button" onclick="del_ask(<?php echo $result['pid']; ?>)" class="btn btn-danger">Delete</button>
								<?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<script>
	<?php if( !check_module('Inventory/Update') ) { ?>
	var access_denied = true;
	<?php } ?>
</script>