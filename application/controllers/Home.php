<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'home';

		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_content_model');
		$this->load->model('tbl_branches_model');
		$this->load->model('tbl_devices_model');
		$this->load->model('tbl_payment_model');
		$this->load->model('log_attendance_model');
		$this->load->model('log_join_model');
		$this->load->model('log_payment_model');
		$this->load->model('tbl_secondary_model');
		
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");

	}

	public function index()
	{

		auth_must('login');
		
		// check empty branches
		if( count(my_branches()) == 0 ) redirect('branches_new/list');
		
		// check branch expired
		// if( strtotime(date('Y-m-d')) > strtotime(branch_now('expired_date')) ) {
			
			// alert_new('warning', '<b>'.branch_now('title').'</b> license expired! Please renew the license and continue');
			
			// redirect('branches_new/list');
			
		// }
		
		

		$data['thispage'] = [
			'title' => 'Home',
			'group' => $this->group,
			'js' => $this->group.'/home',
		];
		
		$data['month'] = (isset($_GET['month']))?$_GET['month']:date('m');

		$data['total_student'] = count($this->tbl_users_model->total_active_user('student', branch_now('pid')));
		$data['monthly_student'] = count($this->tbl_users_model->monthly_user('student', branch_now('pid')));
		$data['birthday_student'] = $this->tbl_users_model->birthday_user('student', branch_now('pid'));
		// Tan Jing Suan
		$data['kindergarden_student_joined'] = $this->tbl_users_model->kindergarden_student_joined('student', branch_now('pid'), $data['month']);		
		$data['primary_student_joined'] = $this->tbl_users_model->primary_student_joined('student', branch_now('pid'), $data['month']);
		$data['secondary_student_joined'] = $this->tbl_users_model->secondary_student_joined('student', branch_now('pid'), $data['month']);
		
		$data['check_in'] = count($this->log_attendance_model->list_gb_user([
		
			'branch' => branch_now('pid'),
			'DATE(datetime)' => date('Y-m-d'),
			
		]));
		
		if(isset($_POST['status'])) {
			
			header('content-type: application/json');
			
			$response = file_get_contents('https://one.synorexcloud.com/api/status_page_single?q=tuition');
			
			die($response);
			
		}
		
		// v1 by soon (del by steve)
		/*$unpaid_items = $this->log_join_model->list('unpaid_item', branch_now('pid'));
		$join_class = $this->log_join_model->list('join_class', branch_now('pid'));
		$unpaid_class = [];
		$outstanding_student = [];
		foreach($join_class as $e) {
			if(empty($this->log_payment_model->list2([
				'user' => $e['user'],
				'class' => $e['class'],
				'period' => date('Y-m', strtotime(date('Y-m', strtotime($e['date']))))
			]))) {
			$unpaid_class[] = $this->log_join_model->list('join_class', branch_now('pid'), ['class' => $e['class'], 'user' => $e['user'], 'date' => $e['date']]);
			}
		}
		
		foreach($unpaid_class as $k => $v) { 
			$outstanding_student[] = $v[0]['user'];
		}
		
		foreach($unpaid_items as $e) { 
			$outstanding_student[] = $e['user'];
		}
		
		$data['outstanding_student'] = count(array_unique($outstanding_student));*/
		
		// unpaid amount v2 by steve
	/* 	$data['outstanding_student'] = '<a href="'.base_url('reports/outstanding_payment').'">N/A</a>';
		$std = $this->tbl_users_model->list('student', branch_now('pid'), [ 'active' => 1 ]);
		
		if(count($std) <= 500) {
			$data['outstanding_student'] = 0;
			foreach( $std as $e ) {
				
				$std_unpaid_result = std_unpaid_result($e['pid']);

				if($std_unpaid_result['count'] > 0) {
					
					$data['outstanding_student']++;
					
				}
				
			}
		} */
		
		if(isset($_POST['save_reason'])) {
			
			$this->log_join_model->edit($_POST['id'], [ 'remark' => $_POST['reason'] ]);
			
			alert_new('success', 'Reason update successfully');
			header('refresh: 0'); exit;
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view('home/index');
		$this->load->view('inc/footer', $data);

	}
	
	public function attendance($id = '')
	{

		$data['thispage'] = [
			'title' => 'Attendance',
			'group' => $this->group,
			'js' => $this->group.'/attendance_qr',
			'css' => $this->group.'/attendance',
		];
		
		$data['result_device'] = $this->tbl_devices_model->view($id);
		
		// check error 404
		if( count($data['result_device']) == 1 ) {
			
			$data['result_device'] = $data['result_device'][0];
			
			// inactive
			if( $data['result_device']['active'] == 0 ) {
				
				redirect('home/error_404');
				
			}
			
			$type = $data['result_device']['type'];
			
		} else {
			
			redirect('home/error_404');
			
		}

		$data['branch'] = datalist_Table('tbl_branches', 'title', $data['result_device']['branch']);
		
		$url;
		
		switch($type) {
			case 'web_qr':
				$url = 'qr';
				break;
				
			case 'web_pin':
				$url = 'pin';
				break;
				
			case 'web_rfid':
				if(isset($_POST['save'])) {

					// rfid convert to user
					$user_id = $this->tbl_users_model->rfid_to_userid($this->input->post('rfid_cardid'), $data['result_device']['branch']);
					// $user_id = datalist_Table('tbl_users', 'pid', $this->input->post('rfid_cardid'), 'rfid_cardid');

					// check rfid exists
					if( empty($user_id) ) {

						alert_new('warning', 'Card ID not found');
			
					} else {
					
						/*$data['user'] = $this->tbl_users_model->view_attendance($this->input->post('rfid_cardid'), branch_now('pid'));
						
						$post_data['branch'] = branch_now('pid');
						$post_data['method'] = '162780129479';
						$post_data['create_by'] = auth_data('pid');
						$post_data['user'] = $data['user'][0]['pid'];
						date_default_timezone_set("Asia/Kuala_Lumpur");
						$post_data['datetime'] = date('Y-m-d H:i:s');
						
						$data['attendance'] = $this->log_attendance_model->view_user($data['user'][0]['pid'], date('Y-m-d'));
						
						$i = 0;
						foreach ($data['attendance'] as $e) { $i++; }
						
						if ($i % 2 == 0) {				
							$post_data['action'] = 'in';
							$post_data['reason'] = 'Check In';
						} else {
							$post_data['action'] = 'out';
							$post_data['reason'] = 'Check Out';
						};
						
						$this->log_attendance_model->add($post_data);*/

						submit_today_attendance('162780129479', $user_id, $this->input->post('temperature'));
						
						alert_new('success', 'Attendance submitted successfully');
						header('refresh: 0'); exit;

					}

				}
				$url = 'rfid';
				break;
		}

		header('refresh: 15');
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/attendance_'.$url , $data);
		$this->load->view('inc/footer', $data);

	}
	
	/*public function attendance_rfid($id = '')
	{

		$data['thispage'] = [
			'title' => 'Attendance',
			'group' => $this->group,
			'js' => $this->group.'/attendance',
			'css' => $this->group.'/attendance',
		];
		
		if( $this->tbl_devices_model->view($id)[0]['active'] == '0') {
			redirect('home/error_404');
		}
		
		$data['result'] = $this->log_attendance_model->list(branch_now('pid'));
		$data['slideshow'] = $this->tbl_content_model->active_list('slideshow', branch_now('pid'));
		$data['branch'] = datalist_Table('tbl_branches', 'title', $this->tbl_devices_model->view($id)[0]['branch']);

		if(isset($_POST['save'])) {

			if(!$this->tbl_users_model->view_attendance($this->input->post('rfid_cardid'), branch_now('pid'))) {

				alert_new('warning', 'Data not found');
			
			} else {
			
				$data['user'] = $this->tbl_users_model->view_attendance($this->input->post('rfid_cardid'), branch_now('pid'));
				
				$post_data['branch'] = branch_now('pid');
				$post_data['create_by'] = auth_data('pid');
				$post_data['user'] = $data['user'][0]['pid'];
				date_default_timezone_set("Asia/Kuala_Lumpur");
				$post_data['datetime'] = date('Y-m-d H:i:s');
				
				$data['attendance'] = $this->log_attendance_model->view_user($data['user'][0]['pid']);
				
				$i = 0;
				foreach ($data['attendance'] as $e) { $i++; }
				
				if ($i % 2 == 0) {				
					$post_data['action'] = 'in';
				} else {
					$post_data['action'] = 'out';
				};
				
				$this->log_attendance_model->add($post_data);
				
				$data['result'] = $this->log_attendance_model->list(branch_now('pid'));
				
				alert_new('success', ucfirst($this->group).' created successfully');

				header('refresh: 0'); exit;

			}

		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/attendance_rfid', $data);
		$this->load->view('inc/footer', $data);

	}
	
	public function attendance_qr($id = '')
	{

		$data['thispage'] = [
			'title' => 'Attendance',
			'group' => $this->group,
			'js' => $this->group.'/attendance_qr',
			'css' => $this->group.'/attendance',
		];
		
		if( $this->tbl_devices_model->view($id)[0]['active'] == '0') {
			redirect('home/error_404');
		}
		
		$data['branch'] = datalist_Table('tbl_branches', 'title', $this->tbl_devices_model->view($id)[0]['branch']);

		header('refresh: 15');
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/attendance_qr', $data);
		$this->load->view('inc/footer', $data);

	}
	
	public function attendance_pin($id = '')
	{

		$data['thispage'] = [
			'title' => 'Attendance',
			'group' => $this->group,
			'js' => $this->group.'/attendance_pin',
			'css' => $this->group.'/attendance',
		];
		
		if( $this->tbl_devices_model->view($id)[0]['active'] == '0') {
			redirect('home/error_404');
		}
		
		$data['branch'] = datalist_Table('tbl_branches', 'title', $this->tbl_devices_model->view($id)[0]['branch']);
		
		header('refresh: 15');

		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/attendance_pin', $data);
		$this->load->view('inc/footer', $data);

	}*/
	
	// 新版本搬过去 attendance/submit_landing 了
	/*public function checkin_landing()
	{
		
		// check token
		if(!isset($_GET['token'])) $_GET['token'] = '';
		
		$login = $this->tbl_users_model->me_token( $_GET['token'] );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			
		} else {
			
			die(app('title').': Token error');
			
		}

		$data['thispage'] = [
			'title' => 'Check-In',
			'group' => $this->group,
		];
		
		$post_data['branch'] = $login['branch'];
		$post_data['user'] = $login['pid'];
		date_default_timezone_set("Asia/Kuala_Lumpur");
		$post_data['datetime'] = date('Y-m-d H:i:s');
		
		$attendance = $this->log_attendance_model->view_user($login['pid']);
		
		$i = 0;
		foreach ($attendance as $e) { $i++; }
		
		if ($i % 2 == 0) {				
			$post_data['action'] = 'in';
		} else {
			$post_data['action'] = 'out';
		};
		
		$this->log_attendance_model->add($post_data);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/checkin_landing');
		$this->load->view('inc/footer', $data);

	}*/

	public function branch_access($id = '')
	{

		auth_must('login');
		
		$this->load->model('tbl_branches_model');

		$result = $this->tbl_branches_model->view($id);

		if(count($result) == 1) {

			setcookie(md5('@highpeakedu-branch'), $id, time() + (86400 * 30), '/');
			// $this->session->set_userdata('branch', $id);

		}

		redirect('');

	}

	public function error_404()
	{

		$data['thispage'] = [
			'title' => 'Error 404',
			'group' => $this->group,
			'css' => $this->group.'/error_404'
		];

		$this->load->view('inc/header', $data);
		$this->load->view('home/error_404');
		$this->load->view('inc/footer', $data);

	}

	public function json_monthly_joined()
	{
		
		auth_must('login');
		
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_secondary_model');
	
		$result = [];
	
		// joined
		for($M=1; $M<=12; $M++) {
			
			$result['student'][] = count($this->tbl_users_model->monthly_user2('student', branch_now('pid'), $M));
			$result['parent'][] = count($this->tbl_users_model->monthly_user2('parent', branch_now('pid'), $M));
			$result['teacher'][] = count($this->tbl_users_model->monthly_user2('teacher', branch_now('pid'), $M));
			
		}
		
		// courses
		foreach($this->tbl_secondary_model->list('course', branch_now('pid'), [
			'active' => 1
		]) as $e) {
			
			$result['courses']['label'][] = $e['title'];
			$result['courses']['data'][] = 0;
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}

}
