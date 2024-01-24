<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './vendor/PHPMailer/src/Exception.php';
require './vendor/PHPMailer/src/PHPMailer.php';
require './vendor/PHPMailer/src/SMTP.php';

class Admins extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'admins';
		$this->single = 'admin';
		
		$this->load->model('tbl_admins_model');
		$this->load->model('log_join_model');

		auth_must('login');

	}

	public function list()
	{
		
		auth_must('login');
		check_module_page('Settings/Read');

		// print_r($this->check_email_exists( 'admin@gmail.com' )); exit;

		$data['thispage'] = [
			'title' => 'All '.ucfirst($this->group),
			'group' => $this->group,
			'js' => $this->group.'/list',
		];

		if(isset($_POST['send'])) {
	
			header('Content-type: application/json');
			
			if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
				
				die(json_encode(array('status' => 'format_error', 'message' => 'Please use a valid email')));
				
			} elseif( $this->check_email_exists($_POST['email']) ) { 
				
				// 这里要判断如果用户已被邀请就显示这个msg
				die(json_encode(array('status' => 'failed', 'message' => 'User exists')));
				
			} elseif( 1==1 ) {
				
				// spam detect, dont remove
				$this->session->set_userdata('send-'.$_POST['email'], 1);
				
				// reject
				$this->log_join_model->rejecte_pending_email_user( $_POST['email'] );
				
				// add data
				// 添加这个用户到log_join
		
				$join_data['type'] = 'pending_active';
				$join_data['active'] = 0;
				$join_data['status'] = 'pending';
				$join_data['branch'] = branch_now('pid');
				$join_data['create_by'] = auth_data('pid');
				$join_data['email'] = $_POST['email'];
				$this->log_join_model->add($join_data);
				
// $msg='Hi there,

// Click the link below to confirm that you have joined '.branch_now('title').'\'s '.app('title').', if you do not agree, you can ignore this email.



// '.base_url().'

// This message was generated automatically. Don\'t reply to this message.';
				
				// mail($_POST['email'], '[Synorex] '.auth_data('nickname').' invite you to use '.app('title'), $msg);
				
				// $this->load->library('email');
				
				// $config['protocol'] = 'smtp';
				// $config['charset'] = 'iso-8859-1';
				// $config['wordwrap'] = TRUE;
				// $config['smtp_host'] = 'whm3042.newipdns.com';
				// $config['smtp_user'] = 'no-reply@synorexcloud.com';
				// $config['smtp_pass'] = '!(gq{m%+Y.8t';
				// $config['smtp_port'] = '465';
				// $config['smtp_crypto'] = 'ssl';
				// $config['mailtype'] = 'html';
				
				// $this->email->initialize($config);

				// $this->email->from('no-reply@synorexcloud.com');
				// $this->email->to($_POST['email']);
				// $this->email->to('yulongsoon@gmail.com');

				// $this->email->subject('[Synorex] '.auth_data('nickname').' invite you to use '.app('title'));
				// $this->email->message('
				// <p>Hi there,</p>
				// <p>Click the link below to confirm that you have joined <b>'.branch_now('title').'</b>\'s '.app('title').', if you do not agree, you can ignore this email.</p>
				// <a href="'.base_url().'" target="_blank">Confirm</a>
				// <p>This message was generated automatically. Don\'t reply to this message.</p>
				// <br>
				// <img src="https://cdn.synorex.link/assets/images/site/logo-x600.png" style="height: 50px;">');

				// $this->email->send();
				
				// send mail
				$resp = pointoapi_Request('SynorexAPI/Email/Send', [
					'to' => $_POST['email'],
					'api_key' => POINTO_API_KEY,
					'subject' => '[Synorex] '.auth_data('nickname').' invite you to use '.app('title'),
					'body' => '
					<p>Hi there,</p>
					<p>Click the link below to confirm that you have joined <b>'.branch_now('title').'</b>\'s '.app('title').', if you do not agree, you can ignore this email.</p>
					<a href="'.base_url().'" target="_blank">Confirm</a>
					<p>This message was generated automatically. Don\'t reply to this message.</p>
					<br>
					<img src="https://cdn.synorex.link/assets/images/site/logo-x600.png" style="height: 50px;">
					',
				]);
				
				// print_r($resp); exit;
				
				die(json_encode(array('status' => 'ok')));
				
			} else {
				
				die(json_encode(array('status' => 'error', 'message' => 'Unknow error')));
				
			}

		}
		
		$data['result'] = $this->log_join_model->list('join_branch', branch_now('pid'));
		$data['result_pending'] = $this->log_join_model->list('pending_active', branch_now('pid'));
	
		if(!defined('WHITELABEL')) {
			$page = '/list';
		} else {
			$page = '/list_whitelabel';
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.$page, $data);
		$this->load->view('inc/footer', $data);

	}

	public function add_whitelabel()
	{
		
		$data['thispage'] = [
			'title' => 'Add '.ucfirst($this->single),
			'group' => $this->group,
			'js' => $this->group . '/add_whitelabel',
		];

		if(isset($_POST['save'])) {
			
			$check_username = $this->tbl_admins_model->list([ 'username' => $this->input->post('username') ]);

	        if(!empty($check_username)) {

	        	alert_new('warning', 'Username has been taken');
	        	refresh();

        	} else {

        		$post_data = [];
				
				foreach([ 'username', 'active', 'password', 'nickname' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}

				$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
				$post_data['create_by'] = auth_data('pid');
				$post_data['password'] = password_hash($post_data['password'], PASSWORD_DEFAULT);
				
				$token = openssl_encrypt(time(), 'AES-128-CTR', 'robocube-tuition-token', 0, '1234567891011121');
				$post_data['token'] = $token;
				
				$id = $this->tbl_admins_model->add($post_data);
				
				$join_data['type'] = 'join_branch';
				$join_data['active'] = 0;
				$join_data['status'] = '';
				$join_data['branch'] = branch_now('pid');
				$join_data['create_by'] = auth_data('pid');
				$join_data['admin'] = $id;
				$this->log_join_model->add($join_data);
	            
	        	alert_new('success', ucfirst($this->single).' created successfully');
				redirect($this->group . '/list');

        	}

		}

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/add_whitelabel');
		$this->load->view('inc/footer', $data);
		
	}
	
	public function edit($id = '')
	{
		
		auth_must('login');
		check_module_page('Settings/Read');

		if(!isset($_GET['tab'])) $_GET['tab'] = 1;
		
		$data['result'] = $this->log_join_model->view($id);

		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect($this->group.'/list');

		} else {
			
			if($data['result'][0]['branch'] != branch_now('pid') || $data['result'][0]['type'] != 'join_branch') {

				redirect('home/error_404');

			} else {
				
				$data['admin'] = $this->tbl_admins_model->view($data['result'][0]['admin'])[0];
				
				$data['thispage'] = [
					'title' => 'Edit '.ucfirst($this->single),
					'group' => $this->group,
					'js' => $this->group.'/edit'
				];

				$data['result'] = $data['result'][0];

				if(isset($_POST['save_permission'])) {

					if(!isset($_POST['permission'])) $_POST['permission'] = [];
					
					// echo '<pre>';
					// print_r($_POST['permission']); exit;
					// echo '</pre>';

					$post_data['permission'] = json_encode($_POST['permission']);
					// $post_data['update_by'] = auth_data('pid');

					$this->log_join_model->edit($id, $post_data);
					
					alert_new('success', 'Permission updated successfully');
					
					header('refresh: 0'); exit;

				}

				if(isset($_POST['save'])) {
			
					$post_data = [];
					
					foreach([ 'active', 'password' ,'nickname'] as $e) {
						
						$post_data[$e] = $this->input->post($e);
						
					}

					$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
					$post_data['update_by'] = auth_data('pid');
					$post_data['password'] = empty($post_data['password']) ? $data['result']['password'] : password_hash($post_data['password'], PASSWORD_DEFAULT);
					
					$this->tbl_admins_model->edit($data['admin']['pid'], $post_data);
					
					alert_new('success', ucfirst($this->single).' updated successfully');
					redirect($this->group . '/list');

				}
				
				if(isset($_POST['save_branch'])) {
					
					$query = $this->log_join_model->list_admin([ 'admin' => $data['admin']['pid'], 'type' => 'join_branch' ]);
					
					if(empty($_POST['branch'])) {
						 alert_new('warning', 'Please choose at least one branch');
						 redirect($this->group . '/edit/' . $id . '?tab=2');
					} else {
						
						foreach($query as $e) {
							$this->log_join_model->del($e['id']);
						}
						
						foreach($_POST['branch'] as $e) {
							
							$join_data['type'] = 'join_branch';
							$join_data['active'] = 1;
							$join_data['status'] = '';
							$join_data['branch'] = $e;
							$join_data['create_by'] = auth_data('pid');
							$join_data['admin'] = $data['admin']['pid'];
							$this->log_join_model->add($join_data);
							
						}
						
						alert_new('success', ucfirst($this->single).' updated successfully');
						redirect($this->group . '/list');
						
					}

				}

				$this->load->view('inc/header', $data);
				$this->load->view($this->group.'/edit', $data);
				$this->load->view('inc/footer', $data);
				
			}

		}	

	}
	
	public function json_del($id = '')
	{
		
		auth_must('login');
		check_module_page('Settings/Delete');
	
		if(!empty($id)) {
			
			// $result = $this->log_join_model->list('join_branch', branch_now('pid'), ['admin' => $id]);
			
			if(defined('WHITELABEL')) {
				$admin_id = $this->log_join_model->view($id)[0]['admin'];
				$this->tbl_admins_model->del($admin_id);
			}
			
			$this->log_join_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Admin removed successfully']));

	}

	private function check_email_exists($email)
	{
	
		// check admin
		$result_admin = $this->tbl_admins_model->list( [ 'username' => $email, 'is_delete' => 0 ] );
		
		if( count( $result_admin ) == 1 ) {
			
			$result_admin = $result_admin[0];
			
			// check admin join
			$result_joined = $this->log_join_model->list( 'join_branch', branch_now('pid'), [ 'admin' => $result_admin['pid'], 'is_delete' => 0 ] );
			
			if( count( $result_joined ) == 1 ) {
				
				return true;
				
			} else {
				
				$result_joined = $this->log_join_model->list( 'pending_active', branch_now('pid'), [ 'email' => $email, 'is_delete' => 0 ] );
				
				return count( $result_joined ) > 0 ? true : false ;
				
			}
			
		} else {
			
			// check admin join
			$result_joined = $this->log_join_model->list( 'pending_active', branch_now('pid'), [ 'email' => $email, 'is_delete' => 0 ] );
			
			return count( $result_joined ) > 0 ? true : false ;
			
		}
	
	}
	
	public function json_check_username($username, $id = '') {
		
		if(empty($id)) {
			$result = $this->tbl_admins_model->list(['username' => urldecode($username)]);
		} else {
			$result = $this->tbl_admins_model->list(['username' => urldecode($username), 'pid !=' => $id ]);
		}
		
		if(count($result) > 0) {
			$valid = false;
		} else {
			$valid = true;
		}

		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $valid]));
		
	}

}
