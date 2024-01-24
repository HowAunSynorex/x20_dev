<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#"><?php echo app('title'); ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url('webapp_teacher/home'); ?>">Home</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url('webapp_teacher/logout'); ?>">Logout</a>
            </li>
            
        </ul>
    </div>
</nav>
