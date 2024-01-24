<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Points extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'points';
		$this->single = 'point';
		
		$this->load->model('log_point_model');
		$this->load->model('tbl_users_model');

		auth_must('login');

	}

	public function list($type = '', $id = '')
	{
		
		auth_must('login');
		check_module_page('Points/Read');
		
		switch($type) {
			case 'epoint':
				check_module_page('Points/Modules/Epoint');
				break;
			default:
				check_module_page('Points/Modules/Ewallet');
				break;
		}

		$data['thispage'] = [
			'title' => ucfirst($type),
			'group' => $this->group,
			'type' => $type,
			'js' => $this->group.'/list',
		];

		$data['id'] = $id;
		$data['result'] = $this->log_point_model->view($id, $type);
		$data['student'] = $this->tbl_users_model->list('student', branch_now('pid'), ['active' => 1]);

		if(!empty($id) && !$this->tbl_users_model->check_user($id)) {

			alert_new('warning', 'Data not found');
			redirect($this->group.'/list/'.$type);
		
		} else {

			if(isset($_POST['save'])) {

				$post_data = [];
				
				foreach([ 'amount_1', 'amount_0', 'remark'] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}

				if ( $post_data['amount_0'] == null && $post_data['amount_1'] == null ) {

					alert_new('warning', 'Please enter at least credit or debit');

				} else {

					$post_data['user'] = $id;
					$post_data['type'] = $type;
					$post_data['title'] = 'Admin adjust';
					$post_data['create_by'] = auth_data('pid');
					
					$this->log_point_model->add($post_data);
					
					alert_new('success', ucfirst($type).' created successfully');

					header('refresh: 0'); exit;
				}
						
			}
			
			if(isset($_POST['edit'])) {

				$post_data = [];
				
				foreach([ 'amount_1', 'amount_0', 'title', 'remark' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}

				$post_data['user'] = $id;
				$post_data['type'] = $type;
				$post_data['update_by'] = auth_data('pid');
							
				$this->log_point_model->edit($type, $this->input->post('id'), $post_data);
				
				alert_new('success', ucfirst($type).' updated successfully');

				header('refresh: 0'); exit();

			}
			
			// if(isset($_POST['delete'])) {
				
				// $id = $this->input->post('id');
				
				// if(!empty($id)) {
					
					// $this->log_point_model->del($id);
					
				// }
							
				// alert_new('success', ucfirst($type).' deleted successfully');

				// header('refresh: 0'); exit;
			// }

		}
       
		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/list', $data);
		$this->load->view('inc/footer', $data);

	}

	public function json_view($id = '')
	{
	
		$result = $this->log_point_model->view_one2($id);
		$result[0]['payment'] = datalist_Table('tbl_payment', 'payment_no', $result[0]['payment']);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}
	
	public function json_del($id = '', $type = '')
	{
	
		if(!empty($id)) {
			
			$this->log_point_model->del($id);
			
		}
				
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($type).' deleted successfully']));

	}

}