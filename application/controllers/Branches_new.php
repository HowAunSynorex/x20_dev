<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Branches_new extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'branches_new';
		$this->single = 'branch';
		
		$this->load->model('tbl_branches_model');
		$this->load->model('tbl_payment_model');
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_uploads_model');
		$this->load->model('log_join_model');
		$this->load->model('tbl_secondary_model');

		auth_must('login');

	}

	public function list()
	{
		
		check_module_page('Settings/Read');
		check_module_page('Settings/Modules/Branches');

		$data['thispage'] = [
			'title' => 'Branches',
			'group' => $this->group,
		];
		
		$data['result'] = $this->tbl_branches_model->list();
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/list', $data);
		$this->load->view('inc/footer', $data);

	}

	public function add()
	{
		
		check_module_page('Settings/Read');
		check_module_page('Settings/Modules/Branches');

		if(isset($_POST['save'])) {

			$post_data = [];
			
			foreach([ 'title', 'ssm_no', 'country', 'currency', 'phone', 'phone2', 'phone3', 'email', 'email2', 'email3', 'address', 'address2', 'address3', 'active' ] as $e) {
				
				$post_data[$e] = $this->input->post($e);
				
			}
			
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
					$image_data['type'] = 'branch_logo';
					$image = $this->tbl_uploads_model->add($image_data);

				}
				
			}
			
			$post_data['image'] = $image;
			$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
			if(empty($this->input->post('country'))) $post_data['country'] = null;
			if(empty($this->input->post('currency'))) $post_data['currency'] = null;
			$post_data['owner'] = auth_data('pid');
			$post_data['create_by'] = auth_data('pid');
			$post_data['expired_date'] = date('Y-m-d', strtotime('+14 days'));
			$post_data['send_msg_whatsapp'] = 'Hey *%NAME%*, here\'s your confirmation for receipt number *%RECEIPT_NO%*. Review your receipt by click the link: %LINK%';
			$post_data['send_msg_sms'] = 'Your receipt %RECEIPT_NO% has been generated!';
			$post_data['send_msg_whatsapp_outstanding'] = 'Your tuition fee is outstanding. Kindly pay by end of this month. Dial %PHONE% for more info. ';
			$post_data['send_msg_sms_outstanding'] = 'Your outstanding statement has been generated. ';

			$new_id = $this->tbl_branches_model->add($post_data);

			$this->log_join_model->add([
				'type' => 'join_branch',
				'branch' => $new_id,
				'admin' => auth_data('pid'),
			]);
			
			alert_new('success', 'Branch created successfully');
			redirect($this->group . '/list');

		}
		
		$data['country'] = $this->tbl_secondary_model->list('country', null);
		$data['currency'] = $this->tbl_secondary_model->list('currency', null);

		$data['thispage'] = [
			'title' => 'Add Branch',
			'group' => $this->group,
		];
			
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/add');
		$this->load->view('inc/footer', $data);

	}

	public function edit($id = '')
	{
		
		check_module_page('Settings/Read');
		check_module_page('Settings/Modules/Branches');
		
		$data['id'] = $id;
		$data['result'] = $this->tbl_branches_model->view($id);
		
		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');
			redirect('settings/' . $this->group);

		} else {

			$data['result'] = $data['result'][0];

			$data['thispage'] = [
				'title' => 'Edit Branch',
				'group' => $this->group,
				'js' => $this->group.'/edit',
			];

			if(isset($_POST['save'])) {

				$post_data = [];
				
				foreach([ 'title', 'ssm_no', 'country', 'currency', 'phone', 'phone2', 'phone3', 'email', 'email2', 'email3', 'address', 'address2', 'address3', 'active' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}
				
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
						$image_data['type'] = 'branch_logo';
						$image = $this->tbl_uploads_model->add($image_data);

					}
					
				}
				
				$post_data['image'] = $image;
				$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
				$post_data['update_by'] = auth_data('pid');
				if( empty($post_data['country']) ) $post_data['country'] = null;
				if( empty($post_data['currency']) ) $post_data['currency'] = null;

				$this->tbl_branches_model->edit($id, $post_data);
				
				alert_new('success', 'Branch updated successfully');
				redirect($this->group . '/list');

			}
			
			$data['country'] = $this->tbl_secondary_model->list('country', null);
			$data['currency'] = $this->tbl_secondary_model->list('currency', null);

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}

	public function json_del($id = '')
	{

		if(!empty($id)) {
	
			$this->tbl_branches_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Branch deleted successfully']));

	}
	
	public function json_remove_logo($id = '')
	{
		
		$this->tbl_branches_model->edit($id, [
			'image' => null
		]);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Logo removed successfully']));

	}

}
