<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'reports';

		// auth_must('login');

		$this->load->model('tbl_branches_model');
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_payment_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_inventory_model');
		$this->load->model('tbl_secondary_model');
		$this->load->model('log_inventory_model');
		$this->load->model('log_attendance_model');
		$this->load->model('log_join_model');
		$this->load->model('log_payment_model');
		$this->load->model('log_point_model');
		
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		
	}

	public function index()
	{

		auth_must('login');
		check_module_page('Reports/Read');

		$data['thispage'] = [
			'title' => 'Reports',
			'group' => $this->group,
		];

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/index');
		$this->load->view('inc/footer', $data);

	}

	public function received()
	{

		auth_must('login');
		check_module_page('Reports/Read');

		$data['thispage'] = [
			'title' => 'Received',
			'group' => $this->group,
		];
		
		if(!isset($_GET['start'])) $_GET['start'] = date('Y-m-d', strtotime('-7 days'));
		if(!isset($_GET['end'])) $_GET['end'] = date('Y-m-d');

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/received');
		$this->load->view('inc/footer', $data);

	}

	public function unpaid_items()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/UnpaidItems');

		$data['thispage'] = [
			'title' => 'Unpaid Items',
			'group' => $this->group,
			'css' => $this->group.'/report',
			'js' => $this->group.'/unpaid_items',
		];
		
		// $data['result'] = $this->log_join_model->report_list(branch_now('pid'));
		
		$data['result_std'] = $this->tbl_users_model->list('student', branch_now('pid'), [ 'active' => 1 ]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/unpaid_items');
		$this->load->view('inc/footer', $data);

	}

	public function daily_collection()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/DailyCollection');

		$data['thispage'] = [
			'title' => 'Daily Collection',
			'group' => $this->group,
			'js' => $this->group."/daily_collection",
		];
		
		$sql = '
		
			SELECT tbl_payment.*, forms.title AS form_title, tbl_admins.nickname AS admin_nickname,
			COALESCE(payment_methods.title, "-") AS payment_method_title,
			students.code AS student_code, students.fullname_en AS student_fullname_en, students.fullname_cn AS student_fullname_cn,
			DATE_FORMAT(tbl_payment.date, "%Y-%m") AS payment_year_month, tbl_branches.title AS branch_name 
			FROM tbl_payment
			JOIN tbl_users students ON students.pid = tbl_payment.student
			LEFT JOIN tbl_secondary forms ON forms.pid = students.form
			LEFT JOIN tbl_admins ON tbl_admins.pid = tbl_payment.create_by
			LEFT JOIN tbl_branches ON tbl_branches.pid = tbl_payment.branch
			LEFT JOIN tbl_secondary payment_methods ON payment_methods.pid = tbl_payment.payment_method
			WHERE tbl_payment.is_delete = 0
		';
		// Tan Jing Suan
		$sql .= ' AND students.branch = "'.branch_now('pid').'" ';
		
		$_GET['start_date'] = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
		$_GET['end_date'] = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
		
		$sql .= ' AND tbl_payment.date BETWEEN "' . $_GET['start_date'] . '" AND "'. $_GET['end_date'] .'"';
		
		if(isset($_GET['remark'])) {
			$sql .= ' AND tbl_payment.remark LIKE "%' . $_GET['remark'] . '%"';
		}
		
		if(isset($_GET['form'])) {
			$sql .= ' AND students.form IN ("' . join('","',$_GET['form']) . '")';
		}
		
		$data['result'] = $this->db->query($sql)->result_array();
		
		// payment method
		$data['result_payment_method'] = $this->list_payment_method();
		
		$sqlForm = '
			SELECT * FROM tbl_secondary
			WHERE is_delete = 0
			AND active = 1
			AND type = "form"
			AND branch = "'.branch_now('pid').'"
			ORDER BY FIELD(title, "K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5", "G2023")
		';

		$data['form'] = $this->db->query($sqlForm)->result_array();
		

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/daily_collection');
		$this->load->view('inc/footer', $data);

	}
	
	public function monthly_collection()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/MonthlyCollection');

		$data['thispage'] = [
			'title' => 'Monthly Collection',
			'group' => $this->group,
			'js' => $this->group.'/monthly_collection',
		];
		
		$is_draft = isset($_GET['is_draft']) ? '1' : '0';

		if(isset($_GET['month'])) {
			$data['result'] = $this->tbl_payment_model->monthly_list(date("m", strtotime($_GET['month'])), date("Y", strtotime($_GET['month'])), branch_now('pid'), $is_draft);
		} else {
			$data['result'] = $this->tbl_payment_model->monthly_list(date('m'), date('Y'), branch_now('pid'), $is_draft);
		}
		
		if( isset($_POST['pdf']) ) {
			
			$pdfs = '';
			
			foreach( $data['result'] as $e ) {
				
				if($pdfs != '') $pdfs = $pdfs .'|';
				$pdfs = $pdfs. $e['pid'];
				
			}

			redirect('https://system.synorex.space/highpeakedu/export/zip/'.$pdfs);
			
		}

		// payment method
		$data['result_payment_method'] = $this->list_payment_method();

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/monthly_collection');
		$this->load->view('inc/footer', $data);

	}
	
	public function json_monthly_collection()
	{
		
		if( $_POST['month'] ) {
			
			$data['result'] = $this->tbl_payment_model->monthly_list(date("m", strtotime($_POST['month'])), date("Y", strtotime($_POST['month'])), branch_now('pid'), $is_draft);
			
		}

	}

	public function outstanding_payment()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/OutstandingPayment');
		
		$this->load->library('pagination');

		$data['thispage'] = [
			'title' => 'Outstanding Payment',
			'group' => $this->group,
			'css' => $this->group.'/report',
			'js' => $this->group.'/outstanding_payment',
		];
		
		$sql = '
			SELECT * FROM tbl_secondary
			WHERE is_delete = 0
			AND active = 1
			AND type = "form"
			AND branch = "'.branch_now('pid').'"
			ORDER BY FIELD(title, "K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5", "G2023")
		';

		$data['form'] = $this->db->query($sql)->result_array();
				
		$data['min'] = $this->tbl_users_model->student_list('student', branch_now('pid'), ['birthday !=' => null], '', ['birthday' => 'asc']);
		if(count($data['min']) > 0)
		{
			$data['min'] = $data['min'][0];
			$data['min'] = date("Y") - date('Y',strtotime($data['min']['birthday']));
		} else{
			$data['min'] = 0;
		}
		
		$data['max'] = $this->tbl_users_model->student_list('student', branch_now('pid'), ['birthday !=' => null], '', ['birthday' => 'desc']);
		if(count($data['max']) > 0)
		{
			$data['max'] = $data['max'][0];
			$data['max'] = date("Y") - date('Y',strtotime($data['max']['birthday']));
		} else{
			$data['max'] = 0;
		}
		
		if(isset($_POST['send_email'])) {
			
			// die('1');
			
			header('Content-type: application/json');
			
			$result = $this->tbl_users_model->view($_POST['send_email']);
		
			if(count($result) == 1) {
				
				$result = $result[0];
				
				$email = $result['email'];
								
				if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
					
					$response = pointoapi_Request('SynorexAPI/Email/Send', [
						'to' => $email,
						// 'api_key' => POINTO_API_KEY,
						'subject' => '['.app('title').'] '.$result['fullname_en'].'\'s Oustanding Payment Notification',
						'body' => '
						<p>Hi <b>'.$result['fullname_cn'].' '.$result['fullname_en'].'</b>,</p>
						<p>Here\'s the notification for your outstanding payment.</p>
						<br>
						<p>This message send via '.app('title').'. Don\'t reply to this message.</p>
						<br>
						<img src="https://cdn.synorex.link/assets/images/site/logo-x600.png" style="height: 50px;">
						',
					]);
					
					// print_r($response); exit;
					
					if( !isset($response['status']) ) $response['status'] = 'error';
					if( !isset($response['message']) ) $response['message'] = 'Unknow error';
					
					if($response['status'] == 'ok') {
						
						die(json_encode([ 'status' => 'ok', 'message' => 'Email sent successfully']));
						
					} else {
						
						die(json_encode([ 'status' => 'error', 'message' => 'PointoAPI Error: '.$response['message'] ]));
						
					}
					
				} else {
					
					die(json_encode([ 'status' => 'not_found', 'message' => 'Please use a valid email address']));
					
				}
				
			} else {
				
				die(json_encode([ 'status' => 'failed', 'message' => 'Payment not found']));
				
			}
			
		}
		
		if(isset($_POST['send_sms'])) {
			
			header('Content-type: application/json');
			
			$result = $this->tbl_users_model->view($_POST['send_sms']);
		
			if(count($result) == 1) {
				
				$result = $result[0];
				
				$phone = $result['phone'];
				
				if(!empty($phone)) {
					
					if(substr($phone, 0, 1) != '+') $phone = '6'.$phone;

					$msg = branch_now('send_msg_sms_outstanding');
					
					if(empty($msg)) {
						
						die(json_encode([ 'status' => 'required', 'message' => 'SMS content haven\'t been set']));
						
					}
					
					$msg = str_replace('%NAME%', $result['fullname_en'], $msg);
					// $msg = str_replace('%RECEIPT_NO%', $result['payment_no'], $msg);
					$msg = str_replace('%SUBJECT%', $result['fullname_en'], $msg);
					
					$item = '';
					
					$std_unpaid_result = std_unpaid_result($result['pid'], $result['branch']);
					
					if($std_unpaid_result['count'] > 0) {
						$j = 0;
						$i = $std_unpaid_result['count'];
						if(isset($std_unpaid_result['result']['class'])) {
							foreach($std_unpaid_result['result']['class'] as $e) {
								$j++;
								if($j < $i) {
									$item .= $e['title'] . ' x ' . '1, ';
								} else {
									$item .= $e['title'] . ' x ' . '1';
								}
							}
						}
						if(isset($std_unpaid_result['result']['item'])) {
							foreach($std_unpaid_result['result']['item'] as $e) {
								$j++;
								if($j < $i) {
									$item .= $e['title'] . ' x ' . $e['qty'] . ', ';
								} else {
									$item .= $e['title'] . ' x ' . $e['qty'];
								}
							}
						}
					}
					
					$msg = str_replace('%ITEM%', $item, $msg);
					$msg = str_replace('%TOTALOUTSTANDINGAMOUNT%', number_format($std_unpaid_result['total'], 2, '.', ','), $msg);
					
					$response = pointoapi_Request('SynorexAPI/SMS/Send', [
						'to' => $phone,
						'message' => $msg,
					]);
					
					if( !isset($response['status']) ) $response['status'] = 'error';
					if( !isset($response['message']) ) $response['message'] = 'Unknow error';
					
					if($response['status'] == 'ok') {
						
						die(json_encode([ 'status' => 'ok', 'message' => 'SMS sent successfully']));
						
					} else {
						
						die(json_encode([ 'status' => $response['status'], 'message' => $response['message']]));
						
					}
					
				} else {
					
					die(json_encode([ 'status' => 'not_found', 'message' => 'Please use a valid phone number']));
					
				}
				
			} else {
				
				die(json_encode([ 'status' => 'not_found', 'message' => 'Payment not found']));
				
			}
			
		}
		
		// del by steve
		/*$this_ci =& get_instance();
		$data['result'] = $this_ci->db->query('SELECT * FROM log_join WHERE type="unpaid_item" OR type="unpaid_class" AND is_delete=0 GROUP BY user;')->result_array();

		$unpaid_items = $this->log_join_model->list('unpaid_item', branch_now('pid'));
		$join_class = $this->log_join_model->list('join_class', branch_now('pid'));
		$unpaid_class = [];
		foreach($join_class as $e) {
			if(empty($this->log_payment_model->list2([
				'user' => $e['user'],
				'class' => $e['class'],
				'period' => date('Y-m', strtotime(date('Y-m', strtotime($e['date']))))
			]))) {
			$unpaid_class[] = $this->log_join_model->list('join_class', branch_now('pid'), ['class' => $e['class'], 'user' => $e['user'], 'date' => $e['date']]);
			}
		}
		
		$data['unpaid_class'] = $unpaid_class;
		$data['unpaid_items'] = $unpaid_items;*/
		
		// by steve
		// $data['result_std'] = $this->tbl_users_model->list('student', branch_now('pid'), [ 'active' => 1 ]);
		
		if(!isset($_GET['sort'])) $_GET['sort'] = 'asc';
		
		/*$sql = '
		
			SELECT * FROM tbl_users
			WHERE is_delete = 0
			AND type = "student"
			AND active = 1
			AND branch = "'.branch_now('pid').'"
		
		';
		
		if(isset($_GET['q']) && !empty($_GET['q'])) {
			$sql .= ' AND fullname_en LIKE "%'.$_GET['q'].'%"';
		}	
		
		if(isset($_GET['age']) && !empty($_GET['age'])) {
			$sql .= ' AND year(birthday) = (year(CURDATE()) - '.$_GET['age'].')';
		}	
		
		if($_GET['sort'] == 'asc') {
			$sql .= ' ORDER BY fullname_en ASC';
		} else {
			$sql .= ' ORDER BY fullname_en DESC';
		}
	
		$result_std = $this->db->query($sql)->result_array();
				
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		
		$config['base_url'] = base_url('reports/outstanding_payment/');
		$config['total_rows'] = count($result_std);
		$config['uri_segment'] = 3;
		$config['per_page'] = 5;
		$config['first_link'] = 'First Page';
		$config['last_link'] = 'Last Page';

		$this->pagination->initialize($config);
		
		$data['links'] = $this->pagination->create_links();
		
		if(!empty($start)) {
			$page = $start;
		}
		
		$sql .= ' LIMIT ' . $page . ', ' . $config['per_page'];

		$data['result_std'] = $this->db->query($sql)->result_array(); */
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/outstanding_payment');
		$this->load->view('inc/footer', $data);

	}
	
	public function outstanding_payment_class()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/OutstandingPaymentClass');

		if(!isset($_GET['class'])) $_GET['class'] = '';

		$data['thispage'] = [
			'title' => 'Outstanding Payment (Class)',
			'group' => $this->group,
			'css' => $this->group.'/report',
			'js' => $this->group.'/outstanding_payment_class',
		];
		
		if(isset($_POST['send_email'])) {
			
			// die('1');
			
			header('Content-type: application/json');
			
			$result = $this->tbl_users_model->view($_POST['send_email']);
		
			if(count($result) == 1) {
				
				$result = $result[0];
				
				$email = $result['email'];
								
				if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
					
					$response = pointoapi_Request('SynorexAPI/Email/Send', [
						'to' => $email,
						// 'api_key' => POINTO_API_KEY,
						'subject' => '['.app('title').'] '.$result['fullname_en'].'\'s Oustanding Payment Notification',
						'body' => '
						<p>Hi <b>'.$result['fullname_cn'].' '.$result['fullname_en'].'</b>,</p>
						<p>Here\'s the notification for your outstanding payment.</p>
						<br>
						<p>This message send via '.app('title').'. Don\'t reply to this message.</p>
						<br>
						<img src="https://cdn.synorex.link/assets/images/site/logo-x600.png" style="height: 50px;">
						',
					]);
					
					// print_r($response); exit;
					
					if( !isset($response['status']) ) $response['status'] = 'error';
					if( !isset($response['message']) ) $response['message'] = 'Unknow error';
					
					if($response['status'] == 'ok') {
						
						die(json_encode([ 'status' => 'ok', 'message' => 'Email sent successfully']));
						
					} else {
						
						die(json_encode([ 'status' => 'error', 'message' => 'PointoAPI Error: '.$response['message'] ]));
						
					}
					
				} else {
					
					die(json_encode([ 'status' => 'not_found', 'message' => 'Please use a valid email address']));
					
				}
				
			} else {
				
				die(json_encode([ 'status' => 'failed', 'message' => 'Payment not found']));
				
			}
			
		}
		
		if(isset($_POST['send_sms'])) {
			
			header('Content-type: application/json');
			
			$result = $this->tbl_users_model->view($_POST['send_sms']);
		
			if(count($result) == 1) {
				
				$result = $result[0];
				
				$phone = $result['phone'];
				
				if(!empty($phone)) {
					
					if(substr($phone, 0, 1) != '+') $phone = '6'.$phone;

					$msg = branch_now('send_msg_sms_outstanding');
					
					if(empty($msg)) {
						
						die(json_encode([ 'status' => 'required', 'message' => 'SMS content haven\'t been set']));
						
					}
					
					$msg = str_replace('%NAME%', $result['fullname_en'], $msg);
					// $msg = str_replace('%RECEIPT_NO%', $result['payment_no'], $msg);
					
					$response = pointoapi_Request('SynorexAPI/SMS/Send', [
						'to' => $phone,
						'message' => $msg,
					]);
					
					if( !isset($response['status']) ) $response['status'] = 'error';
					if( !isset($response['message']) ) $response['message'] = 'Unknow error';
					
					if($response['status'] == 'ok') {
						
						die(json_encode([ 'status' => 'ok', 'message' => 'SMS sent successfully']));
						
					} else {
						
						die(json_encode([ 'status' => $response['status'], 'message' => $response['message']]));
						
					}
					
				} else {
					
					die(json_encode([ 'status' => 'not_found', 'message' => 'Please use a valid phone number']));
					
				}
				
			} else {
				
				die(json_encode([ 'status' => 'not_found', 'message' => 'Payment not found']));
				
			}
			
		}
		
		// by steve
		// $student = $this->tbl_users_model->list('student', branch_now('pid'), [ 'active' => 1 ]);
		$branch_id = branch_now('pid');
		
		if(isset($_GET['class'])) 
		{
			$sql = '
			
				SELECT tbl_users.* 
				FROM tbl_users
				JOIN (
					SELECT user
					FROM log_join
					WHERE type = "join_class" 
					AND branch = "'. $branch_id .'"
					AND active = 1
					AND class = "'. $_GET['class'] .'"
					GROUP BY user
				) classes ON classes.user = tbl_users.pid
				WHERE tbl_users.is_delete = 0
				AND tbl_users.type = "student"
				AND tbl_users.active = 1
				AND tbl_users.branch = "'. $branch_id .'"
			';
		}
		else
		{
			$sql = '
			
				SELECT tbl_users.* 
				FROM tbl_users
				JOIN (
					SELECT user
					FROM log_join
					WHERE type = "join_class" 
					AND branch = "'. $branch_id .'"
					AND active = 1
					GROUP BY user
				) classes ON classes.user = tbl_users.pid
				WHERE tbl_users.is_delete = 0
				AND tbl_users.type = "student"
				AND tbl_users.active = 1
				AND tbl_users.branch = "'. $branch_id .'"
			';
		}
		/* 
		if(isset($_GET['sort'])) {
			
			if($_GET['sort'] == 'asc') {
				$sql .= ' ORDER BY tbl_users.fullname_en ASC';
			} else {
				$sql .= ' ORDER BY tbl_users.fullname_en DESC';
			}
			
		} else {
			
			$sql .= ' ORDER BY tbl_users.fullname_en ASC';
			
		}
		 */
		if(isset($_GET['sort'])) {
			$sql .= ' ORDER BY (CASE WHEN COALESCE(fullname_en, "") = "" THEN 1 ELSE 0 END) '. strtoupper($_GET['sort']) .', fullname_en '. strtoupper($_GET['sort']) .', fullname_cn '. strtoupper($_GET['sort']);
		}
		else {
			$sql .= ' ORDER BY (CASE WHEN COALESCE(fullname_en, "") = "" THEN 1 ELSE 0 END) ASC, fullname_en ASC, fullname_cn ASC';
		}
		
		$student = $this->db->query($sql)->result_array();
		
		$data['result_std'] = $student;
		/* 
		if(isset($_GET['class'])) {
			
			foreach($student as $e) {
				
				$query = $this->log_join_model->list('join_class', branch_now('pid'), [ 'user' => $e['pid'], 'class' => $_GET['class'], 'active' => 1 ]);
				
				if(count($query) > 0) $data['result_std'][] = $e;
				
			}
		} */
		
		$data['teachers'] = $this->tbl_users_model->list('teacher', branch_now('pid'));
		
		$class_search = '';
		
		if (isset($_GET['teacher']))
		{
			$class_search = [ 'teacher' => $_GET['teacher'], 'active' => 1, 'is_hidden' => 0 ];
		}
		else
		{
			$class_search = [ 'active' => 1, 'is_hidden' => 0 ];
		}
		$data['classes'] = $this->tbl_classes_model->list(branch_now('pid'), $class_search);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/outstanding_payment_class');
		$this->load->view('inc/footer', $data);

	}
	
	
	public function outstanding_payment_parent()
	{
		
		auth_must('login');
		//check_module_page('Reports/Read');
		//check_module_page('Reports/Modules/Sales/OutstandingPaymentParent');
		$this->load->library('pagination');

		if(!isset($_GET['parent'])) $_GET['parent'] = '';

		$data['thispage'] = [
			'title' => 'Outstanding Payment (Parent)',
			'group' => $this->group,
			'css' => $this->group.'/report',
			//'js' => $this->group.'/outstanding_payment_parent',
		];
		
		$branch_id = branch_now('pid');
		$data['branch_pointoapi_key'] = branch_now('pointoapi_key');
		$data['branch_send_msg_whatsapp_outstanding'] = branch_now('send_msg_whatsapp_outstanding');
		$data['branch_phone'] = branch_now('phone');
		
		$data['parents'] = $this->tbl_users_model->list_parent($branch_id);
		
		if (!empty($_GET['parent']))
		{
			$sql = '
			
				SELECT students.*, join_parents.parent AS parent_pid, parents.fullname_en AS parent_fullname_en
				FROM tbl_users students
				INNER JOIN (
					SELECT log_join.user, log_join.parent
					FROM log_join WHERE log_join.active = 1 AND type = "join_parent"
				) join_parents ON join_parents.user = students.pid
				INNER JOIN tbl_users parents ON parents.pid = join_parents.parent
				WHERE students.is_delete = 0
				AND students.type = "student"
				AND students.active = 1
				AND students.branch = "'. $branch_id .'"
			';
		
			$sql .= ' AND join_parents.parent = "'. $_GET['parent'].'"';
		
			$sql .= ' ORDER BY parents.fullname_en ASC';

			$result_std = $this->db->query($sql)->result_array();
		}
		else
		{
			$result_std = [];
		}
		
		$group_by_parent = group_by('parent_pid', $result_std);
		
		$per_page = 50;
		$_GET['per_page'] = isset($_GET['per_page']) ? $_GET['per_page'] : 0;
		$_GET['sort'] = isset($_GET['sort']) ? $_GET['sort'] : 'u.code';
		$_GET['order'] = isset($_GET['order']) ? $_GET['order'] : 'ASC';
		$data['row'] = (($_GET['per_page'] > 0 ? ($_GET['per_page'] - 1) : $_GET['per_page']) * $per_page);

		if ($_GET['sort'] != '' AND $_GET['sort'] != '')
		{
			$sql .= ' ORDER BY '. $_GET['sort']. ' '. $_GET['order'];
		}
		$sql .= ' LIMIT '. (($_GET['per_page'] > 0 ? ($_GET['per_page'] - 1) : $_GET['per_page']) * $per_page). ', '. $per_page;
		 
		/*
        $paginated_results = [];
		
		
        if (count($group_by_parent)) {
            $paginated_results = array_slice($group_by_parent, 0, count($group_by_parent), false);
        }
		
		 foreach($paginated_results as $e) {
			
			$parent_result = [];
			foreach($e as $k => $ee)
			{
				$student_result = [];
				foreach($ee as $eee) {
					
					$std_unpaid_result = std_unpaid_result($eee['pid'], branch_now('pid'));
					$eee['std_unpaid_result'] = $std_unpaid_result;
						
					if($std_unpaid_result['count'] > 0) {
						$eee['total'] = $std_unpaid_result['total'];
						$eee['count'] = $std_unpaid_result['count'];
						$student_result[] = $eee;
					}
					
				}
				if (count($student_result) > 0)
				{
					$student_result['total'] = array_sum(array_column($student_result, 'total'));
					$student_result['count'] = array_sum(array_column($student_result, 'count'));
					$parent_result[$k] = $student_result;
				}
			}
			if (count($parent_result) > 0)
			{
				$parent_result['total'] = array_sum(array_column($student_result, 'total'));
				$parent_result['count'] = count($student_result);
				$result[] = $parent_result;
			}
		} */
		
        $paginated_results = [];
		
        if (count($group_by_parent)) {
            $paginated_results = array_slice($group_by_parent, $data['row'], $per_page, true);
            //$paginated_keys = array_slice($group_by_parent, $data['row'], $per_page, true);
        }
		
		$filtered_students = array_filter($group_by_parent,
								fn ($key) => in_array($key, array_keys($paginated_results)),
								ARRAY_FILTER_USE_KEY
							);
		
		$filtered_students = array_single($filtered_students);
		
		$student_result = [];
		foreach($filtered_students as $e)
		{
			$std_unpaid_result = std_unpaid_result2($e['pid'], $branch_id);
			$new_unpaid_class = [];
			$material_fee = [];
			$subsidy_fee = [];
			$with_class_bundle = [];
			$without_class_bundle = [];
			$total_material_fee = 0;
			$total_subsidy_fee = 0;
			
			if($std_unpaid_result['count'] > 0) {
				
				if(isset($std_unpaid_result['result']['class'])) {
					
					foreach($std_unpaid_result['result']['class'] as $e2) {
						$check_class_bunlde = $this->log_join_model->list('class_bundle_course', $branch_id, [
							'course' => datalist_Table('tbl_classes', 'course', $e2['class'])
						]);
						if(count($check_class_bunlde) > 0) {
							$check_class_bunlde = $check_class_bunlde[0];
							$with_class_bundle[$check_class_bunlde['parent']]['data'][] = $e2;
						} else {
							$without_class_bundle[]['data'] = $e2;
						}
					}
					
					foreach($with_class_bundle as $k2 => $v2) {
						$check_bundle_price = $this->log_join_model->list('class_bundle_price', $branch_id, [
							'parent' => $k2,
							'qty' => count($v2['data'])
						]);
						if(count($check_bundle_price) > 0) {
							$check_bundle_price = $check_bundle_price[0];
							$each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
							$material_fee[] = [
								'title'	=> 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k2) . ']',
								'fee' => $check_bundle_price['material']
							];
							foreach($v2['data'] as $k3 => $v3) {
								$class_price = datalist_Table('tbl_classes', 'fee', $v3['class']);
								$with_class_bundle[$k2]['data'][$k3]['discount'] = $class_price - $each_price;
								$with_class_bundle[$k2]['data'][$k3]['amount'] = $class_price;
								$with_class_bundle[$k2]['data'][$k3]['title'] = $v3['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k2) . ']';
							}
						} else {
							$check_bundle_price = $this->db->query('
								SELECT * FROM log_join
								WHERE is_delete = 0
								AND type = "class_bundle_price"
								AND branch = "' . $branch_id . '"
								AND parent = "' . $k2 . '"
								AND qty < '.count($v2['data']).'
								ORDER BY qty DESC
							')->result_array();
							if(count($check_bundle_price) > 0) {
								$check_bundle_price = $check_bundle_price[0];
								$each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
								for($i=0; $i<floor(count($v2['data']) / $check_bundle_price['qty']); $i++) {
									$material_fee[] = [
										'title'	=> 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k2) . ']',
										'fee' => $check_bundle_price['material']
									];
								}
								foreach($v2['data'] as $k3 => $v3) {
									if($k3 >= $check_bundle_price['qty'] * floor(count($v2['data']) / $check_bundle_price['qty'])) {
										$without_class_bundle[0]['data'][] = $v3;
										unset($with_class_bundle[$k2]['data'][$k3]);
									} else {
										$class_price = datalist_Table('tbl_classes', 'fee', $v3['class']);
										$with_class_bundle[$k2]['data'][$k3]['discount'] = $class_price - $each_price;
										$with_class_bundle[$k2]['data'][$k3]['amount'] = $class_price;
										$with_class_bundle[$k2]['data'][$k3]['title'] = $v3['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k2) . ']';
									}
								}
							}
						} 
					}
					
					foreach($with_class_bundle as $k2 => $v2) {
						$bundle_subsidy = $this->db->query('
							SELECT * FROM log_join
							WHERE is_delete = 0
							AND type = "class_bundle_price"
							AND branch = "' . $branch_id . '"
							AND parent = "' . $k2 . '"
							AND subsidy > 0
							AND qty < ' . count($v2['data']) . '
							ORDER BY qty DESC
						')->result_array();
						if(count($bundle_subsidy) > 0) {
							$bundle_subsidy = $bundle_subsidy[0];
							for($i=0; $i<floor(count($v2['data']) / $bundle_subsidy['qty']); $i++) {
								$subsidy_fee[] = [
									'title'	=> 'Subsidy [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k2) . ']',
									'fee' => $bundle_subsidy['subsidy']
								];
							}
						}
					}
					
					foreach($material_fee as $e2) {
						$total_material_fee += $e2['fee'];
					}
					
					foreach($subsidy_fee as $e2) {
						$total_subsidy_fee += $e2['fee'];
					}
				}
			}
			
			$total_payment = 0;
			$total_discount = 0;
			$total = 0;
			
			$new_result = [];
			
			$new_unpaid_class = array_merge($with_class_bundle, $without_class_bundle);
			
			foreach($new_unpaid_class as $e2) {
				foreach($e2['data'] as $e3) {
					if(isset($e3['amount'])) {
						$new_result['result']['class'][] = $e3;
						$total_payment += $e3['amount'] - $e3['discount'];
					}
				}
				if(isset($e2['data']['amount'])) {
					$new_result['result']['class'][] = $e2['data'];
					$total_payment += $e2['data']['amount'] - $e2['data']['discount'];
				}
			}
			
			$total_payment += $total_material_fee;
			$total_discount += $total_subsidy_fee;
			$total = $total_payment - $total_discount;
			
			if(isset($std_unpaid_result['result']['item'])) {
				$new_result['result']['item'] = $std_unpaid_result['result']['item'];
				$total_payment += array_sum(array_column($std_unpaid_result['result']['item'], 'amount'));
				$total_discount += array_sum(array_column($std_unpaid_result['result']['item'], 'discount'));
				
				/* 
				foreach($std_unpaid_result['result']['item'] as $e2) {
					$new_result['result']['item'][] = $e2;
					$total_payment += $e2['amount'];
					$total_discount += $e2['discount'];
				}
				 */
			}
			
			if(isset($std_unpaid_result['result']['service'])) {
				$new_result['result']['service'] = $std_unpaid_result['result']['service'];
				$total_payment += array_sum(array_column($std_unpaid_result['result']['service'], 'amount'));
				$total_discount += array_sum(array_column($std_unpaid_result['result']['service'], 'discount'));
				/*
				foreach($std_unpaid_result['result']['service'] as $e2) {
					$new_result['result']['service'][] = $e2;
					$total_payment += $e2['amount'];
					$total_discount += $e2['discount'];
				}*/
			} 
			
			$new_result['subtotal'] = $total_payment;
			$new_result['discount'] = $total_discount;
			$new_result['total'] = $total;
			
			$e['std_unpaid_result'] = $new_result;
				
			if($std_unpaid_result['count'] > 0) {
				$e['details'] = [];
				foreach($new_result['result'] as $e2) {
					foreach($e2 as $e3) {
						$e['details'][$e3['month']] = 0;
					}
				}
				foreach($new_result['result'] as $e2) {
					foreach($e2 as $e3) {
						$e['details'][$e3['month']] += ($e3['amount'] - $e3['discount']);
					}
				}
				$e['total'] = $new_result['total'];
				$e['count'] = count($new_result['result']);
				$student_result[] = $e;
			}
		}

		/* 
		$result = [];
		foreach($paginated_results as $k => $v) {
			$student_result = [];
			foreach(group_by('parent_pid', $v) as $ek => $ee)
			{
				$student_result = [];
				foreach($ee as $eee) {
					// $std_unpaid_result = std_unpaid_result2($eee['pid'], branch_now('pid'));
					$std_unpaid_result = std_unpaid_result2($eee['pid'], branch_now('branch'));
					$new_unpaid_class = [];
					$material_fee = [];
					$subsidy_fee = [];
					$with_class_bundle = [];
					$without_class_bundle = [];
					$total_material_fee = 0;
					$total_subsidy_fee = 0;
					
					if($std_unpaid_result['count'] > 0) {
						
						if(isset($std_unpaid_result['result']['class'])) {
							
							foreach($std_unpaid_result['result']['class'] as $e2) {
								$check_class_bunlde = $this->log_join_model->list('class_bundle_course', branch_now('pid'), [
									'course' => datalist_Table('tbl_classes', 'course', $e2['class'])
								]);
								if(count($check_class_bunlde) > 0) {
									$check_class_bunlde = $check_class_bunlde[0];
									$with_class_bundle[$check_class_bunlde['parent']]['data'][] = $e2;
								} else {
									$without_class_bundle[]['data'] = $e2;
								}
							}
							
							foreach($with_class_bundle as $k2 => $v2) {
								$check_bundle_price = $this->log_join_model->list('class_bundle_price', branch_now('pid'), [
									'parent' => $k2,
									'qty' => count($v2['data'])
								]);
								if(count($check_bundle_price) > 0) {
									$check_bundle_price = $check_bundle_price[0];
									$each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
									$material_fee[] = [
										'title'	=> 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k2) . ']',
										'fee' => $check_bundle_price['material']
									];
									foreach($v2['data'] as $k3 => $v3) {
										$class_price = datalist_Table('tbl_classes', 'fee', $v3['class']);
										$with_class_bundle[$k2]['data'][$k3]['discount'] = $class_price - $each_price;
										$with_class_bundle[$k2]['data'][$k3]['amount'] = $class_price;
										$with_class_bundle[$k2]['data'][$k3]['title'] = $v3['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k2) . ']';
									}
								} else {
									$check_bundle_price = $this->db->query('
										SELECT * FROM log_join
										WHERE is_delete = 0
										AND type = "class_bundle_price"
										AND branch = "' . branch_now('pid') . '"
										AND parent = "' . $k2 . '"
										AND qty < '.count($v2['data']).'
										ORDER BY qty DESC
									')->result_array();
									if(count($check_bundle_price) > 0) {
										$check_bundle_price = $check_bundle_price[0];
										$each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
										for($i=0; $i<floor(count($v2['data']) / $check_bundle_price['qty']); $i++) {
											$material_fee[] = [
												'title'	=> 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k2) . ']',
												'fee' => $check_bundle_price['material']
											];
										}
										foreach($v2['data'] as $k3 => $v3) {
											if($k3 >= $check_bundle_price['qty'] * floor(count($v2['data']) / $check_bundle_price['qty'])) {
												$without_class_bundle[0]['data'][] = $v3;
												unset($with_class_bundle[$k2]['data'][$k3]);
											} else {
												$class_price = datalist_Table('tbl_classes', 'fee', $v3['class']);
												$with_class_bundle[$k2]['data'][$k3]['discount'] = $class_price - $each_price;
												$with_class_bundle[$k2]['data'][$k3]['amount'] = $class_price;
												$with_class_bundle[$k2]['data'][$k3]['title'] = $v3['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k2) . ']';
											}
										}
									}
								} 
							}
							
							foreach($with_class_bundle as $k2 => $v2) {
								$bundle_subsidy = $this->db->query('
									SELECT * FROM log_join
									WHERE is_delete = 0
									AND type = "class_bundle_price"
									AND branch = "' . branch_now('pid') . '"
									AND parent = "' . $k2 . '"
									AND subsidy > 0
									AND qty < ' . count($v2['data']) . '
									ORDER BY qty DESC
								')->result_array();
								if(count($bundle_subsidy) > 0) {
									$bundle_subsidy = $bundle_subsidy[0];
									for($i=0; $i<floor(count($v2['data']) / $bundle_subsidy['qty']); $i++) {
										$subsidy_fee[] = [
											'title'	=> 'Subsidy [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k2) . ']',
											'fee' => $bundle_subsidy['subsidy']
										];
									}
								}
							}
							
							foreach($material_fee as $e2) {
								$total_material_fee += $e2['fee'];
							}
							
							foreach($subsidy_fee as $e2) {
								$total_subsidy_fee += $e2['fee'];
							}
						}
					}
					
					$total_payment = 0;
					$total_discount = 0;
					$total = 0;
					
					$new_result = [];
					
					$new_unpaid_class = array_merge($with_class_bundle, $without_class_bundle);
					
					foreach($new_unpaid_class as $e2) {
						foreach($e2['data'] as $e3) {
							if(isset($e3['amount'])) {
								$new_result['result']['class'][] = $e3;
								$total_payment += $e3['amount'] - $e3['discount'];
							}
						}
						if(isset($e2['data']['amount'])) {
							$new_result['result']['class'][] = $e2['data'];
							$total_payment += $e2['data']['amount'] - $e2['data']['discount'];
						}
					}
					
					if(isset($std_unpaid_result['result']['item'])) {
						foreach($std_unpaid_result['result']['item'] as $e2) {
							$new_result['result']['item'][] = $e2;
							$total_payment += $e2['amount'];
							$total_discount += $e2['discount'];
						}
					}
					
					if(isset($std_unpaid_result['result']['service'])) {
						foreach($std_unpaid_result['result']['service'] as $e2) {
							$new_result['result']['service'][] = $e2;
							$total_payment += $e2['amount'];
							$total_discount += $e2['discount'];
						}
					}
					
					$total_payment += $total_material_fee;
					$total_discount += $total_subsidy_fee;
					$total = $total_payment - $total_discount;
					
					$new_result['subtotal'] = $total_payment;
					$new_result['discount'] = $total_discount;
					$new_result['total'] = $total;
					
					$eee['std_unpaid_result'] = $new_result;
						
					if($std_unpaid_result['count'] > 0) {
						$eee['details'] = [];
						foreach($new_result['result'] as $e2) {
							foreach($e2 as $e3) {
								$eee['details'][$e3['month']] = 0;
							}
						}
						foreach($new_result['result'] as $e2) {
							foreach($e2 as $e3) {
								$eee['details'][$e3['month']] += $e3['amount'];
							}
						}
						$eee['total'] = $new_result['total'];
						$eee['count'] = $std_unpaid_result['count'];
						$student_result[] = $eee;
					}
					
				}
				if (count($student_result) > 0)
				{
					$student_result['total'] = array_sum(array_column($student_result, 'total'));
					$student_result['count'] = array_sum(array_column($student_result, 'count'));
					$parent_result[$ek] = $student_result;
				}
			}
			if (count($parent_result) > 0)
			{
				$parent_result['total'] = array_sum(array_column($student_result, 'total'));
				$parent_result['count'] = count($student_result);
				$result[] = $parent_result;
			}
		} */
		
		
		$data['result'] = $paginated_results;
		$data['student_result'] = $student_result;
		
		$config = array();
		
		$config['base_url'] = base_url('/reports/outstanding_payment_parent');
		$config['total_rows'] = count($group_by_parent);
		$config['per_page'] = 100;
		$config['use_page_numbers'] = TRUE;
		$config['page_query_string'] = TRUE;
        $config["uri_segment"] = 2;
		$config['enable_query_strings'] = TRUE;
		$config['reuse_query_string'] = TRUE;
			  
		$config['full_tag_open'] = '<ul class="pagination float-right">';
		$config['full_tag_close'] = '</ul>';
		$config['num_tag_open'] = '<li class="page-item"><span class="page-link">';
		$config['num_tag_close'] = '</span></li>';
		$config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';
		$config['prev_tag_close'] = '</span></li>';
		$config['next_tag_open'] = '<li class="page-item"><span class="page-link">';
		$config['next_tag_close'] = '</span></li>';
		$config['prev_link'] = '<i class="fas fa-backward"></i>';
		$config['next_link'] = '<i class="fas fa-forward"></i>';
		$config['last_tag_open'] = '<li class="page-item"><span class="page-link">';
		$config['last_tag_close'] = '</span></li>';
		$config['first_tag_open'] = '<li class="page-item"><span class="page-link">';
		$config['first_tag_close'] = '</span></li>';
		

		// Initialize
		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();
		
		$data['classes'] = $this->tbl_classes_model->list($branch_id, [ 'active' => 1, 'is_hidden' => 0 ]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/outstanding_payment_parent');
		$this->load->view('inc/footer', $data);

	}
	
	public function epoint_balance()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/EpointBalance');

		$data['thispage'] = [
			'title' => 'Epoint Balance',
			'group' => $this->group,
		];
		
		if(isset($_GET['start_date'])) { 
			$start_date = $_GET['start_date']; 
		} else {
			$start_date = date('Y-m-d'); 
		}

		if(isset($_GET['end_date'])) { 
			$end_date = $_GET['end_date']; 
		} else {
			$end_date = date('Y-m-d'); 
		}

		$data['result'] = $this->log_point_model->list(['type' => 'epoint', 'DATE(create_on) >=' => $start_date, 'DATE(create_on) <=' => $end_date]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/epoint_balance');
		$this->load->view('inc/footer', $data);

	}
	
	public function ewallet_balance()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/EwalletBalance');

		$data['thispage'] = [
			'title' => 'Ewallet Balance',
			'group' => $this->group,
		];
		
		if(isset($_GET['start_date'])) { 
			$start_date = $_GET['start_date']; 
		} else {
			$start_date = date('Y-m-d'); 
		}

		if(isset($_GET['end_date'])) { 
			$end_date = $_GET['end_date']; 
		} else {
			$end_date = date('Y-m-d'); 
		}

		$data['result'] = $this->log_point_model->list(['type' => 'ewallet', 'DATE(create_on) >=' => $start_date, 'DATE(create_on) <=' => $end_date]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/ewallet_balance');
		$this->load->view('inc/footer', $data);

	}
	
	public function sales_by_item()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/SalesbyItem');

		$data['thispage'] = [
			'title' => 'Sales By Item',
			'group' => $this->group,
		];
		
		if(isset($_GET['start_date'])) { 
			$start_date = $_GET['start_date']; 
		} else {
			$start_date = date('Y-m-d'); 
		}

		if(isset($_GET['end_date'])) { 
			$end_date = $_GET['end_date']; 
		} else {
			$end_date = date('Y-m-d'); 
		}
		
		$sql=  '
		
			SELECT l.*, i.title AS item_title, SUM(l.price_amount) AS total, SUM(l.qty) AS qty FROM log_payment l
			INNER JOIN tbl_payment p
			ON l.payment = p.pid
			AND l.is_delete = 0
			AND p.is_delete = 0
			AND p.branch = "'.branch_now('pid').'"
			AND p.status = "paid"
			AND p.date >= "'.$start_date.'"
			AND p.date <= "'.$end_date.'"
			INNER JOIN tbl_inventory i
			ON l.item = i.pid
			AND i.is_delete = 0
			AND i.type = "item"
			AND i.branch = "'.branch_now('pid').'"
			GROUP BY l.item
		
		';
		
		$data['result'] = $this->db->query($sql)->result_array();
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/sales_by_item');
		$this->load->view('inc/footer', $data);

	}
	
	public function sales_by_item_cat()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/SalesbyItemCat');

		$data['thispage'] = [
			'title' => 'Sales By Item Cat.',
			'group' => $this->group,
		];
		
		if(isset($_GET['start_date'])) { 
			$start_date = $_GET['start_date']; 
		} else {
			$start_date = date('Y-m-d'); 
		}

		if(isset($_GET['end_date'])) { 
			$end_date = $_GET['end_date']; 
		} else {
			$end_date = date('Y-m-d'); 
		}
		
		$sql=  '
		
			SELECT l.*, s.title AS item_cat_title, SUM(l.price_amount) AS total, SUM(l.qty) AS qty FROM log_payment l
			INNER JOIN tbl_payment p
			ON l.payment = p.pid
			AND l.is_delete = 0
			AND p.is_delete = 0
			AND p.branch = "'.branch_now('pid').'"
			AND p.status = "paid"
			AND p.date >= "'.$start_date.'"
			AND p.date <= "'.$end_date.'"
			INNER JOIN tbl_inventory i
			ON l.item = i.pid
			AND i.is_delete = 0
			AND i.type = "item"
			AND i.branch = "'.branch_now('pid').'"
			INNER JOIN tbl_secondary s
			ON i.category = s.pid
			AND s.is_delete = 0
			AND s.type = "item_cat"
			GROUP BY i.category
		
		';
		
		$data['result'] = $this->db->query($sql)->result_array();

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/sales_by_item_cat');
		$this->load->view('inc/footer', $data);

	}
	
	public function sales_by_school()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/SalesbySchool');

		$data['thispage'] = [
			'title' => 'Sales By School',
			'group' => $this->group,
		];
		
		$data['students'] = $this->tbl_users_model->list('student', branch_now('pid'), [ 'active' => 1 ]);
		$data['school'] = $this->tbl_secondary_model->list('school', branch_now('pid'), [ 'active' => 1 ]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/sales_by_school');
		$this->load->view('inc/footer', $data);

	}
	
	public function sales_by_course()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/SalesbyCourse');

		$data['thispage'] = [
			'title' => 'Sales By Course',
			'group' => $this->group,
		];
		
		if(isset($_GET['start_date'])) { 
			$start_date = $_GET['start_date']; 
		} else {
			$start_date = date('Y-m-d'); 
		}

		if(isset($_GET['end_date'])) { 
			$end_date = $_GET['end_date']; 
		} else {
			$end_date = date('Y-m-d'); 
		}
		
		$sql=  '
		
			SELECT l.*, s.title AS course_title, SUM(l.price_amount) AS total FROM log_payment l
			INNER JOIN tbl_payment p
			ON l.payment = p.pid
			AND l.is_delete = 0
			AND p.is_delete = 0
			AND p.branch = "'.branch_now('pid').'"
			AND p.status = "paid"
			AND p.date >= "'.$start_date.'"
			AND p.date <= "'.$end_date.'"
			INNER JOIN tbl_classes c
			ON l.class = c.pid
			AND c.is_delete = 0
			AND c.branch = "'.branch_now('pid').'"
			INNER JOIN tbl_secondary s
			ON c.course = s.pid
			AND s.is_delete = 0
			GROUP BY c.course
		
		';
		
		$data['result'] = $this->db->query($sql)->result_array();

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/sales_by_course');
		$this->load->view('inc/footer', $data);

	}
	
	public function sales_by_admin()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/SalesbyAdmin');

		$data['thispage'] = [
			'title' => 'Sales By Admin',
			'group' => $this->group,
		];
		
		$data['admins'] = $this->log_join_model->list('join_branch', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/sales_by_admin');
		$this->load->view('inc/footer', $data);

	}
	
	public function teacher_comm()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/TeacherCommission');

		$data['thispage'] = [
			'title' => 'Teacher Commission',
			'group' => $this->group,
			'css' => $this->group.'/report',
			'js' => $this->group.'/teacher_comm',
		];
		
		$data['branch'] = $this->tbl_branches_model->list([
			'active' => 1,
		]);
		
		if(!isset($_GET['month'])) $_GET['month'] = date('Y-m');
		
		$sql = '
		
			SELECT * FROM tbl_users
			WHERE is_delete = 0
			AND type = "teacher"
			AND active = 1
		
		';
		
		if(isset($_GET['branch'])) {
			$sql .= ' AND branch IN ("' . join('","',$_GET['branch']) . '")';
		}
		
		if(isset($_GET['sort'])) {
			
			if($_GET['sort'] == 'asc') {
				$sql .= ' ORDER BY fullname_en ASC';
			} else {
				$sql .= ' ORDER BY fullname_en DESC';
			}
			
		} else {
			
			$sql .= ' ORDER BY fullname_en ASC';
			
		}
		$data['branch'] = $this->tbl_branches_model->list();
		$data['teachers'] = $this->db->query($sql)->result_array();
		
		$sql = '
		
			SELECT l.* FROM log_payment l
			INNER JOIN tbl_payment t
			ON l.payment = t.pid
			AND l.is_delete = 0
			AND t.is_delete = 0
			AND l.class IS NOT NULL
			AND t.branch = "'.branch_now('pid').'"
			AND MONTH(t.date) = "'.date('m', strtotime($_GET['month'])).'"
			AND YEAR(t.date) = "'.date('Y', strtotime($_GET['month'])).'"
		
		';
		
		$data['payment'] = $this->db->query($sql)->result_array();
		
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/teacher_comm');
		$this->load->view('inc/footer', $data);

	}
	
	public function teacher_comm_det()
	{
		if (!isset($_GET['teacher']))
		{
			redirect(base_url('reports/teacher_comm'));
		}
		
		$data['teacher'] = $this->tbl_users_model->view($_GET['teacher']);
		
		if (count($data['teacher']) == 0)
		{
			redirect(base_url('reports/teacher_comm'));
		}
		
		$data['thispage'] = [
			'title' => 'All Classes By ('. $data['teacher'][0]['fullname_en'] .')',
			'group' => $this->group,
		];
		

		$data['result'] = $this->tbl_classes_model->list(branch_now('pid'), [ 'is_hidden' => 0 ]);
		// Tan Jing Suan
		$baby_morning_sql = "SELECT tbl_classes.pid AS class_id, sub_classes.id AS sub_class_id,
					tbl_classes.title AS class_title, sub_classes.title AS sub_class_title, tbl_classes.teacher,
					sub_classes.time_range, DAYNAME(CONCAT('1970-09-2', sub_classes.qty)) AS class_day, 
					teachers.fullname_cn, teachers.fullname_en
					FROM tbl_classes 
					LEFT JOIN log_join sub_classes ON tbl_classes.pid = sub_classes.class AND sub_classes.type = 'class_timetable' AND sub_classes.is_delete = 0
					LEFT JOIN tbl_secondary course ON tbl_classes.course = course.pid
					JOIN tbl_users teachers ON teachers.pid = tbl_classes.teacher
					WHERE tbl_classes.is_delete = 0
					AND course.title in ('AGE 3','AGE 4','AGE 5','AGE 6')
					AND tbl_classes.branch = '". branch_now('pid') ."'
					AND tbl_classes.is_hidden = 0";
		
		$kid_morning_sql = "SELECT tbl_classes.pid AS class_id, sub_classes.id AS sub_class_id,
					tbl_classes.title AS class_title, sub_classes.title AS sub_class_title, tbl_classes.teacher,
					sub_classes.time_range, DAYNAME(CONCAT('1970-09-2', sub_classes.qty)) AS class_day, 
					teachers.fullname_cn, teachers.fullname_en
					FROM tbl_classes 
					LEFT JOIN log_join sub_classes ON tbl_classes.pid = sub_classes.class AND sub_classes.type = 'class_timetable' AND sub_classes.is_delete = 0
					LEFT JOIN tbl_secondary course ON tbl_classes.course = course.pid
					JOIN tbl_users teachers ON teachers.pid = tbl_classes.teacher
					WHERE tbl_classes.is_delete = 0
					AND course.title in ('K1','K2','Y1','Y2','Y3','Y4','Y5','Y6')
					AND tbl_classes.branch = '". branch_now('pid') ."'
					AND tbl_classes.is_hidden = 0";
		
		$mid_morning_sql = "SELECT tbl_classes.pid AS class_id, sub_classes.id AS sub_class_id,
					tbl_classes.title AS class_title, sub_classes.title AS sub_class_title, tbl_classes.teacher,
					sub_classes.time_range, DAYNAME(CONCAT('1970-09-2', sub_classes.qty)) AS class_day, 
					teachers.fullname_cn, teachers.fullname_en
					FROM tbl_classes 
					LEFT JOIN log_join sub_classes ON tbl_classes.pid = sub_classes.class AND sub_classes.type = 'class_timetable' AND sub_classes.is_delete = 0
					LEFT JOIN tbl_secondary course ON tbl_classes.course = course.pid
					JOIN tbl_users teachers ON teachers.pid = tbl_classes.teacher
					WHERE tbl_classes.is_delete = 0
					AND course.title in ('F1','F2','F3','F4','F5')
					AND tbl_classes.branch = '". branch_now('pid') ."'
					AND tbl_classes.is_hidden = 0";
					
		
		$sql = "SELECT teacher, class_id, sub_class_id, class_title, sub_class_title, time_range, class_day, 
				class_date, week_number, SUM(COALESCE(students.student_count, 0)) AS student_count, 
				(SUM(COALESCE(students.student_count, 0)) * 4) AS max_student_count,
				SUM(COALESCE(present_students.student_count, 0)) AS present_student_count,
				SUM(COALESCE(payments.student_count, 0)) AS payment_student_count
				FROM (
					SELECT tbl_classes.pid AS class_id, sub_classes.id AS sub_class_id,
					tbl_classes.title AS class_title, sub_classes.title AS sub_class_title, tbl_classes.teacher,
					sub_classes.time_range, DAYNAME(CONCAT('1970-09-2', sub_classes.qty)) AS class_day,
					X.DATE AS class_date, (ceiling((day(X.DATE) - (6 - weekday(date_format(X.DATE,'%Y-%m-01'))))/7) + (case when 6 - weekday(date_format(X.DATE,'%Y-%m-01')) > 0 then 1 else 0 end)) AS week_number
					FROM tbl_classes 
					LEFT JOIN log_join sub_classes ON tbl_classes.pid = sub_classes.class AND sub_classes.type = 'class_timetable' AND sub_classes.is_delete = 0
					left join (
						select FROM_UNIXTIME(UNIX_TIMESTAMP(CONCAT(YEAR(CURRENT_DATE()), '-', MONTH(CURRENT_DATE()), '-', n)),'%Y-%m-%d') as DATE, DAYNAME(FROM_UNIXTIME(UNIX_TIMESTAMP(CONCAT(YEAR(CURRENT_DATE()), '-', MONTH(CURRENT_DATE()), '-', n)),'%Y-%m-%d')) AS DAY_NAME
						from (
							select (((b4.0 << 1 | b3.0) << 1 | b2.0) << 1 | b1.0) << 1 | b0.0 as n
									from  (select 0 union all select 1) as b0,
										  (select 0 union all select 1) as b1,
										  (select 0 union all select 1) as b2,
										  (select 0 union all select 1) as b3,
										  (select 0 union all select 1) as b4 
						) t
						where n > 0 and n <= day(last_day(CONCAT(YEAR(CURRENT_DATE()), '-', MONTH(CURRENT_DATE()), '-01')))
					) X ON X.DATE IS NOT NULL AND X.DAY_NAME = DAYNAME(CONCAT('1970-09-2', sub_classes.qty))
					WHERE tbl_classes.is_delete = 0
					AND tbl_classes.branch = '". branch_now('pid') ."'
					
					AND tbl_classes.is_hidden = 0
					GROUP BY tbl_classes.pid, sub_classes.id, tbl_classes.title, sub_classes.title, sub_classes.time_range, sub_classes.qty, (ceiling((day(X.DATE) - (6 - weekday(date_format(X.DATE,'%Y-%m-01'))))/7) + (case when 6 - weekday(date_format(X.DATE,'%Y-%m-01')) > 0 then 1 else 0 end)), X.DATE
				) teacher_class_day
				LEFT JOIN (
					SELECT log_join.class, COUNT(log_join.user) AS student_count
					FROM log_join
					JOIN tbl_users students ON students.pid = log_join.user
					WHERE log_join.type = 'join_class'
					AND log_join.is_delete = 0
					AND log_join.active = 1
					AND students.branch = '". branch_now('pid') ."'
					AND students.type = 'student'
					AND students.active = 1
					AND students.is_delete = 0
					GROUP BY log_join.class
				) students ON students.class = teacher_class_day.class_id
				LEFT JOIN (
					SELECT log_join.class, log_join.sub_class, DATE(log_join.date) AS check_in_date, COUNT(*) AS student_count
					FROM log_join
					JOIN tbl_users students ON students.pid = log_join.user
					WHERE log_join.branch = '". branch_now('pid') ."'
					AND log_join.type = 'class_attendance'
					AND log_join.active = 1
					AND log_join.is_delete = 0
					AND students.type = 'student'
					AND students.active = 1
					AND students.is_delete = 0
					GROUP BY log_join.class, log_join.sub_class, DATE(log_join.date)
				) present_students ON present_students.class = teacher_class_day.class_id AND present_students.sub_class = teacher_class_day.sub_class_id AND present_students.check_in_date = teacher_class_day.class_date
				LEFT JOIN (
					SELECT class, COUNT(user) AS student_count
					FROM log_payment
					WHERE is_delete = 0
					AND period = '".$_GET['month']."'
				) payments ON payments.class = class_id where  
				YEAR(class_date) = '". date_format(date_create($_GET['month']),"Y") ."' 
				AND MONTH(class_date) = '". date_format(date_create($_GET['month']),"m") ."'
				GROUP BY teacher, class_id, sub_class_id, class_title, sub_class_title, time_range, class_day, 
				class_date, week_number
				ORDER BY class_title, sub_class_title, time_range, class_day, class_date, week_number";
				
				
		if(count($data['teacher']) > 0) {
			$sql .= " AND teacher = '". $_GET['teacher'] ."'";
			// Tan Jing Suan
			$baby_morning_sql .= " AND teacher = '". $_GET['teacher'] ."'";
			$kid_morning_sql .= " AND teacher = '". $_GET['teacher'] ."'";
			$mid_morning_sql .= " AND teacher = '". $_GET['teacher'] ."'";
		}
		// Tan Jing Suan
		$data['baby_morning_result'] = $this->db->query($baby_morning_sql)->result_array();
		$data['kid_morning_result'] = $this->db->query($kid_morning_sql)->result_array();
		$data['mid_morning_result'] = $this->db->query($mid_morning_sql)->result_array();
		$data['result'] = $this->db->query($sql)->result_array();
		
		//verbose($data['result']); exit;
		/* 
		$view = 'list';
		if(count($data['teacher']) > 0) {
			$data['result'] = $this->tbl_classes_model->list2([ 'teacher' => $_GET['teacher'], 'is_hidden' => 0 ]);
			$view = 'list_teacher';
		} */

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/teacher_comm_det', $data);
		$this->load->view('inc/footer', $data);

	}
	
	public function sales_by_ref_code()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/SalesbyReferenceCode');

		$data['thispage'] = [
			'title' => 'Sales By Reference Code',
			'group' => $this->group,
			'css' => $this->group.'/report',
			'js' => $this->group.'/sales_by_ref_code',
		];
		
		if(!isset($_GET['start_date']) || empty($_GET['start_date'])) $_GET['start_date'] = date('Y-m-d');
		if(!isset($_GET['end_date']) || empty($_GET['end_date'])) $_GET['end_date'] = date('Y-m-d');
		
		$sql = '
		
			SELECT DISTINCT(ref_code) FROM tbl_users
			WHERE is_delete = 0
			AND type = "student"
			AND active = 1
			AND ref_code IS NOT NULL
			AND DATE(update_on) >= "'.$_GET['start_date'].'"
			AND DATE(update_on) <= "'.$_GET['end_date'].'"
			AND branch = "'.branch_now('pid').'"
		
		';
		
		if(isset($_GET['sort'])) {
			
			if($_GET['sort'] == 'asc') {
				$sql .= ' ORDER BY ref_code ASC';
			} else {
				$sql .= ' ORDER BY ref_code DESC';
			}
			
		} else {
			
			$sql .= ' ORDER BY ref_code ASC';
			
		}
		
		$data['ref_code'] = $this->db->query($sql)->result_array();
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/sales_by_ref_code');
		$this->load->view('inc/footer', $data);

	}

	public function deleted_receipts()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/DeletedReceipts');

		$data['thispage'] = [
			'title' => 'Delete Receipts',
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_payment_model->deleted_list(branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/deleted_receipts');
		$this->load->view('inc/footer', $data);

	}

	public function birthday_student()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Users/Birthday');

		$data['thispage'] = [
			'title' => 'Student Birthday',
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_users_model->list_birthday_only('student', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/birthday_student');
		$this->load->view('inc/footer', $data);

	}

	public function birthday_parent()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Users/BirthdayParent');

		$data['thispage'] = [
			'title' => 'Parent Birthday',
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_users_model->list_birthday_only('parent', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/birthday_parent');
		$this->load->view('inc/footer', $data);

	}

	public function birthday_teacher()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Users/BirthdayTeacher');

		$data['thispage'] = [
			'title' => 'Teacher Birthday',
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_users_model->list_birthday_only('teacher', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/birthday_teacher');
		$this->load->view('inc/footer', $data);

	}
	
	public function student_attendance()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Attendance/StudentAttendance');

		$data['thispage'] = [
			'title' => 'Student Attendance',
			'group' => $this->group,
			'js' => $this->group . '/student_attendance',
		];
		
		if(isset($_GET['start_date'])) { 
			$start_date = $_GET['start_date']; 
		} else { 
			$start_date = date('Y-m-d'); 
		}
		
		if(isset($_GET['end_date'])) { 
			$end_date = $_GET['end_date']; 
		} else { 
			$end_date = date('Y-m-d'); 
		}
		
		$data['std_result'] = [];
		
		if(isset($_GET['student'])) { 
		
			$student = isset($_GET['student']) ? $_GET['student'] : null;
			
			if($student == null) {
				
				$data['result'] = $this->log_attendance_model->list_gb_user_datetime_ob_datetime_asc([
				
					'branch' => branch_now('pid'),
					'DATE(datetime) >=' => $start_date,
					'DATE(datetime) <=' => $end_date,
					
				]);
				
				$tempArr = array_unique(array_column($data['result'], 'user'));
				$data['std_result'] = array_intersect_key($data['result'], $tempArr);
				
			} else {
				
				$data['result'] = $this->log_attendance_model->list_gb_user_datetime_ob_datetime_asc([
				
					'branch' => branch_now('pid'),
					'DATE(datetime) >=' => $start_date,
					'DATE(datetime) <=' => $end_date,
					'user' => $student,
					
				]);
				
				$tempArr = array_unique(array_column($data['result'], 'user'));
				$data['std_result'] = array_intersect_key($data['result'], $tempArr);
				
			}
			
		}
		
		$data['student'] = $this->tbl_users_model->active_list('student', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/student_attendance');
		$this->load->view('inc/footer', $data);

	}
	
	public function student_attendance_class()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Attendance/StudentAttendanceClass');

		$data['thispage'] = [
			'title' => 'Student Attendance (Class)',
			'group' => $this->group,
			'js' => $this->group . '/student_attendance_class',
		];
		
		// $data['student'] = $this->tbl_users_model->active_list('student', branch_now('pid'));
		$data['class'] = $this->tbl_classes_model->list(branch_now('pid'), [ 'active' => 1, 'is_hidden' => 0 ]);
		
		$date;
		if(isset($_GET['month'])) {
			$date = $_GET['month'].'-'.date('d');
		} else {
			$date = date('Y-m-d');
		}
		
		if(isset($_GET['class'])) {
			// $data['result'] = $this->log_join_model->list('join_class', branch_now('pid'), ['user' => $_GET['student'], 'active' => 1]);
			$data['result'] = $this->log_join_model->list('join_class', branch_now('pid'), ['class' => $_GET['class'], 'active' => 1]);
		} else {
			$data['result'] = null;
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/student_attendance_class');
		$this->load->view('inc/footer', $data);

	}
	
	public function class_deductions()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Attendance/ClassDeductions');

		$data['thispage'] = [
			'title' => 'Class Deductions',
			'group' => $this->group,
		];
		
		$data['class'] = $this->tbl_classes_model->list(branch_now('pid'), [ 'is_hidden' => 0 ]);
		
		$data['result'] = [];
		
		if(isset($_GET['class']) && $_GET['month']) {
			
			$result = [];
			
			$month = date('m', strtotime($_GET['month']));
			$year = date('Y', strtotime($_GET['month']));
			
			$class = $this->log_join_model->list('join_class', branch_now('pid'), [
			
				'class' => $_GET['class'],
				'MONTH(date)' => $month,
				'YEAR(date)' => $year,
				'active' => 1
				
			]);
			
			$student = array_unique(array_column($class, 'user'));
			
			foreach($student as $s) {
				
				$class_total = 0;
				
				for($i=1; $i<=cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++) {
					
					$date = $_GET['month'].'-'.$i;
					$day = date('N', strtotime($date));
					if(!empty(datalist_Table('tbl_classes', 'dy_' . $day, $_GET['class']))) $class_total++;
					
				}
				
				$attendance = $this->log_join_model->list('class_attendance', branch_now('pid'), [
					'class' 		=> $_GET['class'],
					'user' 			=> $s,
					'MONTH(date)'	=> $month,
					'YEAR(date)'	=> $year,
				]);
				
				$result[] = [
					'student' 		=> $s,
					'class_total' 	=> $class_total,
					'attend_total' 	=> count($attendance),
				];
				
			}
			
			$data['result'] = $result;
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/class_deductions');
		$this->load->view('inc/footer', $data);

	}
	
	public function student_attendance_class_landing()
	{
		
		$user;
		$data['branch'] = '';
		if(!isset($_GET['student'])) $_GET['student'] = '';
		
		if(!empty($this->tbl_users_model->view($_GET['student']))) {
			
			$user = $_GET['student'];
			$data['branch'] = datalist_Table('tbl_users', 'branch', $_GET['student']);
			
			$data['thispage'] = [
				'title' => 'Student Attendance',
				'group' => $this->group,
				'type' => 'student',
			];
					
		} else {
			
			if(!isset($_GET['token'])) $_GET['token'] = '';
		
			$login = $this->tbl_users_model->me_token( $_GET['token'] );

			if( count($login) == 1 ) {
				
				$login = $login[0];
				
			} else {
				
				die(app('title').': Token error');
				
			}
			
			$user = datalist_Table('tbl_users', 'pid', $_GET['token'], 'token');
			$data['branch'] = datalist_Table('tbl_users', 'branch', $_GET['token'], 'token');
			
			if(datalist_Table('tbl_users', 'type', $_GET['token'], 'token') == 'parent') {
				
				$data['parent'] = $user;
				
				$data['thispage'] = [
					'title' => 'Student Attendance',
					'group' => $this->group,
					'js' => $this->group.'/student_attendance_class_landing',
					'type' => 'parent',
				];
				
			} else {
				
				$data['thispage'] = [
					'title' => 'Student Attendance',
					'group' => $this->group,
					'type' => 'student',
				];
				
			}
			
		}
				
		$date;
		if(isset($_GET['month'])) {
			$date = $_GET['month'].'-'.date('d');
		} else {
			$date = date('Y-m-d');
		}
		
		$data['result'] = $this->log_join_model->list('join_class', $data['branch'], ['user' => $user, 'active' => 1]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/student_attendance_class_landing');
		$this->load->view('inc/footer', $data);

	}
	
	public function student_enroll()
	{
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Attendance/StudentEnroll');

		$data['thispage'] = [
			'title' => 'Student Enroll',
			'group' => $this->group,
		];
		
		if(isset($_GET['start_date'])) { 
			$start_date = $_GET['start_date']; 
		} else { 
			$start_date = date('Y-m-d'); 
		}
		
		if(isset($_GET['end_date'])) { 
			$end_date = $_GET['end_date']; 
		} else { 
			$end_date = date('Y-m-d'); 
		}
		
		$data['result'] = [];
		
		if (isset($_GET['student']))
		{
			$data['result'] = $this->log_join_model->student_enroll(branch_now('pid'), $start_date, $end_date, $_GET['student']);
		}
		
		$data['student'] = $this->tbl_users_model->active_list('student', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/student_enroll');
		$this->load->view('inc/footer', $data);

	}
	
	public function absence_rate()
	{
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Attendance/AbsenceRate');
		
		$data['thispage'] = [
			'title' => 'Absence Rate',
			'group' => $this->group,
		];
		
		if(isset($_GET['start_date'])) { 
			$start_date = $_GET['start_date']; 
		} else { 
			$start_date = date('Y-m-d'); 
		}
		
		if(isset($_GET['end_date'])) { 
			$end_date = $_GET['end_date']; 
		} else { 
			$end_date = date('Y-m-d'); 
		}
		
		$data['main_result'] = [];
		$data['result'] = [];
		
		if (isset($_GET['student']))
		{
			$data['result'] = $this->log_join_model->absence_rate(branch_now('pid'), $start_date, $end_date, $_GET['student']);
		}
		
		//$data['result'] = $this->db->query($sql)->result_array();
		
		$data['student'] = $this->tbl_users_model->active_list('student', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/absence_rate');
		$this->load->view('inc/footer', $data);
	}
	
	// public function student_attendance_landing()
	// {
		
		// auth_must('login');
		// check_module_page('Reports/Read');
		// check_module_page('Reports/Modules/Attendance/StudentAttendance');
		
		// check token
		// if(!isset($_GET['token'])) $_GET['token'] = '';
		
		// $login = $this->tbl_users_model->me_token( $_GET['token'] );

		// if( count($login) == 1 ) {
			
			// $login = $login[0];
			
		// } else {
			
			// header('Content-type: application/json');
			
			// die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));
			
			// die(app('title').': Token error');
			
		// }

		// $data['thispage'] = [
			// 'title' => 'Student Attendance',
			// 'group' => $this->group,
		// ];
		
		// $user = $this->tbl_users_model->list2( ['token' => $_GET['token']] )[0]['pid'];
		// $data['result'] = $this->log_join_model->list('join_class', branch_now('pid'), ['user' => $user, 'date <' => date('Y-m-d')]);

		// $this->load->view('inc/header', $data);
		// $this->load->view($this->group.'/student_attendance_landing');
		// $this->load->view('inc/footer', $data);

	// }
	
	public function teacher_attendance()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Attendance/TeacherAttendance');

		$data['thispage'] = [
			'title' => 'Teacher Attendance',
			'group' => $this->group,
		];
		
		if(isset($_GET['start_date'])) { 
			$start_date = $_GET['start_date']; 
		} else { 
			$start_date = date('Y-m-d'); 
		}
		
		if(isset($_GET['end_date'])) { 
			$end_date = $_GET['end_date']; 
		} else { 
			$end_date = date('Y-m-d'); 
		}
		
		if(isset($_GET['teacher'])) { 
			$teacher = $_GET['teacher']; 
			if($teacher == null) {
				
				$data['result'] = $this->log_attendance_model->list_gb_user_datetime_ob_datetime_asc([
				
					'branch' => branch_now('pid'),
					'DATE(datetime) >=' => $start_date,
					'DATE(datetime) <=' => $end_date,
					
				]);
				
			} else {
				
				$data['result'] = $this->log_attendance_model->list_gb_user_datetime_ob_datetime_asc([
				
					'branch' => branch_now('pid'),
					'DATE(datetime) >=' => $start_date,
					'DATE(datetime) <=' => $end_date,
					'user' => $teacher,
					
				]);
				
			}
		}
		
		$data['teacher'] = $this->tbl_users_model->active_list('teacher', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/teacher_attendance');
		$this->load->view('inc/footer', $data);

	}

	public function daily_attendance()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Attendance/DailyAttendance');

		$data['thispage'] = [
			'title' => 'Daily Attendance',
			'group' => $this->group,
		];
		
		if(isset($_GET['start_date'])) { 
			$start_date = $_GET['start_date']; 
		} else { 
			$start_date = date('Y-m-d'); 
		}
		
		if(isset($_GET['end_date'])) { 
			$end_date = $_GET['end_date']; 
		} else { 
			$end_date = date('Y-m-d'); 
		}
		
		$data['teacher'] = $this->tbl_users_model->active_list('teacher', branch_now('pid'));
		
		$data['result'] = $this->log_join_model->list_daily_attendance(branch_now('pid'), $start_date, $end_date, $_GET['teacher']);
		

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/daily_attendance');
		$this->load->view('inc/footer', $data);

	}

	public function daily_attendance_teacher()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Attendance/DailyAttendanceTeacher');

		$data['thispage'] = [
			'title' => 'Teacher Daily Attendance',
			'group' => $this->group,
		];

		if(isset($_GET['date'])) {
			
			$data['result'] = $this->log_attendance_model->list_gb_user([
			
				'branch' => branch_now('pid'),
				'DATE(datetime)' => $_GET['date'],
			
			]);
			
		} else {
			
			$data['result'] = $this->log_attendance_model->list_gb_user([
			
				'branch' => branch_now('pid'),
				'DATE(datetime)' => date('Y-m-d'),
			
			]);
			
		}

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/daily_attendance_teacher');
		$this->load->view('inc/footer', $data);

	}

	public function monthly_attendance()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Attendance/MonthlyAttendance');

		$data['thispage'] = [
			'title' => 'Monthly Attendance',
			'group' => $this->group,
			'js' => $this->group . '/monthly_attendance',
		];

		$data['student'] = $this->tbl_users_model->list('student', branch_now('pid'), [ 'active' => 1 ]);
 		
		if(isset($_GET['month'])) {
			
			$data['result'] = $this->log_attendance_model->list([
			
				'branch' => branch_now('pid'),
				'MONTH(datetime)' => date('m', strtotime($_GET['month'])),
				'YEAR(datetime)' => date('Y', strtotime($_GET['month'])),
			
			]);
			
		} else {
			
			$data['result'] = $this->log_attendance_model->list([
			
				'branch' => branch_now('pid'),
				'MONTH(datetime)' => date('m'),
				'YEAR(datetime)' => date('Y'),
			
			]);
						
		}

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/monthly_attendance');
		$this->load->view('inc/footer', $data);

	}

	public function monthly_attendance_teacher()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Attendance/MonthlyAttendanceTeacher');

		$data['thispage'] = [
			'title' => 'Teacher Monthly Attendance',
			'group' => $this->group,
		];

		$data['teacher'] = $this->tbl_users_model->list('teacher', branch_now('pid'), [ 'active' => 1 ]);
 		
		if(isset($_GET['month'])) {
			
			$data['result'] = $this->log_attendance_model->list([
			
				'branch' => branch_now('pid'),
				'MONTH(datetime)' => date('m', strtotime($_GET['month'])),
			
			]);
			
		} else {
			
			$data['result'] = $this->log_attendance_model->list([
			
				'branch' => branch_now('pid'),
				'MONTH(datetime)' => date('m'),
			
			]);
						
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/monthly_attendance_teacher');
		$this->load->view('inc/footer', $data);

	}

	public function stock_movement()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Inventory/StockMovement');

		$data['thispage'] = [
			'title' => 'Stock Movement',
			'group' => $this->group,
			'js' => $this->group . '/stock_movement',
		];

		$data['result'] = $this->tbl_inventory_model->list(branch_now('pid'), 'item');

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/stock_movement');
		$this->load->view('inc/footer', $data);

	}

	public function check_students()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/DataCheck/Students');

		$data['thispage'] = [
			'title' => 'Students',
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_users_model->list('student', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/check_students');
		$this->load->view('inc/footer', $data);

	}
	
	public function check_students_export()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/DataCheck/Students');

		$data['thispage'] = [
			'title' => 'Students',
			'group' => $this->group,
			'js' => $this->group . '/check_students_export',
		];

		$data['result'] = $this->tbl_users_model->list('student', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/check_students_export');
		$this->load->view('inc/footer', $data);

	}

	public function check_teachers()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/DataCheck/Teachers');

		$data['thispage'] = [
			'title' => 'Teachers',
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_users_model->list('teacher', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/check_teachers');
		$this->load->view('inc/footer', $data);

	}

	public function check_form_teachers()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/DataCheck/FormTeachers');

		$data['thispage'] = [
			'title' => 'Form Teachers',
			'group' => $this->group,
		];

		//$data['result'] = $this->tbl_users_model->list('teacher', branch_now('pid'));
		
		$sql = "SELECT tbl_users.pid, tbl_users.fullname_en, tbl_users.active, COUNT(students.student_pid) AS number_of_students
				FROM tbl_users 
				JOIN ( 
					SELECT students.form_teacher, students.pid AS student_pid
					FROM tbl_users students
					WHERE students.type = 'student'
					AND students.is_delete = 0
				) students ON students.form_teacher = tbl_users.pid
				WHERE tbl_users.is_delete = 0 AND tbl_users.type = 'teacher' 
				AND tbl_users.branch = '". branch_now('pid'). "'
				GROUP BY tbl_users.fullname_en, tbl_users.active, tbl_users.pid";
		
		$data['result'] = $this->db->query($sql)->result_array();
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/check_form_teachers');
		$this->load->view('inc/footer', $data);

	}

	public function check_parents()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/DataCheck/Parents');

		$data['thispage'] = [
			'title' => 'Parents',
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_users_model->list('parent', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/check_parents');
		$this->load->view('inc/footer', $data);

	}

	public function check_items()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/DataCheck/Items');

		$data['thispage'] = [
			'title' => 'Items',
			'group' => $this->group,
		];

		$data['result'] = $this->tbl_inventory_model->list(branch_now('pid'), 'item');

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/check_items');
		$this->load->view('inc/footer', $data);

	}

	public function check_classes()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/DataCheck/Classes');

		$data['thispage'] = [
			'title' => 'Classes',
			'group' => $this->group,
		];
		
		$data['result'] = $this->tbl_classes_model->list(branch_now('pid'), [ 'is_hidden' => 0 ]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/check_classes');
		$this->load->view('inc/footer', $data);

	}
	
	public function check_payment_trash()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/DataCheck/PaymentTrash');

		$data['thispage'] = [
			'title' => 'Payment (Trash)',
			'group' => $this->group,
		];
		
		$data['result'] = $this->tbl_payment_model->setup_list(branch_now('pid'), [ 'is_delete' => 1]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/check_payment_trash');
		$this->load->view('inc/footer', $data);

	}
	
	public function whatsapp_marketing()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Users/WhatsAppMarketing');

		$data['thispage'] = [
			'title' => 'WhatsApp Marketing',
			'group' => $this->group,
		];
		
		$data['result'] = $this->tbl_users_model->list_v2([
			'type'		=> 'parent', 
			'branch'	=> branch_now('pid'),
		]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/whatsapp_marketing');
		$this->load->view('inc/footer', $data);

	}
	
	public function whatsapp_marketing_student()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Users/WhatsAppMarketingStudent');

		$data['thispage'] = [
			'title' => 'WhatsApp Marketing (Student)',
			'group' => $this->group,
		];
		
		$data['result'] = $this->tbl_users_model->list_v2([
			'type'		=> 'student', 
			'branch'	=> branch_now('pid'),
		]);

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/whatsapp_marketing_student');
		$this->load->view('inc/footer', $data);

	}

	// by steve
	private function list_payment_method() {
		
		$this->load->model('tbl_secondary_model');
		
		$data['result_payment_method'] = [];
		
		foreach($this->tbl_secondary_model->null_list('payment_method', ['active' => 1]) as $e) {
			
			if(!empty($this->log_join_model->list('secondary', branch_now('pid'), ['secondary' => $e['pid']]))) { 
			
				if($this->log_join_model->list('secondary', branch_now('pid'), ['secondary' => $e['pid']])[0]['active'] == 1) { 

					$data['result_payment_method'][] = $e;
			
				}
			
			}
			
		}
		
		foreach($this->tbl_secondary_model->list('payment_method', branch_now('pid'), [ 'active' => 1 ]) as $e) {
			
			$data['result_payment_method'][] = $e;
			
		}
		
		return $data['result_payment_method'];
		
	}

	public function json_del_unpaid_class($user_id, $class_id, $period)
	{
	
		$this->log_payment_model->add([
			'user' => $user_id,
			'title' => '['.$period.'] ' . datalist_Table('tbl_classes', 'title', $class_id),
			'class' => $class_id,
			'period' => $period,
			'qty' => 1,
			'price_unit' => datalist_Table('tbl_classes', 'fee', $class_id),
			'price_amount' => datalist_Table('tbl_classes', 'fee', $class_id),
			'create_by' => auth_data('pid'),
			'remark' => 'Remove unpaid class' 
		]);
		
		alert_new('success', 'Unpaid class deleted successfully');
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Unpaid class deleted successfully']));

	}

	public function advanced_payment()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Sales/AdvancedPayment');
		
		$this->load->library('pagination');

		$data['thispage'] = [
			'title' => 'Advanced Payment',
			'group' => $this->group,
			'css' => $this->group.'/report',
			'js' => $this->group.'/advanced_payment',
		];
		
		if(isset($_POST['send_email'])) {
			
			// die('1');
			
			header('Content-type: application/json');
			
			$result = $this->tbl_users_model->view($_POST['send_email']);
		
			if(count($result) == 1) {
				
				$result = $result[0];
				
				$email = $result['email'];
								
				if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
					
					$response = pointoapi_Request('SynorexAPI/Email/Send', [
						'to' => $email,
						// 'api_key' => POINTO_API_KEY,
						'subject' => '['.app('title').'] '.$result['fullname_en'].'\'s Oustanding Payment Notification',
						'body' => '
						<p>Hi <b>'.$result['fullname_cn'].' '.$result['fullname_en'].'</b>,</p>
						<p>Here\'s the notification for your advanced payment.</p>
						<br>
						<p>This message send via '.app('title').'. Don\'t reply to this message.</p>
						<br>
						<img src="https://cdn.synorex.link/assets/images/site/logo-x600.png" style="height: 50px;">
						',
					]);
					
					// print_r($response); exit;
					
					if( !isset($response['status']) ) $response['status'] = 'error';
					if( !isset($response['message']) ) $response['message'] = 'Unknow error';
					
					if($response['status'] == 'ok') {
						
						die(json_encode([ 'status' => 'ok', 'message' => 'Email sent successfully']));
						
					} else {
						
						die(json_encode([ 'status' => 'error', 'message' => 'PointoAPI Error: '.$response['message'] ]));
						
					}
					
				} else {
					
					die(json_encode([ 'status' => 'not_found', 'message' => 'Please use a valid email address']));
					
				}
				
			} else {
				
				die(json_encode([ 'status' => 'failed', 'message' => 'Payment not found']));
				
			}
			
		}
		
		if(isset($_POST['send_sms'])) {
			
			header('Content-type: application/json');
			
			$result = $this->tbl_users_model->view($_POST['send_sms']);
		
			if(count($result) == 1) {
				
				$result = $result[0];
				
				$phone = $result['phone'];
				
				if(!empty($phone)) {
					
					if(substr($phone, 0, 1) != '+') $phone = '6'.$phone;

					$msg = branch_now('send_msg_sms_outstanding');
					
					if(empty($msg)) {
						
						die(json_encode([ 'status' => 'required', 'message' => 'SMS content haven\'t been set']));
						
					}
					
					$msg = str_replace('%NAME%', $result['fullname_en'], $msg);
					// $msg = str_replace('%RECEIPT_NO%', $result['payment_no'], $msg);
					$msg = str_replace('%SUBJECT%', $result['fullname_en'], $msg);
					
					$item = '';
					
					$std_unpaid_result = std_unpaid_result($result['pid'], $result['branch']);
					
					if($std_unpaid_result['count'] > 0) {
						$j = 0;
						$i = $std_unpaid_result['count'];
						if(isset($std_unpaid_result['result']['class'])) {
							foreach($std_unpaid_result['result']['class'] as $e) {
								$j++;
								if($j < $i) {
									$item .= $e['title'] . ' x ' . '1, ';
								} else {
									$item .= $e['title'] . ' x ' . '1';
								}
							}
						}
						if(isset($std_unpaid_result['result']['item'])) {
							foreach($std_unpaid_result['result']['item'] as $e) {
								$j++;
								if($j < $i) {
									$item .= $e['title'] . ' x ' . $e['qty'] . ', ';
								} else {
									$item .= $e['title'] . ' x ' . $e['qty'];
								}
							}
						}
					}
					
					$msg = str_replace('%ITEM%', $item, $msg);
					$msg = str_replace('%TOTALOUTSTANDINGAMOUNT%', number_format($std_unpaid_result['total'], 2, '.', ','), $msg);
															
					$response = pointoapi_Request('SynorexAPI/SMS/Send', [
						'to' => $phone,
						'message' => $msg,
					]);
					
					if( !isset($response['status']) ) $response['status'] = 'error';
					if( !isset($response['message']) ) $response['message'] = 'Unknow error';
					
					if($response['status'] == 'ok') {
						
						die(json_encode([ 'status' => 'ok', 'message' => 'SMS sent successfully']));
						
					} else {
						
						die(json_encode([ 'status' => $response['status'], 'message' => $response['message']]));
						
					}
					
				} else {
					
					die(json_encode([ 'status' => 'not_found', 'message' => 'Please use a valid phone number']));
					
				}
				
			} else {
				
				die(json_encode([ 'status' => 'not_found', 'message' => 'Payment not found']));
				
			}
			
		}
		
		if(!isset($_GET['sort'])) $_GET['sort'] = 'asc';
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/advanced_payment');
		$this->load->view('inc/footer', $data);

	}
	
	public function annual_comparison()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Summary/AnnualComparison');

		$data['thispage'] = [
			'title' => 'Annual Comparison',
			'group' => $this->group,
			'css' => $this->group.'/report',
			'js' => $this->group.'/annual_comparison',
		];
		
		if(!isset($_GET['year'])) $_GET['year'] = date('Y');
		
		// Tan Jing Suan
		// $data['form'] = $this->db->query('
		
		// 	SELECT * FROM tbl_secondary
		// 	WHERE is_delete = 0
		// 	AND type = "form"
		// 	ORDER BY FIELD (title, "K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5")
		
		// ')->result_array();	
		$data['form'] = $this->db->query('
		
			SELECT * FROM tbl_secondary
			WHERE is_delete = 0
			AND type = "form"
			AND branch = '.branch_now('pid').'
			ORDER BY FIELD (title, "K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5")
		
		')->result_array();	
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/annual_comparison');
		$this->load->view('inc/footer', $data);

	}

	// Tan Jing Suan
	public function annual_comparison01()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Summary/AnnualComparison人数表');

		$data['thispage'] = [
			'title' => 'Annual Comparison 人数表',
			'group' => $this->group,
			'css' => $this->group.'/report',
			'js' => $this->group.'/annual_comparison',
		];
		
		if(!isset($_GET['year'])) $_GET['year'] = date('Y');
		
		$data['form'] = $this->db->query('
		
			SELECT * FROM tbl_secondary
			WHERE is_delete = 0
			AND type = "form"
			AND branch = '.branch_now('pid').'
			ORDER BY FIELD (title, "K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5")
		
		')->result_array();	
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/annual_comparison01');
		$this->load->view('inc/footer', $data);

	}

	// Tan Jing Suan
	public function annual_comparison02()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Summary/AnnualComparison科数表');

		$data['thispage'] = [
			'title' => 'Annual Comparison 科数表',
			'group' => $this->group,
			'css' => $this->group.'/report',
			'js' => $this->group.'/annual_comparison',
		];
		
		if(!isset($_GET['year'])) $_GET['year'] = date('Y');
		
		$data['form'] = $this->db->query('
		
			SELECT * FROM tbl_secondary
			WHERE is_delete = 0
			AND type = "form"
			AND branch = '.branch_now('pid').'
			ORDER BY FIELD (title, "K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5")
		
		')->result_array();	
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/annual_comparison02');
		$this->load->view('inc/footer', $data);

	}

	// Tan Jing Suan
	public function annual_comparison03()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/Summary/AnnualComparison实业绩表');

		$data['thispage'] = [
			'title' => 'Annual Comparison 实业绩表',
			'group' => $this->group,
			'css' => $this->group.'/report',
			'js' => $this->group.'/annual_comparison',
		];
		
		if(!isset($_GET['year'])) $_GET['year'] = date('Y');
		
		$data['form'] = $this->db->query('
		
			SELECT * FROM tbl_secondary
			WHERE is_delete = 0
			AND type = "form"
			AND branch = '.branch_now('pid').'
			ORDER BY FIELD (title, "K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5")
		
		')->result_array();	
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/annual_comparison03');
		$this->load->view('inc/footer', $data);

	}
	
	public function classes_number()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/DataCheck/ClassesNumber');
		
		$data['thispage'] = [
			'title' => 'Classes Number',
			'group' => $this->group,
			'css' => $this->group.'/report',
		];
		
		$data['form'] = $this->db->query('
		
			SELECT * FROM tbl_secondary
			WHERE is_delete = 0
			AND type = "course"
			AND active = 1
			AND branch = "'.branch_now('pid').'"
			ORDER BY FIELD (title, "K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5")
		
		')->result_array();
		
		$data['teacher'] = $this->tbl_users_model->list('teacher', branch_now('pid'));
		$month_year = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
		
		$data['result'] = [];
		
		if(isset($_GET['teacher'])) {
			$data['result'] = $this->tbl_classes_model->classes_number(branch_now('pid'), $month_year, $_GET['teacher'], $_GET['form']);
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/classes_number');
		$this->load->view('inc/footer', $data);
	}

	public function babysitter()
	{
		
		auth_must('login');
		check_module_page('Reports/Read');
		check_module_page('Reports/Modules/DataCheck/Babysitter');
		
		$data['thispage'] = [
			'title' => '保姆名单',
			'group' => $this->group,
			'css' => $this->group.'/report',
			'js'  => $this->group.'/babysitter',
		];
		
		$branch = branch_now('pid');
		
		$data['form'] = $this->db->query('
		
			SELECT * FROM tbl_secondary
			WHERE is_delete = 0
			AND type = "course"
			AND active = 1
			AND branch = "'. $branch .'"
			ORDER BY FIELD (title, "K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5")
		
		')->result_array();
		
		$data['teacher'] = $this->tbl_users_model->list('teacher', branch_now('pid'));
		
		$data['result'] = [];
		
		if(isset($_GET['teacher'])) {
			
			$sql = "SELECT form_teachers.fullname_en AS form_teacher_fullname_en, 
					form_teachers.fullname_cn AS form_teacher_fullname_cn,
					students.pid AS student_pid, students.code AS student_code, 
					students.fullname_en AS student_fullname_en, students.fullname_cn AS student_fullname_cn, 
					students.nric AS student_nric, students.date_call AS student_date_call,
					schools.title AS school_title,
					COALESCE(classes.join_class_title, '-') AS join_class_title, 
					COALESCE(parents.parent_fullname_en, '-') AS parent_fullname_en
					FROM tbl_users students
					JOIN tbl_users form_teachers ON form_teachers.pid = students.form_teacher
					AND form_teachers.type = 'teacher'
					LEFT JOIN tbl_secondary schools ON schools.pid = students.school
					LEFT JOIN tbl_secondary forms ON forms.pid = students.form
					LEFT JOIN (
						SELECT log_join.user, GROUP_CONCAT(tbl_classes.title SEPARATOR '<br>') AS join_class_title
						FROM log_join
						JOIN tbl_classes ON tbl_classes.pid = log_join.class
						WHERE log_join.is_delete = 0
						AND log_join.branch = '". $branch ."'
						AND log_join.type = 'join_class'
						AND log_join.active = 1
						GROUP BY log_join.user
					) classes ON classes.user = students.pid
					LEFT JOIN (
						SELECT log_join.user, 
						GROUP_CONCAT(CONCAT(tbl_users.fullname_en, ' (', tbl_users.phone, ')') SEPARATOR '<br>') AS parent_fullname_en
						FROM log_join
						JOIN tbl_users ON tbl_users.pid = log_join.parent
						WHERE log_join.is_delete = 0
						AND log_join.branch = '". $branch ."'
						AND log_join.type = 'join_parent'
						AND log_join.active = 1
						GROUP BY log_join.user
					) parents ON parents.user = students.pid
					WHERE students.is_delete = 0
					AND students.active = 1
					AND students.branch = '". $branch ."'
					AND students.type = 'student'
					AND students.form_teacher = '". $_GET['teacher'] ."'";
			
			if (isset($_GET['form']))
			{
				$sql .= " AND students.form = '". $_GET['form'] ."'";
			}
			
			$data['result'] = $this->db->query($sql)->result_array();
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/babysitter');
		$this->load->view('inc/footer', $data);
	}


	public function sub_stud_count($course=null,$month=null,$year=null){		// Ling HA
		auth_must('login');
		check_module_page('Reports/Read');
		$year_month = (string) $year.'-'.$month;
		$all_form = $this->subjectStudentCount(1,branch_now('pid'),null,null,null,null);
		$all_course = $this->subjectStudentCount(2,branch_now('pid'), [ 'is_hidden' => 0 ],$course,null,null);
		$all_course_remap = array_map(function($item){
			$students = $this->subjectStudentCount(3,null,null,null,$item->pid,$year_month);
			$student_name_list = array_map(function($st){
				$stud = [
					"student_id"						=> $st['id'],
					"student_name_en"				=> $st['student']['fullname_en'],
					"student_name_cn"				=> $st['student']['fullname_cn']
				];
				return $stud;
			},$students);
			$info = [
				"pid"									=> $item->pid,
				"title"								=> $item->title,
				"student_count"				=> count($students),
				"student_name_list"		=> $student_name_list
			];
			return $info;
		},$all_course);

		$data = array(
			'all_form'			=> $all_form,
			'all_course'		=> $course?$all_course_remap:[],
			'course'				=> $course,
			'year'					=> $year?$year:date('Y'),
			'month'					=> $month?$month:date('m'),
			'thispage'			=> ['title'=>'Subject Student Count','group'=>$this->group]
		);
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/subject_student_count');
		$this->load->view('inc/footer', $data);
	}
	function subjectStudentCount($type,$branch='',$search=[],$course=null,$class=null,$year_month=null){		// Ling HA
		$result = [];
		if($type==1){
			$this->db->where('is_delete', 0)
								->where('active', 1)
								->where('type', 'course')
								->where('branch', $branch)
								->order_by('FIELD(title, "K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5", "G2023")');
			$result = $this->db->get('tbl_secondary')->result();
		}
		if($type==2){
			$this->db->where('is_delete', 0)
								->where('branch', $branch)
								->where('course',$course)
								->where($search);
			$result = $this->db->get('tbl_classes')->result(); 
		}
		if($type==3){
			$result=[];
			$sqlclass_timetable = 'SELECT id FROM log_join WHERE class = ? AND type = "class_timetable" LIMIT 1';
			$class_timetable = $this->db->query($sqlclass_timetable, [$class])->row_array();
			if(!$class_timetable){
					return $result;
			}
			$sub_class = $class_timetable['id'];
			$this->load->model('tbl_users_model');
			$query = $this->db
										->select('user')
										->where('is_delete', 0)
										->where('active', 1)
										->where('class', $class)
										->where('sub_class', $sub_class)
										->where('type', 'join_class')
										->where("DATE_FORMAT(date, '%Y-%m') >= '$year_month'")
										->get('log_join')
										->result_array();
			foreach($query as $e){
					$e_student = $this->tbl_users_model->list_v2([
							'active' => 1,
							'pid' => $e['user'],
					]);
					if(count($e_student)==1){
							$e['student'] = $e_student[0];
							$result[]=$e;
					}
			}
			return $result;
		}
		return $result;
	}

}
