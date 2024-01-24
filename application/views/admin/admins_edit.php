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
                            <a class="dropdown-item text-danger" href="javascript:;" onclick="del_ask(<?php echo $result['pid']; ?>)">Delete</a>
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
                            <label class="col-form-label col-md-3 text-">Name</label>
                            <div class="col-md-9">
								<p class="form-control-plaintext"><?php echo $result['nickname']; ?></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-">Email</label>
                            <div class="col-md-9">
								<p class="form-control-plaintext"><?php echo $result['username']; ?></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Password</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" name="password" placeholder="Set whitelabel login portal password">
								<a href="?disabled" class="d-block small text-danger">Disabled Whitelabel</a>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url('admin/admins_list'); ?>" class="btn btn-link text-muted">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
            
        </div>

        <?php $this->load->view('inc/copyright_admin'); ?>

    </div>

</div>