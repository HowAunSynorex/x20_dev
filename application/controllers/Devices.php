<?php

use function PHPSTORM_META\type;

defined('BASEPATH') OR exit('No direct script access allowed');

class Devices extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'devices';
		$this->single = 'device';
		
		$this->load->model('tbl_devices_model');
		$this->load->model('tbl_secondary_model');

		auth_must('login');

	}

	public function list()
	{
		
		auth_must('login');
		// check_module_page('Inventory/Read');
		// check_module_page('Inventory/Modules/Items');

		$data['thispage'] = [
			'title' => ucfirst($this->group),
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_devices_model->list(branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/list', $data);
		$this->load->view('inc/footer', $data);

	}

	public function add()
	{
		
		auth_must('login');
		// check_module_page('Inventory/Create');
		// check_module_page('Inventory/Modules/Items');

		if(isset($_POST['save'])) {

			$post_data = [];

			foreach([ 'title', 'type' ] as $e) {
		
				$post_data[$e] = $this->input->post($e);
				
			}
			
			switch($this->input->post('type')) {
				case 'web_qr':
				case 'web_rfid':
				case 'web_pin':
					$post_data['otp'] = rand(1111, 9999);
					break;
			}
			
			$post_data['active'] = isset( $_POST['active'] ) ? 1 : 0 ;
			$post_data['branch'] = branch_now('pid');
			$post_data['create_by'] = auth_data('pid');
			
			$id = $this->tbl_devices_model->add($post_data);
						
			redirect($this->group . '/edit/' . $id);

		}

		$data['thispage'] = [
			'title' => 'Add '.ucfirst($this->single),
			'group' => $this->group,
		];
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/add');
		$this->load->view('inc/footer', $data);

	}

	public function edit($id = '')
	{
		
		auth_must('login');
		// check_module_page('Inventory/Read');
		// check_module_page('Inventory/Modules/Items');

		$data['result'] = $this->tbl_devices_model->view($id);

		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect($this->group.'/list');

		} else {

			$data['thispage'] = [
				'title' => 'Edit '.ucfirst($this->single),
				'group' => $this->group,
				'js' => $this->group.'/edit'
			];

			$data['result'] = $data['result'][0];

			if(isset($_POST['save'])) {

				$post_data = [];

				foreach([ 'title', 'type', 'temp_enable' ] as $e) {
			
					$post_data[$e] = $this->input->post($e);
					
				}
				
				switch($this->input->post('type')) {
					case 'web_qr':
					case 'web_rfid':
					case 'web_pin':
						$post_data['otp'] = rand(1111, 9999);
						break;
				}
					
				$post_data['active'] = isset( $_POST['active'] ) ? 1 : 0 ;
				$post_data['update_by'] = auth_data('pid');
				$post_data['branch'] = branch_now('pid');

				$this->tbl_devices_model->edit($id, $post_data);
				
				alert_new('success', ucfirst($this->single).' updated successfully');
				
				redirect($this->group . '/list');

			}

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}

	public function json_del($id = '')
	{
		
		auth_must('login');
		// check_module_page('Inventory/Delete');
		// check_module_page('Inventory/Modules/Items');

		if(!empty($id)) {
	
			$this->tbl_devices_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($this->single) . ' deleted successfully']));

	}

}
