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
                            <label class="col-form-label col-md-3 text-danger">Name</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="nickname" value="<?php echo auth_data('nickname'); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Username</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="username" value="<?php echo auth_data('username'); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Password</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" name="password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                            </div>
                        </div>

                    </div>
                </div>

            </form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>