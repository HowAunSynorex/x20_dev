<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="wrapper">

    <?php $this->load->view('inc/navbar', $thispage); ?>

    <?php $this->load->view('inc/sidebar'); ?>

    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1 class="mb-0"><?php echo $thispage['title']; ?></h1>
                    </div>
                    <div class="col-sm-6 text-right my-auto">
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="row">

                <div class="col-md-6 offset-md-3">

                    <?php echo alert_get(); ?>

                    <form method="post">

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">General</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">

                                <div class="form-group row">
                                    <label class="col-form-label col-md-3">Name</label>
                                    <div class="col-md-9">
                                        <p class="form-control-plaintext"><?php echo $result['nickname']; ?></p>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-md-3">Email</label>
                                    <div class="col-md-9">
                                        <p class="form-control-plaintext"><?php echo $result['username']; ?></p>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-md-3">Password</label>
                                    <div class="col-md-9">
                                        <p class="form-control-plaintext">******</p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="text-right">
                            <a href="<?php echo base_url('admins/list'); ?>" class="btn btn-link text-muted px-0 float-left">Cancel</a>
                            <button type="button" onclick="del_ask(<?php echo $result['pid']; ?>)" class="btn btn-danger">Delete</button>
                            <a href="<?php echo base_url('admins/edit/'.$result['pid']); ?>" class="btn btn-warning">Edit</a>
                        </div>

                    </form>

                </div>

            </div>
        </section>

    </div>

    <?php $this->load->view('inc/copyright'); ?>

</div>
