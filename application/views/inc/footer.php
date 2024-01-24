<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

	<form method="post" onsubmit="window.location.href='<?php echo base_url(); ?>'+$('#modal-command input').val(); return false" class="modal fade" id="modal-command" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body p-0">
					<input type="search" class="form-control py-3 border-0" placeholder="Enter your command..." name="search" list="datalist-command" onchange="window.location.href='<?php echo base_url(); ?>'+this.value; return false" required>
					<datalist id="datalist-command">
						<?php
						
						foreach([
							'home',
							
							'payment/list',
							'payment/add',
							
							'students/list',
							'students/add',
							
							'parents/list',
							'parents/add',
							
							'calendar',
							
							'attendance/daily',
							'attendance/classes',
							'attendance/manually',
							
							'items/list',
							'items/add',
							
							'movement/list',
							'movement/add',
							
							'announcement/list',
							'announcement/add',
							
							'slideshow/list',
							// 'announcement/add',
							
							'classes/list',
							'classes/add',
							
							'teachers/list',
							'teachers/add',
							
							// 'reports/XX',
						] as $k => $v) echo '<option value="'.strtolower($v).'">'.$v.'</option>';
						?>
					</datalist>
				</div>
			</div>
		</div>
	</form>

    <script>var base_url = "<?php echo base_url(); ?>";</script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script src="https://cdn.synorexcloud.com/template/164753773795/js/theme.js"></script>
    <script src="<?php echo base_url('assets/js/custom.js?v='.time()); ?>"></script>

    <?php

    switch($thispage['group'].'/'.$thispage['title']) {

        case 'calendar/Calendar':
			echo '<script src="https://cdn.synorexcloud.com/libraries/fullcalendar/4.2/packages/core/main.min.js"></script>';
			echo '<script src="https://cdn.synorexcloud.com/libraries/fullcalendar/4.2/packages/daygrid/main.min.js"></script>';
			echo '<script src="https://cdn.synorexcloud.com/libraries/fullcalendar/4.2/packages/list/main.min.js"></script>';
			echo '<script src="https://cdn.synorexcloud.com/libraries/fullcalendar/4.2/packages/interaction/main.min.js"></script>';
			echo '<script src="https://cdn.synorexcloud.com/libraries/fullcalendar/4.2/packages/timegrid/main.min.js"></script>';
            break;

		case 'home/Home':
			echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.4.1/chart.min.js"></script>';
			// echo '<script src="https://cdn.jsdelivr.net/npm/shepherd.js@5.0.1/dist/js/shepherd.js"></script>';
			echo '<script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>';
			break;

		case 'content/Add Announcement':
		case 'content/Edit Announcement':
		case 'homework/Add Homework':
		case 'homework/Edit Homework':
			echo '<script src="https://cdn.ckeditor.com/ckeditor5/19.0.0/classic/ckeditor.js"></script>';
			break;

        case 'payment/Add Payment':
        case 'payment/Edit Payment':
			echo '<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>';
            break;
            
        case 'payment/All Payment':
            echo '<script type="text/javascript" src="'.base_url('assets/pages/payment/epos-2.24.0.js').'"></script>';
            break;
			
        case 'students/Edit Student':
			echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js"></script>';
            break;

		case 'classes/Edit Class':
		case 'points/Ewallet':
		case 'reports/Daily Collection':
		case 'reports/Monthly Collection':
		case 'reports/Monthly Attendance':
		case 'reports/Student Attendance':
		case 'reports/Student Attendance (Class)':
		case 'reports/Stock Movement':
		case 'reports/Students':
			?>
			<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
			<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
			<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
			<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
			<?php
			break;
			
		case 'landing/Application E-form':
			?>
			<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
			<?php
			break;


    }

    if(isset($thispage['js'])) echo '<script src="'.base_url('assets/pages/'.$thispage['js'].'.js?v='.time()).'"></script>';
    ?>
	
	<?php /* if(branch_now('active_support_box')) { ?>
		<script>  var MessageBirdChatWidgetSettings = {     widgetId: '22f4aa44-d1c8-43d4-a993-ed12a72fe2f4',     initializeOnLoad: true,   };  !function(){"use strict";if(Boolean(document.getElementById("live-chat-widget-script")))console.error("MessageBirdChatWidget: Snippet loaded twice on page");else{var e,t;window.MessageBirdChatWidget={},window.MessageBirdChatWidget.queue=[];for(var i=["init","setConfig","toggleChat","identify","hide","on","shutdown"],n=function(){var e=i[d];window.MessageBirdChatWidget[e]=function(){for(var t=arguments.length,i=new Array(t),n=0;n<t;n++)i[n]=arguments[n];window.MessageBirdChatWidget.queue.push([[e,i]])}},d=0;d<i.length;d++)n();var a=(null===(e=window)||void 0===e||null===(t=e.MessageBirdChatWidgetSettings)||void 0===t?void 0:t.widgetId)||"",o=function(){var e,t=document.createElement("script");t.type="text/javascript",t.src="https://livechat.messagebird.com/bootstrap.js?widgetId=".concat(a),t.async=!0,t.id="live-chat-widget-script";var i=document.getElementsByTagName("script")[0];null==i||null===(e=i.parentNode)||void 0===e||e.insertBefore(t,i)};"complete"===document.readyState?o():window.attachEvent?window.attachEvent("onload",o):window.addEventListener("load",o,!1)}}();</script>
	<?php } */ ?>
    
</body>

</html>
