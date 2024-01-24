<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'landing';
		$this->single = 'landing';
		
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_secondary_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('log_join_model');

		// auth_must('login');

	}

	public function apply_form($branch_id = '')
	{
	
		if(empty(datalist_Table('tbl_branches', 'pid', $branch_id))) die(app('title').': Branch ID not found');

		if(isset($_POST['save'])) {

			if(!$this->tbl_users_model->check_username( $this->input->post('username'), $branch_id ) && !empty( $this->input->post('username') ) ) {

				alert_new('warning', 'Username has been taken');
				
				redirect($this->uri->uri_string());

			} else {
				
				$post_data = [];
				
				foreach([ 'nickname', 'fullname_en', 'fullname_cn', 'gender', 'nric', 'birthday', 'email', 'email2', 'email3', 'phone', 'phone2', 'phone3', 'address', 'address2', 'address3', 'parent', 'parent_cn', 'temp_parent_phone', 'temp_parent_gender', 'temp_parent_relationship', 'parent2', 'parent_cn2', 'temp_parent_phone2', 'temp_parent_gender2', 'temp_parent_relationship2', 'school', 'temp_class', 'ref_code', 'childcare', 'childcare_teacher', 'form_teacher', 'transport', 'remark_important', 'remark', 'remark_important2', 'remark2' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}
				
				$post_data['fullname_en'] = strtoupper($post_data['fullname_en']);
				$post_data['address'] = strtoupper($post_data['address']);
				$post_data['address2'] = strtoupper($post_data['address2']);
				$post_data['address3'] = strtoupper($post_data['address3']);

				$post_data['active'] = 0;
				$post_data['branch'] = $branch_id;
				$post_data['type'] = 'student_pending';
				$post_data['temp_class'] = empty($post_data['temp_class']) ? null : json_encode($_POST['temp_class']);
				if(isset($_FILES['receipt'])) {
					// print_r($_FILES['receipt']); exit;
					$post_data['receipt'] = pointoapi_Upload($_FILES['receipt'], [
						'type'		=> 'student_receipt',
						'api_key'	=> datalist_Table('tbl_branches', 'pointoapi_key', $branch_id),
						'branch' 	=> $branch_id,
					]);
				}
				
				$post_data['parent'] = empty($post_data['parent']) ? null : strtoupper($post_data['parent']);
				$post_data['school'] = empty($post_data['school']) ? null : strtoupper($post_data['school']);
				if(empty($post_data['birthday'])) $post_data['birthday'] = null;
				
				if(empty($post_data['temp_parent_phone'])) $post_data['temp_parent_phone'] = null;
				if(empty($post_data['temp_parent_gender'])) $post_data['temp_parent_gender'] = null;
				if(empty($post_data['temp_parent_relationship'])) $post_data['temp_parent_relationship'] = null;
				
				$new_student_id = $this->tbl_users_model->add($post_data);
				
				foreach($_POST['question'] as $k => $v)
				{					
					$this->log_join_model->add([
						'type' 			=> 'student_question',
						'branch' 		=> $branch_id,
						'user' 			=> $new_student_id,
						'title' 		=> $k,
						'remark' 		=> $v
					]);
				}
				
				redirect('landing/apply_ok');

			}

		}

		$data['thispage'] = [
			'title' => 'Application E-form',
			'group' => $this->group,
			'branch' => $branch_id,
			'js' => $this->group . '/add'
		];
		
		$data['branch_img'] = datalist_Table('tbl_branches', 'image', $branch_id);
		
		$data['childcare'] = $this->tbl_secondary_model->active_list('childcare', branch_now('pid'));
		$data['teacher'] = $this->tbl_users_model->list('teacher', branch_now('pid'));
		$data['parent'] = $this->tbl_users_model->active_list('parent', $branch_id);
		$data['school'] = $this->tbl_secondary_model->active_list('school', $branch_id);
		$data['form'] = $this->tbl_secondary_model->active_list('form', $branch_id);
		$data['course'] = $this->tbl_secondary_model->active_list('course', $branch_id, ['active' => 1]);
		$data['class'] = $this->tbl_classes_model->list($branch_id, ['active' => 1]);
		
		$branch_transport = [];
		foreach($this->log_join_model->list('secondary', branch_now('pid'), ['active' => 1]) as $e) {
			if(datalist_Table('tbl_secondary', 'type', $e['secondary']) == 'transport') {
				$branch_transport[] = $e;
			}
		}
		$data['transport'] = $this->tbl_secondary_model->list('transport', branch_now('pid'), ['active' => 1]);
		$data['branch_transport'] = $branch_transport;
		

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/add');
		$this->load->view('inc/footer', $data);

	}
	
	public function apply_form_cn($branch_id = '')
	{
	
		if(empty(datalist_Table('tbl_branches', 'pid', $branch_id))) die(app('title').': Branch ID not found');

		if(isset($_POST['save'])) {

			if(!$this->tbl_users_model->check_username( $this->input->post('username'), $branch_id ) && !empty( $this->input->post('username') ) ) {

				alert_new('warning', 'Username has been taken');
				
				redirect($this->uri->uri_string());

			} else {
				
				$post_data = [];
				
				foreach([ 'nickname', 'fullname_en', 'fullname_cn', 'gender', 'nric', 'birthday', 'email', 'email2', 'email3', 'phone', 'phone2', 'phone3', 'address', 'address2', 'address3', 'parent', 'parent_cn', 'temp_parent_phone', 'temp_parent_gender', 'temp_parent_relationship', 'parent2', 'parent_cn2', 'temp_parent_phone2', 'temp_parent_gender2', 'temp_parent_relationship2', 'school', 'temp_class', 'ref_code', 'childcare', 'childcare_teacher', 'form_teacher', 'transport', 'remark_important', 'remark', 'remark_important2', 'remark2' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}
				
				$post_data['fullname_en'] = strtoupper($post_data['fullname_en']);
				$post_data['address'] = strtoupper($post_data['address']);
				$post_data['address2'] = strtoupper($post_data['address2']);
				$post_data['address3'] = strtoupper($post_data['address3']);

				$post_data['active'] = 0;
				$post_data['branch'] = $branch_id;
				$post_data['type'] = 'student_pending';
				$post_data['temp_class'] = empty($post_data['temp_class']) ? null : json_encode($_POST['temp_class']);
				if(isset($_FILES['receipt'])) {
					// print_r($_FILES['receipt']); exit;
					$post_data['receipt'] = pointoapi_Upload($_FILES['receipt'], [
						'type'		=> 'student_receipt',
						'api_key'	=> datalist_Table('tbl_branches', 'pointoapi_key', $branch_id),
						'branch' 	=> $branch_id,
					]);
				}
				
				$post_data['parent'] = empty($post_data['parent']) ? null : strtoupper($post_data['parent']);
				$post_data['school'] = empty($post_data['school']) ? null : strtoupper($post_data['school']);
				if(empty($post_data['birthday'])) $post_data['birthday'] = null;
				
				if(empty($post_data['temp_parent_phone'])) $post_data['temp_parent_phone'] = null;
				if(empty($post_data['temp_parent_gender'])) $post_data['temp_parent_gender'] = null;
				
				$this->tbl_users_model->add($post_data);
				
				foreach($_POST['classes'] as $e)
				{
					$this->log_join_model->add([
						'user' => $new_student_id,
						'class' => $e['class'],
						'date' => date('Y-m-d'),
						'branch' => $branch_id,
						'type' => 'join_class',
					]);
				}
								
				redirect('landing/apply_ok');

			}

		}

		$data['thispage'] = [
			'title' => '申请表格',
			'group' => $this->group,
			'branch' => $branch_id,
			'js' => $this->group . '/add'
		];
		
		$data['branch_img'] = datalist_Table('tbl_branches', 'image', $branch_id);
		
		$data['childcare'] = $this->tbl_secondary_model->active_list('childcare', branch_now('pid'));
		$data['teacher'] = $this->tbl_users_model->list('teacher', branch_now('pid'));
		$data['parent'] = $this->tbl_users_model->active_list('parent', $branch_id);
		$data['school'] = $this->tbl_secondary_model->active_list('school', $branch_id);
		$data['form'] = $this->tbl_secondary_model->active_list('form', $branch_id);
		$data['course'] = $this->tbl_secondary_model->active_list('course', $branch_id, ['active' => 1]);
		$data['class'] = $this->tbl_classes_model->list($branch_id, ['active' => 1]);
		
		$branch_transport = [];
		foreach($this->log_join_model->list('secondary', branch_now('pid'), ['active' => 1]) as $e) {
			if(datalist_Table('tbl_secondary', 'type', $e['secondary']) == 'transport') {
				$branch_transport[] = $e;
			}
		}
		$data['transport'] = $this->tbl_secondary_model->list('transport', branch_now('pid'), ['active' => 1]);
		$data['branch_transport'] = $branch_transport;

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/add_cn');
		$this->load->view('inc/footer', $data);

	}
	
	public function apply_ok()
	{

		$data['thispage'] = [
			'title' => 'Application Submitted',
			'group' => $this->group,
		];

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/ok');
		$this->load->view('inc/footer', $data);

	}
	
	public function json_discount($count = '')
	{
		$course = $this->uri->segment(3);
		$count = $this->uri->segment(4);
		
		$sql = 'SELECT b.amount, b.material, b.subsidy FROM log_join a
			JOIN log_join b  ON b.parent = a.parent
			WHERE (a.type = "class_bundle_course" OR b.type = "class_bundle_price") and a.course = "'.$course.'" and b.qty = '.$count.' and a.is_delete = 0 and b.is_delete = 0';
		$data = $this->db->query($sql)->result_array();
		
		$material = ($data[0]['material'] == NULL)?0:$data[0]['material'];
		$subsidy = ($data[0]['subsidy'] == NULL)?0:$data[0]['subsidy'];
		
		
		header('Content-type: application/json');
		die(json_encode(['price' => $data[0]['amount'], 'material' => $material, 'subsidy' => $subsidy]));
	}

}
