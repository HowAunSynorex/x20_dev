<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="sidebar-wrapper">
    <ul class="sidebar-nav" id="accordion">

        <li class="<?php if ($thispage['group'] == 'home') echo 'active'; ?>">
            <a href="<?php echo base_url($this->group); ?>"><i class="fa fa-fw fa-home"></i> Home</a>
        </li>

        <li class="<?php if ($thispage['group'] == 'calendar') echo 'active'; ?>">
            <a href="<?php echo base_url($this->group.'/calendar'); ?>"><i class="fa fa-fw fa-calendar"></i> Calendar</a>
        </li>

        <li class="<?php if ($thispage['group'] == 'secondary') echo 'active'; ?>">
            <a href="javascript:;" data-toggle="collapse" data-target="#child-secondary"><i class="fa fa-fw fa-tags"></i> Secondary</a>
            <div class="child collapse <?php if ($thispage['group'] == 'secondary') echo 'show'; ?>" data-parent="#accordion" id="child-secondary">
                <?php foreach(datalist('secondary_type_admin') as $k => $v) { ?>
                <a href="<?php echo base_url($this->group.'/secondary_list/'.$k); ?>" class="<?php if ($thispage['title'] == $v['label']) echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> <?php echo $v['label']; ?></a>
                <?php } ?>
            </div>
        </li>

        <li class="<?php if (in_array($thispage['group'], [ 'admins', 'branches', 'settings' ])) echo 'active'; ?>">
            <a href="javascript:;" data-toggle="collapse" data-target="#child-settings"><i class="fa fa-fw fa-cogs"></i> Settings</a>
            <div class="child collapse <?php if (in_array($thispage['group'], [ 'admins', 'branches', 'settings' ])) echo 'show'; ?>" data-parent="#accordion" id="child-settings">
                <a href="<?php echo base_url($this->group.'/admins_list'); ?>" class="<?php if ($thispage['title'] == 'All Admins') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Admins</a>
                <a href="<?php echo base_url($this->group.'/branches_list'); ?>" class="<?php if ($thispage['title'] == 'All Branches') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Branches</a>
                <a href="<?php echo base_url($this->group.'/settings_advanced'); ?>" class="<?php if ($thispage['title'] == 'Advanced') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Advanced</a>
				<a href="<?php echo base_url($this->group.'/agents_list'); ?>" class="<?php if ($thispage['title'] == 'All Agents') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Agents</a>
            </div>
        </li>

    </ul>
</div>