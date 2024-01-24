<?php

use function PHPSTORM_META\type;

defined('BASEPATH') OR exit('No direct script access allowed');

class Homework extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'homework';
		$this->single = 'homework';
		
		$this->load->model('tbl_content_model');
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('log_join_model');

	}

	public function list()
	{

		auth_must('login');
		check_module_page('Homework/Read');
		
		$data['thispage'] = [
			'title' => ucfirst($this->group),
			'group' => $this->group,
			'js' => $this->group.'/list',
			'css' => $this->group.'/list',
		];

		$data['result'] = $this->tbl_content_model->list('homework', branch_now('pid'));
		
		// if(isset($_POST['add-slideshow'])) {
		
			// $post_data = [];
			// $post_data['image'] = pointoapi_Upload($_FILES['image'], [
				// 'type' => 'slideshow',
				// 'branch' => branch_now('pid'),
			// ]);
			
			// $post_data['type'] = $type;
			// $post_data['branch'] = branch_now('pid');
			// $post_data['create_by'] = auth_data('pid');
			// $this->tbl_content_model->add($post_data);
			
			// alert_new('success', 'Slideshow created successfully');
			// header('refresh: 0'); exit();
			
		// }

		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/list', $data);
		$this->load->view('inc/footer', $data);

	}

	public function add($id = '')
	{
		
		auth_must('login');
		check_module_page('Homework/Create');
		
		if(!empty($id) && count($this->tbl_classes_model->view($id)) == 0) {

			alert_new('warning', 'Data not found');

			redirect($this->group);

		} else {
	
			if(isset($_POST['save'])) {

				$post_data = [];
				
				foreach([ 'subject', 'body', 'class', 'student', 'status', 'date' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}
				
				$post_data['type'] = 'homework';
				$post_data['branch'] = branch_now('pid');
				$post_data['create_by'] = auth_data('pid');
				if(empty($post_data['date'])) $post_data['date'] = null;

				$this->tbl_content_model->add($post_data);
				
				alert_new('success', ucfirst($this->group) . ' created successfully');
				
				redirect($this->group.'/list');

			}
			
			$data['id'] = $id;
			$data['class'] = $this->tbl_classes_model->list(branch_now('pid'), ['active' => 1, 'is_hidden' => 0]);
			$data['student'] = $this->tbl_users_model->list('student', branch_now('pid'), ['active' => 1]);

			$data['thispage'] = [
				'title' => 'Add '.ucfirst($this->group),
				'group' => $this->group,
				'js' => $this->group.'/add'
			];
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/add');
			$this->load->view('inc/footer', $data);
			
		}
			
	}

	public function edit($id = '')
	{
		
		auth_must('login');
		check_module_page('Homework/Read');

		$data['result'] = $this->tbl_content_model->view($id);
		$data['class'] = $this->tbl_classes_model->list(branch_now('pid'), ['active' => 1, 'is_hidden' => 0]);
		$data['student'] = $this->tbl_users_model->list('student', branch_now('pid'), ['active' => 1]);

		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect('');

		} else {

			$data['result'] = $data['result'][0];

			$data['thispage'] = [
				'title' => 'Edit '.ucfirst($this->group),
				'group' => $this->group,
				'js' => $this->group.'/edit'
			];
					
			if(isset($_POST['save'])) {

				$post_data = [];
				
				foreach([ 'subject', 'body', 'student', 'status', 'date' ] as $e) {
						
					$post_data[$e] = $this->input->post($e);
					
				}

				$post_data['update_by'] = auth_data('pid');

				$this->tbl_content_model->edit($id, $post_data);
				
				alert_new('success', 'Homework updated successfully');
				
				redirect($this->group.'/list');

			}

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}

	public function json_del($id = '')
	{

		auth_must('login');
		check_module_page('Homework/Delete');

		if(!empty($id)) {
	
			$this->tbl_content_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Homework deleted successfully']));

	}
	
	public function json_list()
	{	

		header('Content-type: application/json');	
		
		$user = $this->tbl_users_model->me_token( post_data('token') );

		if( count($user) == 0 ) {
			
			die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));
			
		}
		
		$user = $user[0];
		
		$sql = '
			SELECT * FROM tbl_content
			WHERE type = "homework"
			AND is_delete = 0
			AND status != "done"
			ORDER BY date DESC
		
		';
		
		$result = $this->db->query($sql)->result_array();
		
		$sql = '
			SELECT * FROM tbl_content
			WHERE type = "homework"
			AND is_delete = 0
			AND status = "done"
			ORDER BY date DESC
		
		';
		
		$result_done = $this->db->query($sql)->result_array();
		
		$final = [];
		$final['result'] = [];
		$final['result_done'] = [];
		
		switch($user['type']) {
			
			case 'parent':
				$child = $this->tbl_users_model->list('student', $user['branch'], [ 'parent' => $user['pid'] ]);
				
				foreach($child as $c) {
					
					foreach($result as $e) {
				
						$join_class = $this->log_join_model->list('join_class', $c['branch'], ["class" => $e['class']]);
						$show = false;
						if(empty($e['student'])) {
							if(count($join_class) > 0) {
								$show = true;
							}
						} else {
							if(count($join_class) > 0 && $e['student'] == $c['pid']) {
								$show = true;
							}
						}
						
						if($show && !in_array($e, $final['result'])) {
							$final['result'][] = $e;
						}
						
					}
					
					foreach($result_done as $e) {
						
						$join_class = $this->log_join_model->list('join_class', $c['branch'], ["class" => $e['class']]);
						$show = false;
						if(empty($e['student'])) {
							if(count($join_class) > 0) {
								$show = true;
							}
						} else {
							if(count($join_class) > 0 && $e['student'] == $c['pid']) {
								$show = true;
							}
						}
						
						if($show && !in_array($e, $final['result_done'])) {
							$final['result_done'][] = $e;
						}
						
					}
					
				}
				
				break;
			
			case 'student':
			
				foreach($result as $e) {
			
					$join_class = $this->log_join_model->list('join_class', $user['branch'], ["class" => $e['class']]);
					$show = false;
					if(empty($e['student'])) {
						if(count($join_class) > 0) {
							$show = true;
						}
					} else {
						if(count($join_class) > 0 && $e['student'] == $user['pid']) {
							$show = true;
						}
					}
					
					if($show) {
						$final['result'][] = $e;
					}
					
				}
				
				foreach($result_done as $e) {
					
					$join_class = $this->log_join_model->list('join_class', $user['branch'], ["class" => $e['class']]);
					$show = false;
					if(empty($e['student'])) {
						if(count($join_class) > 0) {
							$show = true;
						}
					} else {
						if(count($join_class) > 0 && $e['student'] == $user['pid']) {
							$show = true;
						}
					}
					
					if($show) {
						$final['result_done'][] = $e;
					}
					
				}
				
				break;
			
		}
		
		die(json_encode([ 'status' => 'ok', 'result' => $final]));
		
	}
	
	/*
	 * @author Steve
	 *
	**/
	public function json_upload_image()
	{

		header('Content-type: application/json');
		
		if( empty(branch_now('pointoapi_key')) ) {
			
			die(json_encode(array('error' => [ 'message' => 'Setup PointoAPI API Key to use this function' ] )));
			
		} else {
			
			$pointoapi_Upload = pointoapi_Upload($_FILES['upload'], [
				'type' => 'content_image',
				'branch' => branch_now('pid'),
			]);
			
			die(json_encode(array('uploaded' => true, 'url' => pointoapi_UploadSource( $pointoapi_Upload ) )));
			
		}
		
		
		
	}

}
