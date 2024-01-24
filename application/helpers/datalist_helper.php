<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function datalist($type) {

	$array = [
	
		'branch_version' => [
			''			=> 'General',
			'shushi'	=> 'Shushi',
			'faire'		=> 'Faire',
			'receipt'	=> 'Receipt',
		],
		'branch_status_sort' => [
			'table-warning'		=> '1',
			''					=> '2',
			'table-secondary'	=> '3',
			'table-danger'		=> '4',
		],
	
		'class_type' => [
			'monthly' => [
				'label' => 'Per Month',
			],
			'yearly' => [
				'label' => 'Per Year',
			],
			'check_in' => [
				'label' => 'Per Check-in',
			],
		],
	
		'homework_status' => [
			'new' => [
				'label' => 'New',
				'color' => 'info',
			],
			'pending' => [
				'label' => 'Pending Review',
				'color' => 'warning',
			],
			'redo' => [
				'label' => 'Redo',
				'color' => 'secondary',
			],
			'done' => [
				'label' => 'Done',
				'color' => 'success',
			],
			'done_late' => [
				'label' => 'Done but Late',
				'color' => 'success',
			],
		],

		'payment_gateway' => [
			'senangpay' => [
		        'label' => 'senangPay',
		    ],
			'senangpay_sandbox' => [
		        'label' => 'senangPay (Sandbox)',
		    ],
		],
		
		'day_name' => [
			7 => [
		        'name' => 'Sunday',
		        'shortname' => 'SUN',
		    ],
			1 => [
		        'name' => 'Monday',
		        'shortname' => 'MON',
		    ],
			2 => [
		        'name' => 'Tuesday',
		        'shortname' => 'TUE',
		    ],
			3 => [
		        'name' => 'Wednesday',
		        'shortname' => 'WED',
		    ],
			4 => [
		        'name' => 'Thursday',
		        'shortname' => 'THU',
		    ],
			5 => [
		        'name' => 'Friday',
		        'shortname' => 'FRI',
		    ],
			6 => [
		        'name' => 'Saturday',
		        'shortname' => 'SAT',
		    ],
		],
		
		'device_type' => [

			'web_qr' => [
				'label' => 'QR Code',
			],
			
			'web_rfid' => [
				'label' => 'RFID',
			],
			
			/*'web_pin' => [
				'label' => 'Pin Code',
			],
			
			'machine_fingerprint' => [
				'label' => 'Fingerprint',
			],
			
			'machine_face' => [
				'label' => 'Face Recognition',
			],
			
			'machine_pin' => [
				'label' => 'Machine Pin',
			],*/
			
		],

		'notify_type' => [
			'payment_success' => [
				'label' => 'After payment has been created successfully',
				'title' => 'The action will be triggered when the student has created the payment successfully.',
				'column' => 'notify_payment',
			],
			'outstanding' => [
				'label' => 'Notify the student who has outstanding payment',
				'title' => 'The action will be triggered when the student has outstanding payment and notification is required to notify the student to clear the outstanding payment.',
				'column' => 'notify_outstanding',
			],
			'checkin_success' => [
				'label' => 'After student has signed the attendance successfully',
				'title' => 'The action will be triggered when the student has signed the attendance successfully.',
				'column' => 'notify_attendance',
			],
		],

		'notify_method' => [
			'sms' => 'SMS',
			'email' => 'Email',
			'whatsapp' => 'WhatsApp',
		],
		
		'app_payment_type' => [
		
			'fpx' => [
				'label' => 'FPX Payment',
				'column' => 'gateway_fpx',
			],
			
			'ccard' => [
				'label' => 'Debit / Credit Card',
				'column' => 'gateway_ccard',
			],
			
			'ewallet' => [
				'label' => 'E-wallet (Boost, Touch n Go)',
				'column' => 'gateway_ewallet',
			],
			
			'qrpay' => [
				'label' => 'QR Pay (Boost, GrabPay, ShopeePay, DuitNow, Touch n Go)',
				'column' => 'gateway_qrpay',
				'input' => [
					'boost' => 'Boost',
					'grab' => 'GrabPay',
					'shopee' => 'ShopeePay',
					'duit' => 'DuitNow',
					'tng' => 'Touch n Go'
				]
			],
			
			'transfer' => [
				'label' => 'Online Transfer',
				'column' => 'gateway_transfer',
			],
			
		],

		'payment_status' => [
			'draft' => [
				'title' => 'Draft',
				'status' => 'secondary',
			],
			'pending' => [
				'title' => 'Pending',
				'status' => 'warning',
			],
			'paid' => [
				'title' => 'Paid',
				'status' => 'success',
			],
			'unpaid' => [
				'title' => 'Unpaid',
				'status' => 'danger',
			],
			'canceled' => [
				'title' => 'Canceled',
				'status' => 'dark',
			],
		],

		'point_type' => [
			'epoint' => [
				'label' => 'Epoint',
			],
			'ewallet' => [
				'label' => 'Ewallet',
			],
		],

		/*'receipt_no_format' => [
			'RCPT-%YY%0000',
			'RCPT-%YY%000000',
			'RCPT-000000',
			'HQ-000000',
			'BA-000000',
			'000000',
			'00000000',
			'%YY%0000',
			'%YY%000000',
		],*/

		'event_type' => [
			'event' => [
				'label' => 'Events',
				'single' => 'Event',
			],
			'holiday' => [
				'label' => 'Holidays',
				'single' => 'Holiday',
			],
		],

		'inventory_type' => [
			
			'items' => [
				'label' => 'Items',
				'single' => 'Item',
			],

			'movement' => [
				'label' => 'Movement',
				'single' => 'Movement',
			],

		],

		'secondary_type_admin' => [
		
			'bank' => [
				'label' => 'Bank',
				'single' => 'Bank',
			],
			
			'plan' => [
				'label' => 'Plans',
				'single' => 'Plan',
			],
			
			'receipt' => [
				'label' => 'Receipts',
				'single' => 'Receipt',
			],

			'country' => [
				'label' => 'Countries',
				'single' => 'Country',
			],

			'currency' => [
				'label' => 'Currencies',
				'single' => 'Currency',
			],
			
			'transport' => [
				'label' => 'Transports',
				'single' => 'Transport',
			],

			'payment_method' => [
				'label' => 'Payment Methods',
				'single' => 'Method',
			],
			
			'attendance_method' => [
				'label' => 'Attendance Methods',
				'single' => 'Method',
			],
			
		],

		'secondary_type' => [
		
// 			'tp' => [
// 				'label' => 'T/P',
// 				'single' => 'T/P',
// 			],
		
			'bank' => [
				'label' => 'Bank',
				'single' => 'Bank',
			],
			
			'form' => [
				'label' => 'Form',
				'single' => 'Form',
			],
			
			'reason' => [
				'label' => 'Reasons',
				'single' => 'Reason',
			],
			
			'school' => [
				'label' => 'Schools',
				'single' => 'School',
			],

			'course' => [
				'label' => 'Courses',
				'single' => 'Course',
			],

			'class_bundle' => [
				'label' => 'Class Bundles',
				'single' => 'Class Bundle',
			],

			'item_cat' => [
				'label' => 'Item Cat.',
				'single' => 'Category',
			],
			
			'transport' => [
				'label' => 'Transports',
				'single' => 'Transport',
			],
			
			'childcare' => [
				'label' => 'Childcare',
				'single' => 'Childcare',
			],

			'payment_method' => [
				'label' => 'Payment Methods',
				'single' => 'Method',
			],

			'exam' => [
				'label' => 'Exams',
				'single' => 'Exam',
			],

			'check_in_method' => [
				'label' => 'Check-in Methods',
				'single' => 'Method',
			],
			
		],

		'setting_type' => [
			
			'admins' => [
				'label' => 'Admins',
				'single' => 'Admin',
			],

			'branches' => [
				'label' => 'Branches',
				'single' => 'branch',
			],

			'advanced' => [
				'label' => 'Advanced',
			],

			'expImp' => [
				'label' => 'Export & Import',
			],

		],

		'user_type' => [
			
			'teacher' => [
				'label' => 'Teachers',
				'single' => 'Teacher',
			],

			'student' => [
				'label' => 'Students',
				'single' => 'Student',
			],

			'parent' => [
				'label' => 'Parents',
				'single' => 'Parent',
			],

		],

		'content_type' => [
			
			'announcement' => [
				'label' => 'Announcement',
				'single' => 'Post',
			],

			'slideshow' => [
				'label' => 'Slideshow',
				'single' => 'slideshow',
			],

		],

		'gender' => [
			'male' => 'Male',
			'female' => 'Female',
		],
		
		'gender_cn' => [
			'male' => '男',
			'female' => '女',
		],

		'insurance_status' => [
			
			'pending' => [
				'label' => 'Pending',
				'badge' => 'warning',
			],
			'confirm' => [
				'label' => 'Confirmed',
				'badge' => 'success',
			],
			'reject' => [
				'label' => 'Reject',
				'badge' => 'danger',
			]
		],

		'student_question' => [
			'1' => '怎么样知道學而思？',
			'2' => '朋友/亲戚的名字是？',
			'3' => '为什么选學而思？',
			'4' => '之前在哪裡补习？',
			'5' => '为什么不补了？',
		],

	];

	return isset($array[ $type ]) ? $array[ $type ] : null ;

}

function datalist_Table($tbl, $col, $id, $id_col = 'pid') {

	$this_ci =& get_instance();

	$this_ci->db->where($id_col, $id);
	
	$query = $this_ci->db->get($tbl);
	
	$result = $query->result_array();

	if(count($result) > 0) {

		$result = $result[0];

		return isset($result[$col]) ? $result[$col] : null ;

	} else {

		return null;

	}

}

function datalist_List($tbl) {

	$this_ci =& get_instance();

	$this_ci->db->where('is_delete', 0);
	$this_ci->db->where('active', 1);
	
	$query = $this_ci->db->get($tbl);
	
	return $query->result_array();

}