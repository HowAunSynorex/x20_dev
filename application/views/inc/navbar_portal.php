<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <a class="navbar-brand mr-4" href="javascript:void(0)" data-target="#toggle-sidebar"><i class="fa fa-fw fa-bars"></i></a>
    
    <a class="navbar-brand" href="<?php echo base_url($this->group); ?>"><?php echo app('title'); ?> <span class="badge badge-warning">Portal</span></a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#Navbar">
        <i class="fa fa-fw fa-user"></i>
    </button>

    <div class="collapse navbar-collapse" id="Navbar">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown">Hi, <?php echo auth_data('nickname'); ?></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="<?php echo base_url('auth/logout'); ?>"><i class="fa fa-fw mr-2 fa-sign-out-alt"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>