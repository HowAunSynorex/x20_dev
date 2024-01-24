<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal extends CI_Controller {

	public function __construct()
	{

		parent::__construct();
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_branches_model');

		$this->group = 'portal';
		
		
	}
	
	
	public function login()
	{

		/* auth_must('logout'); */
		
		/* if (!defined('WHITELABEL')) {
	    	redirect('https://one.synorexcloud.com/client/services?pg=sso&id=161687809860');
        } */
		// auth_must('logout');
		
		$data['thispage'] = [
			'title' => 'Login',
			'group' => $this->group,
		];
		
		$this->load->model('tbl_agents_model');
		
		if(isset($_POST['login'])) {
		    
		    $login = $this->tbl_agents_model->login($this->input->post('username'), $this->input->post('password'));
			
			
			if( $login != false ) {
				
				$token = openssl_encrypt(time(), 'AES-128-CTR', 'robocube-tuition-token', 0, '1234567891011121');
				$this->tbl_agents_model->edit($login, [ 'token' => $token ]);
				setcookie(md5('@robocube-tuition-portal'), $token, time() + (86400 * 30), '/');
				redirect('portal');
				
			} else {

				alert_new('warning', 'Login failed');
				header('refresh: 0'); exit;

			}
		    
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/login', $data);
		$this->load->view('inc/footer', $data);

	}
	

	public function index()
	{
		auth_must('login');
		
		$this->load->model('tbl_agents_model');
		$logged_user_id = auth_data_agent('pid');
		
		$data['students_result'] = [];
		$data['this_agent'] = $this->tbl_agents_model->view($logged_user_id);
		$data['branches'] = $this->tbl_branches_model->list();
		

		//$data['branches'] = $this->tbl_branches_model->view($data['result']['branch']);
		if(isset($_GET['search'])) {
			
			$data['students_result'] = $this->tbl_users_model->student_list('student',$_GET['branch_id'],['insurance' => $_GET['status']]);
			
		}
		
		if(isset($_POST['insurance_action'])) {
			$action = $_POST['action_take'];
			
			if ($action == 'reject')
			{
				$action = null;
			}
			if ($action == 'approve')
			{
				$action = 'confirm';
			}
			
			foreach($_POST['student'] as $e) {
				$this->tbl_users_model->edit($e, ['insurance' => $action]);
			}
			
			alert_new('success', 'Insurance '. $_POST['action_take'] .' successfully');
			header('refresh: 0'); exit;
			
		}
		
		$data['thispage'] = [
			'title' => 'Home',
			'group' => $this->group,
			'js' => $this->group.'/list'
		];

		$this->load->view('inc/header_admin', $data);
		$this->load->view($this->group.'/index');
		$this->load->view('inc/footer_admin', $data);

	}
	
	/*
	 * calendar
	 *
	**/
	public function calendar()
	{

		//$this->auth();

		$this->load->model('tbl_events_model');
		
		$data['thispage'] = [
			'title' => 'Calendar',
			'group' => 'calendar',
			'js' => $this->group.'/calendar'
		];

		if(!isset($_GET['tab'])) $_GET['tab'] = 1;

		if (isset($_POST['save'])) {

			$post_data = [];

			foreach (['title', 'date_start', 'date_end', 'remark'] as $e) {
				$post_data[$e] = $this->input->post($e);
			}

			$post_data['type'] = 'holiday';
			if(empty($this->input->post('date_end'))) $post_data['date_end'] = null ;
			$post_data['branch'] = null;

			$this->tbl_events_model->add($post_data);

			alert_new('success', 'Holiday created successfully');

			header('refresh: 0'); exit;
		}
		
		$data['result'] = $this->tbl_events_model->list_admin(['branch' => null, 'type' => 'holiday']);

		$this->load->view('inc/header_admin', $data);
		$this->load->view($this->group.'/calendar', $data);
		$this->load->view('inc/footer_admin', $data);

	}

	public function calendar_edit($id = '')
	{

		//$this->auth();

		$this->load->model('tbl_events_model');

		$data['result'] = $this->tbl_events_model->view($id);

		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect($this->group);

		} else {

			$data['result'] = $data['result'][0];

			if(isset($_POST['save'])) {

				$post_data = [];
				
				foreach (['title', 'date_start', 'date_end', 'remark'] as $e) {
					$post_data[$e] = $this->input->post($e);
				}

				$post_data['type'] = 'holiday';
				$post_data['date_end'] = !empty( $post_data['date_end'] ) ? $post_data['date_end'] : null ;
				$post_data['branch'] = null;

				$this->tbl_events_model->edit($id, $post_data);
				
				alert_new('success', 'Holiday updated successfully');
				
				refresh();

			}

			$data['thispage'] = [
				'title' => 'Edit '.datalist('event_type')[ $data['result']['type'] ]['single'], // 这里要放dynamic datalist的title
				'group' => 'calendar',
				'js' => $this->group.'/calendar_edit'
			];

			$this->load->view('inc/header_admin', $data);
			$this->load->view($this->group.'/calendar_edit', $data);
			$this->load->view('inc/footer_admin', $data);

		}

	}

	public function json_calendar_list($type = '')
	{

		//$this->auth();

		$result = [];

		$this->load->model('tbl_events_model');

		$search = [
			'active' => 1,
			'type' => 'holiday'
		];

		unset($search['date_start >=']);
		unset($search['date_end <=']);

		$search['date_start >='] = date('Y-m-d', strtotime($_GET['start']));
		$search['date_start <='] = date('Y-m-d', strtotime($_GET['end']));

		foreach($this->tbl_events_model->list_admin($search) as $e) {

			$result[] = [
				'title' => $e['title'],
                'date' => $e['date_start'],
                'end' => $e['date_end'],
                'url' => base_url($this->group.'/calendar_edit/'.$e['pid']),
			];

		}

		
		header('Content-type: application/json');
		die(json_encode($result));

	}

	public function json_calendar_del($id = '')
	{
	
		//$this->auth();
		
		$this->load->model('tbl_events_model');
		
		$type = $this->tbl_events_model->view($id)[0]['type'];
		
		if(!empty($id)) {
			
			$this->tbl_events_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($type) . ' deleted successfully']));

	}

	/*
	 * secondary
	 *
	**/
	public function secondary_list($type)
	{

		//$this->auth();

		$this->load->model('tbl_secondary_model');
		
		if(isset( datalist('secondary_type_admin')[$type] )) {

			$data['thispage'] = [
				'title' => datalist('secondary_type_admin')[$type]['label'],
				'group' => 'secondary',
				'type' => $type,
			];

			$data['result'] = $this->tbl_secondary_model->list_admin($type);

			$this->load->view('inc/header_admin', $data);
			$this->load->view($this->group . '/secondary_list', $data);
			$this->load->view('inc/footer_admin', $data);

		} else {

			alert_new('warning', 'Data type not found');

			redirect();
			
		}

	}

	public function secondary_add($type = '')
	{

		//$this->auth();

		$this->load->model('tbl_secondary_model');
		
		if (isset($_POST['save'])) {

			$post_data = [];

			foreach (['title', 'country_id', 'currency_id', 'fee', 'remark', 'method_id', 'phone_code'] as $e) {
				$post_data[$e] = empty($this->input->post($e)) ? '' : $this->input->post($e) ;
			}

			$post_data['type'] = $type;
			$post_data['active'] = isset( $_POST['active'] ) ? 1 : 0 ;
			$post_data['branch'] = null;
			
			if(isset($_FILES['image'])) {
				$post_data['image'] = pointoapi_Upload($_FILES['image'], [
					'type' => 'receipt',
					'api_key' => POINTO_API_KEY
				]);
			}

			$this->tbl_secondary_model->add($post_data);

			alert_new('success', ucfirst(datalist('secondary_type_admin')[$type]['single']) . ' created successfully');

			redirect('admin/secondary_list/' . $type);
		}

		$data['thispage'] = [
			'title' => 'Add ' . datalist('secondary_type_admin')[$type]['single'],
			'group' => 'secondary',
			'type' => $type,
		];

		$data['result'] = $this->tbl_secondary_model->all_list();

		$this->load->view('inc/header_admin', $data);
		$this->load->view($this->group . '/secondary_add');
		$this->load->view('inc/footer_admin', $data);

	}

	public function secondary_edit($id = '')
	{
		
		//$this->auth();

		$this->load->model('tbl_secondary_model');
		
		if(!isset($_GET['tab'])) $_GET['tab'] = 1;
		
		$data['result'] = $this->tbl_secondary_model->view($id);
		$data['parent'] = $this->tbl_secondary_model->all_list();
		$data['id'] = $id;
		
		if (count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect($this->group . '/list');

		} else {

			$data['result'] = $data['result'][0];
			$type = $data['result']['type'];

			$data['thispage'] = [
				'title' => 'Edit ' . datalist('secondary_type_admin')[$type]['single'],
				'group' => 'secondary',
				'type' => $type,
				'js' => $this->group . '/secondary_edit'
			]; 

			if (isset($_POST['save'])) {

				$post_data = [];

				foreach (['title', 'active', 'country_id', 'currency_id', 'fee', 'remark', 'method_id', 'phone_code'] as $e) {
					$post_data[$e] = $this->input->post($e);
				}

				$post_data['active'] = isset( $_POST['active'] ) ? 1 : 0 ;
				$post_data['branch'] = null;
				
				if(isset($_FILES['image'])) {
					$post_data['image'] = pointoapi_Upload($_FILES['image'], [
						'default' => $data['result']['image'],
						'type' => 'receipt',
						'api_key' => POINTO_API_KEY
					]);
				}
				
				$this->tbl_secondary_model->edit($id, $post_data);

				alert_new('success', ucfirst(datalist('secondary_type_admin')[$type]['single']) . ' updated successfully');
				
				redirect('admin/secondary_list/' . $type);
			}
			
			if(isset($_POST['save_modules'])) {

				if(!isset($_POST['modules'])) $_POST['modules'] = [];
				
		        $post_data['modules'] = json_encode($_POST['modules']);

				$this->tbl_secondary_model->edit($id, $post_data);
				
				alert_new('success', 'Modules updated successfully');
				
				refresh();

			}

			$this->load->view('inc/header_admin', $data);
			$this->load->view($this->group . '/secondary_edit', $data);
			$this->load->view('inc/footer_admin', $data);

		}

	}

	public function json_secondary_del($id = '')
	{
		
		//$this->auth();

		$this->load->model('tbl_secondary_model');
		
		$type = $this->tbl_secondary_model->view($id)[0]['type'];

		if (!empty($id)) {

			$this->tbl_secondary_model->del($id);
		}

		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => datalist('secondary_type_admin')[$type]['single'].' deleted successfully']));

	}
	
	/*
	 * admins
	 *
	**/
	public function admins_list()
	{

		//$this->auth();

		$this->load->model('tbl_admins_model');
		$this->load->model('tbl_branches_model');
		$this->load->model('log_join_model');
		
		$data['thispage'] = [
			'title' => 'All Admins',
			'group' => 'admins',
		];

		$data['result'] = $this->tbl_admins_model->list();

		$this->load->view('inc/header_admin', $data);
		$this->load->view($this->group.'/admins_list', $data);
		$this->load->view('inc/footer_admin', $data);

	}

	public function admins_edit($id = '')
	{

		//$this->auth();

		$this->load->model('tbl_admins_model');

		if(!isset($_GET['tab'])) $_GET['tab'] = 1;

		$data['result'] = $this->tbl_admins_model->view($id);
		
		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect($this->group.'/admins_list');

		} else {

			$data['thispage'] = [
				'title' => 'Edit Admin',
				'group' => 'admins',
			];
			
			$data['result'] = $data['result'][0];
			
			// save
			if(isset($_POST['save'])) {

				$this->tbl_admins_model->edit($id, [
					'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
				]);
				
				alert_new('success', 'Admin updated successfully');
				
				header('refresh: 0'); exit;

			}
			
			if(isset($_GET['disabled'])) {
				
				$this->tbl_admins_model->edit($id, [
					'password' => ''
				]);
				
				alert_new('success', 'Whitelabel disabled successfully');
				
				redirect('admin/admins_edit/'.$id);
				
			}
			
			$this->load->view('inc/header_admin', $data);
			$this->load->view($this->group.'/admins_edit', $data);
			$this->load->view('inc/footer_admin', $data);

		}

	}
	
	/*
	 * branches
	 *
	**/
	public function branches_list()
	{

		//$this->auth();

		$this->load->model('tbl_branches_model');
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_payment_model');
		$this->load->model('log_join_model');
		
		$data['thispage'] = [
			'title' => 'All Branches',
			'group' => 'branches',
			'type' => 'branches',
		];

		$data['result'] = $this->tbl_branches_model->all_list();

		$this->load->view('inc/header_admin', $data);
		$this->load->view($this->group.'/branches_list', $data);
		$this->load->view('inc/footer_admin', $data);

	}

	public function branches_edit($id = '')
	{

		//$this->auth();

		$this->load->model('tbl_branches_model');
		$this->load->model('tbl_secondary_model');
		$this->load->model('tbl_admins_model');
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_inventory_model');
		$this->load->model('log_join_model');
		$this->load->model('log_attendance_model');

		$data['result'] = $this->tbl_branches_model->view($id);
		$data['country'] = $this->tbl_secondary_model->list_admin('country');
		$data['currency'] = $this->tbl_secondary_model->list_admin('currency');
		$data['plan'] = $this->tbl_secondary_model->list_admin('plan');
		$data['admins'] = $this->tbl_admins_model->list();
		$data['join_admins'] = $this->log_join_model->list_admin([ 'branch' => $id, 'type' => 'join_branch' ]);
		$data['bill'] = $this->log_join_model->list_admin([ 'branch' => $id, 'type' => 'bill' ]);
		$data['result'] = $data['result'][0];
		
		// overview
		$data['students'] = $this->tbl_users_model->setup_list($id, ['type' => 'student']);
		$data['parents'] = $this->tbl_users_model->setup_list($id, ['type' => 'parent']);
		$data['teachers'] = $this->tbl_users_model->setup_list($id, ['type' => 'teacher']);
		$data['classes'] = $this->tbl_classes_model->setup_list($id);
		$data['items'] = $this->tbl_inventory_model->setup_list($id, ['type' => 'item']);
		$data['movement'] = $this->tbl_inventory_model->setup_list($id, ['type' => 'movement']);
		$data['attendance'] = $this->log_attendance_model->setup_list($id);
 		
		if(!isset($_GET['tab'])) $_GET['tab'] = 1;

		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect($this->group.'/list');

		} else {

			$data['thispage'] = [
				'title' => 'Edit Branch',
				'group' => 'branches',
				'js' => $this->group.'/branches_edit'
			];
			
			// save
			if(isset($_POST['save'])) {

				$post_data = [];
				
				foreach([ 'owner', 'title', 'amount', 'amount_unit', 'ssm_no', 'country', 'currency', 'plan', 'expired_date', 'phone', 'phone2', 'phone3', 'email', 'email2', 'email3', 'address', 'address2', 'address3', 'remark', 'version' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}

				if( empty($post_data['country']) ) $post_data['country'] = null;
				$post_data['active'] = isset( $_POST['active'] ) ? 1 : 0 ;
				$post_data['image'] = pointoapi_Upload($_FILES['image'], [
					'default' => $data['result']['image'],
					'type' => 'branch_logo',
					'api_key' => POINTO_API_KEY
				]);
				
				if(empty($post_data['owner'])) $post_data['owner'] = null;
				if(empty($post_data['currency'])) $post_data['currency'] = null;
				if(empty($post_data['plan'])) $post_data['plan'] = null;
				if(empty($post_data['timezone'])) $post_data['timezone'] = null;
				if(empty($post_data['expired_date'])) $post_data['expired_date'] = null;

				$this->tbl_branches_model->edit($id, $post_data);
				
				alert_new('success', 'Branch updated successfully');
				
				header('refresh: 0'); exit;

			}
			
			// add_user
			if(isset($_POST['add_user'])) {
				
				if( count( $this->log_join_model->list_admin([ 'branch' => $id, 'type' => 'join_branch', 'admin' => $this->input->post('admin') ]) ) == 0 ) {

					$post_data = [];
					
					foreach([ 'admin' ] as $e) {
						
						$post_data[$e] = $this->input->post($e);
						
					}

					$post_data['branch'] = $id;
					$post_data['type'] = 'join_branch';
					
					$this->log_join_model->add($post_data);
					
					alert_new('success', 'Admin added successfully');
					
				} else {
					
					alert_new('warning', 'Admin has been added');
					
				}
				
				redirect($this->group.'/branches_edit/'.$id.'?tab=1');

			}
			
			// add_bill
			if(isset($_POST['add_bill'])) {
				
				$post_data = [];
				
				foreach([ 'plan', 'date_start', 'date_end', 'amount', 'amount_unit', 'remark' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}

				$post_data['branch'] = $id;
				$post_data['type'] = 'bill';
				
				$this->log_join_model->add($post_data);
				
				alert_new('success', 'Bill added successfully');
				
				redirect($this->group.'/branches_edit/'.$id.'?tab=2');

			}
			
			// save_bill
			if(isset($_POST['save_bill'])) {
				
				$post_data = [];
				
				foreach([ 'plan', 'date_start', 'date_end', 'amount', 'amount_unit', 'remark' ] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}

				$post_data['branch'] = $id;
				$bill_id = $this->input->post('id');
				
				$this->log_join_model->edit($bill_id, $post_data);
				
				alert_new('success', 'Bill updated successfully');
				
				redirect($this->group.'/branches_edit/'.$id.'?tab=2');

			}

			$this->load->view('inc/header_admin', $data);
			$this->load->view($this->group.'/branches_edit', $data);
			$this->load->view('inc/footer_admin', $data);

		}

	}
	
	public function json_branches_view($id = '')
	{
	
		//$this->auth();

		$this->load->model('log_join_model');

		$result = $this->log_join_model->view($id);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}

	public function json_branches_del($id = '')
	{

		//$this->auth();

		$this->load->model('tbl_branches_model');

		if(!empty($id)) {
	
			$this->tbl_branches_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Branch deleted successfully']));

	}
	
	public function json_branches_restore($id = '')
	{

		//$this->auth();

		$this->load->model('tbl_branches_model');
		
		if(!empty($id)) {
	
			$this->tbl_branches_model->restore($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Branch restored successfully']));

	}

	public function json_branches_remove($id = '')
	{

		//$this->auth();

		$this->load->model('log_join_model');

		$this->log_join_model->del($id);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Admin removed successfully']));

	}
	
	public function json_branches_overview_del($id, $type)
	{

		//$this->auth();

		$this->load->model('tbl_users_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_inventory_model');
		$this->load->model('log_attendance_model');

		switch($type) {
			
			case 'students':
				foreach($this->tbl_users_model->setup_list($id, ['type' => 'student']) as $e) {
					$this->tbl_users_model->del($e['pid']);
				}
				break;
				
			case 'parents':
				foreach($this->tbl_users_model->setup_list($id, ['type' => 'parent']) as $e) {
					$this->tbl_users_model->del($e['pid']);
				}
				break;
				
			case 'teachers':
				foreach($this->tbl_users_model->setup_list($id, ['type' => 'teacher']) as $e) {
					$this->tbl_users_model->del($e['pid']);
				}
				break;
				
			case 'classes':
				foreach($this->tbl_classes_model->setup_list($id) as $e) {
					$this->tbl_classes_model->del($e['pid']);
				}
				break;
				
			case 'items':
				foreach($this->tbl_inventory_model->setup_list($id, ['type' => 'item']) as $e) {
					$this->tbl_inventory_model->del($e['pid']);
				}
				break;
				
			case 'movement':
				foreach($this->tbl_inventory_model->setup_list($id, ['type' => 'movement']) as $e) {
					$this->tbl_inventory_model->del($e['pid']);
				}
				break;
				
			case 'attendance':
				foreach($this->log_attendance_model->setup_list($id) as $e) {
					$this->log_attendance_model->del($e['id']);
				}
				break;
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($type) . ' deleted successfully']));

	}
	
	public function json_branches_overview_active_del($id, $type)
	{

		//$this->auth();

		$this->load->model('tbl_users_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_inventory_model');
		$this->load->model('log_attendance_model');

		switch($type) {
			
			case 'students':
				foreach($this->tbl_users_model->setup_list($id, ['type' => 'student', 'active' => 1]) as $e) {
					$this->tbl_users_model->del($e['pid']);
				}
				break;
				
			case 'parents':
				foreach($this->tbl_users_model->setup_list($id, ['type' => 'parent', 'active' => 1]) as $e) {
					$this->tbl_users_model->del($e['pid']);
				}
				break;
				
			case 'teachers':
				foreach($this->tbl_users_model->setup_list($id, ['type' => 'teacher', 'active' => 1]) as $e) {
					$this->tbl_users_model->del($e['pid']);
				}
				break;
				
			case 'classes':
				foreach($this->tbl_classes_model->setup_list($id, ['active' => 1]) as $e) {
					$this->tbl_classes_model->del($e['pid']);
				}
				break;
				
			case 'items':
				foreach($this->tbl_inventory_model->setup_list($id, ['type' => 'item', 'active' => 1]) as $e) {
					$this->tbl_inventory_model->del($e['pid']);
				}
				break;
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($type) . ' deleted successfully']));

	}
	
	public function json_branches_overview_inactive_del($id, $type)
	{

		//$this->auth();

		$this->load->model('tbl_users_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_inventory_model');
		$this->load->model('log_attendance_model');

		switch($type) {
			
			case 'students':
				foreach($this->tbl_users_model->setup_list($id, ['type' => 'student', 'active' => 0]) as $e) {
					$this->tbl_users_model->del($e['pid']);
				}
				break;
				
			case 'parents':
				foreach($this->tbl_users_model->setup_list($id, ['type' => 'parent', 'active' => 0]) as $e) {
					$this->tbl_users_model->del($e['pid']);
				}
				break;
				
			case 'teachers':
				foreach($this->tbl_users_model->setup_list($id, ['type' => 'teacher', 'active' => 0]) as $e) {
					$this->tbl_users_model->del($e['pid']);
				}
				break;
				
			case 'classes':
				foreach($this->tbl_classes_model->setup_list($id, ['active' => 0]) as $e) {
					$this->tbl_classes_model->del($e['pid']);
				}
				break;
				
			case 'items':
				foreach($this->tbl_inventory_model->setup_list($id, ['type' => 'item', 'active' => 0]) as $e) {
					$this->tbl_inventory_model->del($e['pid']);
				}
				break;
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($type) . ' deleted successfully']));

	}
	
	/*
	 * settings_advanced
	 *
	**/
	public function settings_advanced() 
	{
		
		//$this->auth();

		$this->load->model('sys_app_model');
		
		$data['thispage'] = [
			'title' => 'Advanced',
			'group' => 'settings',
		];

		$data['result'] = $this->sys_app_model->view('marquee');
		
		if(isset($_POST['save'])) {
				
			$post_data['k'] = 'marquee';
			$post_data['v'] = $this->input->post('marquee');

			$this->sys_app_model->edit('marquee', $post_data);
			
			alert_new('success', 'Saved');
			
			header('refresh: 0'); exit;

		}

		$this->load->view('inc/header_admin', $data);
		$this->load->view($this->group.'/settings_advanced', $data);
		$this->load->view('inc/footer_admin', $data);

	}
	
	/*
	 * sso
	 *
	**/
	public function sso($token = '') 
	{
		
		$token = isset($_GET['token']) ? $_GET['token'] : $token ;
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://one.synorexcloud.com/api/sso/login?token='.$token.'&service='.ONE_SERVICE_ID,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Cookie: PHPSESSID=8e0abed5ff3918bf3be509c095302116'
			),
		));

		$response = json_decode(curl_exec($curl), true);
		
		curl_close($curl);
		
		// echo '<pre>'; print_r($response); exit;
		
		if($response['status'] == 'ok') {
			
			$this->session->set_userdata( md5('@robocube-tuition-sso-admin'), $token);
			
			redirect('admin');
			
		} else {
			
			die(app('title').': Token expired');
			
		}
			
	}
	
	private function auth() {
		
		if( empty($this->session->userdata( md5('@robocube-tuition-sso-admin') )) ) die(app('title').': Access denied');

	}
	
}
