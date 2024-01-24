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
                <div id="del-section" class="col-6 my-auto text-right">
                    <div class="dropdown">
					<?php if( check_module('Calendar/Delete') ) { ?>
                        <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item text-danger" href="javascript:;" onclick="del_ask(<?php echo $result['pid'];?>, '<?php echo $thispage['type']; ?>')">Delete</a>
                        </div>
					<?php } ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php 
			
			echo alert_get(); 
			
			if( $result['branch'] == null ) echo '<div class="alert alert-info">This '.$thispage['type'].' generated via system</div>';
			?>
			
            <form method="post">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Title</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="title" value="<?php echo $result['title']; ?>" required>
                                <?php foreach(datalist('event_type') as $k => $v) { ?>
                                <div class="custom-control custom-radio custom-control-inline mt-2">
                                    <input type="radio" id="radio-type-<?php echo $k; ?>" name="type" class="custom-control-input" value="<?php echo $k; ?>" <?php if( $result['type'] == $k ) { echo 'checked'; }  ?>>
                                    <label class="custom-control-label" for="radio-type-<?php echo $k; ?>"><?php echo $v['single']; ?></label>
                                </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group row" id="section-date_start">
                            <label class="col-form-label col-md-3" datal-label="label"><?php if( $result['date_end'] == '0000-00-00' ) { echo 'Date'; } else { echo 'Start'; } ?></label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date_start" value="<?php echo $result['date_start']; ?>">
                                <div class="custom-control custom-checkbox mt-2">
                                    <!-- 这里要判断如果只有一个日期就checked -->
                                    <input type="checkbox" class="custom-control-input" id="checkbox-same_day" value="1" <?php if( $result['date_end'] == '0000-00-00' ) echo 'checked';?>>
                                    <label class="custom-control-label" for="checkbox-same_day">Same Day</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row <?php if( $result['date_end'] == '0000-00-00' ) echo 'd-none';?>" id="section-date_end">
                            <label class="col-form-label col-md-3">End</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date_end" value="<?php echo $result['date_end']; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="4" name="remark"><?php echo $result['remark']; ?></textarea>
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
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url($thispage['group']); ?>" class="btn btn-link text-muted">Cancel</a>
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
	// <?php if( !check_module('Calendar/Update') ) { ?>
	// var access_denied = true;
	// <?php } ?>
	
	<?php if( $result['branch'] == null || !check_module('Calendar/Update') ) { ?>
	var access_denied1 = true;
	<?php } ?>
</script>