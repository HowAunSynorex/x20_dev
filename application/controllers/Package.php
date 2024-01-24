<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Package extends CI_Controller
{

	public function __construct()
	{

		parent::__construct();

		$this->group = 'package';
		$this->single = 'package';

		$this->load->model('tbl_secondary_model');
		$this->load->model('tbl_inventory_model');
		// Tan Jing Suan
		$this->load->model('log_join_model');

		auth_must('login');
	}

	public function list()
	{
		
		auth_must('login');
		check_module_page('Secondary/Read');
		check_module_page('Secondary/Modules/Schools');
		
		$data['thispage'] = [
			'title' => 'Package',
			'group' => $this->group,
			'js' => $this->group.'/list',
			'css' => $this->group.'/list',
		];

		$data['result'] = $this->tbl_secondary_model->list('package', branch_now('pid'));
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/list', $data);
		$this->load->view('inc/footer', $data);

	}

	public function add()
	{
		
		auth_must('login');
		check_module_page('Secondary/Create');
		
		$data['thispage'] = [
			'title' => 'Add Package',
			'group' => $this->group,
		];
		
		$data['items'] = $this->tbl_inventory_model->list(branch_now('pid'), 'item', ['active' => 1]);

		if (isset($_POST['save'])) {

			$post_data = [];

			foreach (['title', 'active', 'remark'] as $e) {
				$post_data[$e] = $this->input->post($e);
			}

			$post_data['type'] = 'package';
			$post_data['item'] = json_encode($_POST['item']);
			$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
			$post_data['branch'] = branch_now('pid');
			$post_data['create_by'] = auth_data('pid');

			$this->tbl_secondary_model->add($post_data);

			alert_new('success', 'Package created successfully');
			redirect($this->group . '/list');
			
		}

		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/add');
		$this->load->view('inc/footer', $data);

	}

	public function edit($id = '')
	{
		
		auth_must('login');
		check_module_page('Secondary/Read');
		
		$data['thispage'] = [
			'title' => 'Edit Package',
			'group' => $this->group,
			'js' => $this->group . '/edit'
		];
		
		$data['result'] = $this->tbl_secondary_model->view($id);
		
		if(!isset($_GET['tab'])) $_GET['tab'] = 1;

		if (count($data['result']) == 0) {

			alert_new('warning', 'Data not found');
			redirect();

		}
		
		$data['result'] = $data['result'][0];
		
		$data['items'] = $this->tbl_inventory_model->list(branch_now('pid'), 'item', ['active' => 1]);
			
		if (isset($_POST['save'])) {
			
			$post_data = [];

			foreach (['title', 'active', 'remark'] as $e) {
				$post_data[$e] = $this->input->post($e);
			}
			
			$post_data['item'] = json_encode($_POST['item']);
			$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
			$post_data['update_by'] = auth_data('pid');
			$post_data['branch'] = branch_now('pid');

			$this->tbl_secondary_model->edit($id, $post_data);

			alert_new('success', 'Package updated successfully');
			redirect($this->group . '/list');
			
		}
		
		if(isset($_POST['del'])) {
			
			$this->tbl_secondary_model->del($id);
			
			header('Content-type: application/json');
			die(json_encode([ 'status' => 'ok', 'message' => 'Package deleted successfully' ]));
			
		}

		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/edit', $data);
		$this->load->view('inc/footer', $data);

	}
	
	public function json_view($id)
	{
		
		$query = $this->tbl_secondary_model->view($id)[0];
		
		$items = json_decode($query['item'], true);
		if(!is_array($items)) $items = [];
		
		$result = [];
		
		foreach($items as $e) {
			$item = $this->tbl_inventory_model->view($e)[0];
			$result[] = $item;
		}
		
		header('Content-type: application/json');
		die(json_encode([ 'status' => 'ok', 'result' => $result ]));
		
	}
	
}
