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
        
        <div class="container container-wrapper pt-4">

            <?php echo alert_get(); ?>

            <div class="row">
				<?php
				
				foreach([
					
					'<i class="fa fa-fw fa-chart-line mr-1"></i> Sales' => [
						//'unpaid_items' 	=> 'Unpaid Items',
						'daily_collection' 				=> 'Daily Collection',
						'monthly_collection' 			=> 'Monthly Collection',
						'outstanding_payment' 			=> 'Outstanding Payment',
						'outstanding_payment_class' 	=> 'Outstanding Payment (Class)',
						'outstanding_payment_parent' 	=> 'Outstanding Payment (Parent)',
						'deleted_receipts' 				=> 'Deleted Receipts',
						//'epoint_balance' 				=> 'Epoint Balance',
						'ewallet_balance' 				=> 'Ewallet Balance',
						//'sales_by_item' 				=> 'Sales by Item',
						//'sales_by_item_cat' 			=> 'Sales by Item Cat.',
						'sales_by_school' 				=> 'Sales by School',
						'sales_by_course' 				=> 'Sales by Course',
						//'sales_by_admin' 				=> 'Sales by Admin',
						'sales_by_ref_code' 			=> 'Sales by Reference Code',
						//'advanced_payment' 				=> 'Advanced Payment',
						'teacher_comm' 					=> 'Teacher Commission',
						'received' 					=> 'Received',
					],
					
					'<i class="fa fa-fw fa-users mr-1"></i> Users' => [
						'birthday_student' 					=> 'Birthday',
						'birthday_parent' 					=> 'Birthday (Parent)',
						'birthday_teacher' 					=> 'Birthday (Teacher)',
						'whatsapp_marketing' 				=> 'WhatsApp Marketing',
						'whatsapp_marketing_student' 		=> 'WhatsApp Marketing (Student)',
					],
					
					'<i class="fa fa-fw fa-clock mr-1"></i> Attendance' => [
						'student_attendance' 			=> 'Student Attendance',
						'student_attendance_class' 		=> 'Student Attendance (Class)',
						'teacher_attendance' 			=> 'Teacher Attendance',
						'daily_attendance' 				=> 'Daily Attendance',
						'daily_attendance_teacher' 		=> 'Daily Attendance (Teacher)',
						'monthly_attendance' 			=> 'Monthly Attendance',
						'monthly_attendance_teacher' 	=> 'Monthly Attendance (Teacher)',
						'class_deductions' 				=> 'Class Deductions',
						'student_enroll' 				=> 'Student Enroll',
						'absence_rate' 					=> 'Absence Rate',
						'daily_absent_report' 			=> 'Daily Absent Report',													
					],
					
					'<i class="fa fa-fw fa-boxes mr-1"></i> Inventory' => [
						'stock_movement' 		=> 'Stock Movement',
					],
					
					'<i class="fa fa-fw fa-chart-pie mr-1"></i> Data Check' => [
						'check_students' 		=> 'Students',
						'check_parents' 		=> 'Parents',
						'check_teachers'		=> 'Teachers',
						'check_form_teachers'	=> 'Form Teachers',
						'check_items' 			=> 'Items',
						'check_classes'			=> 'Classes',
						'check_payment_trash'	=> 'Payment (Trash)',
						'classes_number'		=> 'Classes Number',
						'babysitter'			=> 'Babysitter',
					],
					
					'<i class="fa fa-fw fa-chart-line mr-1"></i> Summary' => [					
						// Tan Jing Suan 
						// 'annual_comparison' 		=> 'Annual Comparison',
						'annual_comparison01' 		=> 'Annual Comparison 人数表', //人数表
						'annual_comparison02' 		=> 'Annual Comparison 科数表', //科数表
						'annual_comparison03' 		=> 'Annual Comparison 实业绩表', //实业绩表
					],
					
					'<i class="fa fa-fw fa-book mr-1"></i> Subject Student Count' => [		
						// Ling HA			
						'sub_stud_count' 		=> 'Subject Student Count',
					],
					
				] as $k => $v) {
					?>
					<div class="col-md-3 mb-5">
						<h5 class="font-weight-bold mb-3"><?php echo $k; ?></h5>
						<ul class="nav flex-column">
							<?php
							$i = 0;
							$modules = json_decode(datalist_Table('tbl_secondary', 'modules', branch_now('plan')), true);
							foreach($v as $k2 => $v2) {									
								if( check_module('Reports/Modules/'. str_replace(' ', '', strip_tags($k) ) .'/'.$v2) ) { $i++;
									?>
									<li class="nav-item">
										<a class="nav-link px-0 py-2" href="<?php echo base_url('reports/'.$k2); ?>"><?php echo $v2; ?></a>
									</li>
									<?php
									
								}
								
							}
							
							if($i == 0) echo '<em class="text-muted">No result found</em>';
							?>
						</ul>
					</div>
					<?php
				}
				?>
			</div>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>