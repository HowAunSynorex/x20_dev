<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'students';
		$this->single = 'student';
		
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_secondary_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_inventory_model');
		$this->load->model('tbl_uploads_model');
		$this->load->model('log_join_model');
		$this->load->model('log_payment_model');
		$this->load->model('log_inventory_model');

		auth_must('login');
		
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");

	}


	public function list($type = '')
	{
		
		/* for($i=1; $i<510; $i++) {
			$this->tbl_users_model->add([
				'type' => 'student',
				'branch' => branch_now('pid'),
				'fullname_en'	=> 'dd'.$i
			]);
			sleep(1);
		} */
		
		auth_must('login');
		check_module_page('Students/Read');

		$this->load->library('session');
		$this->load->library('pagination');
		
		$title = empty($type) ? 'All '.ucfirst($this->group) : 'Pending';
		$type = empty($type) ? 'student' : 'student_pending';
		
		$data['thispage'] = [
			'title' => $title,
			'group' => $this->group,
			'type'	=> $type,
			'js' => $this->group.'/list',
			'css' => $this->group.'/list',
		];
		
		$data['class_bundle'] = $this->tbl_secondary_model->list('class_bundle', branch_now('pid'), [ 'active' => 1 ]);
		
		$sql = '
			SELECT * FROM tbl_secondary
			WHERE is_delete = 0
			AND active = 1
			AND type = "form"
			AND branch = "'.branch_now('pid').'"
			ORDER BY FIELD(title, "K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5", "G2023")
		';

		$data['form'] = $this->db->query($sql)->result_array();
		
		/*
		 * all students
		 *
		**/
		
		$sql = '
			SELECT u.*, (year(CURDATE()) - year(birthday)) AS age, schools.title AS school_title, forms.title AS form_title, COALESCE(classes.join_class_title, "-") AS join_class_title, COALESCE(parents.parent_fullname_en, "-") AS parent_fullname_en
			FROM tbl_users u
			LEFT JOIN tbl_secondary schools ON schools.pid = u.school
			LEFT JOIN tbl_secondary forms ON forms.pid = u.form
			LEFT JOIN (
				SELECT log_join.user, GROUP_CONCAT(tbl_classes.title SEPARATOR "<br>") AS join_class_title
				FROM log_join
				JOIN tbl_classes ON tbl_classes.pid = log_join.class
				WHERE log_join.is_delete = 0
				AND log_join.branch = "'.branch_now('pid').'"
				AND log_join.type = "join_class"
				AND log_join.active = 1
				GROUP BY log_join.user
			) classes ON classes.user = u.pid
			LEFT JOIN (
				SELECT log_join.user, GROUP_CONCAT(tbl_users.fullname_en SEPARATOR "<br>") AS parent_fullname_en
				FROM log_join
				JOIN tbl_users ON tbl_users.pid = log_join.parent
				WHERE log_join.is_delete = 0
				AND log_join.branch = "'.branch_now('pid').'"
				AND log_join.type = "join_parent"
				AND log_join.active = 1
				GROUP BY log_join.user
			) parents ON parents.user = u.pid
			WHERE u.is_delete = 0
			AND u.type = "'.$type.'" AND u.branch = "'.branch_now('pid').'"
		';

		$data['total_count'] = count($this->db->query($sql)->result_array());
			
		if(isset($_GET['parent'])) {
			
			$sql .= ' AND EXISTS (SELECT log_join.parent FROM log_join WHERE user = u.pid AND log_join.is_delete = 1 AND log_join.parent = "'. $_GET['parent'] .'" LIMIT 1)';
		}

		if(isset($_GET['class'])) {

			$sql .= ' AND EXISTS (SELECT log_join.student FROM log_join WHERE type = "join_class" AND user = u.pid AND branch = "'. branch_now('pid') .'" AND log_join.active = 1 AND log_join.class = "'. $_GET['class'] .'" LIMIT 1)';
		}

		if(isset($_GET['active'])) {

			$sql .= ' AND u.active= '.$_GET['active'];
		}

		if(isset($_GET['class'])) {

			$data['result'] = $this->log_join_model->student_list('join_class', branch_now('pid'), ["class" => $_GET['class']]);
			
		}

		if(isset($_GET['search'])) {

			$search_param = ['fullname_en', 'fullname_cn', 'rfid_cardid', 'phone', 'code', 'form'];
			foreach($search_param as $e) {
				
				if(isset($_GET[$e])) {
					if(!empty($_GET[$e])) {
						if ($e == 'code')
						{
							$sql .= ' AND u.'. $e.' LIKE "%'.$_GET[$e].'%"';
						}
						else
						{
							
							$sql .= ' AND '.$e.' LIKE "%'.$_GET[$e].'%"';
						}
					}
				}
				
			}
		}
		
		$load_view = ($type == 'student_pending') ? 'pending' : 'list';
		
		$per_page = 100;
		$_GET['per_page'] = isset($_GET['per_page']) ? $_GET['per_page'] : 0;
		$_GET['sort'] = isset($_GET['sort']) ? $_GET['sort'] : 'u.code';
		$_GET['order'] = isset($_GET['order']) ? $_GET['order'] : 'ASC';
		$data['row'] = (($_GET['per_page'] > 0 ? ($_GET['per_page'] - 1) : $_GET['per_page']) * $per_page);

		if ($_GET['sort'] != '' AND $_GET['sort'] != '')
		{
			$sql .= ' ORDER BY '. $_GET['sort']. ' '. $_GET['order'];
		}
		$sql .= ' LIMIT '. (($_GET['per_page'] > 0 ? ($_GET['per_page'] - 1) : $_GET['per_page']) * $per_page). ', '. $per_page;

		$config = array();
		
		if ($type == 'student')
		{
			$config['base_url'] = base_url('/students/list');
		}
		else
		{
			$config['base_url'] = base_url('/students/list').$type;
		}
		
		$config['total_rows'] = $data['total_count'];
		$config['per_page'] = 100;
		$config['use_page_numbers'] = TRUE;
		$config['page_query_string'] = TRUE;
        $config["uri_segment"] = 2;
		$config['enable_query_strings'] = TRUE;
		$config['reuse_query_string'] = TRUE;
			  
		$config['full_tag_open'] = '<ul class="pagination float-right">';
		$config['full_tag_close'] = '</ul>';
		$config['num_tag_open'] = '<li class="page-item"><span class="page-link">';
		$config['num_tag_close'] = '</span></li>';
		$config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';
		$config['prev_tag_close'] = '</span></li>';
		$config['next_tag_open'] = '<li class="page-item"><span class="page-link">';
		$config['next_tag_close'] = '</span></li>';
		$config['prev_link'] = '<i class="fas fa-backward"></i>';
		$config['next_link'] = '<i class="fas fa-forward"></i>';
		$config['last_tag_open'] = '<li class="page-item"><span class="page-link">';
		$config['last_tag_close'] = '</span></li>';
		$config['first_tag_open'] = '<li class="page-item"><span class="page-link">';
		$config['first_tag_close'] = '</span></li>';
		

		// Initialize
		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();
		$data['result'] = [];
		
		if ($type == 'student')
		{
			if (isset($_GET['search']))
			{
				$data['result'] = $this->db->query($sql)->result_array();
			}
		}
		else
		{
			$data['result'] = $this->db->query($sql)->result_array();
		}
		
		if(isset($_GET['parent'])) {
		    $r = [];
		    $c = $this->log_join_model->list('join_parent', branch_now('pid'), [ 'parent' => $_GET['parent'], 'active' => 1 ]);
		    foreach($c as $e) {
		        $er = $this->tbl_users_model->list_v2([ 'pid' => $e['user'] ]);
		        if(count($er) == 1) {
		            $a = $er[0];
		            $a['school_title'] = datalist_Table('tbl_secondary', 'title', $a['school']);
		            $a['form_title'] = datalist_Table('tbl_secondary', 'title', $a['form']);
		            $r[] = $a;
		        }
		    }
		    $data['result'] = $r;
		}
		
		//echo '<pre>'; print_r($this->db->last_query()); exit;
		
		if(isset($_POST['del'])) {
			
			foreach($_POST['student'] as $e) {
				$this->tbl_users_model->del($e);
			}
			
			alert_new('success', 'Student deleted successfully');
			header('refresh: 0'); exit;
			
		}
		
		if(isset($_POST['active'])) {
			
			foreach($_POST['student'] as $e) {
				$this->tbl_users_model->edit($e, ['active' => 1]);
			}
			
			alert_new('success', 'Student activated successfully');
			header('refresh: 0'); exit;
			
		}
		
		if(isset($_POST['inactive'])) {
			
			foreach($_POST['student'] as $e) {
				$this->tbl_users_model->edit($e, ['active' => 0]);
			}
			
			alert_new('success', 'Student inactivated successfully');
			header('refresh: 0'); exit;
			
		}
		
		if(isset($_POST['approve'])) {

			$student = $this->tbl_users_model->view($_POST['approve']);
			
			if(count($student) == 1) {
				
				$student = $student[0];
								
				/* $school = '';
				
				if(!empty($student['school'])) {
					$check_school = $this->tbl_secondary_model->list('school', $student['branch'], [ 'title' => $student['school'] ]);
					
					if(count($check_school) > 0) {
						$school = $check_school[0]['pid'];
					} else {
						$school = $this->tbl_secondary_model->add([
							'type'		=> 'school',
							'title'		=> $student['school'],
							'branch'	=> $student['branch'],
						]);
					}
				} */
				
				if(!empty($student['parent'])) {
					$check_parent = $this->tbl_users_model->list('parent', $student['branch'], [ 'fullname_en' => $student['parent'] ]);
					if(count($check_parent) > 0) {
						$parent = $check_parent[0]['pid'];
					} else {
						$parent = $this->tbl_users_model->add([
							'type'			=> 'parent',
							'fullname_en'	=> $student['parent'],
							'fullname_cn'	=> $student['parent_cn'],
							'branch'		=> $student['branch'],
							'phone'			=> $student['temp_parent_phone'],
							'gender'		=> $student['temp_parent_gender'],
							'remark'		=> $student['temp_parent_relationship'],
						]);
					}
					$this->log_join_model->add([
						'type'			=> 'join_parent',
						'branch'		=> $student['branch'],
						'title'			=> $student['temp_parent_relationship'],
						'parent'		=> $parent,
						'user'			=> $student['pid'],
						'create_by'		=> auth_data('pid'),
					]);
				}
				
				$temp_class = json_decode($student['temp_class'], true);
				if(empty($temp_class)) $temp_class = [];
				
				foreach($temp_class as $e) {
					$this->log_join_model->add([
						'type'			=> 'join_class',
						'user'			=> $student['pid'],
						'branch'		=> $student['branch'],
						'class'			=> $e,
						'active'		=> 1,
						'date'			=> date('Y-m-d'),
					]);
				}
				
				$this->tbl_users_model->edit($student['pid'], [
					'type'			=> 'student',
					'active'		=> 1,
					'date_join'		=> date('Y-m-d'),
					'temp_class'	=> '',
				]);
				
				die(json_encode([ 'status' => 'ok', 'message' => 'Student approved successfully' ]));
				
			} else {
				
				die(json_encode([ 'status' => 'error', 'message' => 'Student not found' ]));
			}
			
			header('Content-type: application/json');
			die(json_encode([ 'status' => 'ok', 'message' => 'Student approved successfully' ]));
			
		}
		
		if(isset($_POST['reject'])) {
			
			$student = $this->tbl_users_model->edit($_POST['reject'], [
				'type'	=> 'rejected',
			]);
			
			header('Content-type: application/json');
			die(json_encode([ 'status' => 'ok', 'message' => 'Student rejected successfully' ]));
			
		}

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/'.$load_view, $data);
		$this->load->view('inc/footer', $data);

	}

	public function list2($type = '')
	{
		
		/* for($i=1; $i<510; $i++) {
			$this->tbl_users_model->add([
				'type' => 'student',
				'branch' => branch_now('pid'),
				'fullname_en'	=> 'dd'.$i
			]);
			sleep(1);
		} */
		
		auth_must('login');
		check_module_page('Students/Read');
		
		$title = empty($type) ? 'All '.ucfirst($this->group) : 'Pending';
		$type = empty($type) ? 'student' : 'student_pending';
		
		$data['thispage'] = [
			'title' => $title,
			'group' => $this->group,
			'type'	=> $type,
			'js' => $this->group.'/list',
			'css' => $this->group.'/list',
		];
				
		/*
		 * all students
		 *
		**/
		$data['all'] = $this->tbl_users_model->list_v2([
			'type'		=> $type, 
			'branch'	=> branch_now('pid'),
		]);
		
		$data['form'] = $this->tbl_secondary_model->list('form', branch_now('pid'), [ 'active' => 1 ]);
			
		$load_view = 'lite_dtable';
		
		$data['min'] = $this->tbl_users_model->student_list('student', branch_now('pid'), ['birthday !=' => null], '', ['birthday' => 'asc']);
		if(count($data['min']) > 0)
		{
			$data['min'] = $data['min'][0];
			$data['min'] = date("Y") - date('Y',strtotime($data['min']['birthday']));
		} else {
			$data['min'] = 0;
		}
		
		$data['max'] = $this->tbl_users_model->student_list('student', branch_now('pid'), ['birthday !=' => null], '', ['birthday' => 'desc']);
		if(count($data['max']) > 0)
		{
			$data['max'] = $data['max'][0];
			$data['max'] = date("Y") - date('Y',strtotime($data['max']['birthday']));
		} else {
			$data['max'] = 0;
		}
		
		if(isset($_GET['parent'])) {
			
			$sql = '
			
				SELECT u.* FROM tbl_users u
				INNER JOIN log_join l
				ON l.user = u.pid
				AND l.is_delete = 0
				AND u.is_delete = 0
				AND u.type = "student"
				AND l.parent = "'.$_GET['parent'].'"
			
			';

			$data['result'] = $this->db->query($sql)->result_array();

		} else if(isset($_GET['class'])) {

			$data['result'] = $this->log_join_model->student_list('join_class', branch_now('pid'), ["class" => $_GET['class']]);
			
		} else if(isset($_GET['search'])) {

			$search_param = ['fullname_en', 'fullname_cn', 'rfid_cardid', 'phone', 'code', 'form'];
			$query = 'SELECT *, (year(CURDATE()) - year(birthday)) AS age 
					FROM tbl_users 
					WHERE type = "student" AND is_delete = 0 AND branch = '.branch_now('pid');
			
			// print_r(1); exit;
			
			foreach($search_param as $e) {
				
				if(isset($_GET[$e])) {
					if(!empty($_GET[$e])) {
						// if($e == 'age') {
							// if($_GET[$e] <> '') {
								// $query .= ' AND year(birthday) = (year(CURDATE()) - '.$_GET[$e].') ';
							// } 
						// if($e == 'form') {
							// if($_GET[$e] <> '') {
								// $query .= ' AND form = "'.$_GET[$e].'"';
							// } 
						// } else {
							$query .= ' AND '.$e.' LIKE "%'.$_GET[$e].'%"';
						// }
					}
				}
				
			}
			
			// print_r($query); exit;
			
			$data['result'] = $this->db->query($query)->result_array();
			
		} elseif(count($data['all']) <= 500 || isset($_GET['dtable'])) {
			
			$data['result'] = $data['all'];
			
		} else {
			/* 
			if(!isset($_GET['p'])) $_GET['p'] = 1;
			$limit = 100;
			$data['limit'] = $limit;
			
			if(!isset($_GET['sort_name'])) $_GET['sort_name'] = 'ASC';
			if(!isset($_GET['sort_join'])) $_GET['sort_join'] = 'ASC';
			if(!isset($_GET['sort_status'])) $_GET['sort_status'] = 'ASC';
			if(!isset($_GET['sort_gender'])) $_GET['sort_gender'] = 'ASC';
			if(!isset($_GET['sort_phone'])) $_GET['sort_phone'] = 'ASC';
			if(!isset($_GET['sort_school'])) $_GET['sort_school'] = 'ASC';
			if(!isset($_GET['sort_parent'])) $_GET['sort_parent'] = 'ASC';
			if(!isset($_GET['sort_form'])) $_GET['sort_form'] = 'ASC';
			$data['sort_name'] = $_GET['sort_name'] == 'ASC' ? 'DESC' : 'ASC' ; 
			$data['sort_join'] = $_GET['sort_join'] == 'ASC' ? 'DESC' : 'ASC' ; 
			$data['sort_status'] = $_GET['sort_status'] == 'ASC' ? 'DESC' : 'ASC' ; 
			$data['sort_gender'] = $_GET['sort_gender'] == 'ASC' ? 'DESC' : 'ASC' ; 
			$data['sort_phone'] = $_GET['sort_phone'] == 'ASC' ? 'DESC' : 'ASC' ; 
			$data['sort_school'] = $_GET['sort_school'] == 'ASC' ? 'DESC' : 'ASC' ; 
			$data['sort_parent'] = $_GET['sort_parent'] == 'ASC' ? 'DESC' : 'ASC' ; 
			$data['sort_form'] = $_GET['sort_form'] == 'ASC' ? 'DESC' : 'ASC' ; 
			
			$limit_start = $limit * ( $_GET['p'] - 1 );
			
			$sort_array = [];
			
			if( isset($_GET['sort_name_click']) && isset($_GET['sort_name']) ) $sort_array['fullname_en'] = $_GET['sort_name'];
			if( isset($_GET['sort_join_click']) && isset($_GET['sort_join']) ) $sort_array['date_join'] = $_GET['sort_join'];
			if( isset($_GET['sort_status_click']) && isset($_GET['sort_status']) ) $sort_array['active'] = $_GET['sort_status'];
			if( isset($_GET['sort_gender_click']) && isset($_GET['sort_gender']) ) $sort_array['gender'] = $_GET['sort_gender'];
			if( isset($_GET['sort_phone_click']) && isset($_GET['sort_phone']) ) $sort_array['phone'] = $_GET['sort_phone'];
			if( isset($_GET['sort_school_click']) && isset($_GET['sort_school']) ) $sort_array['school'] = $_GET['sort_school'];
			if( isset($_GET['sort_parent_click']) && isset($_GET['sort_parent']) ) $sort_array['parent'] = $_GET['sort_parent'];
			if( isset($_GET['sort_form_click']) && isset($_GET['sort_form']) ) $sort_array['form'] = $_GET['sort_form'];
			
			$data['result'] = $this->tbl_users_model->student_list('student', branch_now('pid'), [], [ $limit, $limit_start ], $sort_array);
			$data['p_section'] = 1;

			$max_p = count($data['result']) > 0 ? count($data['all']) / $limit : 1 ;
			$max_p2 = ceil($max_p);
			$data['max_p'] = $max_p < $max_p2 ? $max_p2 : $max_p;
			*/
			
			$data['result'] = $this->tbl_users_model->student_list('student', branch_now('pid'));
			$load_view = 'list';
			
		} 
		
		if($type == 'student_pending') $load_view = 'pending';
		
		if(isset($_GET['dev'])) {
			echo count($data['all']); exit;
		}
		
		if(isset($_POST['del'])) {
			
			foreach($_POST['student'] as $e) {
				$this->tbl_users_model->del($e);
			}
			
			alert_new('success', 'Student deleted successfully');
			header('refresh: 0'); exit;
			
		}
		
		if(isset($_POST['active'])) {
			
			foreach($_POST['student'] as $e) {
				$this->tbl_users_model->edit($e, ['active' => 1]);
			}
			
			alert_new('success', 'Student activated successfully');
			header('refresh: 0'); exit;
			
		}
		
		if(isset($_POST['inactive'])) {
			
			foreach($_POST['student'] as $e) {
				$this->tbl_users_model->edit($e, ['active' => 0]);
			}
			
			alert_new('success', 'Student inactivated successfully');
			header('refresh: 0'); exit;
			
		}
		
		if(isset($_POST['approve'])) {
			
			$student = $this->tbl_users_model->view($_POST['approve']);
			
			if(count($student) == 1) {
				
				$student = $student[0];
				
				$school = '';
				$parent = '';
				
				if(!empty($student['school'])) {
					$check_school = $this->tbl_secondary_model->list('school', $student['branch'], [ 'title' => $student['school'] ]);
					
					if(count($check_school) > 0) {
						$school = $check_school[0]['pid'];
					} else {
						$school = $this->tbl_secondary_model->add([
							'type'		=> 'school',
							'title'		=> $student['school'],
							'branch'	=> $student['branch'],
						]);
					}
				}
				
				if(!empty($student['parent'])) {
					$check_parent = $this->tbl_users_model->list('parent', $student['branch'], [ 'fullname_en' => $student['parent'] ]);
					
					if(count($check_parent) > 0) {
						$parent = $check_parent[0]['pid'];
					} else {
						$parent = $this->tbl_users_model->add([
							'type'			=> 'parent',
							'fullname_en'	=> $student['parent'],
							'fullname_cn'	=> $student['parent_cn'],
							'branch'		=> $student['branch'],
							'phone'			=> $student['temp_parent_phone'],
							'gender'		=> $student['temp_parent_gender'],
							'title'			=> $student['temp_parent_relationship'],
						]);
					}
				}
				
				$temp_class = json_decode($student['temp_class'], true);
				if(empty($temp_class)) $temp_class = [];
				
				foreach($temp_class as $e) {
					$this->log_join_model->add([
						'type'			=> 'join_class',
						'user'			=> $student['pid'],
						'branch'		=> $student['branch'],
						'class'			=> $e,
						'active'		=> 1,
						'date'			=> date('Y-m-d'),
					]);
				}
				
				$this->tbl_users_model->edit($student['pid'], [
					'type'			=> 'student',
					'active'		=> 1,
					'school'		=> $school,
					'parent'		=> $parent,
					'date_join'		=> date('Y-m-d'),
					'temp_class'	=> '',
				]);
				
				die(json_encode([ 'status' => 'ok', 'message' => 'Student approved successfully' ]));
				
			} else {
				
				die(json_encode([ 'status' => 'error', 'message' => 'Student not found' ]));
			}
			
			header('Content-type: application/json');
			die(json_encode([ 'status' => 'ok', 'message' => 'Student approved successfully' ]));
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/'.$load_view, $data);
		$this->load->view('inc/footer', $data);

	}
	

	public function bulk()
	{
		
		auth_must('login');
		
		if(!isset($_POST['bulk'])) $_POST['bulk'] = [];

		// run bulk
		if( isset($_POST['yes']) && isset($_POST['action']) ) {
			
			foreach($_POST['bulk'] as $e) {
				
				switch($_POST['action']) {
					
					case 'DELETE':
						$this->tbl_users_model->del($e);
						break;
					
				}
				
			}
			
			alert_new('success', 'Bulk updated successfully');
			
			redirect('students/list');
			
		}
		
		// check action
		if(isset($_POST['delete'])) {
			$data['action'] = 'DELETE';
			check_module_page('Students/Delete');
		} else {
			die(app('title').': Action required');
		}
		
		$data['thispage'] = [
			'title' => 'Bulk Update',
			'group' => $this->group,
		];
		
		$data['output'] = '';
		foreach($_POST['bulk'] as $e) {
			
			$data['output'] .= '['.$data['action'].'] '.datalist_Table('tbl_users', 'fullname_en', $e).PHP_EOL;
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/bulk', $data);
		$this->load->view('inc/footer', $data);

	}

	public function add()
	{

		auth_must('login');
		check_module_page('Students/Create');

		if(isset($_POST['save'])) {

			if(!$this->tbl_users_model->check_username( $this->input->post('username') ) && !empty( $this->input->post('username') ) ) {

				alert_new('warning', 'Username has been taken');
				
				redirect($this->uri->uri_string());

			} else {
				
				if(!$this->tbl_users_model->check_rfid( $this->input->post('rfid_cardid'), branch_now('pid') ) && !empty( $this->input->post('rfid_cardid') ) ) {

					alert_new('warning', 'Card ID has been taken');
					
					redirect($this->uri->uri_string());
				
				} else {

					$post_data = [];
					
					foreach([ 'username', 'active', 'nickname', 'password', 'fullname_en', 'fullname_cn', 'gender', 'nric', 'birthday', 'email', 'email2', 'email3', 'phone', 'phone2', 'phone3', 'address', 'address2', 'address3', 'date_join', 'school', 'rfid_cardid', 'remark', 'transport', 'car_plate_no', 'code', 'remark_active', 'remark_important', 'form', 'childcare', 'address_pickup', 'address_dropoff', 'childcare_teacher', 'form_teacher', 'date_call' ] as $e) {
						
						$post_data[$e] = $this->input->post($e);
						
					}
                  
					$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
					$post_data['branch'] = branch_now('pid');
					$post_data['create_by'] = auth_data('pid');
					$post_data['password'] = password_hash($post_data['password'], PASSWORD_DEFAULT);
					$post_data['type'] = 'student';
					
					$image = null;
			
					if(isset($_FILES['image'])) {

						$target_dir = "uploads/data/";

						if ($_FILES['image']['size'] != 0) {

							$temp = explode(".", $_FILES["image"]["name"]);
							$newfilename = get_new('id') . '.' . end($temp);
							$target_file = $target_dir . $newfilename;
							$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
							move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

							$file_size = $_FILES["image"]["size"];
							$image_data['file_name'] = $_FILES["image"]["name"];
							$image_data['file_type'] = $fileType;
							$image_data['file_size'] = $file_size;
							$image_data['file_source'] = base_url($target_file);
							$image_data['create_by'] = auth_data('pid');
							$image_data['type'] = 'avatar_student';
							$image = $this->tbl_uploads_model->add($image_data);

						}
						
					}
					
					$post_data['image'] = $image;

					if( empty($post_data['school']) ) $post_data['school'] = null;
					// if( empty($post_data['parent']) ) $post_data['parent'] = null;
					if( empty($post_data['birthday']) ) $post_data['birthday'] = null;
					if( empty($post_data['date_join']) ) $post_data['date_join'] = null;
					
					$NewId = $this->tbl_users_model->add($post_data);
					
					foreach($_POST['question'] as $k => $v)
					{
						$question_data = ['type' => 'student_question', 'branch' => branch_now('pid'), 'user' => $NewId, 'title' => $k,'remark' => $v ];
						
						$this->log_join_model->add($question_data);
					}
					
					alert_new('success', ucfirst($this->single).' created successfully');
					
					redirect($this->group . '/edit/'.$NewId);
					// redirect($this->group . '/list');
					
				}

			}

		}

		$data['thispage'] = [
			'title' => 'Add '.ucfirst($this->single),
			'group' => $this->group,
			'js' => $this->group.'/add'
		];

		$data['parent'] = $this->tbl_users_model->active_list('parent', branch_now('pid'));
		$data['school'] = $this->tbl_secondary_model->active_list('school', branch_now('pid'));
		$data['form'] = $this->tbl_secondary_model->active_list('form', branch_now('pid'));
		$data['childcare'] = $this->tbl_secondary_model->active_list('childcare', branch_now('pid'));
		$data['teacher'] = $this->tbl_users_model->list('teacher', branch_now('pid'));
		
		$branch_transport = [];
		foreach($this->log_join_model->list('secondary', branch_now('pid'), ['active' => 1]) as $e) {
			if(datalist_Table('tbl_secondary', 'type', $e['secondary']) == 'transport') {
				$branch_transport[] = $e;
			}
		}
		$data['transport'] = $this->tbl_secondary_model->list('transport', branch_now('pid'), ['active' => 1]);
		$data['branch_transport'] = $branch_transport;
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/add');
		$this->load->view('inc/footer', $data);

	}

	public function edit($id = '')
	{
		
		auth_must('login');
		check_module_page('Students/Read');

		$data['result'] = $this->tbl_users_model->view($id);
		$data['parent'] = $this->tbl_users_model->active_list('parent', branch_now('pid'));
		$data['school'] = $this->tbl_secondary_model->active_list('school', branch_now('pid'));
		$data['form'] = $this->tbl_secondary_model->active_list('form', branch_now('pid'));
		$data['course'] = $this->tbl_secondary_model->active_list('course', branch_now('pid'));
		$data['class'] = $this->tbl_classes_model->list(branch_now('pid'), ['active' => 1, 'is_hidden' => 0]);
		$data['services'] = $this->tbl_inventory_model->list(branch_now('pid'), 'item', ['active' => 1, 'item_type' => 'service']);
		$data['childcare'] = $this->tbl_secondary_model->active_list('childcare', branch_now('pid'));
		$data['teacher'] = $this->tbl_users_model->list('teacher', branch_now('pid'));
		$data['items'] = $this->tbl_inventory_model->list( branch_now('pid'), 'item', ['active' => 1]);
		$data['unpaid_items'] = $this->log_join_model->list('unpaid_item', branch_now('pid'), ['user' => $id]);
		$data['active_class'] = $this->log_join_model->list('join_class', branch_now('pid'), ['user' => $id]);
		$data['active_service'] = $this->log_join_model->list('service_item', branch_now('pid'), ['user' => $id]);
		$data['questions'] = $this->log_join_model->list('student_question', branch_now('pid'), ['user' => $id]);
		$data['class_bundle'] = $this->tbl_secondary_model->list('class_bundle', branch_now('pid'), [ 'active' => 1, 'is_delete' => 0 ]);

		$branch_transport = [];
		foreach($this->log_join_model->list('secondary', branch_now('pid'), ['active' => 1]) as $e) {
			if(datalist_Table('tbl_secondary', 'type', $e['secondary']) == 'transport') {
				$branch_transport[] = $e;
			}
		}
		$data['transport'] = $this->tbl_secondary_model->list('transport', branch_now('pid'), ['active' => 1]);
		$data['branch_transport'] = $branch_transport;
		
		$data['join_parent'] = $this->log_join_model->list('join_parent', branch_now('pid'), [ 'user' => $id, 'active' => 1 ]);

		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect($this->group.'/list');

		} else {

			$data['thispage'] = [
				'title' => 'Edit '.ucfirst($this->single),
				'group' => $this->group,
				'css' => $this->group . '/edit',
				'js' => $this->group . '/edit',
			];

			$data['result'] = $data['result'][0];

			// save student profile
			if(isset($_POST['save'])) {
				
				if(!$this->tbl_users_model->check_username( $this->input->post('username'), $id ) && !empty( $this->input->post('username') ) ) {

		        	alert_new('warning', 'Username has been taken');
		            
	        	} else {
					
					if(!$this->tbl_users_model->check_rfid( $this->input->post('rfid_cardid'), branch_now('pid'), $id ) && !empty( $this->input->post('rfid_cardid') ) ) {

						alert_new('warning', 'Card ID has been taken');
						
						redirect($this->uri->uri_string());
					
					} else {
					
						$post_data = [];
						
						foreach([ 'username', 'active', 'nickname', 'password', 'fullname_en', 'fullname_cn', 'gender', 'nric', 'birthday', 'email', 'email2', 'email3', 'phone', 'phone2', 'phone3', 'address', 'address2', 'address3', 'date_join', 'school', 'rfid_cardid', 'remark', 'transport', 'car_plate_no', 'guardian', 'code', 'remark_active', 'remark_important', 'form', 'childcare', 'address_pickup', 'address_dropoff', 'childcare_teacher', 'form_teacher' ,'date_call'] as $e) {
						
							$post_data[$e] = $this->input->post($e);
							
						}
		
						$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
						$post_data['branch'] = branch_now('pid');
						$post_data['update_by'] = auth_data('pid');
						$post_data['password'] = !empty($post_data['password']) ? password_hash($post_data['password'], PASSWORD_DEFAULT) : $data['result']['password'] ;
						
						$image = $data['result']['image'];
			
						if(isset($_FILES['image'])) {

							$target_dir = "uploads/data/";

							if ($_FILES['image']['size'] != 0) {

								$temp = explode(".", $_FILES["image"]["name"]);
								$newfilename = get_new('id') . '.' . end($temp);
								$target_file = $target_dir . $newfilename;
								$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
								move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

								$file_size = $_FILES["image"]["size"];
								$image_data['file_name'] = $_FILES["image"]["name"];
								$image_data['file_type'] = $fileType;
								$image_data['file_size'] = $file_size;
								$image_data['file_source'] = base_url($target_file);
								$image_data['create_by'] = auth_data('pid');
								$image_data['type'] = 'avatar_student';
								$image = $this->tbl_uploads_model->add($image_data);

							}
							
						}
						
						$post_data['image'] = $image;
						
						if( empty($post_data['school']) ) $post_data['school'] = null;
						// if( empty($post_data['parent']) ) $post_data['parent'] = null;
						if( empty($post_data['birthday']) ) $post_data['birthday'] = null;
						if( empty($post_data['date_join']) ) $post_data['date_join'] = null;
						
						$this->tbl_users_model->edit($id, $post_data);
						
						foreach($_POST['question'] as $k => $v)
						{
							$find_key = ['user' => $id, 'title' => $k];
							
							$question_exist = $this->log_join_model->list('student_question', branch_now('pid'), $find_key);
							
							if (count($question_exist) > 0)
							{
								$this->log_join_model->edit($question_exist[0]['id'], [ 'remark' => $v ]);
							}
							else
							{
								$question_data = ['type' => 'student_question', 'branch' => branch_now('pid'), 'user' => $id, 'title' => $k,'remark' => $v ];
								
								$this->log_join_model->add($question_data);
							}
						}
						
						alert_new('success', ucfirst($this->single).' updated successfully');
						
					}

				}
		
				header('refresh: 0'); exit;
				
			}
			
			//add new parent
			if(isset($_POST['add_new_parent']))
			{
				$parent_data['fullname_en'] = $this->input->post('fullname_en');
				$parent_data['fullname_cn'] = $this->input->post('fullname_cn');
				$parent_data['active'] = 1;
				$parent_data['branch'] = branch_now('pid');
				$parent_data['create_by'] = auth_data('pid');
				$parent_data['type'] = 'parent';
				
				$new_parent_id = $this->tbl_users_model->add($parent_data);
				
				$post_data['user'] = $id;
				$post_data['parent'] = $new_parent_id;
				$post_data['title'] = $this->input->post('relationship');
				$post_data['type'] = 'join_parent';
				$post_data['branch'] = branch_now('pid');
				$post_data['create_by'] = auth_data('pid');

				$this->log_join_model->add($post_data);
				
				alert_new('success', 'Parent added successfully');
				redirect($this->uri->uri_string()."?tab=3");
			}
			
			// add parent
			if(isset($_POST['add-parent'])) {
				
				$post_data['user'] = $id;
				$post_data['parent'] = $this->input->post('parent');
				$post_data['title'] = $this->input->post('relationship');
				$post_data['type'] = 'join_parent';
				$post_data['branch'] = branch_now('pid');
				$post_data['create_by'] = auth_data('pid');

				$this->log_join_model->add($post_data);
				
				alert_new('success', 'Parent added successfully');
				redirect($this->uri->uri_string()."?tab=3");
	
			}

			// add unpaid item
			if(isset($_POST['add-item'])) {
				
				foreach($_POST['item'] as $k => $e) {
					
					if( datalist_Table('tbl_inventory', 'stock_ctrl', $e) == 1 ) {
						
						// add unpaid item to inventory table
						$new_id = $this->tbl_inventory_model->add([
							'date' => $this->input->post('date'),
							'branch' => branch_now('pid'),
							'title' => 'Unpaid item from '.datalist_Table('tbl_users', 'fullname_en', $data['result']['pid']),
							'type' => 'movement',
							'create_by' => auth_data('pid'),
						]);

						// add unpaid item to log inventory table
						$log_id = $this->log_inventory_model->add([
							'branch' => branch_now('pid'),
							'inventory' => $new_id,
							'item' => $e,
							'qty_in' => '0',
							'qty_out' => $_POST['qty'][$k],
							'remark' => $this->input->post('remark'),
							'create_by' => auth_data('pid'),
						]);
							
						$post_data['movement'] = $new_id;
						$post_data['movement_log'] = $log_id[0]['id'];
					}
					
					$amount = $this->tbl_inventory_model->view($e)[0]['price_sale'] * $_POST['qty'][$k];
					$post_data['user'] = $data['result']['pid'];
					$post_data['date'] = $this->input->post('date');
					$post_data['item'] = $e;
					$post_data['qty'] = $_POST['qty'][$k];
					$post_data['type'] = 'unpaid_item';
					$post_data['amount'] = $amount;
					$post_data['remark'] = $this->input->post('remark');
					$post_data['branch'] = branch_now('pid');
					$post_data['create_by'] = auth_data('pid');

					$this->log_join_model->add($post_data);
					
				}
				
				alert_new('success', 'Unpaid item created successfully');
				redirect($this->uri->uri_string()."?tab=5");
	
			}

			// edit unpaid item
			if(isset($_POST['save-item'])) {
				
				$post_data = [];

				foreach([ 'item', 'date', 'qty', 'remark' ] as $e) {
				
					$post_data[$e] = $this->input->post($e);
					
				}
				
				$amount = $this->tbl_inventory_model->view($this->input->post('item'))[0]['price_sale'] * $this->input->post('qty');
				$post_data['amount'] = $amount;
				$post_data['create_by'] = auth_data('pid');

				$this->log_join_model->edit($this->input->post('id'), $post_data);
				
				foreach($_POST['inventory'] as $e) {

					$this->tbl_inventory_model->edit($e['inventory_id'], [
						'date' => $this->input->post('date'),
						'update_by' => auth_data('pid'),
					]);
							
					$this->log_inventory_model->edit($e['log_inventory_id'], [
						'qty_out' => $this->input->post('qty'),
					]);
					
				}
					
				alert_new('success', 'Unpaid item updated successfully');

				redirect($this->uri->uri_string()."?tab=5");

			}
			
			if(isset($_POST['edit_outstanding'])) {
				
				if(!empty($this->input->post('month'))) {
					
					$discount = datalist_Table('log_join', 'discount', $this->input->post('id'), 'id');
					$discount = json_decode($discount, true);
					if(!is_array($discount)) $discount = [];
					
					$discount[$this->input->post('month')] = $this->input->post('discount');
					
					$discount = json_encode($discount);
					
				} else {
					
					$discount = $this->input->post('discount');
					
				}
				
				$this->log_join_model->edit($this->input->post('id'), [
					'discount'		=> $discount,
					'remark'		=> $this->input->post('remark'),
				]);
				
				alert_new('success', 'Unpaid item updated successfully');
				header('refresh: 0'); exit;
				
			}
			
			if(isset($_POST['view_unpaid'])) {
				
				$result = $this->log_join_model->view($_POST['view_unpaid']);
				
				foreach($result as &$e) {
					if($e['type'] == 'unpaid_item') {
						$e['title'] = datalist_Table('tbl_inventory', 'title', $e['item']);
					} else if ($e['type'] == 'join_class') {
						if(datalist_Table('tbl_classes', 'type', $e['class']) == 'check_in') {
							$qty = floor(abs(class_credit_balance($e['class'], $e['user'])['balance'] / datalist_Table('tbl_classes', 'credit', $e['class']))) + 1;
							$e['title'] = datalist_Table('tbl_classes', 'title', $e['class']);
							$e['amount'] = datalist_Table('tbl_classes', 'fee', $e['class']) * $qty;
							$e['qty'] = $qty;
						} else {
							$e['title'] = datalist_Table('tbl_classes', 'title', $e['class']);
							$e['amount'] = datalist_Table('tbl_classes', 'fee', $e['class']);
							$e['qty'] = 1;
						}
					} else if ($e['type'] == 'join_service') {
						$e['title'] = datalist_Table('tbl_inventory', 'title', $e['item']);
						$e['amount'] = datalist_Table('tbl_inventory', 'price_sale', $e['item']);
						$e['qty'] = 1;
					}
				}
				
				header('Content-type: application/json');
				die(json_encode([ 'status' => 'ok', 'result' => $result ]));
				
			}
			
			if(!isset($_GET['tab'])) $_GET['tab'] = 2;

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}
	
	public function scores()
	{
		
		auth_must('login');
		//check_module_page('Students/Modules/Score');
		
		$data['thispage'] = [
			'title' => 'Score',
			'group' => $this->group,
			'js' => $this->group.'/scores',
		];
		
		$_GET['student'] = isset($_GET['student']) ? $_GET['student'] : '';
		$_GET['exam'] = isset($_GET['exam']) ? $_GET['exam'] : '';
		$_GET['exam_date'] = isset($_GET['exam_date']) ? $_GET['exam_date'] : date('Y-m-d');

		$data['student'] = $this->tbl_users_model->list('student', branch_now('pid'), ['active' => 1]);
		$data['exam'] = $this->tbl_secondary_model->active_list('exam', branch_now('pid'));
		$data['selected_exam'] = $_GET['exam'] == '' ? [] : search($data['exam'], 'pid', $_GET['exam'])[0];
		$data['subject'] = empty($data['selected_exam']['subject']) ? [] : json_decode($data['selected_exam']['subject']);
		
		$data['result'] = [];

		if(!empty($_GET['student']) && !$this->tbl_users_model->check_user($_GET['student'])) {

			alert_new('warning', 'Data not found');
			redirect($this->group.'/list/'.$type);
		
		} else {
			$data['result'] = $this->log_join_model->list('exam_score', branch_now('pid'), ['user' => $_GET['student'], 'secondary' => $_GET['exam'], 'date' => $_GET['exam_date']]);
			
			if(isset($_POST['save'])) {
				
				foreach($_POST['exam_score'] as $e)
				{
					$exits = $this->log_join_model->list('exam_score', branch_now('pid'), ['user' => $_POST['student'], 'secondary' => $_POST['exam'], 'date' => $_POST['exam_date'], 'subject' => $e['subject']]);
					
					if (count($exits) > 0)
					{
						$this->log_join_model->edit($exits[0]['id'], [
							'score' => $e['score'],
						]);
					}
					else
					{
						$this->log_join_model->add([
							'user'          => $_GET['student'],
							'secondary'     => $_GET['exam'],
							'date'          => $_GET['exam_date'],
							'subject'       => $e['subject'],
							'score'         => $e['score'],
							'branch'        => branch_now('pid'),
							'type'          => 'exam_score',
						]);
					}
				}
					
				alert_new('success', 'Student Score saved successfully');

				header('refresh: 0'); exit;
						
			}
		}

		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/scores', $data);
		$this->load->view('inc/footer', $data);

	}
	
	public function json_del($id = '')
	{
		auth_must('login');
		check_module_page('Students/Delete');
		
		if(!empty($id)) {
			
			$this->tbl_users_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($this->single) . ' deleted successfully']));

	}
	
	public function json_list_join($user = '')
	{
	
		$result = $this->log_join_model->list2('join_class', branch_now('pid'), ['user' => $user]);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}
	
	// by steve
	public function json_joined()
	{
		
		header('Content-type: application/json');
		
		$class_timetable = $this->log_join_model->list('class_timetable', branch_now('pid'), ['class' => $_GET['class']]);
		
		$timetable = null;
		
		if(count($class_timetable) > 0) {
			$timetable = $class_timetable[0]['id'];
		}
		
		if( isset($_GET['user']) && isset($_GET['class']) ) {
			
			if(!isset($_GET['date'])) $_GET['date'] = date('Y-m-d');
			
			$action = $this->log_join_model->std_class_active($_GET['class'], $_GET['user'], $_GET['date'], $timetable, auth_data('pid'));
			die(json_encode(['status' => 'ok', 'result' => $action]));
			
		} else if( isset($_GET['user']) && isset($_GET['service']) ) {
			
			if(!isset($_GET['date'])) $_GET['date'] = date('Y-m-d');
			
			$action = $this->log_join_model->std_service_active($_GET['service'], $_GET['user'], $_GET['date']);
			
			die(json_encode(['status' => 'ok', 'result' => $action]));
			
		} else {
			
			die(json_encode(['status' => 'error']));
			
		}
		
	}
	
	
	//v2 soon
	public function json_join_class() {
		
		
		header('Content-type: application/json');
		
		if(count($this->log_join_model->list4('join_class', branch_now('pid'), [
					'user' => $_GET['user'],
					'class' => $_GET['class'],
				])) == 0) {
			
			$this->log_join_model->add([
				'user' => $_GET['user'],
				'class' => $_GET['class'],
				'date' => date('Y-m-d'),
				'branch' => branch_now('pid'),
				'type' => 'join_class',
				'create_by' => auth_data('pid'),
			]);
			
		} else {
			
			foreach($this->log_join_model->list4('join_class', branch_now('pid'), [
						'user' => $_GET['user'],
						'class' => $_GET['class'],
					]) as $e) {
						
				if($e['active'] == 1) {
					
					$this->log_join_model->edit($e['id'], ['active' => 0]);
					
				} else {
					
					$this->log_join_model->edit($e['id'], ['active' => 1]);
					
				}
				
			}
			
		}
		
		$result = $this->log_join_model->list4('join_class', branch_now('pid'), ['user' => $_GET['user'], 'class' => $_GET['class']])[0];
		
		die(json_encode(['status' => 'ok', 'result' => $result]));
		
	}
	
	public function json_edit_date_join()
	{
		
		if(!empty($_GET['date'])) {
			
			if (isset($_GET['class']))
			{	
				foreach($this->log_join_model->list('join_class', branch_now('pid'), [
					
						'user' => $_GET['user'],
						'class' => $_GET['class'],
						
				]) as $e) {
					
					$this->log_join_model->edit($e['id'], [ 'date' => $_GET['date'], 'active' => 1 ]);
				}
			}
			
			if (isset($_GET['service']))
			{	
				foreach($this->log_join_model->list('join_service', branch_now('pid'), [
					
						'user' => $_GET['user'],
						'item' => $_GET['service'],
						
				]) as $e) {
					
					$this->log_join_model->edit($e['id'], [ 'date' => $_GET['date'], 'active' => 1 ]);
				}
			}
			
		} else {
			
			if (isset($_GET['class']))
			{	
				foreach($this->log_join_model->list('join_class', branch_now('pid'), [
					
						'user' => $_GET['user'],
						'class' => $_GET['class'],
						
				]) as $e) {
					
					$this->log_join_model->edit($e['id'], [ 'active' => 0 ]);
				}				
			}	
			
			if (isset($_GET['service']))
			{	
				foreach($this->log_join_model->list('join_service', branch_now('pid'), [
					
						'user' => $_GET['user'],
						'item' => $_GET['service'],
						
				]) as $e) {
					
					$this->log_join_model->edit($e['id'], [ 'active' => 0 ]);
				}				
			}
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));

	}
	
	public function json_edit_timetable_join()
	{
		
		if(!empty($_GET['timetable'])) {
			
			if (isset($_GET['class']))
			{	
				foreach($this->log_join_model->list('join_class', branch_now('pid'), [
					
						'user' => $_GET['user'],
						'class' => $_GET['class'],
						
				]) as $e) {
					
					$this->log_join_model->edit($e['id'], [ 'sub_class' => $_GET['timetable'], 'active' => 1 ]);
				}
			}
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));

	}


	public function json_view_join($id = '')
	{
	
		$result = $this->log_join_model->view($id);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}
	
	public function json_del_join($id = '')
	{
	
		if(!empty($id)) {
			
			$item = datalist_Table('log_join', 'item', $id, 'id');
			
			if( datalist_Table('tbl_inventory', 'stock_ctrl', $item) == 1 ) {
				
				// add unpaid item to inventory table
				$new_id = $this->tbl_inventory_model->add([
					'date' 		=> date('Y-m-d'),
					'branch'	=> branch_now('pid'),
					'title' 	=> 'Unpaid item cancelled',
					'type' 		=> 'movement',
					'create_by'	=> auth_data('pid'),
				]);

				// add unpaid item to log inventory table
				$log_id = $this->log_inventory_model->add([
					'branch' 		=> branch_now('pid'),
					'inventory' 	=> $new_id,
					'item' 			=> $item,
					'qty_in' 		=> datalist_Table('log_join', 'qty', $id, 'id'),
					'qty_out' 		=> 0,
					'create_by' 	=> auth_data('pid'),
				]);
				
			}
			
			$type = $this->log_join_model->view($id)[0]['type'];
			// $this->log_inventory_model->del( $this->log_join_model->view($id)[0]['movement_log'] );
			$this->log_join_model->del($id);
			
		}
		
		$title = '';
		if ($type == 'unpaid_item')
		{
			$title = 'Unpaid item';
		}
		else
		{
			$title = 'Parent';
		}
		
		alert_new('success', $title.' deleted successfully');
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => $title.' deleted successfully']));

	}
	
	public function check_username($username, $id) {
		
		if($id == 'null') {
			$result = $this->tbl_users_model->check_username(urldecode($username));
		} else {
			$result = $this->tbl_users_model->check_username(urldecode($username), $id);
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));
		
	}
	
	public function check_rfid($cardid, $id) {
		
		if($id == 'null') {
			$result = $this->tbl_users_model->check_rfid($cardid, branch_now('pid'));
		} else {
			$result = $this->tbl_users_model->check_rfid($cardid, branch_now('pid'), $id);
		}
		
		if(empty($cardid)) {
			$result = false;
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));
		
	}
	
	public function json_edit_relationship()
	{
		if(!empty($_GET['id'])) {
			$this->log_join_model->edit($_GET['id'], [ 'title' => $_GET['relationship'] ]);
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));
	}
	
	public function json_edit_date_call()
	{
		if(!empty($_GET['id'])) {
			$this->tbl_users_model->edit($_GET['id'], [ 'date_call' => $_GET['date_call'] ]);
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));
	}
	
	/* public function json_list()
	{
		
		if(isset($_GET['parent'])) {

			$result = $this->tbl_users_model->total_children($_GET['parent']);

		} else if(isset($_GET['class'])) {

			$result = $this->log_join_model->list('join_class', branch_now('pid'), ["class" => $_GET['class']]);
			
		} else if(isset($_GET['search'])) {

			$search_param = ['fullname_en', 'fullname_en', 'rfid_cardid', 'phone', 'email'];
			$query = 'SELECT * FROM tbl_users WHERE type = "student" AND is_delete = 0 AND branch = '.branch_now('pid');
			
			foreach($search_param as $e) {
				
				if(isset($_GET[$e])) {
					
					$query .= ' AND '.$e.' LIKE "%'.$_GET[$e].'%"';
					
				}
				
			}
			
			$result = $this->db->query($query)->result_array();
			
		} else {

			$result = $this->tbl_users_model->student_list('student', branch_now('pid'));
			
		}
		
		$data = [];
		$i=0;
		
		foreach($result as $e) {
			
			$i++;
			
			ob_start();?><div class="media"><?php if( check_module('Students/Update') ) { ?><a href="<?php echo base_url($this->group . '/edit/' . $e['pid']); ?>"><?php } ?><img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="mr-2 rounded-circle border" style="height: 85px; width: 85px; object-fit: cover"><?php if( check_module('Students/Update') ) { ?></a><?php } ?><div class="media-body my-auto"><?php if( check_module('Students/Update') ) { ?><a href="<?php echo base_url($this->group . '/edit/' . $e['pid']); ?>"><?php } ?><?php echo $e['fullname_en']; ?><?php if( check_module('Students/Update') ) { ?></a><?php } ?><div style="font-size: 1.25rem"><a href="<?php echo (empty($e['phone'])) ? 'javascript:;" style="opacity: .5;' : 'https://wa.me/'.$e['phone'].'" target="_blank'; ?>" class="text-muted" data-toggle="tooltip" title="WhatsApp"><i class="fab fa-fw fa-whatsapp"></i></a><a href="<?php echo (empty($e['phone'])) ? 'javascript:;" style="opacity: .5;' : 'tel:'.$e['phone']; ?>" class="text-muted" data-toggle="tooltip" title="Call"><i class="fa fa-fw fa-phone"></i></a><a href="<?php echo (empty($e['email'])) ? 'javascript:;" style="opacity: .5;' : 'mailto:'.$e['email'].'" target="_blank'; ?>" class="text-muted" data-toggle="tooltip" title="Email"><i class="fa fa-fw fa-envelope"></i></a><a href="<?php echo (empty($e['address'])) ? 'javascript:;" style="opacity: .5;' : 'https://maps.google.com/?daddr='.urlencode($e['address']).'" target="_blank'; ?>" class="text-muted" data-toggle="tooltip" title="Map"><i class="fa fa-fw fa-map-marker-alt"></i></a></div></div></div><?php
			$fullname_en = ob_get_clean();
			
			array_push($data, [
				$i,
				$fullname_en,
				badge($e['active']),
				ucfirst($e['gender']),
				empty($e['date_join']) ? '-' : $e['date_join'],
				empty($e['phone']) ? '-' : $e['phone'],
				empty($e['school']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['school']),
				empty($e['parent']) ? '-' : datalist_Table('tbl_users', 'fullname_en', $e['parent'])
			]);
			
		}
		
		$new_result = [
			'recordsTotal' => count($result),
			'recordsFiltered' => count($result),
			'data' => $data
		];
		
		echo json_encode($new_result);
		
	} */
	
	public function json_list_count()
	{
		
		if(isset($_GET['parent'])) {

			$result = $this->tbl_users_model->total_children($_GET['parent']);

		} else if(isset($_GET['class'])) {

			$result = $this->log_join_model->list('join_class', branch_now('pid'), ["class" => $_GET['class']]);
			
		} else if(isset($_GET['search'])) {

			$search_param = ['fullname_en', 'fullname_en', 'rfid_cardid', 'phone', 'email'];
			$query = 'SELECT * FROM tbl_users WHERE type = "student" AND is_delete = 0 AND branch = '.branch_now('pid');
			
			foreach($search_param as $e) {
				
				if(isset($_GET[$e])) {
					
					$query .= ' AND '.$e.' LIKE "%'.$_GET[$e].'%"';
					
				}
				
			}
			
			$result = $this->db->query($query)->result_array();
			
		} else {

			$result = $this->tbl_users_model->list('student', branch_now('pid'));
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['result' => count($result)]));
		
	}

}
