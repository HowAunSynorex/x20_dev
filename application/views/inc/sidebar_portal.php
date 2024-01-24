<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="sidebar-wrapper">
    <ul class="sidebar-nav" id="accordion">

        <li class="<?php if ($thispage['group'] == 'portal') echo 'active'; ?>">
            <a href="<?php echo base_url($this->group); ?>"><i class="fa fa-fw fa-home"></i> Home</a>
        </li>



    </ul>
</div>