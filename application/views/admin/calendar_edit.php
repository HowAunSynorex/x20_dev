<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar_admin', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar_admin'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
                </div>
                <div class="col-6 my-auto text-right">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item text-danger" href="javascript:;" onclick="del_ask(<?php echo $result['pid'];?>, '<?php echo $result['type']; ?>')">Delete</a>
                        </div>
                    </div>
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

                        <div class="form-group row" id="section-date_start">
                            <label class="col-form-label col-md-3" datal-label="label"><?php if( $result['date_end'] == '0000-00-00' ) { echo 'Date'; } else { echo 'Start'; } ?></label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date_start" value="<?php echo $result['date_start']; ?>" data-end="<?php echo $result['date_end']; ?>">
                                <div class="custom-control custom-checkbox mt-2">
                                    <!-- 这里要判断如果只有一个日期就checked -->
                                    <input type="checkbox" class="custom-control-input" id="checkbox-same_day" value="1" <?php if( empty($result['date_end']) ) echo 'checked';?>>
                                    <label class="custom-control-label" for="checkbox-same_day">Same Day</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row <?php if( empty($result['date_end']) ) echo 'd-none';?>" id="section-date_end">
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
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" <?php if( $result['active'] == 1 ) echo 'checked'; ?>>
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
                                <a href="javascript:;" onclick="window.history.back();" class="btn btn-link text-muted">Cancel</a>
                            </div>
                        </div>

                    </div>
                </div>

            </form>

        </div>

        <?php $this->load->view('inc/copyright_admin'); ?>

    </div>

</div>