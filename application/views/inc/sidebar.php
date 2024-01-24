<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="sidebar-wrapper" data-title="Sidebar" data-intro="All the functions of the system are gathered in the left menu">
    <ul class="sidebar-nav" id="accordion">

        <li class="<?php if ($thispage['title'] == 'Home') echo 'active'; ?>">
            <a href="<?php echo base_url(); ?>"><i class="fa fa-fw fa-home"></i> Home</a>
        </li>
		
		<?php
		
		if( check_module('Payment/Read') ) {
			$url = base_url('payment/add');
			if(branch_now('version') == 'shushi') $url = base_url('payment/add2');
		?>
			<li class="<?php if ($thispage['title'] == 'Add Payment') echo 'active'; ?>">
				<a href="<?php echo $url; ?>"><i class="fa fa-fw fa-file-invoice"></i> Add Payment</a>
			</li>
			<li class="<?php if ($thispage['title'] == 'Payment') echo 'active'; ?>">
				<a href="<?php echo base_url('payment/list'); ?>"><i class="fa fa-fw fa-file-invoice"></i> Payment</a>
			</li>
			<?php
		}
		
		if( check_module('Points/Read') ) {
			?>
			<li class="<?php if ($thispage['group'] == 'points') echo 'active'; ?>">
				<a href="javascript:;" data-toggle="collapse" data-target="#child-points"><i class="fa fa-fw fa-star"></i> Points</a>
				<div class="child collapse <?php if ($thispage['group'] == 'points') echo 'show'; ?>" data-parent="#accordion" id="child-points">
					<?php
					if( check_module('Points/Modules/Epoint') ) {
						?>
						<a href="<?php echo base_url('points/list/epoint'); ?>" class="<?php if ($thispage['title'] == 'Epoint') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Epoint</a><?php
					}
					
					if( check_module('Points/Modules/Ewallet') ) {
						?>
						<a href="<?php echo base_url('points/list/ewallet'); ?>" class="<?php if ($thispage['title'] == 'Ewallet') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Ewallet</a><?php
					}
					?>
				</div>
			</li>
			<?php
		}
		
		if( check_module('Students/Read') ) {
			?>
			<li class="<?php if ($thispage['group'] == 'students') echo 'active'; ?>">
				<a href="javascript:;" data-toggle="collapse" data-target="#child-students"><i class="fa fa-fw fa-users"></i> Students</a>
				<div class="child collapse <?php if ($thispage['group'] == 'students') echo 'show'; ?>" data-parent="#accordion" id="child-students">
					<?php
					// if( check_module('Points/Modules/Epoint') ) {
						?>
						<a href="<?php echo base_url('students/list'); ?>" class="<?php if ($thispage['title'] == 'All Students') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> All Students</a><?php
					// }
					
					// if( check_module('Points/Modules/Ewallet') ) {
						?>
						<a href="<?php echo base_url('students/list/pending'); ?>" class="<?php if ($thispage['title'] == 'Pending') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Pending</a><?php
					// }
					
					// if( check_module('Points/Modules/Ewallet') ) {
						?>
						<a href="<?php echo base_url('students/scores'); ?>" class="<?php if ($thispage['title'] == 'Score') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Score</a><?php
					// }
					?>
				</div>
			</li>
			<!--<li class="<?php if ($thispage['group'] == 'students') echo 'active'; ?>">
				<a href="<?php echo base_url('students/list'); ?>"><i class="fa fa-fw fa-users"></i> Students</a>
			</li>-->
			<?php
		} 
		
		if( check_module('Parents/Read') ) {
			?>
			<li class="<?php if ($thispage['group'] == 'parents') echo 'active'; ?>">
				<a href="<?php echo base_url('parents/list'); ?>"><i class="fa fa-fw fa-user-circle"></i> Parents</a>
			</li>
			<?php
		} 
		
		if( check_module('Calendar/Read') ) {
			?>
			<li class="<?php if ($thispage['group'] == 'calendar') echo 'active'; ?>">
				<a href="<?php echo base_url('calendar'); ?>"><i class="fa fa-fw fa-calendar"></i> Calendar</a>
			</li>
			<?php
		} 
		
		if( check_module('Attendance/Read') ) {
			?>
			<li class="<?php if ($thispage['group'] == 'attendance') echo 'active'; ?>">
				<a href="javascript:;" data-toggle="collapse" data-target="#child-attendance"><i class="fa fa-fw fa-clock"></i> Attendance</a>
				<div class="child collapse <?php if ($thispage['group'] == 'attendance') echo 'show'; ?>" data-parent="#accordion" id="child-attendance">
					<?php
					
					if( check_module('Attendance/Modules/DailyAttendance') ) {
						
						?><a href="<?php echo base_url('attendance/daily'); ?>" class="<?php if ($thispage['title'] == 'Daily Attendance') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Daily</a><?php
						
					}
					
					if( check_module('Attendance/Modules/ClassAttendance') ) {
						
						?><a href="<?php echo base_url('attendance/classes'); ?>" class="<?php if ($thispage['title'] == 'Class Attendance') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Classes</a><?php
						
					}
					
					if( check_module('Attendance/Modules/ManuallyAttendance') ) {
						
						?><a href="<?php echo base_url('attendance/manually'); ?>" class="<?php if ($thispage['title'] == 'Manually Attendance') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Manually</a><?php
						
					}
					?>
				</div>
			</li>
			<?php
		} 
		
		if( check_module('Inventory/Read') ) {
			?>
		<li class="<?php if (in_array($thispage['group'], [ 'items', 'movement', 'package' ])) echo 'active'; ?>">
				<a href="javascript:;" data-toggle="collapse" data-target="#child-inventory"><i class="fa fa-fw fa-boxes"></i> Inventory</a>
				<div class="child collapse <?php if (in_array($thispage['group'], [ 'items', 'movement', 'package' ])) echo 'show'; ?>" data-parent="#accordion" id="child-inventory">
				
					<?php
					if( check_module('Inventory/Modules/Items') ) {
						 ?>
						<a href="<?php echo base_url('items/list'); ?>" class="<?php if ($thispage['title'] == 'Items') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Items</a><?php
					}
					
					if( check_module('Inventory/Modules/Movement') ) {
						 ?>
						<a href="<?php echo base_url('package/list'); ?>" class="<?php if ($thispage['title'] == 'Package') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Package</a><?php
					}
					
					if( check_module('Inventory/Modules/Movement') ) {
						 ?>
						<a href="<?php echo base_url('movement/list'); ?>" class="<?php if ($thispage['title'] == 'Movement') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Movement</a><?php
					}
					?>
					
				</div>
			</li>
			<?php
		}

		if( check_module('Content/Read') ) {
			?>
			<li class="<?php if ($thispage['group'] == 'content') echo 'active'; ?>">
				<a href="javascript:;" data-toggle="collapse" data-target="#child-content"><i class="fa fa-fw fa-bullhorn"></i> Content</a>
				<div class="child collapse <?php if ($thispage['group'] == 'content') echo 'show'; ?>" data-parent="#accordion" id="child-content">
					<?php 
					
					if( check_module('Content/Modules/Announcement') ) {
						
						?><a href="<?php echo base_url('content/list/announcement'); ?>" class="<?php if ($thispage['title'] == 'Announcement') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Announcement</a><?php
					
					}
					
					if( check_module('Content/Modules/Slideshow') ) {
						
						?><a href="<?php echo base_url('content/list/slideshow'); ?>" class="<?php if ($thispage['title'] == 'Slideshow') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Slideshow</a><?php
					
					}
					?>
				</div>
			</li>
			<?php
		}
		
		if( check_module('Homework/Read') ) {
			?>
			<li class="<?php if ($thispage['group'] == 'homework') echo 'active'; ?>">
				<a href="<?php echo base_url('homework/list'); ?>"><i class="fa fa-fw fa-book"></i> Homework</a>
			</li>
			<?php
		}
		
		if( check_module('Classes/Read') ) {
			?>
			<li class="<?php if ($thispage['group'] == 'classes') echo 'active'; ?>">
				<a href="<?php echo base_url('classes/list'); ?>"><i class="fa fa-fw fa-school"></i> Classes</a>
			</li>
			<?php
		}
		
		if( check_module('Teachers/Read') ) {
			?>
			<li class="<?php if ($thispage['group'] == 'teachers') echo 'active'; ?>">
				<a href="<?php echo base_url('teachers/list'); ?>"><i class="fa fa-fw fa-user-tie"></i> Teachers</a>
			</li>
			<?php
		}
		
		if( check_module('Reports/Read') ) {
			?>
			<li class="<?php if ($thispage['group'] == 'reports') echo 'active'; ?>">
				<a href="<?php echo base_url('reports'); ?>"><i class="fa fa-fw fa-chart-pie"></i> Reports</a>
			</li>
			<?php
		}
		
		if( check_module('Secondary/Read') ) {
			?>
			<li class="<?php if ($thispage['group'] == 'secondary') echo 'active'; ?>">
				<a href="javascript:;" data-toggle="collapse" data-target="#child-secondary"><i class="fa fa-fw fa-tags"></i> Supplement</a>
				<div class="child collapse <?php if ($thispage['group'] == 'secondary') echo 'show'; ?>" data-parent="#accordion" id="child-secondary">
						
						<?php 
						
						/*if( check_module('Secondary/Modules/Banks') ) {
							?><a href="<?php echo base_url('secondary/list/tp'); ?>" class="<?php if ($thispage['title'] == 'T/P') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> T/P</a><?php
						}*/
						
						if( check_module('Secondary/Modules/Banks') ) {
							?><a href="<?php echo base_url('secondary/list/bank'); ?>" class="<?php if ($thispage['title'] == 'Bank') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Bank</a><?php
						}
						
						if( check_module('Secondary/Modules/Banks') ) {
							?><a href="<?php echo base_url('secondary/list/form'); ?>" class="<?php if ($thispage['title'] == 'Form') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Form</a><?php
						}
						
						?>
						<a href="<?php echo base_url('secondary/list/reason'); ?>" class="<?php if ($thispage['title'] == 'Reasons') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Reasons</a>
						<?php
						
						if( check_module('Secondary/Modules/Schools') ) {
							?>
							<a href="<?php echo base_url('secondary/list/school'); ?>" class="<?php if ($thispage['title'] == 'Schools') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Schools</a><?php
						}
						
						if( check_module('Secondary/Modules/Courses') ) {
							?>
							<a href="<?php echo base_url('secondary/list/course'); ?>" class="<?php if ($thispage['title'] == 'Courses') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Courses</a><?php
						}
						
						if( check_module('Secondary/Modules/ClassBundles') ) {
							?>
							<a href="<?php echo base_url('secondary/list/class_bundle'); ?>" class="<?php if ($thispage['title'] == 'Class Bundles') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Class Bundle</a><?php
						}
						
						if( check_module('Secondary/Modules/ItemCat') ) {
							?>
							<a href="<?php echo base_url('secondary/list/item_cat'); ?>" class="<?php if ($thispage['title'] == 'Item Cat.') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Item Cat.</a><?php
						}
						
						if( check_module('Secondary/Modules/Childcare') ) {
							?>
							<a href="<?php echo base_url('secondary/list/childcare'); ?>" class="<?php if ($thispage['title'] == 'Childcare') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Childcare</a><?php
						}
						
						if( check_module('Secondary/Modules/Transports') ) {
							?>
							<a href="<?php echo base_url('secondary/list/transport'); ?>" class="<?php if ($thispage['title'] == 'Transports') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Transportations</a><?php
						}
						
						if( check_module('Secondary/Modules/PaymentMethods') ) {
							?>
							<a href="<?php echo base_url('secondary/list/payment_method'); ?>" class="<?php if ($thispage['title'] == 'Payment Methods') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Payment Methods</a><?php
						}
						
						if( check_module('Secondary/Modules/Exams') ) {
							?>
							<a href="<?php echo base_url('secondary/list/exam'); ?>" class="<?php if ($thispage['title'] == 'Exams') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Exams</a><?php
						}?>
		
				</div>
			</li>
			<?php
		}
		
		if( check_module('Settings/Read') ) { ?> 
			
			<li class="<?php if (in_array($thispage['group'], [ 'devices', 'admins', 'branches', 'settings', 'branches_new' ])) echo 'active'; ?> ">
				<a href="javascript:;" data-toggle="collapse" data-target="#child-settings"><i class="fa fa-fw fa-cogs"></i> Settings</a>
				<div class="child collapse <?php if (in_array($thispage['group'], [ 'devices', 'admins', 'branches', 'settings', 'branches_new' ])) echo 'show'; ?>" data-parent="#accordion" id="child-settings">
					<!--<a href="<?php echo base_url('settings/notify'); ?>" class="<?php if ($thispage['title'] == 'Notify') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Notify</a>-->
					<?php

					if( check_module('Settings/Modules/General') ) {

						?><a href="<?php echo base_url('settings/general'); ?>" class="<?php if ($thispage['title'] == 'General') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> General</a><?php

					}					
					
					if( check_module('Settings/Modules/Admins') ) {

						?><a href="<?php echo base_url('admins/list'); ?>" class="<?php if ($thispage['title'] == 'All Admins') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Admins</a><?php

					}
					
					if( check_module('Settings/Modules/Branches') ) {

						?><a href="<?php echo base_url('branches_new/list'); ?>" class="<?php if ($thispage['title'] == 'Branches') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Branches</a><?php

					}
					
					if( check_module('Settings/Modules/Devices') ) {

						?><a href="<?php echo base_url('devices/list'); ?>" class="<?php if ($thispage['title'] == 'Devices') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Devices</a><?php

					}
					
					if( check_module('Settings/Modules/Migrate') ) {
						
						?><a href="<?php echo base_url('settings/migrate'); ?>" class="<?php if ($thispage['title'] == 'Migrate') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Migrate</a><?php
					
					}
					
					if( check_module('Settings/Modules/Receipt') ) {
						
						?><a href="<?php echo base_url('settings/receipt'); ?>" class="<?php if ($thispage['title'] == 'Receipt') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Receipt</a><?php
				
					}
					
					/*if( check_module('Settings/Modules/PointoAPI') ) {
						
						?><a href="<?php echo base_url('settings/pointoapi'); ?>" class="<?php if ($thispage['title'] == 'PointoAPI') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> PointoAPI</a><?php
					
					}
					
					if( check_module('Settings/Modules/ResetStudent') ) {
						
						?><a href="<?php echo base_url('settings/reset_std'); ?>" class="<?php if ($thispage['title'] == 'Reset Student') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Reset Student</a><?php
					
					}
					
					if( check_module('Settings/Modules/ResetParent') ) {
						
						?><a href="<?php echo base_url('settings/reset_parent'); ?>" class="<?php if ($thispage['title'] == 'Reset Parent') echo 'text-primary'; ?>"><i class="fa fa-fw fa-circle invisible"></i> Reset Parent</a><?php
					
					}*/
					?>
				</div>
			</li>
			<?php
		}
		
			/* ?>
			<li class="<?php if ($thispage['group'] == 'insurance') echo 'active'; ?>">
				<a href="<?php echo base_url('insurance/list'); ?>"><i class="fa fa-fw fa-shield-alt"></i> Insurance <span class="badge badge-danger">New</span></a>
			</li>
			<?php */
		
		?>

    </ul>
</div>