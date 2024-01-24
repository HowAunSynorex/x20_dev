<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parents extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'parents';
		$this->single = 'parent';
		
		$this->load->model('log_join_model');
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_uploads_model');

		auth_must('login');

	}

// 	public function list()
// 	{
		
// 		auth_must('login');
// 		check_module_page('Parents/Read');

// 		$data['thispage'] = [
// 			'title' => 'All '.ucfirst($this->group),
// 			'group' => $this->group,
// 		];

// 		$data['result'] = $this->tbl_users_model->list('parent', branch_now('pid'));

// 		$this->load->view('inc/header', $data);
// 		$this->load->view($this->group.'/list', $data);
// 		$this->load->view('inc/footer', $data);

// 	}

	public function list()
	{
		
		auth_must('login');
		check_module_page('Parents/Read');
		
		$this->load->library('session');
		$this->load->library('pagination');

		$data['thispage'] = [
			'title' => 'All '.ucfirst($this->group),
			'group' => $this->group,
		];
		
		$type='parent';

        $sql = '
		
			SELECT u.*, (year(CURDATE()) - year(birthday)) AS age, schools.title AS school_title, forms.title AS form_title
			FROM tbl_users u
			LEFT JOIN tbl_secondary schools ON schools.pid = u.school
			LEFT JOIN tbl_secondary forms ON forms.pid = u.form
			WHERE u.is_delete = 0
			AND u.type = "'.$type.'"
		';

		$data['total_count'] = count($this->db->query($sql)->result_array());
			
		if(isset($_GET['parent'])) {
			
			$sql .= ' AND EXISTS (SELECT log_join.parent FROM log_join WHERE user = u.pid AND log_join.is_delete = 1 AND log_join.parent = "'. $_GET['parent'] .'" LIMIT 1)';
		}

		if(isset($_GET['class'])) {

			$sql .= ' AND EXISTS (SELECT log_join.student FROM log_join WHERE type = "join_class" AND user = u.pid AND branch = "'. branch_now('pid') .'" AND log_join.active = 1 AND log_join.class = "'. $_GET['class'] .'" LIMIT 1)';
		}

		if(isset($_GET['class'])) {

			$data['result'] = $this->log_join_model->student_list('join_class', branch_now('pid'), ["class" => $_GET['class']]);
			
		}

		if(isset($_GET['search'])) {

			$search_param = ['fullname_en', 'fullname_cn', 'phone'];
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
        // die($sql);
        
		$config = array();
		
		if ($type == 'parent')
		{
			$config['base_url'] = base_url('/parents/list');
		}
		else
		{
			$config['base_url'] = base_url('/parents/list').$type;
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
		$data['result'] = $this->db->query($sql)->result_array();

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/list_pagination', $data);
		$this->load->view('inc/footer', $data);

	}

	public function add()
	{
		
		auth_must('login');
		check_module_page('Parents/Create');

		$this->load->helper('form');

		if(isset($_POST['save'])) {

			if(!$this->tbl_users_model->check_username( $this->input->post('username') ) && !empty( $this->input->post('username') ) ) {

				alert_new('warning', 'Username has been taken');
				
				redirect($this->uri->uri_string());

			} else {

				$post_data = [];
				
				foreach([ 'username', 'active', 'nickname', 'password', 'fullname_en', 'fullname_cn', 'gender', 'nric', 'birthday', 'email', 'email2', 'email3', 'phone', 'phone2', 'phone3', 'address', 'address2', 'address3', 'date_join', 'remark' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}

				$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
				$post_data['branch'] = branch_now('pid');
				$post_data['create_by'] = auth_data('pid');
				$post_data['password'] = password_hash($post_data['password'], PASSWORD_DEFAULT);
				$post_data['type'] = 'parent';
				
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
						$image_data['type'] = 'avatar_parent';
						$image = $this->tbl_uploads_model->add($image_data);

					}
					
				}
				
				$post_data['image'] = $image;
				
				if( empty($post_data['birthday']) ) $post_data['birthday'] = null;
				if( empty($post_data['date_join']) ) $post_data['date_join'] = null;
				
				$this->tbl_users_model->add($post_data);
				
				alert_new('success', ucfirst($this->single).' created successfully');
				
				redirect($this->group . '/list');

			}

		}

		$data['thispage'] = [
			'title' => 'Add '.ucfirst($this->single),
			'group' => $this->group,
			'js' => $this->group.'/add'
		];

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/add');
		$this->load->view('inc/footer', $data);

	}

	public function edit($id = '')
	{
		
		auth_must('login');
		check_module_page('Parents/Read');

		$data['result'] = $this->tbl_users_model->view($id);

		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect($this->group . '/list');

		} else {

			$data['thispage'] = [
				'title' => 'Edit '.ucfirst($this->single),
				'group' => $this->group,
				'js' => $this->group.'/edit'
			];

			$data['result'] = $data['result'][0];

			if(isset($_POST['save'])) {

				if(!$this->tbl_users_model->check_username( $this->input->post('username'), $id ) && !empty( $this->input->post('username') ) ) {

		        	alert_new('warning', 'Username has been taken');
		            
		        	redirect($this->uri->uri_string());

	        	} else {

					$post_data = [];

					foreach([ 'username', 'active', 'nickname', 'password', 'fullname_en', 'fullname_cn', 'gender', 'nric', 'birthday', 'email', 'email2', 'email3', 'phone', 'phone2', 'phone3', 'address', 'address2', 'address3', 'date_join', 'remark' ] as $e) {
					
						$post_data[$e] = $this->input->post($e);
						
					}
	
					if (isset( $post_data['active'] )) {
	
						$post_data['active'] = 1;
	
					} else {
	
						$post_data['active'] = 0;
	
					}

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
							$image_data['type'] = 'avatar_parent';
							$image = $this->tbl_uploads_model->add($image_data);

						}
						
					}
					
					$post_data['image'] = $image;
					
					if( empty($post_data['birthday']) ) $post_data['birthday'] = null;
					if( empty($post_data['date_join']) ) $post_data['date_join'] = null;
					
					$this->tbl_users_model->edit($id, $post_data);
					
					alert_new('success', ucfirst($this->single).' updated successfully');
					
					redirect($this->group . '/list');
					
				}

			}

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}
	
	public function json_del($id = '')
	{
		
		auth_must('login');
		check_module_page('Parents/Delete');
	
		if(!empty($id)) {
			
			$this->tbl_users_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($this->single) . ' deleted successfully']));

	}

}
