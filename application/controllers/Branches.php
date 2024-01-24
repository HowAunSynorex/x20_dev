<?php

use function PHPSTORM_META\type;

defined('BASEPATH') OR exit('No direct script access allowed');

class Branches extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'branches';
		$this->single = 'branch';
		
		$this->load->model('tbl_branches_model');
		$this->load->model('tbl_users_model');
		$this->load->model('log_join_model');
		$this->load->model('tbl_secondary_model');

		auth_must('login');

	}

	public function list()
	{

		$data['thispage'] = [
			'title' => 'Branches',
			'group' => $this->group,
		];

		$this->load->model('tbl_branches_model');

		$data['result'] = $this->tbl_branches_model->list([
			'active' => 1,
		]);
		

		
		if(isset($_POST['pay_now'])) {
			
			$price = [
				164791733983 => 288,
				164791734327 => 588,
				164791734720 => 1288,
			];
			
			$expired_date = datalist_Table('tbl_branches', 'expired_date', $_POST['branch']);
			$license_start_date = strtotime($expired_date) < strtotime(date('Y-m-d')) ? date('Y-m-d') : $expired_date ;
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://invoice.synorex.xyz/api/new_payment',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => array(
					'title' 		=> 'Robocube Tuition License Renew',
					'remark' 		=> '',
					'status' 		=> 'unpaid',
					'date' 			=> date('Y-m-d'),
					'due_date'		=> date('Y-m-d', strtotime('+7 days')),
					'amount' 		=> $price[ $_POST['plan'] ],
					'currency' 		=> 'MYR',
					'data' 			=> json_encode([
						'one_id'	=> auth_data('pid'),
						'branch_id'	=> $_POST['branch'],
						'next_date'	=> date('Y-m-d', strtotime('+1 year', strtotime( $license_start_date ))),
						'callback'	=> base_url('api/billing_callback?branch_id=&next_date='),
					]),
				),
			));

			$response = json_decode(curl_exec($curl), true);
			if(!is_array($response)) $response = [ 'status' => 'api_error' ];
			curl_close($curl);
			
			if($response['status'] == 'ok') {
				
				redirect($response['redirect'].'&back='.urlencode(base_url('branches/list')));
				
			} else {
				
				alert_new('danger', $response['status']);
				
			}
			
			refresh();
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/list', $data);
		$this->load->view('inc/footer', $data);

	}

	public function add()
	{

		if(isset($_POST['save'])) {

			$post_data = [];
			
			foreach([ 'title', 'ssm_no', 'country', 'currency', 'phone', 'phone2', 'phone3', 'email', 'email2', 'email3', 'address', 'address2', 'address3' ] as $e) {
				
				$post_data[$e] = $this->input->post($e);
				
			}

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
		
		$data['id'] = $id;
		$data['result'] = $this->tbl_branches_model->view($id);
		$data['bill'] = $this->log_join_model->list('bill', $id);
		
		if(!isset($_GET['tab'])) $_GET['tab'] = 1;

		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect('settings/' . $this->group);

		} else {

			$data['result'] = $data['result'][0];

			$data['thispage'] = [
				'title' => 'Branch: '.$data['result']['title'],
				'group' => $this->group,
				'css' => $this->group.'/edit',
				'js' => $this->group.'/edit',
			];

			if(isset($_POST['save'])) {

				$post_data = [];
				
				foreach([ 'title', 'ssm_no', 'country', 'currency', 'phone', 'phone2', 'phone3', 'email', 'email2', 'email3', 'address', 'address2', 'address3' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}

				// $post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
				$post_data['update_by'] = auth_data('pid');
				if( empty($post_data['country']) ) $post_data['country'] = null;
				if( empty($post_data['currency']) ) $post_data['currency'] = null;

				$this->tbl_branches_model->edit($id, $post_data);
				
				alert_new('success', 'Branch updated successfully');
				
				// redirect($this->group . '/list');
				header('refresh: 0'); exit; 

			}
			
			if(isset($_POST['add-logo'])) {
				
				$post_data['image'] = pointoapi_Upload($_FILES['image'], [
					'default' => $data['result']['image'],
					'type' => 'branch_logo',
					'api_key' => POINTO_API_KEY,
				]);
				
				$this->tbl_branches_model->edit($id, $post_data);
				header('refresh: 0'); exit;
				
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
