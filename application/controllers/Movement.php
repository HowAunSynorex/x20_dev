<?php

use function PHPSTORM_META\type;

defined('BASEPATH') OR exit('No direct script access allowed');

class Movement extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'movement';
		$this->single = 'movement';
		
		$this->load->model('tbl_inventory_model');
		$this->load->model('log_inventory_model');

		auth_must('login');

	}

	public function list($type = '')
	{
		
		auth_must('login');
		check_module_page('Inventory/Read');
		check_module_page('Inventory/Modules/Movement');

		$data['thispage'] = [
			'title' => ucfirst($this->group),
			'group' => $this->group,
			'type' => $type,
		];

		$data['result'] = $this->tbl_inventory_model->list_batch(branch_now('pid'), 'movement');

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/list', $data);
		$this->load->view('inc/footer', $data);

	}

	public function add()
	{
		
		auth_must('login');
		check_module_page('Inventory/Create');
		check_module_page('Inventory/Modules/Movement');

		if(isset($_POST['save'])) {

			$post_data = [];
				
			foreach([ 'date', 'title', 'remark' ] as $e) {
		
				$post_data[$e] = $this->input->post($e);
				
			}

			$post_data['type'] = 'movement';
			$post_data['branch'] = branch_now('pid');
			$post_data['create_by'] = auth_data('pid');
			// echo '<pre>'; print_r($_POST); exit;

			$new_id = $this->tbl_inventory_model->add($post_data);
			
			if(!isset($_POST['log_data'])) $_POST['log_data'] = [];
			
			foreach($_POST['log_data'] as $e) {
				
				if(!empty($e['item'])) {

					$this->log_inventory_model->add([
						'branch' => branch_now('pid'),
						'inventory' => $new_id,
						'item' => $e['item'],
						'qty_in' => $e['in'],
						'qty_out' => $e['out'],
						'create_by' => auth_data('pid'),
					]);

					if(!empty($e['adjust'])) {
						
						$this->log_inventory_model->add([
							'branch' => branch_now('pid'),
							'inventory' => $new_id,
							'item' => $e['item'],
							'qty_in' => $e['adjust'],
							'qty_out' => $e['stock'],
							'create_by' => auth_data('pid'),
						]);

					}
					
				}
				
			}
			
			alert_new('success', 'Movement created successfully');
			
			redirect($this->group . '/list');

		}

		$data['thispage'] = [
			'title' => 'Add ' . ucfirst($this->single),
			'group' => $this->group,
			'js' => $this->group.'/add',
		];

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/add');
		$this->load->view('inc/footer', $data);

	}

	public function edit($id = '')
	{
		
		auth_must('login');
		check_module_page('Inventory/Read');
		check_module_page('Inventory/Modules/Movement');

		$data['result'] = $this->tbl_inventory_model->view($id);

		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect($this->group.'/list');

		} else {

			$data['thispage'] = [
				'title' => 'Edit ' . ucfirst($this->single),
				'group' => $this->group,
				'js' => $this->group.'/edit'
			];

			$data['result'] = $data['result'][0];

			if(isset($_POST['save'])) {

				$post_data = [];

				foreach([ 'date', 'title', 'remark' ] as $e) {
		
					$post_data[$e] = $this->input->post($e);
					
				}

				$post_data['type'] = 'movement';
				$post_data['branch'] = branch_now('pid');
				$post_data['update_by'] = auth_data('pid');
				
				$this->tbl_inventory_model->edit($id, $post_data);
				
				alert_new('success', 'Movement updated successfully');
				
				redirect($this->group . '/list');

			}
			
			$data['result_log'] = $this->log_inventory_model->list($id);

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}

	public function json_del($id = '')
	{
		
		auth_must('login');
		check_module_page('Inventory/Delete');
		check_module_page('Inventory/Modules/Movement');
		
		if(!empty($id)) {
	
			$this->tbl_inventory_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Movement deleted successfully']));

	}

}
