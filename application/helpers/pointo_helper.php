<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function app($type) {

	switch($type) {

		case 'title':
			$output = POINTO_APP_TITLE;
			break;

		default:
			$output = '';

	}

	return $output;

}

// func_get_arg()

function badge($status) {

	return $status == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-dark">Inactive</span>' ;

}

function get_new($type) {
	
	switch($type) {

		case 'id':
			$output = time().rand(11, 99);
			break;

		default:
			$output = null;

	}

	return $output;

}

function alert_new($type, $message) {

	$this_ci =& get_instance();

	$this_ci->session->set_flashdata('alert_type', $type);
	$this_ci->session->set_flashdata('alert_message', $message);

}

function alert_get() {

	$this_ci =& get_instance();

	$type = $this_ci->session->flashdata('alert_type');
	$message = $this_ci->session->flashdata('alert_message');
	
	$this_ci->session->set_flashdata('alert_type', '');
	$this_ci->session->set_flashdata('alert_message', '');

	if( !empty($type) && !empty($message) ) return '<div class="alert alert-'.$type.'">'.$message.'</div>';

}

function auth_must($action) {

	$this_ci =& get_instance();

	$auth_id = isset($_COOKIE[md5('@highpeakedu-sso')]) ? $_COOKIE[md5('@highpeakedu-sso')] : '' ;
	// $auth_id = $this_ci->session->userdata('auth');

	$this_ci->load->model('tbl_admins_model');
	$this_ci->load->model('tbl_branches_model');

	$result = $this_ci->tbl_admins_model->me_token($auth_id);
	
	if(count($result) == 1) {
		
		$result2 = $result[0];
		
		$this_ci->tbl_admins_model->edit($result2['pid'], [
			'last_online'	=> date('Y-m-d H:i:s')
		]);
		
		$this_ci->tbl_branches_model->edit(branch_now('pid'), [
			'last_online'	=> date('Y-m-d H:i:s')
		]);

	}

	if($action == 'login') {

		if(count($result) == 0) {
			
			if (!defined('WHITELABEL')) {
				redirect('auth/logout');
			} else {
				redirect('auth/login');
			}
			// redirect('https://synorexcloud.com/client/services?pg=sso&id=161687809860');

		}

	} else {

		if(count($result) == 1) {
			
			redirect();
			
		}

	}

}

function auth_must_teacher($action) {

	$this_ci =& get_instance();

	$auth_id = isset($_COOKIE[md5('@highpeakedu-teacher-sso')]) ? $_COOKIE[md5('@highpeakedu-teacher-sso')] : '' ;
	// $auth_id = $this_ci->session->userdata('auth');

	$this_ci->load->model('tbl_users_model');

	$result = $this_ci->tbl_users_model->me_teacher_token($auth_id);
	
	if(count($result) == 1) {
		
		$result2 = $result[0];
		
		$this_ci->tbl_users_model->edit($result2['pid'], [
			'last_online'	=> date('Y-m-d H:i:s')
		]);
		
	}

	if($action == 'login') {

		if(count($result) == 0) {
			
			if (!defined('WHITELABEL')) {
				redirect('webapp_teacher/logout');
			} else {
				redirect('webapp_teacher/login');
			}

		}

	} else {

		if(count($result) == 1) {
			
			redirect('webapp_teacher');
			
		}

	}

}

function auth_data($output) {

	$this_ci =& get_instance();

	$auth_id = isset($_COOKIE[md5('@highpeakedu-sso')]) ? $_COOKIE[md5('@highpeakedu-sso')] : '' ;
	// $auth_id = $this_ci->session->userdata('auth');

	$this_ci->load->model('tbl_admins_model');

	$result = $this_ci->tbl_admins_model->me_token($auth_id);
	
	if(count($result) == 1) {

		$result = $result[0];

		return isset($result[$output]) ? $result[$output] : null ;

	} else {

		return null;

	}

}


function auth_data_user($output) {

	$this_ci =& get_instance();

	$auth_id = isset($_COOKIE[md5('@highpeakedu-portal')]) ? $_COOKIE[md5('@highpeakedu-portal')] : '' ;
	// $auth_id = $this_ci->session->userdata('auth');

	$this_ci->load->model('tbl_users_model');

	$result = $this_ci->tbl_users_model->me_token($auth_id);
	
	if(count($result) == 1) {

		$result = $result[0];

		return isset($result[$output]) ? $result[$output] : null ;

	} else {

		return null;

	}

}

function auth_data_agent($output) {

	$this_ci =& get_instance();

	$auth_id = isset($_COOKIE[md5('@highpeakedu-portal')]) ? $_COOKIE[md5('@highpeakedu-portal')] : '' ;

	$this_ci->load->model('tbl_agents_model');

	$result = $this_ci->tbl_agents_model->me_token($auth_id);
	
	if(count($result) == 1) {

		$result = $result[0];

		return isset($result[$output]) ? $result[$output] : null ;

	} else {

		return null;

	}

}

function auth_data_teacher($output) {

	$this_ci =& get_instance();

	$auth_id = isset($_COOKIE[md5('@highpeakedu-teacher-sso')]) ? $_COOKIE[md5('@highpeakedu-teacher-sso')] : '' ;

	$this_ci->load->model('tbl_users_model');
	
	$result = $this_ci->tbl_users_model->me_teacher_token($auth_id);
	
	if(count($result) == 1) {

		$result = $result[0];

		return isset($result[$output]) ? $result[$output] : null ;

	} else {

		return null;

	}

}

function branch_now($output) {

	$this_ci =& get_instance();

	$branch_id = isset($_COOKIE[md5('@highpeakedu-branch')]) ? $_COOKIE[md5('@highpeakedu-branch')] : '' ;
	// $branch_id = $this_ci->session->userdata('branch');

	if(empty($branch_id)) {

		$result = my_branches();

		if(count($result) > 0) {
			
			$result = $result[0];
			
			if( datalist_Table('tbl_branches', 'active', $result['branch']) == 1 && datalist_Table('tbl_branches', 'is_delete', $result['branch']) == 0 ) {

			$branch_id = $result['branch'];
			
			} else {
				
				$branch_id = null;
				
			}

		} else {

			$branch_id = null;

		}

	}

	return datalist_Table('tbl_branches', $output, $branch_id);

}

function refresh() {
	
	header('refresh: 0'); exit;
	
}

// Tan Jing Suan
function auth_must_teacher_qr($last_visit_url) {
	$this_ci =& get_instance();
	$auth_id = isset($_COOKIE[md5('@highpeakedu-teacher-sso')]) ? $_COOKIE[md5('@highpeakedu-teacher-sso')] : '' ;
	$this_ci->load->model('tbl_users_model');
	$result = $this_ci->tbl_users_model->me_teacher_token($auth_id);
	if(count($result) == 1) {		
		$result2 = $result[0];		
		$this_ci->tbl_users_model->edit($result2['pid'], [
			'last_online'	=> date('Y-m-d H:i:s')
		]);		
	}
	if(count($result) == 0) {		
		if (!defined('WHITELABEL')) {
			redirect('webapp_teacher/logout');
		} else {
			redirect('webapp_teacher/login?next='.$last_visit_url);
		}
	}
}