<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Teachers extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'teachers';
		$this->single = 'teacher';
		
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_uploads_model');

		auth_must('login');

	}

	public function list()
	{
		
		auth_must('login');
		check_module_page('Teachers/Read');

		$data['thispage'] = [
			'title' => 'All '.ucfirst($this->group),
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_users_model->list('teacher', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/list', $data);
		$this->load->view('inc/footer', $data);

	}

	public function add()
	{
		
		auth_must('login');
		check_module_page('Teachers/Create');

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
					
					foreach([ 'active', 'nickname', 'username', 'password', 'fullname_en', 'fullname_cn', 'gender', 'nric', 'birthday', 'email', 'email2', 'email3', 'phone', 'phone2', 'phone3', 'address', 'address2', 'address3', 'date_join', 'rfid_cardid', 'remark' ] as $e) {
						
						$post_data[$e] = $this->input->post($e);
						
					}

					$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
					$post_data['branch'] = branch_now('pid');
					$post_data['create_by'] = auth_data('pid');
					$post_data['password'] = password_hash($post_data['password'], PASSWORD_DEFAULT);
					$post_data['type'] = 'teacher';
					
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
							$image_data['type'] = 'avatar_teacher';
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
		check_module_page('Teachers/Read');

		$data['id'] = $id;
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
					
					if(!$this->tbl_users_model->check_rfid( $this->input->post('rfid_cardid'), branch_now('pid'), $id ) && !empty( $this->input->post('rfid_cardid') ) ) {

						alert_new('warning', 'Card ID has been taken');
											
					} else {

						$post_data = [];
						
						foreach([ 'active', 'nickname', 'username', 'fullname_en', 'fullname_cn', 'gender', 'nric', 'birthday', 'email', 'email2', 'email3', 'phone', 'phone2', 'phone3', 'address', 'address2', 'address3', 'date_join', 'rfid_cardid', 'remark', 'password' ] as $e) {
							
							$post_data[$e] = $this->input->post($e);
							
						}

						$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
						$post_data['update_by'] = auth_data('pid');
						$post_data['branch'] = branch_now('pid');
						$post_data['password'] = empty( $post_data['password'] ) ? $data['result']['password'] : password_hash($post_data['password'], PASSWORD_DEFAULT);
						
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
								$image_data['type'] = 'avatar_teacher';
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

			}

			if(isset($_POST['save_working_hrs'])){

				foreach(['dy_1', 'dy_2', 'dy_3', 'dy_4', 'dy_5', 'dy_6', 'dy_7'] as $e) {
					$post_data[$e] = !empty($this->input->post($e.'[1]')) ? $this->input->post($e.'[1]').'-'.$this->input->post($e.'[2]') : null;
				}

				$post_data['update_by'] = auth_data('pid');

				$this->tbl_users_model->edit($id, $post_data);
				
				alert_new('success', ucfirst($this->single).$post_data['dy_1'].' updated successfully');
				
				header('refresh: 0'); exit;
			
			}

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}

	public function json_view($id = '')
	{
	
		$result = $this->tbl_users_model->view($id);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}

	public function json_del($id = '')
	{
		
		auth_must('login');
		check_module_page('Teachers/Delete');
	
		if(!empty($id)) {
			
			$this->tbl_users_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($this->single) . ' deleted successfully']));

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

}
