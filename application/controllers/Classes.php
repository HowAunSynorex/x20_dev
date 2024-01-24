<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Classes extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'classes';
		$this->single = 'class';
		
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_secondary_model');
		$this->load->model('log_join_model');
		
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");

		auth_must('login');

	}

	public function list()
	{
		
		// echo '<pre>'; print_r( $this->log_join_model->list_classes_students( '162847328880' ) ); exit;
		
		auth_must('login');
		check_module_page('Classes/Read');

		$data['thispage'] = [
			'title' => 'All '.ucfirst($this->group),
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_classes_model->list(branch_now('pid'), [ 'is_hidden' => 0 ]);
		
		$view = 'list';
		if(isset($_GET['teacher'])) {
			$data['result'] = $this->tbl_classes_model->list2([ 'is_hidden' => 0, 'teacher' => $_GET['teacher'] ]);
			$view = 'list_teacher';
		}

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/'.$view, $data);
		$this->load->view('inc/footer', $data);

	}

	public function add()
	{
		
		auth_must('login');
		check_module_page('Classes/Create');

		if(isset($_POST['save'])) {

			$post_data = [];
			
				foreach([ 'title', 'active', 'teacher', 'course', 'fee', 'remark', 'date_start', 'date_end', 'type', 'credit' ] as $e) {
				
				$post_data[$e] = $this->input->post($e);
				
			}

			$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
			$post_data['branch'] = branch_now('pid');
			$post_data['create_by'] = auth_data('pid');
			if( empty($post_data['teacher']) ) $post_data['teacher'] = null;
			if( empty($post_data['course']) ) $post_data['course'] = null;

			$this->tbl_classes_model->add($post_data);
			
			alert_new('success', ucfirst($this->single).' created successfully');
			
			redirect($this->group . '/list');

		}

		$data['thispage'] = [
			'title' => 'Add '.ucfirst($this->single),
			'group' => $this->group,
			'js' => $this->group . '/add',
		];

		$data['teacher'] = $this->tbl_users_model->list('teacher', branch_now('pid'), ['active' => 1]);
		$data['course'] = $this->tbl_secondary_model->list('course', branch_now('pid'), ['active' => 1]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/add');
		$this->load->view('inc/footer', $data);

	}

	public function edit($id = '')
	{

		auth_must('login');
		check_module_page('Classes/Read');
		
		$data['id'] = $id;
		$data['result'] = $this->tbl_classes_model->view($id);
		$data['teacher'] = $this->tbl_users_model->list('teacher', branch_now('pid'), ['active' => 1]);
		$data['course'] = $this->tbl_secondary_model->list('course', branch_now('pid'), ['active' => 1]);
		$data['result2'] = $this->log_join_model->list('join_class', branch_now('pid'), [ 'class' => $id, 'active' => 1 ]);
		
		// active_class_std
		$data['active_class_std'] = 0;
		
		foreach($data['result2'] as $e) {
			if( datalist_Table('tbl_users', 'active', $e['user']) == 1 && datalist_Table('tbl_users', 'is_delete', $e['user']) == 0 ) { 
				$data['active_class_std']++;
			}
		}
		
		if(!isset($_GET['tab'])) $_GET['tab'] = 1;

		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect($this->group.'/list');

		} else {

			$data['thispage'] = [
				'title' => 'Edit '.ucfirst($this->single),
				'group' => $this->group,
				'js' => $this->group.'/edit',
				'css' => $this->group.'/edit',
			];

			$data['result'] = $data['result'][0];
			
			$sql = '
				
				SELECT s.* FROM tbl_secondary s
				INNER JOIN tbl_secondary s2
				ON s.title = s2.title
				AND s.is_delete = 0
				AND s2.is_delete = 0
				AND s.type = "form"
				AND s2.type = "course"
				AND s2.pid = "'.$data['result']['course'].'"
			
			';
			
			$single_form = $this->db->query($sql)->result_array();
			
			$students_data[ 'active' ] = 1;
			if(count($single_form) > 0) {
				$students_data[ 'form' ] = $single_form[0]['pid'];
			}
			// Tan Jing Suan
			// $data['students'] = $this->tbl_users_model->list('student', branch_now('pid'), $students_data);
			$data['students'] = [];
			$allstudents = $this->tbl_users_model->list('student', branch_now('pid'), $students_data);
			foreach ($allstudents as $student) {
				$sqllateststudent = ' SELECT * FROM `tbl_users` 
					WHERE pid="'.$student['pid'].'" ORDER BY update_on DESC LIMIT 1 ';
				$lateststudent = $this->db->query($sqllateststudent)->result_array();
				if ( count($lateststudent) < 0 ) {
					continue;
				}
				if ( $lateststudent[0]['is_delete'] <= 0 ) {
					continue;
				}
				$data['students'][] = $student;
			}
			
			$data['timetables'] = $this->log_join_model->list('class_timetable', branch_now('pid'), ['class' => $data['result']['pid']]);

			if(isset($_POST['save'])) {

				$post_data = [];
				
				foreach([ 'title', 'active', 'teacher', 'course', 'fee', 'remark', 'date_start', 'date_end', 'type', 'credit' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}
				
				if($post_data['type'] != 'check_in') $post_data['credit'] = 1;
				$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
				$post_data['update_by'] = auth_data('pid');
				if( empty($post_data['teacher']) ) $post_data['teacher'] = null;
				if( empty($post_data['course']) ) $post_data['course'] = null;
				
				$this->tbl_classes_model->edit($id, $post_data);
				
				alert_new('success', ucfirst($this->single).' updated successfully');
				
				header('refresh: 0'); exit;

			}

			if(isset($_POST['save_working_hrs'])){
				
				/* foreach(['dy_1', 'dy_2', 'dy_3', 'dy_4', 'dy_5', 'dy_6', 'dy_7'] as $e) {
					$post_data[$e] = !empty($this->input->post($e.'[1]')) ? $this->input->post($e.'[1]').'-'.$this->input->post($e.'[2]') : null;
				}

				$post_data['update_by'] = auth_data('pid');

				$this->tbl_classes_model->edit($id, $post_data); */
				
				
				
				alert_new('success', 'Timetable updated successfully');
				
				header('refresh: 0'); exit;
			
			}
			
			if(isset($_POST['clone'])) {
				
				$result = $this->tbl_classes_model->view($_POST['clone'])[0];
				
				$post_data = [];
			
				foreach([ 'title', 'active', 'teacher', 'course', 'fee', 'remark', 'branch', 'teacher', 'course', 'dy_1', 'dy_2', 'dy_3', 'dy_4', 'dy_5', 'dy_6', 'dy_7' ] as $e) {
				
					$post_data[$e] = $result[$e];
					
				}
				
				$post_data['title'] = $post_data['title'] . ' (Clone)';
				
				$post_data['create_by'] = auth_data('pid');
				
				$new_id = $this->tbl_classes_model->add($post_data);
				
				header('Content-type: application/json');
				die(json_encode(['status' => 'ok', 'message' => ucfirst($this->single) . ' cloned successfully', 'result' => $new_id]));
				
			}
			
			if(isset($_POST['save-swap'])) {
				
				$arr = explode('@', $_POST['class']);
				$date = explode(' ', $arr[0]);
				
				$this->log_join_model->add([
					'type'		=> 'swap_class',
					'branch'	=> branch_now('pid'),
					'user'		=> $_POST['teacher'],
					'class'		=> $id,
					'date'		=> $date[1],
					'remark'	=> $_POST['class'],
				]);
				
				alert_new('success', 'Classes swapped successfully');
				header('refresh: 0'); exit;
				
			}
			
			if(isset($_POST['update_swap'])) {
				
				$arr = explode('@', $_POST['class']);
				$date = explode(' ', $arr[0]);
				
				$this->log_join_model->edit($_POST['id'], [
					'user'		=> $_POST['teacher'],
					'date'		=> $date[1],
					'remark'	=> $_POST['class'],
				]);
				
				alert_new('success', 'Swap updated successfully');
				header('refresh: 0'); exit;
				
			}
			
			if(isset($_POST['edit_swap'])) {
				
				$result = $this->log_join_model->view($_POST['edit_swap'])[0];
				
				$swap_teacher = $this->tbl_users_model->list('teacher', branch_now('pid'), [
					'active' => 1,
					'pid !=' => $data['result']['teacher']
				]);
				
				$teacher = [];
				
				foreach($swap_teacher as $e) {
					$teacher[] = [
						'id' 	=> $e['pid'],
						'text'	=> $e['fullname_en']
					];
				}
				
				$swap_classes = [];
				
				for($i=1; $i<=14; $i++) {
					
					$date = date('Y-m-d', strtotime('+'.$i.'days', time()));
					$day = date('N', strtotime($date));
					$day_name = date('D', strtotime($date));

					$sql = '
					
						SELECT
							c.title as class_title,
							l.time_range as time_range,
							l.title as class_subtitle,
							l.id as class_id,
							"' . $day_name . ' ' . $date . '" as date_time
						FROM log_join l
						INNER JOIN tbl_classes c
						ON l.class = c.pid
						AND c.teacher = "' . $data['result']['teacher'] . '"
						AND c.is_delete = 0
						AND c.active = 1
						AND l.is_delete = 0
						AND l.type = "class_timetable"
						AND l.qty = '.$day.'
					
					';
					
					foreach($this->db->query($sql)->result_array() as $e) {
						$swap_classes[] = $e;
					}
					
				}
					
				$swapped_class = $this->log_join_model->list('swap_class', branch_now('pid'), [
					'type'		=> 'swap_class',
					'class'		=> $id,
					'id !='		=> $_POST['edit_swap'],
				]);
				
				$check_class = [];
				$class = [];
				
				foreach($swapped_class as $e) {
					$check_class[] = $e['remark'];
				}
			
				foreach($swap_classes as $e) {
					if(!in_array($e['date_time'] . '@' . $e['class_id'], $check_class)) {
						$class[] = [
							'id' 	=> $e['date_time'] . '@' . $e['class_id'],
							'text'	=> $e['date_time'] . ' ' . $e['time_range'] . ' ' . $e['class_subtitle'],
						];
					}
				}
				
				header('Content-type: application/json');
				die(json_encode(['status' => 'ok', 'result' => $result, 'teacher' => $teacher, 'class' => $class]));
				
			}

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}
	
	public function manage_time()
	{
		auth_must('login');
		if ($_POST['action_take'] == 'add')
		{
			$this->log_join_model->add([
				'qty' => $_POST['dy_id'],
				'title' => $_POST['title'],
				'class' => $_POST['class'],
				'time_range' => $_POST['time_start'].'-'.$_POST['time_end'],
				'branch' => branch_now('pid'),
				'type' => 'class_timetable',
			]);
		}
		else if ($_POST['action_take'] == 'edit')
		{	
			$this->log_join_model->edit($_POST['id'], ['time_range' => $_POST['time_start'].'-'.$_POST['time_end'], 'title' => $_POST['title']]);
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));

	}

	public function json_del_time($id = '')
	{
		auth_must('login');
	
		if(!empty($id)) {
			
			$this->log_join_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Timetable deleted successfully']));

	}

	public function json_del($id = '')
	{
		
		auth_must('login');
		check_module_page('Classes/Delete');
	
		if(!empty($id)) {
			
			$this->tbl_classes_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($this->single) . ' deleted successfully']));

	}
	
	public function json_del_swap($id = '')
	{
		
		auth_must('login');
		check_module_page('Classes/Delete');
	
		if(!empty($id)) {
			
			$this->log_join_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Swap deleted successfully']));

	}

	public function json_view($id = '')
	{
	
		$result = $this->tbl_classes_model->view($id);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}
	
	public function json_edit_join($id = '')
	{
		$data['active'] = 0;
		$this->log_join_model->edit($id, $data);
		
		alert_new('success', 'Student removed successfully');
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Student removed successfully']));

	}

}
