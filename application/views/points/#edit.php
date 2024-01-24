<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>

    <div id="page-content-wrapper">

        <div class="h-min-100">

            <div class="container-fluid py-2">
                <div class="row">
                    <div class="col-6 my-auto">
                        <h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
                    </div>
                    <div class="col-6 my-auto text-right">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item text-danger" href="javascript:;" onclick="del_ask(<?php echo $result['pid']; ?>)">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="container-fluid h-min-100">

                <?php echo alert_get(); ?>
                
                <form method="post">

                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group row">
                                <label class="col-form-label col-md-3 text-danger">Student</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="student" required>
                                        <option value="">-</option>';
                                        <?php foreach ($student as $e) { ?>
                                        <option value="<?php echo $e['pid']; ?>" <?php if( isset($id) ) {if($id == $e['pid'] ) { echo 'selected'; } } ?>><?php echo $e['fullname_en']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3 text-danger">Credit</label>
                                <div class="col-md-9">
                                    <input type="number" class="form-control" name="amount_1" value="<?php echo $result['amount_1']; ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3 text-danger">Debit</label>
                                <div class="col-md-9">
                                    <input type="number" class="form-control" name="amount_0" value="<?php echo $result['amount_0']; ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">    
                                <label class="col-form-label col-md-3">Remark</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" name="remark" rows="4"><?php echo $result['remark']; ?></textarea>
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

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>