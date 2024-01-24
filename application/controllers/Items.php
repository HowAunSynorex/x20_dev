<?php

use function PHPSTORM_META\type;

defined('BASEPATH') OR exit('No direct script access allowed');

class Items extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'items';
		$this->single = 'item';
		
		$this->load->model('tbl_inventory_model');
		$this->load->model('tbl_secondary_model');
		$this->load->model('log_join_model');

		auth_must('login');

	}

	public function list()
	{
		
		auth_must('login');
		check_module_page('Inventory/Read');
		check_module_page('Inventory/Modules/Items');

		$data['thispage'] = [
			'title' => ucfirst($this->group),
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_inventory_model->list(branch_now('pid'), 'item');

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/list', $data);
		$this->load->view('inc/footer', $data);

	}

	public function add()
	{
		
		auth_must('login');
		check_module_page('Inventory/Create');
		check_module_page('Inventory/Modules/Items');

		if(isset($_POST['save'])) {

			$post_data = [];

			foreach([ 'title', 'item_type', 'category', 'sku', 'price_cost', 'price_min', 'price_sale', 'remark' ] as $e) {
		
				$post_data[$e] = $this->input->post($e);
				
			}
			
			if(empty($post_data['category'])) $post_data['category'] = null;
			if(empty($post_data['price_cost'])) $post_data['price_cost'] = 0;
			if(empty($post_data['price_min'])) $post_data['price_min'] = 0;
			if(empty($post_data['price_sale'])) $post_data['price_sale'] = 0;

			$post_data['active'] = isset( $_POST['active'] ) ? 1 : 0 ;
			$post_data['stock_ctrl'] = isset( $_POST['stock_ctrl'] ) ? 1 : 0 ;
			$post_data['type'] = 'item';
			$post_data['branch'] = branch_now('pid');
			$post_data['create_by'] = auth_data('pid');
			
			$this->tbl_inventory_model->add($post_data);
			
			alert_new('success', ucfirst($this->single).' created successfully');
			
			redirect($this->group . '/list');

		}

		$data['thispage'] = [
			'title' => 'Add '.ucfirst($this->single),
			'group' => $this->group,
		];
		
		$branch_item_cat = [];
		foreach($this->log_join_model->list('secondary', branch_now('pid'), ['active' => 1]) as $e) {
			if(datalist_Table('tbl_secondary', 'type', $e['secondary']) == 'item_cat') {
				$branch_item_cat[] = $e;
			}
		}
			
		$data['item_cat'] = $this->tbl_secondary_model->list('item_cat', branch_now('pid'), ['active' => 1]);
		$data['branch_item_cat'] = $branch_item_cat;
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/add');
		$this->load->view('inc/footer', $data);

	}

	public function edit($id = '')
	{
		
		auth_must('login');
		check_module_page('Inventory/Read');
		check_module_page('Inventory/Modules/Items');

		$data['result'] = $this->tbl_inventory_model->view($id);
		
		$branch_item_cat = [];
		foreach($this->log_join_model->list('secondary', branch_now('pid'), ['active' => 1]) as $e) {
			if(datalist_Table('tbl_secondary', 'type', $e['secondary']) == 'item_cat') {
				$branch_item_cat[] = $e;
			}
		}
			
		$data['item_cat'] = $this->tbl_secondary_model->list('item_cat', branch_now('pid'), ['active' => 1]);
		$data['branch_item_cat'] = $branch_item_cat;

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

				foreach([ 'title', 'item_type', 'category', 'sku', 'price_cost', 'price_min', 'price_sale', 'remark' ] as $e) {
			
					$post_data[$e] = $this->input->post($e);
					
				}

				if(empty($post_data['category'])) $post_data['category'] = null;
				if(empty($post_data['price_cost'])) $post_data['price_cost'] = 0;
				if(empty($post_data['price_min'])) $post_data['price_min'] = 0;
				if(empty($post_data['price_sale'])) $post_data['price_sale'] = 0;

				$post_data['active'] = isset( $_POST['active'] ) ? 1 : 0 ;
				$post_data['stock_ctrl'] = isset( $_POST['stock_ctrl'] ) ? 1 : 0 ;
				$post_data['update_by'] = auth_data('pid');
				$post_data['type'] = 'item';
				$post_data['branch'] = branch_now('pid');

				
				$this->tbl_inventory_model->edit($id, $post_data);
				
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
		check_module_page('Inventory/Delete');
		check_module_page('Inventory/Modules/Items');

		if(!empty($id)) {
	
			$this->tbl_inventory_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($this->single) . ' deleted successfully']));

	}

	public function json_list()
	{

		if(!isset($_GET['term'])) $_GET['term'] = '';

		header('Content-type: application/json');
		
		$result = [];
		
		foreach($this->tbl_inventory_model->list_search_title(branch_now('pid'), 'item', $_GET['term']) as $e) {
			
			$result[] = [
				'id' => $e['pid'], 
				'text' => $e['title']
			];
			
		}
		
		die(json_encode(array(
			"total_count" => count($result),
			"incomplete_results" => true,
			'items' => $result
		)));

	}

	public function json_view($id = '')
	{
	
		$result = $this->tbl_inventory_model->view($id);
		$result['stock_on_hand'] = stock_on_hand($id);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}
	
	//normal version
	public function json_view_item($id = '')
	{
	
		$result = $this->tbl_inventory_model->view($id);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}
	
	public function json_list_select2()
	{

		header('Content-type: application/json');
		
		$result = [];
		
		$result[] = [
			'id' => '',
			'text'	=> '-'
		];
		
		foreach($this->tbl_inventory_model->list(branch_now('pid'), 'item', ['active => 1']) as $e) {
			
			$result[] = [
				'id' => $e['pid'], 
				'text' => $e['title']
			];
			
		}
		
		die(json_encode(array('result' => $result)));

	}

}
