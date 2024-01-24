<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'attendance';
		$this->single = 'attendance';
		
		$this->load->model('log_attendance_model');
		$this->load->model('log_join_model');
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_classes_model');
		
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		
	}

	public function daily()
	{
		
		auth_must('login');
		check_module_page('Students/Read');

		$data['thispage'] = [
			'title' => 'Daily Attendance',
			'group' => $this->group,
		];

		if(isset($_POST['save'])) {
			
			// rfid convert to user
			$user_id = $this->tbl_users_model->rfid_to_userid($this->input->post('rfid_cardid'), branch_now('pid'));
			
			// check rfid exists
			if( empty($user_id) ) {

				alert_new('warning', 'Card ID not found');
			
			} else {
				
				submit_today_attendance('162780129479', $user_id, $this->input->post('temperature'));
				
				alert_new('success', ucfirst($this->group).' created successfully');

			}

			header('refresh: 0'); exit;
		
		}
		
		$data['result'] = $this->log_attendance_model->list([
		
			'branch' => branch_now('pid'),
			'DATE(datetime)' => date('Y-m-d')
			
		]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/daily', $data);
		$this->load->view('inc/footer', $data);

	}
	
	public function submit_landing()
	{
		
		// auth_must('login');
		// check_module_page('Students/Read');
		// $reason;
		// check token
		
		if(isset($_GET['token'])) {
			
			$login = $this->tbl_users_model->me_token( $_GET['token'] );

			if( count($login) == 1 ) {
				$login = $login[0];
			} else {
				die(app('title').': Token error');
			}
			
			submit_today_attendance('162780128983', $login['pid'], null);
			
			$post_data['method'] = '162780128983';
			$post_data['branch'] = $branch = $login['branch'];
			$post_data['user'] = $user = $login['pid'];
			
			date_default_timezone_set("Asia/Kuala_Lumpur");
			$post_data['datetime'] = date('Y-m-d H:i:s');

			$attendance = $this->log_attendance_model->view_user($user, date('Y-m-d'));
			
			$i = 0; foreach ($attendance as $e) { $i++; }
						
			if ($i % 2 == 0) {				
				$post_data['action'] = 'in';
				$post_data['reason'] = 'Check In';

			} else {
				$post_data['action'] = 'out';
				$post_data['reason'] = 'Check Out';
			};
						
			$this->log_attendance_model->add($post_data);
			
			redirect($this->uri->uri_string().'?ok=ok&check='.$post_data['action']);
			
		} else if (isset($_GET['ok'])) {
			
			$data['thispage'] = [
				'title' => '',
				'group' => $this->group,
			];
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/ok');
			$this->load->view('inc/footer', $data);
			
		} else {
			
			die(app('title').': Token error');
			
		}
		
	}

	// steve
	public function json_my_daily()
	{
		
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_content_model');
		
		header('Content-type: application/json');
		
		$login = $this->tbl_users_model->me_token( post_data('token') );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			
			$user_id = empty( post_data('student') ) ? $login['pid'] : post_data('student') ;
			
			$attendance = $this->log_attendance_model->list2($login['branch'], [
				'user' => $user_id,
				'create_on LIKE' => '%'.date('Y-m-d').'%'
			]);
			
			$result = [
				'session' => $login,
				'attendance' => $attendance,
				'student' => $user_id,
			];

			die(json_encode([ 'status' => 'ok', 'result' => $result ]));

		} else {

			die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));

		}

	}

	public function manually()
	{
		
		auth_must('login');
		check_module_page('Students/Read');

		$data['thispage'] = [
			'title' => 'Manually Attendance',
			'group' => $this->group,
			'js' => $this->group.'/manually',
		];
		
		$data['result'] = [];
		$data['student'] = $this->tbl_users_model->list('student', branch_now('pid'), ['active' => 1]);
		$data['teacher'] = $this->tbl_users_model->list('teacher', branch_now('pid'), ['active' => 1]);

		if(isset($_GET['save'])) {

			// $data['result'] = $this->log_attendance_model->list(branch_now('pid'), [ 'user' => $_GET['user'] ]);
			$data['result'] = $this->log_attendance_model->list_desc([
			
				'branch' => branch_now('pid'), 
				'user' => $_GET['user'],
				'DATE(datetime)' => $_GET['date']
			
			]);

		}
		
		if(isset($_POST['add'])) {

			$post_data = [];
			
			foreach([ 'user', 'action', 'reason', 'temperature', 'remark' ] as $e) {
				
				$post_data[$e] = $this->input->post($e);
				
			}

			$post_data['method'] = '162780155412'; // manually
			$post_data['datetime'] = ($_GET['date'].' '.$this->input->post('time'));
			$post_data['update_by'] = auth_data('pid');
			$post_data['branch'] = branch_now('pid');

			$this->log_attendance_model->add($post_data);
			
			alert_new('success', ucfirst($this->group).' created successfully');

			header('refresh: 0'); exit;
			
		}
		
		if(isset($_POST['edit'])) {

			$post_data = [];
			
			foreach([ 'action', 'reason', 'temperature', 'remark' ] as $e) {
				
				$post_data[$e] = $this->input->post($e);
				
			}
			
			$post_data['datetime'] = ($_GET['date'].' '.$this->input->post('time'));
			
			// modify by steve
			/*if($post_data['action'] == 'in') {
				$post_data['reason'] = 'Check In';
			} else {
				$post_data['reason'] = 'Check Out';
			}*/

			$this->log_attendance_model->edit($this->input->post('id'), $post_data);
			
			alert_new('success', ucfirst($this->group).' updated successfully');
			
			header('refresh: 0'); exit;

		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/manually', $data);
		$this->load->view('inc/footer', $data);

	}
	
	public function webview_attendance()
	{				
	
		if(isset($_GET['token'])) {
			
			$user = $this->tbl_users_model->me_token($_GET['token']);
			
		} else {
			
			die('Invalid token');
			
		}
		
		if(empty($user)) {
			
			die('User not found');
			
		} else {
		
			$data['thispage'] = [
				'title' => 'Attendance',
				'group' => $this->group,
			];
			
			$user = $user[0];
			$data['token'] = post_data('token');
			
// 			$data['is_parent'] = false;
			
// 			$start_date = date('Y-m-d');
// 			$end_date = date('Y-m-d');
			
// 			if(isset($_GET['start_date']) && !empty($_GET['start_date'])) $start_date = $_GET['start_date'];
// 			if(isset($_GET['end_date']) && !empty($_GET['end_date'])) $end_date = $_GET['end_date'];
			
// 			$query = '
			
// 				SELECT * FROM log_attendance
// 				WHERE is_delete = 0
// 				AND DATE(datetime) >= "' . $start_date . '"
// 				AND DATE(datetime) <= "' . $end_date . '"
			
// 			';
			
// 			if($user[0]['type'] == 'parent') {
				
// 				$data['is_parent'] = true;
				
// 				$data['child'] = $this->tbl_users_model->list_v2([
				
// 					'type' => 'student',
// 					'parent' => $user[0]['pid'],
// 					'active' => 1
				
// 				]);
				
// 				if(isset($_GET['user']) && !empty($_GET['user'])) {
// 					$query .= ' AND user = "' . $_GET['user'] . '"';
// 				} else {
// 					$query .= ' AND user = null';
// 				}
				
// 			} else {
				
// 				$query .= ' AND user = "' . $user[0]['pid'] . '"';
				
// 			}
			
// 			$data['result'] = $this->db->query($query)->result_array();
            
            if(!isset($_GET['u'])) $_GET['u'] = '';
            if(!isset($_GET['user'])) $_GET['user'] = '';
            if(!isset($_GET['month'])) $_GET['month'] = date('Y-m');
            
            // loop all child
            if($user['type'] == 'parent') {
                $_GET['u'] = $user['pid'];
                
                $r = [];
    		    $c = $this->log_join_model->list_all([ 'type' => 'join_parent', 'parent' => $_GET['u'], 'active' => 1 ]);
    		    foreach($c as $e) {
    		        $er = $this->tbl_users_model->list_v2([ 'pid' => $e['user'] ]);
    		        if(count($er) == 1) {
    		            $a = $er[0];
    		            $a['school_title'] = datalist_Table('tbl_secondary', 'title', $a['school']);
    		            $a['form_title'] = datalist_Table('tbl_secondary', 'title', $a['form']);
    		            $r[] = $a;
    		        }
    		    }
                $data['child'] = $r;
            } else {
                $_GET['u'] = $user['pid'];
            }
            
            // loop child join class
            $_GET['u'] = $user['type'] == 'parent' ? $_GET['user'] : $user['pid']; // default is std
            $data['result'] = $this->log_join_model->list_all([ 'type' => 'join_class', 'user' => $_GET['u'], 'active' => 1 ]);
            
            $data['user'] = $user;
            
            // print_r($r); exit;
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/webview_attendance', $data);
			$this->load->view('inc/footer', $data);
			
		}
		
	}
	
	public function classes()
	{
		
		auth_must('login');
		check_module_page('Students/Read');
		
		$data['thispage'] = [
			'title' => 'Class Attendance',
			'group' => $this->group,
			'css' => $this->group.'/classes',
			'js' => $this->group.'/classes',
		];
		
		if(isset($_GET['class'])) {
			
			$sql = '
			
				SELECT
					c.title as class_title,
					l.time_range as time_range,
					l.title as class_subtitle,
					l.id as class_id,
					CASE 
						WHEN l.qty = 1 THEN "Mon"
						WHEN l.qty = 2 THEN "Tue"
						WHEN l.qty = 3 THEN "Wed"
						WHEN l.qty = 4 THEN "Thu"
						WHEN l.qty = 5 THEN "Fri"
						WHEN l.qty = 6 THEN "Sat"
						WHEN l.qty = 7 THEN "Sun"
					END as day
				FROM log_join l
				INNER JOIN tbl_classes c
				ON l.class = c.pid
				AND c.is_delete = 0
				AND c.active = 1
				AND l.is_delete = 0
				AND l.type = "class_timetable"
				AND l.class = "'.$_GET['class'].'"
			
			';
			
			$data['sub_class'] = $this->db->query($sql)->result_array();
			
		}
		
		if(isset($_GET['class']) && $_GET['sub_class']) {
			
			$month;
			$year;
			if($_GET['month']) {
				$month = date('m', strtotime($_GET['month']));
				$year = date('Y', strtotime($_GET['month']));
			} else {
				$month = date('m');
				$year = date('Y');
			}
			
			// $sql = '
				// SELECT l.* FROM log_join l
				// INNER JOIN tbl_users u
				// ON l.branch = '.branch_now('pid').'
				// AND l.active = 1
				// AND l.is_delete = 0
				// AND l.type = "join_class"
				// AND l.class = '.$_GET['class'].'
				// AND l.user = u.pid
				// AND DATE(l.date) <= "'.$month.'"
				// AND YEAR(l.date) <= "'.$year.'"
				// GROUP BY l.user
			// ';
			
			$sql = '
				SELECT l.* FROM log_join l
				INNER JOIN tbl_users u
				ON l.branch = '.branch_now('pid').'
				AND l.active = 1
				AND l.is_delete = 0
				AND l.type = "join_class"
				AND l.class = '.$_GET['class'].'
				AND l.sub_class = '.$_GET['sub_class'].'
				AND l.user = u.pid
				AND DATE_FORMAT(l.date, "%Y-%m-01") <= "'.date('Y-m-01', strtotime($_GET['month'])).'"
				GROUP BY l.user
			';
			
			if(isset($_GET['sort'])) {
				if($_GET['sort'] == 'asc') {
					$sql .= 'ORDER BY u.fullname_en ASC';
				} else if($_GET['sort'] == 'desc') {
					$sql .= 'ORDER BY u.fullname_en DESC';
				} 
			} else {
				$sql .= 'ORDER BY u.fullname_en ASC';
			}
			
			$data['result'] = $this->db->query($sql)->result_array();
			
			if(!empty($data['result'])) {
				
				$data['result2'] = $this->tbl_classes_model->view($data['result'][0]['class']);

				$classDays= [];
				foreach([
					'dy_1' => '1',
					'dy_2' => '2',
					'dy_3' => '3',
					'dy_4' => '4',
					'dy_5' => '5',
					'dy_6' => '6',
					'dy_7' => '0',
					] as $k => $v) {
					if($data['result2'][0][$k] != null) {
						$classDays[] = $v;
					}
				}
				
				$month = date('m', strtotime($_GET['month']));
				$year = date('Y', strtotime($_GET['month']));
				
				$days = array();
				$firstDay = date('N', mktime(0, 0, 0, $month, 1, $year));
				$addDays = (8 - $firstDay) % 7;
				
				foreach($classDays as $e) {
					$days[] = date('d', mktime(0, 0, 0, $month, $e + $addDays, $year));
					$nextMonth = mktime(0, 0, 0, $month + 1, 1, $year);
					for ($week = 1, $time = mktime(0, 0, 0, $month, $e + $addDays + $week * 7, $year);
						$time < $nextMonth;
						++$week, $time = mktime(0, 0, 0, $month, $e + $addDays + $week * 7, $year))
					{
						$days[] = date('d', $time);
					}
				}
				
				$data['result2'] = $days;

			}
			
		} else {
			
			$data['result'] = [];
			
		}
		
		if(isset($_POST['save'])) {
			
			if(isset($_POST['attendance'])) {
		
				foreach($_POST['attendance'] as $e) {
					
					$user = explode(",", $e)[0];
					$date = explode(",", $e)[1];
					
					$post_data['user'] = $user;
					$post_data['branch'] = branch_now('pid');
					$post_data['type'] = 'class_attendance';
					$post_data['date'] = $date;
					$post_data['class'] = $_GET['class'];
					$post_data['sub_class'] = $_GET['sub_class'];
					$post_data['create_by'] = auth_data('pid');
					$this->log_join_model->add($post_data);

				}
			}
			
			if(isset($_POST['removed_list'])) {
				
				foreach(explode(",", $_POST['removed_list']) as $e) {
					$post_data['is_delete'] = 1;
					$this->log_join_model->edit($e, $post_data);
				}
				
			}
			
			alert_new('success', 'Attendance submitted successfully');
			
			header('refresh: 0'); exit();
		}
		
		$data['teacher'] = $this->tbl_users_model->list('teacher', branch_now('pid'), ['active' => 1]);
		
		$class_filter['active'] = 1;
		$class_filter['is_hidden'] = 0;
		
		if(isset($_GET['teacher']) && !empty($_GET['teacher'])) {
			$class_filter['teacher'] = $_GET['teacher'];
		}
		
		$data['class'] = $this->tbl_classes_model->list(branch_now('pid'), $class_filter);
		
		$this->load->view('inc/header', $data);
		// $this->load->view($this->group . '/classes', $data);
		$this->load->view($this->group . '/classes@v3', $data);
		$this->load->view('inc/footer', $data);
		
	}
	
	public function json_view_attendance($id) {
		
		$result = $this->log_join_model->view($id);
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));
		
	}
	
	public function json_edit_attendance($id, $is_delete) {
		
		$post_data['is_delete'] = $is_delete;
		$this->log_join_model->edit($id, $post_data);
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));
		
	}
	
	public function json_sign_attendance($user, $date, $class) {
		
		$post_data['user'] = $user;
		$post_data['branch'] = branch_now('pid');
		$post_data['type'] = 'class_attendance';
		$post_data['date'] = $date;
		$post_data['class'] = $class;
		$post_data['create_by'] = auth_data('pid');
		$result = $this->log_join_model->add($post_data);
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok' , 'result' => $result]));
		
	}

	public function json_view($id = '')
	{
	
		$result = $this->log_attendance_model->view($id);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}
	
	public function json_del($id = '')
	{
	
		if(!empty($id)) {
			$this->log_attendance_model->del($id);
		}

		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Attendance deleted successfully']));

	}
	
	/*public function json_my_daily()
	{
	
		header('Content-type: application/json');
	
		$login = $this->tbl_users_model->me_token( post_data('token') );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			
			unset($login['password']);

			$attendance = $this->log_attendance_model->list_manual($login['pid'], date('Y-m-d'));

			$result = [
				'session' => $login,
				'attendance' => $attendance,
			];

			die(json_encode([ 'status' => 'ok', 'result' => $result ]));

		} else {

			die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));

		}

	}*/
	
}