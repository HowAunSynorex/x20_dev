<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'payment';
		$this->single = 'payment';
		
		$this->load->model('tbl_payment_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_secondary_model');
		$this->load->model('tbl_inventory_model');
		
		$this->load->model('tbl_users_model');
		$this->load->model('log_inventory_model');
		$this->load->model('log_join_model');
		$this->load->model('log_point_model');
		$this->load->model('log_payment_model');
		$this->load->model('tbl_uploads_model');

	}

	public function test() {
		//echo new_receipt_no(branch_now('pid'));
		// echo extension_loaded('imagick') ? 'imagick extension is enabled' : 'imagick extension is not enabled';
		echo phpinfo();

	}
	
	public function list()
	{

		auth_must('login');
		check_module_page('Payment/Read');
		
		$this->load->library('pagination');

		$data['thispage'] = [
			'title' => 'All '.ucfirst($this->group),
			'group' => $this->group,
			'js' => $this->group.'/list',
		];
		
		if(isset($_POST['send_email'])) {
			
			// die('1');
			
			header('Content-type: application/json');
			
			$result = $this->tbl_payment_model->view($_POST['send_email']);
		
			if(count($result) == 1) {
				
				$result = $result[0];
				
				$email = datalist_Table('tbl_users', 'email', $result['student']);
				
				if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
					
					$response = pointoapi_Request('SynorexAPI/Email/Send', [
						'to' => $email,
						// 'api_key' => POINTO_API_KEY,
						'subject' => '['.app('title').'] '.$result['payment_no'].'\'s Invoice',
						'body' => '
						<p>Hi <b>'.datalist_Table('tbl_users', 'fullname_cn', $result['student']).' '.datalist_Table('tbl_users', 'fullname_en', $result['student']).'</b>,</p>
						<p>Click the link to view your payment invoice: <a href="'.base_url('export/pdf/'.$result['pid']).'" target="_blank">View Invoice</a></p>
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
			
			$result = $this->tbl_payment_model->view($_POST['send_sms']);
		
			if(count($result) == 1) {
				
				$result = $result[0];
				
				$phone = datalist_Table('tbl_users', 'phone', $result['student']);
				
				if(!empty($phone)) {
					
					if(substr($phone, 0, 1) != '+') $phone = '6'.$phone;

					$msg = branch_now('send_msg_sms');
					
					if(empty($msg)) {
						
						die(json_encode([ 'status' => 'required', 'message' => 'SMS content haven\'t been set']));
						
					}
					
					$msg = str_replace('%NAME%', datalist_Table('tbl_users', 'fullname_en', $result['student']), $msg);
					$msg = str_replace('%RECEIPT_NO%', $result['payment_no'], $msg);
					
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
		
		$_GET['tab'] = isset($_GET['tab']) ? $_GET['tab'] : '';
		
		$sql = '
		
			SELECT p.* FROM tbl_payment p
			INNER JOIN tbl_users u
			ON p.branch = "'.branch_now('pid').'"
			AND u.pid = p.student
		
		';
		
		if($_GET['tab'] == 'draft')
		{
			$sql .= ' AND is_draft = 1 ';
			$sql .= ' AND status != "pending" ';
		}
		else
		{
			if($_GET['tab'] == 'pending')
			{
				$sql .= ' AND status = "pending" ';
			}
			else
			{
				$sql .= ' AND status != "pending" ';
			}
			$sql .= ' AND is_draft = 0 ';
		}
		
		if(isset($_GET['student'])) {
			$sql .= ' AND p.student = "'.$_GET['student'].'"';
		}
		
		if(isset($_GET['q'])) {
			$sql .= ' AND (p.payment_no LIKE"%'.$_GET['q'].'%" OR u.fullname_en LIKE "%'.$_GET['q'].'%" OR u.fullname_cn LIKE "%'.$_GET['q'].'%")';
		}
		
		if(!isset($_GET['sort'])) $_GET['sort'] = 'desc';
		
		if($_GET['sort'] == 'asc') {
			$sql .= ' ORDER BY p.create_on ASC';
		} else {
			$sql .= ' ORDER BY p.create_on DESC';
		}
// 		echo $sql;
		
		$data['result'] = $this->db->query($sql)->result_array();
		
		if(count($data['result']) > 500) {
			
			$config['base_url'] = base_url('payment/list/');
			$config['total_rows'] = count($data['result']);
			$config['per_page'] = 100;
			$config['uri_segment'] = 3;
			$config['first_link'] = 'First Page';
			$config['last_link'] = 'Last Page';

			$this->pagination->initialize($config);
			
			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
			
			if(!empty($start)) {
				$page = $start;
			}
			
			$data['links'] = $this->pagination->create_links();
				
			$sql .= ' LIMIT ' . $page . ', ' . $config['per_page'];

			$data['result'] = $this->db->query($sql)->result_array();
			
			$data['more'] = 1;
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/list', $data);
		$this->load->view('inc/footer', $data);

	}

	public function view_landing($id)
	{

		// check token
		if(!isset($_GET['token'])) $_GET['token'] = '';
		
		$login = $this->tbl_users_model->me_token( $_GET['token'] );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			
		} else {
			
			die(app('title').': Token error');
			
		}

		$data['result'] = $this->tbl_payment_model->view($id);
		
		if( count($data['result']) == 1 ) {
			
			$data['result'] = $data['result'][0];
			
		} else {
			
			die(app('title').': Payment not found');
			
		}
		
		if( $data['result']['is_delete'] == 1) {
			
			die(app('title').': Payment not found');
			
		} else {

			$data['result2'] = $this->log_payment_model->list($data['result']['pid']);
			$data['unit'] = datalist_Table('tbl_secondary', 'title', branch_now('amount_unit'));
			$data['image'] = datalist_Table('tbl_branches', 'image', $data['result']['branch'] );

			$data['thispage'] = [
				'title' => 'View Payment',
				'group' => $this->group,
				'css' => $this->group . '/view_landing',
			];
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/view_landing', $data);
			$this->load->view('inc/footer', $data);

		}

	}
	
	// public function view_landing_pdf()
	// {
		// $data['thispage'] = [
			// 'title' => '123',
			// 'group' => $this->group,
			// 'js' => $this->group.'/view_landing_pdf',
		// ];
		
		// $this->load->view('inc/header', $data);
		// $this->load->view($this->group . '/view_landing_pdf', $data);
		// $this->load->view('inc/footer', $data);
		
	// }
	
	public function std_unpaid_landing($id)
	{

		// check token
		if(!isset($_GET['token'])) $_GET['token'] = '';
		
		$login = $this->tbl_users_model->me_token( $_GET['token'] );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			
		} else {
			
			die(app('title').': Token error');
			
		}

		$data['result'] = $this->tbl_users_model->view($id);
		
		if( count($data['result']) == 1 ) {
			
			$data['result'] = $data['result'][0];
			
		} else {
			
			die(app('title').': User not found');
			
		}
		
		if( $data['result']['is_delete'] == 1) {
			
			die(app('title').': User not found');
			
		} else {
			
			$data['thispage'] = [
				'title' => 'Student Unpaid Payment',
				'group' => $this->group,
				'css' => $this->group . '/std_unpaid_landing',
			];
			
			$data['image'] = datalist_Table('tbl_branches', 'image', $data['result']['branch'] );
			$data['currency'] = datalist_Table('tbl_branches', 'currency', $data['result']['branch'] );
			$data['currency_code'] = datalist_Table('tbl_secondary', 'currency_id', $data['currency'] );
				
			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/std_unpaid_landing', $data);
			$this->load->view('inc/footer', $data);

		}

	}
	
	public function pay_landing($id)
	{

		// check token
		if(!isset($_GET['token'])) $_GET['token'] = '';
		
		$login = $this->tbl_users_model->me_token( $_GET['token'] );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			
		} else {
			
			die(app('title').': Token error');
			
		}

		$data['result'] = $this->tbl_users_model->view($id);
		
		if( count($data['result']) == 1 ) {
			
			$data['result'] = $data['result'][0];
			
		} else {
			
			die(app('title').': User not found');
			
		}
		
		if( $data['result']['is_delete'] == 1) {
			
			die(app('title').': User not found');
			
		} else {
			
			$data['unpaid_item'] = $this->log_join_model->list('unpaid_item', $data['result']['branch'], ['user' => $id]);
			$data['join_class'] = $this->log_join_model->list('join_class', $data['result']['branch'], ['user' => $id, 'active' => 1]);
			$data['log_payment'] = $this->log_payment_model->list2(['user' => $id, 'item' => null]);
			
			$data['thispage'] = [
				'title' => 'Pay Payment',
				'group' => $this->group,
				'js' => $this->group.'/pay_landing',
			];
			
			// fpx
			if( isset($_POST['pay_pg']) ) {
				
				// print_r($response); exit;
				
				$this->session->set_userdata('temp_branch', $data['result']['branch'] );
				$this->session->set_userdata('temp_user_id', $id );
				$this->session->set_userdata('pay_by', $login['pid'] );
				
				$response = pointoapi_Request('SynorexAPI/Payment/New', [
					'method' => datalist_Table('tbl_branches', 'gateway_'.$this->input->post('gateway').'_pg', $data['result']['branch']),
					// 'method' => 'senangpay_sandbox',
					'amount' => $this->input->post('total_unpaid'),
					'title' => 'Pay Invoice',
					'name' => $data['result']['fullname_en'],
					'email' => $data['result']['email'],
					'remark' => '',
					'phone' => $data['result']['phone'],
					'id' => time(),
					
					'api_key' => datalist_Table('tbl_branches', 'pointoapi_key', $data['result']['branch'])
				]);
				
				// print_r($response); exit;
				
				if($response['status'] == 'ok') {
					// print_r($response); exit;
					redirect($response['redirect']);
					
				} else {
					
					alert_new('danger', 'Payment API error');
					
					header('refresh: 0'); exit;
					
				}
				
			}
			
			
			// transfer
			if( isset($_POST['pay_transfer']) ) {
				
				$image = null;
				
				if(isset($_FILES['receipt'])) {

					$target_dir = "uploads/data/";

					if ($_FILES['receipt']['size'] != 0) {

						$temp = explode(".", $_FILES["receipt"]["name"]);
						$newfilename = get_new('id') . '.' . end($temp);
						$target_file = $target_dir . $newfilename;
						$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
						move_uploaded_file($_FILES["receipt"]["tmp_name"], $target_file);

						$file_size = $_FILES["receipt"]["size"];
						$image_data['file_name'] = $_FILES["receipt"]["name"];
						$image_data['file_type'] = $fileType;
						$image_data['file_size'] = $file_size;
						$image_data['file_source'] = base_url($target_file);
						$image_data['create_by'] = auth_data('pid');
						$image_data['type'] = 'image_item';
						$image = $this->tbl_uploads_model->add($image_data);
						
					}
				
					$this->session->set_userdata('temp_branch', $data['result']['branch'] );
					$this->session->set_userdata('temp_user_id', $id );
					$this->session->set_userdata('pay_by', $login['pid'] );
					
					// pay_by
					$pay_by = $this->session->userdata('pay_by');
					
					// start create invoice
					$log_payment = $this->log_payment_model->list2(['user' => $this->session->userdata('temp_user_id'), 'item' => null]);
					
					// create new payment
					$std_unpaid_result = std_unpaid_result($this->session->userdata('temp_user_id'));
		
					$tax = ($std_unpaid_result['subtotal'] * floatval(datalist_Table('tbl_branches', 'tax', $this->session->userdata('temp_branch'))) / 100);
					
					$new_payment_id = $this->tbl_payment_model->add([
						'status'			=> 'pending',
						'branch'			=> $this->session->userdata('temp_branch'),
						'student'			=> $this->session->userdata('temp_user_id'),
						'payment_no'		=> new_receipt_no( $this->session->userdata('temp_branch') ),
						'payment_method'	=> '162710432427', // Online Transfer
						'date'				=> date('Y-m-d'),
						'subtotal'			=> $std_unpaid_result['subtotal'],
						'tax'				=> $tax,
						'total'				=> $std_unpaid_result['subtotal'] + $tax,
						'discount_type'		=> '$',
						'discount'			=> $std_unpaid_result['discount'],
						'create_by'			=> $pay_by,
					]);
					
					//create log_payment
					if(isset($std_unpaid_result['result']['class'])) {
							
						foreach($std_unpaid_result['result']['class'] as $e) {
							
							$this->log_payment_model->add([
								'payment' 		=> $new_payment_id,
								'user' 			=> $this->session->userdata('temp_user_id'),
								'title'			=> isset($e['period']) ? '['.$e['period'].'] '.$e['title'] : '',
								'class' 		=> $e['class'],
								'period' 		=> isset($e['period']) ? $e['period'] : '',
								'qty' 			=> $e['qty'],
								'price_unit'	=> $e['amount'],
								'price_amount' 	=> $e['amount'],
								'dis_amount' 	=> $e['discount'],
								'create_by' 	=> $pay_by,
							]);
							
							if(datalist_Table('tbl_classes', 'type', $e['class']) == 'check_in') {
								$this->log_point_model->add([
									'type'			=> 'class_credit',
									'user'			=> $this->session->userdata('temp_user_id'),
									'class'			=> $e['class'],
									'amount_1'		=> $e['qty'] * datalist_Table('tbl_classes', 'credit', $e['class']),
									'create_by'		=> auth_data('pid')
								]);
							}

						}

					}
					
					if(isset($std_unpaid_result['result']['item'])) {
							
						foreach($std_unpaid_result['result']['item'] as $e) {
							
							$this->log_payment_model->add([
								'payment' 			=> $new_payment_id,
								'user' 				=> $this->session->userdata('temp_user_id'),
								'title' 			=> $e['title'],
								'item'				=> $e['item'],
								'qty'				=> $e['qty'],
								'movement' 			=> datalist_Table('log_join', 'movement', $e['id'], 'id'),
								'movement_log' 		=> datalist_Table('log_join', 'movement_log', $e['id'], 'id'),
								'price_unit' 		=> datalist_Table('tbl_inventory', 'price_sale', $e['item']),
								'price_amount'		=> $e['amount'],
								'dis_amount' 		=> $e['discount'],
								'create_by'			=> $pay_by,
							]);
							
						}
						
					}
					
					if(isset($std_unpaid_result['result']['service'])) {
							
						foreach($std_unpaid_result['result']['service'] as $e) {
							
							$this->log_payment_model->add([
								'payment' 			=> $new_payment_id,
								'user' 				=> $this->session->userdata('temp_user_id'),
								'title'				=> isset($e['period']) ? '['.$e['period'].'] '.$e['title'] : '',
								'item'				=> $e['item'],
								'qty'				=> $e['qty'],
								'movement' 			=> datalist_Table('log_join', 'movement', $e['id'], 'id'),
								'movement_log' 		=> datalist_Table('log_join', 'movement_log', $e['id'], 'id'),
								'price_unit' 		=> $e['amount'],
								'price_amount'		=> $e['amount'],
								'dis_amount' 		=> $e['discount'],
								'create_by'			=> $pay_by,
							]);
							
						}
						
					}
					
					// delete all unpaid items
					$std_unpaid_result = std_unpaid_result( $this->session->userdata('temp_user_id'));

					if($std_unpaid_result['count'] > 0) {
						if(isset($std_unpaid_result['result']['class'])) {
							foreach($std_unpaid_result['result']['class'] as $e) {
								$unpaid_data['is_delete'] = 1;
								$unpaid_data['payment'] = $new_payment_id;
								$this->log_join_model->edit($e['id'], $unpaid_data);
							}
						}
						if(isset($std_unpaid_result['result']['item'])) {
							foreach($std_unpaid_result['result']['item'] as $e) {
								$unpaid_data['is_delete'] = 1;
								$unpaid_data['payment'] = $new_payment_id;
								$this->log_join_model->edit($e['id'], $unpaid_data);
							}
						}
						if(isset($std_unpaid_result['result']['service'])) {
							foreach($std_unpaid_result['result']['service'] as $e) {
								$unpaid_data['is_delete'] = 1;
								$unpaid_data['payment'] = $new_payment_id;
								$this->log_join_model->edit($e['id'], $unpaid_data);
							}
						}
					}
				
				
					$this->tbl_payment_model->edit($new_payment_id, [
						'receipt' => $image
					]); 
					// end create invoice
					
					alert_new('success', 'Receipt Uploaded.');
					
					header('refresh: 0'); exit;
				}
				else {
					
					alert_new('danger', 'Payment API error');
					
					header('refresh: 0'); exit;
					
				}
			}
			
			$data['currency'] = datalist_Table('tbl_branches', 'currency', $data['result']['branch'] );
			$data['currency_code'] = datalist_Table('tbl_secondary', 'currency_id', $data['currency'] );
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/pay_landing', $data);
			$this->load->view('inc/footer', $data);

		}

	}

	public function pay_status_landing()
	{
		
		// echo $_GET['status'] . $_GET['id'];
		// exit;
		
		// print_r($response); exit;
		
		if( isset($_GET['status']) && isset($_GET['id']) ) {
			
			$response = pointoapi_Request('SynorexAPI/Payment/Check', [
				'id' => $_GET['id'],
				'api_key' => datalist_Table('tbl_branches', 'pointoapi_key', $this->session->userdata('temp_branch') )
			]);
			// echo '<pre>'; print_r($response); exit;
			
			if(!isset($response['status'])) $response['status'] = 'error';
			
			if( $response['status'] == 'ok' && isset($response['result']) ) {
				
				if($response['result']['status'] == 'approved') {
					
					// echo '<pre>'; print_r($response); exit;
					
					// pay_by
					$pay_by = $this->session->userdata('pay_by');
					
					// start create invoice
					$log_payment = $this->log_payment_model->list2(['user' => $this->session->userdata('temp_user_id'), 'item' => null]);
					
					
					// create new payment
					
					// $subtotal = 0;
					
					$std_unpaid_result = std_unpaid_result($this->session->userdata('temp_user_id'), $this->session->userdata('temp_branch'));
					
					//cal subtotal
					/* if($std_unpaid_result['count'] > 0) {
						
						if(isset($std_unpaid_result['result']['class'])) {
							
							foreach($std_unpaid_result['result']['class'] as $e) {
								
								$subtotal += $e['amount'];
								
							}
							
						}
						
						if(isset($std_unpaid_result['result']['item'])) {
							
							foreach($std_unpaid_result['result']['item'] as $e) {
								
								$subtotal += $e['amount'];
								
							}
							
						}
						
					} */
					
					$tax = ($std_unpaid_result['subtotal'] * floatval(datalist_Table('tbl_branches', 'tax', $this->session->userdata('temp_branch'))) / 100);
					
					$new_payment_id = $this->tbl_payment_model->add([
						'status'			=> 'paid',
						'branch'			=> $this->session->userdata('temp_branch'),
						'student'			=> $this->session->userdata('temp_user_id'),
						'payment_no'		=> new_receipt_no( $this->session->userdata('temp_branch') ),
						'payment_method'	=> '164623458519', // senangpay
						// 'payment_method'	=> 'null', // senangpay
						'date'				=> date('Y-m-d'),
						'subtotal'			=> $std_unpaid_result['subtotal'],
						'tax'				=> $tax,
						'total'				=> $std_unpaid_result['subtotal'] + $tax,
						'discount_type'		=> '$',
						'discount'			=> $std_unpaid_result['discount'],
						'create_by'			=> $pay_by,
					]);
					
					// debug
					// echo '<a href="">Refresh</a>';
					// echo '<pre>'; print_r($post_data); exit;
					
					//create log_payment
					if(isset($std_unpaid_result['result']['class'])) {
							
						foreach($std_unpaid_result['result']['class'] as $e) {
							
							$this->log_payment_model->add([
								'payment' 		=> $new_payment_id,
								'user' 			=> $this->session->userdata('temp_user_id'),
								'title'			=> isset($e['period']) ? '['.$e['period'].'] '.$e['title'] : '',
								'class' 		=> $e['class'],
								'period' 		=> isset($e['period']) ? $e['period'] : '',
								'qty' 			=> $e['qty'],
								'price_unit'	=> $e['amount'],
								'price_amount' 	=> $e['amount'],
								'dis_amount' 	=> $e['discount'],
								'create_by' 	=> $pay_by,
							]);
							
							if(datalist_Table('tbl_classes', 'type', $e['class']) == 'check_in') {
								$this->log_point_model->add([
									'type'			=> 'class_credit',
									'user'			=> $this->session->userdata('temp_user_id'),
									'class'			=> $e['class'],
									'amount_1'		=> $e['qty'] * datalist_Table('tbl_classes', 'credit', $e['class']),
									'create_by'		=> auth_data('pid')
								]);
							}

						}

					}
					
					if(isset($std_unpaid_result['result']['item'])) {
							
						foreach($std_unpaid_result['result']['item'] as $e) {
							
							$this->log_payment_model->add([
								'payment' 			=> $new_payment_id,
								'user' 				=> $this->session->userdata('temp_user_id'),
								'title' 			=> $e['title'],
								'item'				=> $e['item'],
								'qty'				=> $e['qty'],
								'movement' 			=> datalist_Table('log_join', 'movement', $e['id'], 'id'),
								'movement_log' 		=> datalist_Table('log_join', 'movement_log', $e['id'], 'id'),
								'price_unit' 		=> datalist_Table('tbl_inventory', 'price_sale', $e['item']),
								'price_amount'		=> $e['amount'],
								'dis_amount' 		=> $e['discount'],
								'create_by'			=> $pay_by,
							]);
							
						}
						
					}
					if(isset($std_unpaid_result['result']['service'])) {
							
						foreach($std_unpaid_result['result']['service'] as $e) {
							
							$this->log_payment_model->add([
								'payment' 			=> $new_payment_id,
								'user' 				=> $this->session->userdata('temp_user_id'),
								'title' 			=> $e['title'],
								'item'				=> $e['service'],
								'qty'				=> $e['qty'],
								'movement' 			=> datalist_Table('log_join', 'movement', $e['id'], 'id'),
								'movement_log' 		=> datalist_Table('log_join', 'movement_log', $e['id'], 'id'),
								'price_unit' 		=> datalist_Table('tbl_inventory', 'price_sale', $e['service']),
								'price_amount'		=> $e['amount'],
								'dis_amount' 		=> $e['discount'],
								'create_by'			=> $pay_by,
							]);
							
						}
						
					}
					
					// delete all unpaid items
					$std_unpaid_result = std_unpaid_result( $this->session->userdata('temp_user_id'), $this->session->userdata('temp_branch') );
					
					// debug
					// echo '<a href="">Refresh</a>';
					// echo '<pre>'; print_r($std_unpaid_result); exit;
					
					if($std_unpaid_result['count'] > 0) {
						if(isset($std_unpaid_result['result']['item'])) {
							foreach($std_unpaid_result['result']['item'] as $e) {
								$unpaid_data['is_delete'] = 1;
								$unpaid_data['payment'] = $new_payment_id;
								$this->log_join_model->edit($e['id'], $unpaid_data);
							}
						}
					}
					
					// end create invoice
					
					$data['isPaid'] = 1;
					$data['payment_callback'] = $response['result'];
					
				}
				
			}
			
			$data['thispage'] = [
				'title' => 'Pay Payment',
				'group' => $this->group,
				// 'js' => $this->group.'/pay_landing',
			];
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/pay_status_landing', $data);
			$this->load->view('inc/footer', $data);

		} else {
			
			die(app('title').': Param error');
			
		}

	}

	public function add($id = '')
	{
		auth_must('login');
		check_module_page('Payment/Create');

		$_GET['doc_type'] = isset($_GET['doc_type']) ? $_GET['doc_type'] : '';
		
		if(branch_now('version') == 'shushi') redirect($this->group . '/add2/' . $id);

		$this->load->model('tbl_users_model');
		
		if( 1==2 ) {
// 		if(!empty($id) && !$this->tbl_users_model->check_user($id)) {

			alert_new('warning', 'Data not found');

			redirect($this->group);

		} else {
			
			if(isset($_POST['save']) || isset($_POST['save_draft'])) {
				
				if (isset($_POST['save_draft']))
				{
					$payment_no = new_receipt_no('', 'draft');
					$is_draft = 1;
				}
				else
				{
					$payment_no = new_receipt_no('', '');
					$is_draft = 0;
				}
				
				if(!$this->tbl_payment_model->check_payment_no( $payment_no, '', branch_now('pid'), $is_draft )) {

					alert_new('warning', 'Payment no has been taken');
					
				} else {

					$post_data = [];
					
					foreach([ 'student', 'payment_method', 'date', 'remark', 'subtotal', 'discount', 'discount_type', 'adjust', 'adjust_label', 'material_fee','transport_fee','childcare_fee','total', 'tax','receive' ] as $e) {
						
						$post_data[$e] = $this->input->post($e);
						
					}

					$post_data['is_draft'] = $is_draft;
					$post_data['payment_no'] = $payment_no;
					$post_data['status'] = 'paid';
					$post_data['branch'] = branch_now('pid');
					$post_data['create_by'] = auth_data('pid');
					
					// create new payment
					$new_payment_id = $this->tbl_payment_model->add($post_data);
					
					if(isset($_POST['ewallet'])) {
						
						$this->log_point_model->add([
							'type'		=> 'ewallet',
							'amount_0'	=> abs($this->input->post('adjust')),
							'payment'	=> $new_payment_id,
							'user'		=> $id,
							'title'		=> 'Payment',
							'create_by'	=> auth_data('pid'),
						]);
						
					}
					
					// if(!isset($_POST['item'])) $_POST['item'] = [];
					
					$stock_adjust = [];

					// check unpaid item
					if(isset($_POST['unpaid'])) {
						foreach($_POST['unpaid'] as $e) {
							
							if (isset($e['selected'])) {
								
								$unpaid_data['is_delete'] = 1;
								$unpaid_data['payment'] = $new_payment_id;
								$this->log_join_model->edit($e['id'], $unpaid_data);
								
								if($this->input->post('discount_type') == '%') {
									$discount = $e['price_unit'] * $this->input->post('discount') / 100;
								} else {
									$discount = $this->input->post('discount');
								}
								
								$this->log_payment_model->add([
									'payment' => $new_payment_id,
									'user' => $this->input->post('student'),
									'title' => $e['title'],
									'item' => $e['item'],
									'movement' => $e['movement'],
									'movement_log' => $e['movement_log'],
									'remark' => $e['remark'],
									'qty' => $e['qty'],
									'dis_amount' => $e['dis_amount'],
									'dis_unit' => $this->input->post('discount_type'),
									'price_unit' => $e['price_unit'],
									'price_amount' => $e['amount'],
									'create_by' => auth_data('pid'),
								]);
							}
						}
					}
					
					// echo '<pre>'; print_r($_POST['item']);
					
					if(isset($_POST['item'])) {

						// loop items into stock adjust array
						foreach($_POST['item'] as $e) {
							
							if (isset($e['selected'])) {
								if ($e['type'] == 'item') {
									
									$stock_adjust[] = [
										'title' => $e['title'],
										'item' => $e['item'],
										'remark' => $e['remark'],
										'qty' => $e['qty'],
										'dis_amount' => $e['dis_amount'],
										'price_unit' => $e['price_unit'],
										'amount' => $e['amount'],
									];
									
								} else if ($e['type'] == 'class') {
									
									if($this->input->post('discount_type') == '%') {
										$discount = $e['price_unit'] * $this->input->post('discount') / 100;
									} else {
										$discount = $this->input->post('discount');
									}
									
									// create log payment for class
									$this->log_payment_model->add([
										'payment' => $new_payment_id,
										'user' => $this->input->post('student'),
										'title' => $e['title'],
										'class' => $e['item'],
										'period' => $e['period'],
										'remark' => $e['remark'],
										'qty' => $e['qty'],
										'dis_amount' => $e['dis_amount'],
										'dis_unit' => $this->input->post('discount_type'),
										'price_unit' => $e['price_unit'],
										'price_amount' => $e['amount'],
										'create_by' => auth_data('pid'),
										
									]);
									
								} else {
									
									if($this->input->post('discount_type') == '%') {
										$discount = $e['price_unit'] * $this->input->post('discount') / 100;
									} else {
										$discount = $this->input->post('discount');
									}
									
									// create log payment for class
									$this->log_payment_model->add([
										'payment' => $new_payment_id,
										'user' => $this->input->post('student'),
										'title' => $e['title'],
										'item' => $e['item'],
										'period' => $e['period'],
										'remark' => $e['remark'],
										'qty' => $e['qty'],
										'dis_amount' => $e['dis_amount'],
										'dis_unit' => $this->input->post('discount_type'),
										'price_unit' => $e['price_unit'],
										'price_amount' => $e['amount'],
										'create_by' => auth_data('pid'),
										
									]);
									
								}
							}
						}
						
						if(!empty($stock_adjust)) {
							
							$new_id = null;
							
							if(datalist_Table('tbl_inventory', 'stock_ctrl', $e['item']) == 1) {
					
								$new_id = $this->tbl_inventory_model->add([
									'date' => $this->input->post('date'),
									'branch' => branch_now('pid'),
									'title' => 'Item purchase from '.$post_data['payment_no'],
									'type' => 'movement',
									'create_by' => auth_data('pid'),
								]);
							
							}
							
							foreach($stock_adjust as $e) {
								
								if($this->input->post('discount_type') == '%') {
									$discount = $e['price_unit'] * $this->input->post('discount') / 100;
								} else {
									$discount = $this->input->post('discount');
								}
								
								$log_id = null;
								
								if(datalist_Table('tbl_inventory', 'stock_ctrl', $e['item']) == 1) {
									$log_id = $this->log_inventory_model->add([
										'branch' => branch_now('pid'),
										'inventory' => $new_id,
										'item' => $e['item'],
										'qty_in' => '0',
										'qty_out' => $e['qty'],
										'create_by' => auth_data('pid'),
									]);
									$log_id = $log_id[0]['id'];
								}
									
								$this->log_payment_model->add([
									'payment' => $new_payment_id,
									'user' => $this->input->post('student'),
									'title' => $e['title'],
									'item' => $e['item'],
									'movement' => $new_id,
									'movement_log' => $log_id,
									'remark' => $e['remark'],
									'qty' => $e['qty'],
									'dis_amount' => $e['dis_amount'],
									'dis_unit' => $this->input->post('discount_type'),
									'price_unit' => $e['price_unit'],
									'price_amount' => $e['amount'],
									'create_by' => auth_data('pid'),
								]);
								
							}
							
						}
						
					}
					
					alert_new('success', ucfirst($this->single).' created successfully');
					redirect($this->group . '/list');

				}
				
				header('refresh: 0'); exit;
				
			}

			$data['id'] = $id;
			// $data['items'] = $this->tbl_inventory_model->list( branch_now('pid'), 'item' );
			$data['item_cat'] = $this->tbl_secondary_model->active_list('item_cat', branch_now('pid'));
			$data['package'] = $this->tbl_secondary_model->active_list('package', branch_now('pid'));
			$data['classes'] = $this->tbl_classes_model->list(branch_now('pid'), [ 'active' => 1, 'is_hidden' => 0 ]);
			$data['services'] = $this->tbl_inventory_model->list( branch_now('pid'), 'item', ['item_type' => 'service'] );
			
			$branch_payment = [];
			foreach($this->log_join_model->list('secondary', branch_now('pid'), ['active' => 1]) as $e) {
				if(datalist_Table('tbl_secondary', 'type', $e['secondary']) == 'payment_method') {
					$branch_payment[] = $e;
				}
			}
			
			$data['payment_all'] = $branch_payment;
			$data['payment_now'] = $this->tbl_secondary_model->list('payment_method', branch_now('pid'), [ 'active' => 1 ]);
			
			$data['class_bundle'] = $this->tbl_secondary_model->list('class_bundle', branch_now('pid'), [ 'active' => 1 ]);
			
			$data['thispage'] = [
				'title' => 'Add '.ucfirst($this->single) . ($_GET['doc_type'] == 'draft' ? ' (Draft)' : ''),
				'group' => $this->group,
				'js' => $this->group.'/add',
			];
			
			if(isset($_GET['class'])) {
				// Tan Jing Suan
				// $data['student'] = $this->log_join_model->list('join_class', branch_now('pid'), [ 'class' => $_GET['class'], 'active' => 1 ]);
				$data['student'] = $this->log_join_model->selectlist('join_class', branch_now('pid'), [ 'class' => $_GET['class'], 'active' => 1 ]);
			} else {
				// $data['student'] = $student = $this->tbl_users_model->list('student', branch_now('pid'), [ 'active' => 1 ]);
				$data['student'] = $student = $this->tbl_users_model->list('student', branch_now('pid'), [  ]);
			}


			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/add');
			$this->load->view('inc/footer', $data);

		}

	}	
	
	// public function json_add($id = '')
	// {
		
		// $_GET['doc_type'] = isset($_GET['doc_type']) ? $_GET['doc_type'] : '';
		
		// if(branch_now('version') == 'shushi') redirect($this->group . '/add2/' . $id);

		// $this->load->model('tbl_users_model');
		
		// $data['id'] = $id;
		// $data['items'] = $this->tbl_inventory_model->list( branch_now('pid'), 'item' );
		// $data['item_cat'] = $this->tbl_secondary_model->active_list('item_cat', branch_now('pid'));
		// $data['package'] = $this->tbl_secondary_model->active_list('package', branch_now('pid'));
		// $data['classes'] = $this->tbl_classes_model->list(branch_now('pid'), [ 'active' => 1, 'is_hidden' => 0 ]);
		// $data['services'] = $this->tbl_inventory_model->list( branch_now('pid'), 'item', ['item_type' => 'service'] );
		
		// $branch_payment = [];
		// foreach($this->log_join_model->list('secondary', branch_now('pid'), ['active' => 1]) as $e) {
			// if(datalist_Table('tbl_secondary', 'type', $e['secondary']) == 'payment_method') {
				// $branch_payment[] = $e;
			// }
		// }
		
		// $data['payment_all'] = $branch_payment;
		// $data['payment_now'] = $this->tbl_secondary_model->list('payment_method', branch_now('pid'), [ 'active' => 1 ]);
		
		// $data['class_bundle'] = $this->tbl_secondary_model->list('class_bundle', branch_now('pid'), [ 'active' => 1 ]);
		
		// $data['thispage'] = [
			// 'title' => 'Add '.ucfirst($this->single) . ($_GET['doc_type'] == 'draft' ? ' (Draft)' : ''),
			// 'group' => $this->group,
			// 'js' => $this->group.'/add',
		// ];
		
		// if(isset($_GET['class'])) {
			// $data['student'] = $this->log_join_model->list('join_class', branch_now('pid'), [ 'class' => $_GET['class'], 'active' => 1 ]);
		// } else {
			// $data['student'] = $student = $this->tbl_users_model->list('student', branch_now('pid'), [ 'active' => 1 ]);
			// $data['student'] = $student = $this->tbl_users_model->list('student', branch_now('pid'), [  ]);
		// }
		
		// view function
		// $student_data = $this->tbl_users_model->view($id)[0];
		// $std_unpaid_result = std_unpaid_result2($id);
		// $std_default_result = std_default_result($id);


		// $new_unpaid_class = [];
		// $material_fee = [];
		// $subsidy_fee = [];
		// $with_class_bundle = [];
		// $without_class_bundle = [];

		// $default_material_fee = [];
		// $default_subsidy_fee = [];
		// $with_default_class_bundle = [];
		// $without_default_class_bundle = [];

		// $total_material_fee = 0;
		// $total_subsidy_fee = 0;

		// $total_default_subsidy_fee = 0;

		// get default class data (not check payment make or not)
		// if (isset($std_default_result['result']['default_class']))
		// {
			// foreach ($std_default_result['result']['default_class'] as $e)
			// {
				// $default_class_bunlde = $this->log_join_model->list('class_bundle_course', branch_now('pid'), [
					// 'course' => datalist_Table('tbl_classes', 'course', $e['class'])
				// ]);

			   // print_array($default_class_bunlde);
				// if (count($default_class_bunlde) > 0)
				// {
					// $default_class_bunlde = $default_class_bunlde[0];
					// $with_default_class_bundle[$default_class_bunlde['parent']]['data'][] = $e;
				// }
				// else
				// {
					// $without_default_class_bundle[]['data'] = $e;
				// }
			// }


			// foreach ($with_default_class_bundle as $k => $v)
			// {
				// $check_bundle_price = $this->log_join_model->list('class_bundle_price', branch_now('pid'), [
					// 'parent' => $k,
					// 'qty' => count($v['data'])
				// ]);

				// /*
				// echo branch_now('pid');
				// echo '---'.$k;
				// echo '---'. count($v['data']);
				// print_array($check_bundle_price);
				// die;
				// */
				// if (count($check_bundle_price) > 0)
				// {
					// $check_bundle_price = $check_bundle_price[0];
					// $each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
					// $default_material_fee[] = [
						// 'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
						// 'fee' => $check_bundle_price['material']
					// ];

					// foreach ($v['data'] as $k2 => $v2)
					// {
						// $class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
						// $with_default_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
						// $with_default_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
						// $with_default_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
					// }
				// }
				// else
				// {
					// $check_bundle_price = $this->db->query('
						// SELECT * FROM log_join
						// WHERE is_delete = 0
						// AND type = "class_bundle_price"
						// AND branch = "' . branch_now('pid') . '"
						// AND parent = "' . $k . '"
						// AND qty < ' . count($v['data']) . '
						// ORDER BY qty DESC
					// ')->result_array();
					// if (count($check_bundle_price) > 0)
					// {
						// $check_bundle_price = $check_bundle_price[0];
						// $each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
						// for ($i = 0; $i < floor(count($v['data']) / $check_bundle_price['qty']); $i++)
						// {
							// $default_material_fee[] = [
								// 'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
								// 'fee' => $check_bundle_price['material']
							// ];
						// }
						// foreach ($v['data'] as $k2 => $v2)
						// {
							// if ($k2 >= $check_bundle_price['qty'] * floor(count($v['data']) / $check_bundle_price['qty']))
							// {
								// $without_default_class_bundle[0]['data'][] = $v2;
								// unset($with_default_class_bundle[$k]['data'][$k2]);
							// }
							// else
							// {
								// $class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
								// $with_default_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
								// $with_default_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
								// $with_default_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
							// }
						// }
					// }
				// }
			// }


			// foreach ($with_default_class_bundle as $k => $v)
			// {
				// get actual class qty , array is combine with multi outstanding month now
				// $actual_class_lists = array();
				// foreach ($v['data'] as $v_row)
				// {
					// $actual_class_lists[$v_row['id']] = $v_row;
				// }
				// $actual_class_count = count($actual_class_lists);

				// $bundle_subsidy = $this->db->query('
						// SELECT * FROM log_join
						// WHERE is_delete = 0
						// AND type = "class_bundle_price"
						// AND branch = "' . branch_now('pid') . '"
						// AND parent = "' . $k . '"
						// AND subsidy > 0
						// AND qty <= ' . $actual_class_count . '
						// ORDER BY qty DESC
					// ')->result_array();


				// if (count($bundle_subsidy) > 0)
				// {
					// $bundle_subsidy = $bundle_subsidy[0];
					// for ($i = 0; $i < floor(count($v['data']) / $check_bundle_price['qty']); $i++)
					// {

						// $default_subsidy_fee[] = [
							// 'title' => 'Subsidy [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
							// 'fee' => $bundle_subsidy['subsidy']
						// ];

					// }

					// foreach ($v['data'] as $k2 => $v2)
					// {
						// $with_default_class_bundle[$k]['data'][$k2]['subsidy_fee'] = $bundle_subsidy['subsidy'];
					// }
				// }
			// }

			// foreach ($default_subsidy_fee as $e)
			// {
				// $total_default_subsidy_fee += $e['fee'];
			// }
		// }

		// $new_default_unpaid_class = array_merge($with_default_class_bundle, $without_default_class_bundle);


		// if ($std_unpaid_result['count'] > 0)
		// {

			// if (isset($std_unpaid_result['result']['class']))
			// {

				// foreach ($std_unpaid_result['result']['class'] as $e)
				// {
					// $check_class_bunlde = $this->log_join_model->list('class_bundle_course', branch_now('pid'), [
						// 'course' => datalist_Table('tbl_classes', 'course', $e['class'])
					// ]);
					// if (count($check_class_bunlde) > 0)
					// {
						// $check_class_bunlde = $check_class_bunlde[0];
						// $with_class_bundle[$check_class_bunlde['parent']]['data'][] = $e;
					// }
					// else
					// {
						// $without_class_bundle[]['data'] = $e;
					// }
				// }


				// foreach ($with_class_bundle as $k => $v)
				// {

					// get actual class qty , array is combine with multi outstanding month now
					// $actual_class_lists = array();
					// foreach ($v['data'] as $v_row)
					// {
						// $actual_class_lists[$v_row['id']] = $v_row;
					// }
					// $actual_class_count = count($actual_class_lists);
					// $actual_material_count = count($v['data']) / $actual_class_count;
					// $actual_subsidy_count = count($v['data']) / $actual_class_count;

					// $check_bundle_price = $this->log_join_model->list('class_bundle_price', branch_now('pid'), [
						// 'parent' => $k,
						// 'qty' => $actual_class_count
					// ]);

					// if (count($check_bundle_price) > 0)
					// {
						// $check_bundle_price = $check_bundle_price[0];
						// $each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];

						// for ($a = 0; $a < $actual_material_count; $a++)
						// {
							// $material_fee[] = [
								// 'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
								// 'fee' => $check_bundle_price['material']
							// ];
						// }

						// foreach ($v['data'] as $k2 => $v2)
						// {
							// $class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
							// $with_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
							// $with_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
							// $with_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
							// $with_class_bundle[$k]['data'][$k2]['material_fee'] = $check_bundle_price['material'];
						// }


					// }
					// else
					// {
						// $check_bundle_price = $this->db->query('
							// SELECT * FROM log_join
							// WHERE is_delete = 0
							// AND type = "class_bundle_price"
							// AND branch = "' . branch_now('pid') . '"
							// AND parent = "' . $k . '"
							// AND qty < ' . $actual_class_count . '
							// ORDER BY qty DESC
						// ')->result_array();
						// if (count($check_bundle_price) > 0)
						// {
							// $check_bundle_price = $check_bundle_price[0];
							// $each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
							// for ($i = 0; $i < floor($actual_class_count / $check_bundle_price['qty']); $i++)
							// {

								// for ($a = 0; $a < $actual_material_count; $a++)
								// {
									// $material_fee[] = [
										// 'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
										// 'fee' => $check_bundle_price['material']
									// ];
								// }
							// }

							// foreach ($v['data'] as $k2 => $v2)
							// {
								// if ($k2 >= $check_bundle_price['qty'] * floor($actual_class_count / $check_bundle_price['qty']))
								// {
									// $without_class_bundle[0]['data'][] = $v2;
									// unset($with_class_bundle[$k]['data'][$k2]);
								// }
								// else
								// {
									// $class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
									// $with_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
									// $with_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
									// $with_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
									// $with_class_bundle[$k]['data'][$k2]['material_fee'] = $check_bundle_price['material'];

								// }
							// }
						// }
					// }
				// }


				// foreach ($with_class_bundle as $k => $v)
				// {
					// get actual class qty , array is combine with multi outstanding month now
					// $actual_class_lists = array();
					// foreach ($v['data'] as $v_row)
					// {
						// $actual_class_lists[$v_row['id']] = $v_row;
					// }
					// $actual_class_count = count($actual_class_lists);

					// $bundle_subsidy = $this->db->query('
						// SELECT * FROM log_join
						// WHERE is_delete = 0
						// AND type = "class_bundle_price"
						// AND branch = "' . branch_now('pid') . '"
						// AND parent = "' . $k . '"
						// AND subsidy > 0
						// AND qty <= ' . $actual_class_count . '
						// ORDER BY qty DESC
					// ')->result_array();


					// if (count($bundle_subsidy) > 0)
					// {
						// $bundle_subsidy = $bundle_subsidy[0];
						// for ($i = 0; $i < floor($actual_class_count / $bundle_subsidy['qty']); $i++)
						// {
							// for ($a = 0; $a < $actual_subsidy_count; $a++)
							// {
								// $subsidy_fee[] = [
									// 'title' => 'Subsidy [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
									// 'fee' => $bundle_subsidy['subsidy']
								// ];
							// }
						// }

						// foreach ($v['data'] as $k2 => $v2)
						// {
							// $with_class_bundle[$k]['data'][$k2]['subsidy_fee'] = $bundle_subsidy['subsidy'];
						// }
					// }
				// }

				// foreach ($material_fee as $e)
				// {
					// $total_material_fee += $e['fee'];
				// }

				// foreach ($subsidy_fee as $e)
				// {
					// $total_subsidy_fee += $e['fee'];
				// }
			// }
		// }
		
		// $res = [
			// 'unpaid_class' 				=> $new_default_unpaid_class,
			// 'student_data' 				=> $student_data,
			// 'default_material_fee' 		=> $default_material_fee,
			// 'total_default_discount' 	=> $total_default_discount,
			// 'default_subsidy_fee' 		=> $default_subsidy_fee,
		// ];
		
		// die(json_encode([ 'status' => 'ok', 'result' => $res ]));


	// }
	
	public function add2($id = '')
	{
	
		auth_must('login');
		check_module_page('Payment/Create');

		$this->load->model('tbl_users_model');
		
		if(!empty($id) && !$this->tbl_users_model->check_user($id)) {

			alert_new('warning', 'Data not found');

			redirect($this->group);

		} else {
			if(isset($_POST['save'])) {

				if(!$this->tbl_payment_model->check_payment_no( $this->input->post('payment_no'), '', branch_now('pid') )) {

					alert_new('warning', 'Payment no has been taken');
					
				} else {

					$post_data = [];
					
					foreach([ 'student', 'payment_no', 'payment_method', 'date', 'remark', 'subtotal', 'discount', 'discount_type', 'adjust', 'adjust_label', 'total', 'tax'] as $e) {
						
						$post_data[$e] = $this->input->post($e);
						
					}	
					
					$post_data['status'] = 'paid';
					$post_data['branch'] = branch_now('pid');
					$post_data['create_by'] = auth_data('pid');
					
					// create new payment
					$new_payment_id = $this->tbl_payment_model->add($post_data);
					
					if(isset($_POST['ewallet'])) {
						
						$this->log_point_model->add([
							'type'		=> 'ewallet',
							'amount_0'	=> abs($this->input->post('adjust')),
							'payment'	=> $new_payment_id,
							'user'		=> $id,
							'title'		=> 'Payment',
							'create_by'	=> auth_data('pid'),
						]);
						
					}
					
					// if(!isset($_POST['item'])) $_POST['item'] = [];
					
					$stock_adjust = [];
					
					// check unpaid item
					if(isset($_POST['unpaid'])) {
						foreach($_POST['unpaid'] as $e) {
							$unpaid_data['is_delete'] = 1;
							$unpaid_data['payment'] = $new_payment_id;
							$this->log_join_model->edit($e['id'], $unpaid_data);
							
							if($this->input->post('discount_type') == '%') {
								$discount = $e['price_unit'] * $this->input->post('discount') / 100;
							} else {
								$discount = $this->input->post('discount');
							}
							
							$this->log_payment_model->add([
								'payment' => $new_payment_id,
								'user' => $this->input->post('student'),
								'title' => $e['title'],
								'item' => $e['item'],
								'movement' => $e['movement'],
								'movement_log' => $e['movement_log'],
								'remark' => $e['remark'],
								'qty' => $e['qty'],
								'dis_amount' => $e['dis_amount'],
								'dis_unit' => $this->input->post('discount_type'),
								'price_unit' => $e['price_unit'],
								'price_amount' => $e['amount'],
								'create_by' => auth_data('pid'),
							]);
						}
					}
					
					if(isset($_POST['item'])) {
					
						// loop items into stock adjust array
						foreach($_POST['item'] as $e) {
							
							if ($e['type'] == 'item') {
								
								$stock_adjust[] = [
									'title' => $e['title'],
									'item' => $e['item'],
									'remark' => $e['remark'],
									'qty' => $e['qty'],
									'dis_amount' => $e['dis_amount'],
									'price_unit' => $e['price_unit'],
									'amount' => $e['amount'],
								];
								
							} else {
								
								if($this->input->post('discount_type') == '%') {
									$discount = $e['price_unit'] * $this->input->post('discount') / 100;
								} else {
									$discount = $this->input->post('discount');
								}
								
								if(datalist_Table('tbl_classes', 'type', $e['item']) == 'check_in') {
									$this->log_point_model->add([
										'type'			=> 'class_credit',
										'user'			=> $this->input->post('student'),
										'class'			=> $e['item'],
										'amount_1'		=> $e['qty'] * datalist_Table('tbl_classes', 'credit', $e['item']),
										'create_by'		=> auth_data('pid')
									]);
								}
								
								// create log payment for class
								$this->log_payment_model->add([
									'payment' => $new_payment_id,
									'user' => $this->input->post('student'),
									'title' => $e['title'],
									'class' => $e['item'],
									'period' => $e['period'],
									'remark' => $e['remark'],
									'qty' => $e['qty'],
									'dis_amount' => $e['dis_amount'],
									'dis_unit' => $this->input->post('discount_type'),
									'price_unit' => $e['price_unit'],
									'price_amount' => $e['amount'],
									'create_by' => auth_data('pid'),
									
								]);
								
							}
							
						}
						
						if(!empty($stock_adjust)) {
							
							$new_id = null;
							
							if(datalist_Table('tbl_inventory', 'stock_ctrl', $e['item']) == 1) {
					
								$new_id = $this->tbl_inventory_model->add([
									'date' => $this->input->post('date'),
									'branch' => branch_now('pid'),
									'title' => 'Item purchase from '.$post_data['payment_no'],
									'type' => 'movement',
									'create_by' => auth_data('pid'),
								]);
							
							}
							
							foreach($stock_adjust as $e) {
								
								if($this->input->post('discount_type') == '%') {
									$discount = $e['price_unit'] * $this->input->post('discount') / 100;
								} else {
									$discount = $this->input->post('discount');
								}
								
								$log_id = null;
								
								if(datalist_Table('tbl_inventory', 'stock_ctrl', $e['item']) == 1) {
									$log_id = $this->log_inventory_model->add([
										'branch' => branch_now('pid'),
										'inventory' => $new_id,
										'item' => $e['item'],
										'qty_in' => '0',
										'qty_out' => $e['qty'],
										'create_by' => auth_data('pid'),
									]);
									$log_id = $log_id[0]['id'];
								}
									
								$this->log_payment_model->add([
									'payment' => $new_payment_id,
									'user' => $this->input->post('student'),
									'title' => $e['title'],
									'item' => $e['item'],
									'movement' => $new_id,
									'movement_log' => $log_id,
									'remark' => $e['remark'],
									'qty' => $e['qty'],
									'dis_amount' => $e['dis_amount'],
									'dis_unit' => $this->input->post('discount_type'),
									'price_unit' => $e['price_unit'],
									'price_amount' => $e['amount'],
									'create_by' => auth_data('pid'),
								]);
								
							}
							
						}
						
					}
					
					alert_new('success', ucfirst($this->single).' created successfully');
					redirect($this->group . '/list');

				}
				
				header('refresh: 0'); exit;
				
			}

			$data['id'] = $id;
			// $data['items'] = $this->tbl_inventory_model->list( branch_now('pid'), 'item' );
			$data['item_cat'] = $this->tbl_secondary_model->active_list('item_cat', branch_now('pid'));
			$data['classes'] = $this->tbl_classes_model->list(branch_now('pid'), [ 'active' => 1, 'is_hidden' => 0 ]);
			
			$branch_payment = [];
			foreach($this->log_join_model->list('secondary', branch_now('pid'), ['active' => 1]) as $e) {
				if(datalist_Table('tbl_secondary', 'type', $e['secondary']) == 'payment_method') {
					$branch_payment[] = $e;
				}
			}
			
			$data['payment_all'] = $branch_payment;
			$data['payment_now'] = $this->tbl_secondary_model->list('payment_method', branch_now('pid'), [ 'active' => 1 ]);

			$data['thispage'] = [
				'title' => 'Add '.ucfirst($this->single),
				'group' => $this->group,
				'js' => $this->group.'/add2',
			];

			if(isset($_GET['class'])) {
				$data['student'] = $this->log_join_model->list('join_class', branch_now('pid'), [ 'class' => $_GET['class'], 'active' => 1 ]);
			} else {
				$data['student'] = $student = $this->tbl_users_model->list('student', branch_now('pid'), [  ]);
				// $data['student'] = $student = $this->tbl_users_model->list('student', branch_now('pid'), [ 'active' => 1 ]);
			}
				
			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/add2');
			$this->load->view('inc/footer', $data);

		}

	}

	public function edit($id = '')
	{
		
		auth_must('login');
		check_module_page('Payment/Read');

		$data['result'] = $this->tbl_payment_model->view($id);
		$user = $data['result'][0]['student'];
		$data['id'] = $user;
		$data['student'] = $this->tbl_users_model->list('student', branch_now('pid'));
		// $data['items'] = $this->tbl_inventory_model->list( branch_now('pid'), 'item' );
		$data['services'] = $this->tbl_inventory_model->list( branch_now('pid'), 'item', ['item_type' => 'service'] );
		$data['item_cat'] = $this->tbl_secondary_model->active_list('item_cat', branch_now('pid'));
		$data['classes'] = $this->tbl_classes_model->list(branch_now('pid'), [ 'active' => 1, 'is_hidden' => 0 ]);
		
		$branch_payment = [];
		foreach($this->log_join_model->list('secondary', branch_now('pid'), ['active' => 1]) as $e) {
			if(datalist_Table('tbl_secondary', 'type', $e['secondary']) == 'payment_method') {
				$branch_payment[] = $e;
			}
		}
		$data['payment_all'] = $branch_payment;
		$data['payment_now'] = $this->tbl_secondary_model->list('payment_method', branch_now('pid'), [ 'active' => 1 ]);
		
		$data['result2'] = $this->log_payment_model->list($id);

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
				
				if(!$this->tbl_payment_model->check_payment_no( $this->input->post('payment_no'), $id, branch_now('pid') )) {

		        	alert_new('warning', 'Payment no has been taken');
		            
		        	redirect($this->uri->uri_string());

	        	} else {

					$post_data = [];
				
					foreach([ 'payment_no', 'payment_method', 'date', 'remark', 'subtotal', 'discount', 'discount_type', 'adjust', 'adjust_label', 'material_fee','total', 'tax', 'status' ] as $e) {
						
						$post_data[$e] = $this->input->post($e);
						
					}
	
					$post_data['branch'] = branch_now('pid');
					$post_data['update_by'] = auth_data('pid');
					
					$image = $data['result']['receipt'];
			
					if(isset($_FILES['image'])) {

						$target_dir = "uploads/data/";
						
						if ($_FILES['image']['size'] != 0) {
							
							$temp = explode(".", $_FILES["image"]["name"]);
							$newfilename = get_new('id') . '.' . end($temp);
							$target_file = $target_dir . $newfilename;
							$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
							move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

							$file_size = $_FILES["image"]["size"];
							$image_data['file_name'] = $_FILES["image"]["name"];
							$image_data['file_type'] = $fileType;
							$image_data['file_size'] = $file_size;
							$image_data['file_source'] = base_url($target_file);
							$image_data['create_by'] = auth_data('pid');
							$image_data['type'] = 'image_item';
							$image = $this->tbl_uploads_model->add($image_data);

						}
					}

					$post_data['receipt'] = $image;
					
					$this->tbl_payment_model->edit($id, $post_data);
					
					
					$removed = $this->input->post('removedList');
					$removedList = explode(",", $removed); 
					
					if(isset($removedList)) {
						foreach($removedList as $k => $v) {
							$this->log_payment_model->del_by_id($v);
						}
					}
					
					if(!isset($_POST['item'])) $_POST['item'] = [];
					
					if(isset($_POST['old'])) {
																				
						foreach($_POST['old'] as $e) {
							
							if ($e['type'] == 'item') {
								
								$this->tbl_inventory_model->edit($e['inventory_id'], [
									'date' => $this->input->post('date'),
									'update_by' => auth_data('pid'),
								]);
								
								if(!empty($e['log_inventory_id'])) {
									$this->log_inventory_model->edit($e['log_inventory_id'], [
										'qty_out' => $e['qty'],
									]);	
								}
								
								$this->log_payment_model->edit($e['log_id'], [
									'title' => $e['title'],
									'item' => $e['item'],
									'remark' => $e['remark'],
									'qty' => $e['qty'],
									'dis_amount' => $e['dis_amount'],
									'dis_unit' => $this->input->post('discount_type'),
									'price_unit' => $e['price_unit'],
									'price_amount' => $e['amount'],
									'update_by' => auth_data('pid'),
								]);
								
							} else {
								
								$this->log_payment_model->edit($e['log_id'], [
									'title' => $e['title'],
									'remark' => $e['remark'],
									'qty' => $e['qty'],
									'dis_unit' => $this->input->post('discount_type'),
									'price_unit' => $e['price_unit'],
									'price_amount' => $e['amount'],
									'update_by' => auth_data('pid'),
								]);
								
							}
							
						}
						
					}
					
					if(isset($_POST['item'])) {
						
						foreach($_POST['item'] as $e) {
					
							if ($e['type'] == 'item') {
								
								$stock_adjust[] = [
									'title' => $e['title'],
									'item' => $e['item'],
									'remark' => $e['remark'],
									'qty' => $e['qty'],
									'dis_amount' => $e['dis_amount'],
									'price_unit' => $e['price_unit'],
									'amount' => $e['amount'],
								];
								
							} else {
								
								$this->log_payment_model->add([
									'payment' => $id,
									'user' => $this->input->post('student'),
									'title' => $e['title'],
									'class' => $e['item'],
									'period' => $e['period'],
									'remark' => $e['remark'],
									'qty' => $e['qty'],
									'dis_amount' => $e['dis_amount'],
									'dis_unit' => $this->input->post('discount_type'),
									'price_unit' => $e['price_unit'],
									'price_amount' => $e['amount'],
									'create_by' => auth_data('pid'),
								]);
								
							}
						
						}
						
						if(!empty($stock_adjust)) {
							
							$new_id = null;
							
							if(datalist_Table('tbl_inventory', 'stock_ctrl', $e['item']) == 1) {
							
								$new_id = $this->tbl_inventory_model->add([
									'date' => $this->input->post('date'),
									'branch' => branch_now('pid'),
									'title' => 'Item purchase by '.datalist_Table('tbl_users', 'fullname_en', $this->input->post('student')),
									'type' => 'movement',
									'create_by' => auth_data('pid'),
								]);
							
							}
							
							foreach($stock_adjust as $e) {
								
								$log_id = null;
								
								if(datalist_Table('tbl_inventory', 'stock_ctrl', $e['item']) == 1) {
								
									$log_id = $this->log_inventory_model->add([
										'branch' => branch_now('pid'),
										'inventory' => $new_id,
										'item' => $e['item'],
										'qty_in' => '0',
										'qty_out' => $e['qty'],
										'create_by' => auth_data('pid'),
									]);
								
									$log_id = $log_id[0]['id'];
								
								}
									
								$this->log_payment_model->add([
									'payment' => $id,
									'user' => $this->input->post('student'),
									'title' => $e['title'],
									'item' => $e['item'],
									'movement' => $new_id,
									'movement_log' => $log_id,
									'remark' => $e['remark'],
									'qty' => $e['qty'],
									'dis_amount' => $e['dis_amount'],
									'dis_unit' => $this->input->post('discount_type'),
									'price_unit' => $e['price_unit'],
									'price_amount' => $e['amount'],
									'create_by' => auth_data('pid'),
								]);
								
							}
							
						}
						
					}
					
					alert_new('success', ucfirst($this->single).' updated successfully');
					
					// redirect($this->group . '/list');
					
					header('refresh: 0'); exit;

				}

			}

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}
	
	// loop log payment data when payment deleted
	public function json_list_log($id = '')
	{

		$result = $this->log_payment_model->list($id);
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}

	public function json_del($id = '')
	{
	
		auth_must('login');
		check_module_page('Payment/Delete');
		
		if(!empty($id)) {
			
			$this->tbl_payment_model->del($id);
			$this->log_payment_model->del($id);
			foreach( $this->log_join_model->list_all([ 'type' => 'unpaid_item', 'payment' => $id ]) as $e ) {
				$this->log_join_model->edit($e['id'], [ 'is_delete' => 0 ]);
			}
			foreach( $this->log_join_model->list_all([ 'type' => 'join_service', 'payment' => $id ]) as $e ) {
				$this->log_join_model->edit($e['id'], [ 'is_delete' => 0 ]);
			}
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($this->single) . ' deleted successfully']));

	}
	
	public function json_view_image($type = '')
	{
	
		auth_must('login');
		
		$result = pointoapi_UploadSource(branch_now('gateway_qrpay_img_'.$type));
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}

	/*
	 * @author Steve
	 *
	**/
	public function json_list()
	{
		
		$this->load->model('tbl_users_model');
		
		header('Content-type: application/json');
		
		$login = $this->tbl_users_model->me_token( post_data('token') );
		
		if( count($login) == 1 ) {
			
			$login = $login[0];
			
			// session
			unset($login['password']);

			$login['image_src'] = pointoapi_UploadSource($login['image']);
			
			// payment
			$payment = [];
			// die( $login['branch'] );
			
			// unpaid
			$std_unpaid_result = std_unpaid_result(post_data('student'), $login['branch']);
			
			if($std_unpaid_result['count'] > 0) {
				
				$payment[] = [
					'pid' => null,
					'branch' => $login['branch'],
					'student' => post_data('student'),
					'payment_no' => null,
					'payment_method' => null,
					'remark' => null,
					'subtotal' => null,
					'discount' => null,
					'discount_type' => null,
					'adjust' => null,
					'adjust_label' => null,
					'tax' => null,
					'create_on' => null,
					'create_by' => null,
					'update_on' => null,
					'update_by' => null,
					// 'status' => null,
					// 'status' => null,
					
					'link' => base_url('payment/std_unpaid_landing/'.post_data('student').'?token='.$login['token']),
					'status' => 'Unpaid',
					'payment_no' => 'Outstanding Payment',
					'date' => date('Y-m-d'),
					'total' => number_format($std_unpaid_result['total'], 2, '.', ','),
				];
				
			}
			
			foreach($this->tbl_payment_model->student_list( post_data('student') ) as $e) {
				
				$e['link'] = base_url('payment/view_landing/'.$e['pid'].'?token='.$login['token']);
				$e['total'] = number_format($e['total'], 2, '.', ',');
				$e['status'] = ucfirst($e['status']);
				
				$payment[] = $e;
				
			}
			
			// return
			$result = [
				'session' => $login,
				/*'childs' => $this->tbl_users_model->list('student', branch_now('pid'), [
					'active' => 1,
					'parent' => $login['pid'],
				]),*/
				'payment' => $payment,
			];

			// parent
			if($login['type'] == 'parent') {
				
				$result['students'] = [];
				
				foreach($this->tbl_users_model->total_children($login['pid']) as $e) {
					
					unset($e['password']);

					$e['image_src'] = pointoapi_UploadSource($e['image']);
					
					$result['students'][] = $e;
					
				}
				
			}
			
			die(json_encode([ 'status' => 'ok', 'result' => $result]));

		} else {

			die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));

		}

	}
	
	public function json_item_list() {
		
		$item_cat = $this->tbl_secondary_model->active_list('item_cat', branch_now('pid'));
		
		$result = [];
		
		$result[] = [
			'id' => '',
			'text' => '-',
		];
		
		foreach($item_cat as $e) {
			
			$query = $this->tbl_inventory_model->list(branch_now('pid'), 'item', ['active' => 1, 'category' => $e['pid']]);
			
			$children = [];
			
			foreach($query as $e2) {
				$children[] = [
					'id' => $e2['pid'],
					'text'	=> $e2['title']
				];
			}
			
			$result[] = [
				'text' => $e['title'],
				'children' => $children,
			];
			
		}
		
		header('Content-type: application/json');
		die(json_encode([ 'status' => 'ok', 'result' => $result]));
		
	}
	
	public function json_filter_item($id) {
		
		$query = $this->tbl_inventory_model->list(branch_now('pid'), 'item', ['active' => 1, 'category' => $id]);
		
		$result = [];
		
		foreach($query as $e) {
			$result[] = [
				'id' => $e['pid'],
				'text'	=> $e['title']
			];
		}
				
		header('Content-type: application/json');
		die(json_encode([ 'status' => 'ok', 'result' => $result]));
		
	}
	
	// by steve
	/*private function pay_landing_total($join_class, $result, $unpaid_item) {
		
		$total = 0;
		
		if(isset($join_class)) {
			
			foreach($join_class as $e) { $unpaid_class = [];
				
				for($m = floatval(date('m', strtotime($e['date'])));
					$m <= floatval(date('m'));
					$m ++) {
						
					$month; if($m < 10) { $month = '0'.$m; } else { $month = $m; }
					
					if(empty($this->log_payment_model->list2([
						'user' => $result['pid'],
						'class' => $e['class'],
						'is_delete' => 0,
						'period' => date('Y', strtotime($e['date'])).'-'.$month,
					]))) {
							
						$unpaid_class[] = [
							'class' => $e['class'],
							'period' => date('Y', strtotime($e['date'])).'-'.$month,
						];	
						
					}
					
				}
				
				if(empty($unpaid_class)) {
					
					foreach($this->log_join_model->list('join_class', branch_now('pid'), [
						'user' => $result['pid'],
						'active' => 1
					]) as $e2) {
						
						if(floatval(date('m', strtotime($e2['date']))) > floatval($month)) {
							
							$unpaid_class[] = [
								'class' => $e2['class'],
								'period' => date('Y-m', strtotime($e2['date'])),
							];
							
						}
						
					}
						
				}
				
				if(!empty($unpaid_class)) {
					
					foreach($unpaid_class as $k => $v) {
						
						$total += datalist_Table('tbl_classes', 'fee', $v['class']);
							
					}
						
				}
				
			}
			
		}
		
		if(isset($unpaid_item)) {
			
			foreach($unpaid_item as $e3) {
			
				$total += (datalist_Table('tbl_inventory', 'price_sale', $e3['item']) * $e3['qty']);
				
			}
			
		}
		
		return $total;
		
	}*/

	// Tan Jing Suan
	public function json_additem($pid = '')
	{
		$result = [];
		if ( isset($_GET['type']) ) {
			switch ( $_GET['type'] ) {
				case 'package':
					$package = $this->tbl_secondary_model->view($_GET['packagepid']);
					if ( count($package) > 0 ) {
						$items = json_decode($package[0]['item'], true);
						if(!is_array($items)) $items = [];		
						foreach($items as $e) {
							$item = $this->tbl_inventory_model->view($e)[0];
							$id = $this->log_join_model->additem([
								// 'type'			=> 'secondary',
								'type'			=> 'unpaid_item',
								'user'			=> $pid,
								'branch'		=> branch_now('pid'),
								'secondary'		=> $_GET['packagepid'],
								'item'			=> $item['pid'],
								'active'		=> 1,
								'date'			=> date('Y-m-d'),
								'qty'		    => 1,
								'amount'		=> $item['price_sale'],
								'amount_unit'	=> $item['price_sale'],
								'title'		    => $item['title'],
								'create_by'     => auth_data('pid'),
							]);
							if ( isset($id) ) {
								$result[] = $this->log_join_model->listitem($id)[0];
							}
						}
					}
					break;
				case 'class':
					$class = $this->tbl_classes_model->view( $_GET['classpid'] );
					if ( count($class) > 0 ) {
						$id = $this->log_join_model->additem([
							'type'			=> 'join_class',
							'user'			=> $pid,
							'branch'		=> branch_now('pid'),
							'class'			=> $_GET['classpid'],
							'active'		=> 1,
							'date'			=> date('Y-m-d'),
							'qty'		    => $_GET['classqty'],
							'amount'		=> $_GET['classamount'],
							'amount_unit'	=> $_GET['classprice_unit'],
							'title'		    => $class[0]['title'],
							'remark'        => $_GET['classremark'],
							'create_by'     => auth_data('pid'),
						]);
					}
					if ( isset($id) ) {
						$result = $this->log_join_model->listitem($id);
					}
					break;
				case 'item':
					$items = $this->tbl_inventory_model->view( $_GET['itempid'] );
					if ( count($items) > 0 ) {
						$id = $this->log_join_model->additem([
							'type'			=> 'unpaid_item',
							'user'			=> $pid,
							'branch'		=> branch_now('pid'),
							'item'			=> $_GET['itempid'],
							'active'		=> 1,
							'date'			=> date('Y-m-d'),
							'qty'		    => $_GET['itemqty'],
							'amount'		=> $_GET['itemamount'],
							'amount_unit'	=> $_GET['itemprice_unit'],
							'title'		    => $items[0]['title'],
							'remark'        => $_GET['itemremark'],
							'create_by'     => auth_data('pid'),
						]);	
					}
					if ( isset($id) ) {
						$result = $this->log_join_model->listitem($id);
					}
					break;
				default:
					break;
			}
		}
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));
	}
	
	// Tan Jing Suan
	public function json_edititem($id = '')
	{
		header('Content-type: application/json');
		if ( !isset($_GET['key']) ) {
			die(json_encode([ 'status' => 'failed', 'message' => 'key not found']));			
		}
		if ( !isset($_GET['value']) ) {
			die(json_encode([ 'status' => 'failed', 'message' => 'value not found']));			
		}
		$key = $_GET['key'];
		$value = $_GET['value'];
		$data[$key] = $value;
		$data['update_by'] = auth_data('pid');
		$this->log_join_model->edit($id, $data);
		die(json_encode(['status' => 'ok', 'message' => 'Item edited successfully']));
	}

	public function json_delitem($id = '')
	{
		header('Content-type: application/json');
		// if ( isset($_GET['type']) && isset($_GET['item']) ) {
		// 	switch ( $_GET['type'] ) {
		// 		case "package":
		// 			$this->log_join_model->delsecondary($id, $_GET['item']);
		// 			break;
		// 		case "class":
		// 			$this->log_join_model->delclass($id, $_GET['item']);
		// 			break;
		// 		case "item":
		// 			$this->log_join_model->delitem($id, $_GET['item']);
		// 			break;
		// 		default:
		// 			break;
		// 	}
		// }
		if ( !isset($id) ) {
			die(json_encode([ 'status' => 'failed', 'message' => 'id not found']));			
		}
		$this->log_join_model->delid($id);
		die(json_encode(['status' => 'ok', 'message' => 'Item deleted successfully']));
	}

	// Tan Jing Suan
	public function json_autosave($pid) {
		header('Content-type: application/json');
		if ( !isset($_GET['key']) ) {
			die(json_encode([ 'status' => 'failed', 'message' => 'key not found']));			
		}
		if ( !isset($_GET['value']) ) {
			die(json_encode([ 'status' => 'failed', 'message' => 'value not found']));			
		}
		$autosave = $this->log_join_model->list('auto_save_payment', branch_now('pid'), ['user' => $pid]);
		$key = $_GET['key'];
		$value = $_GET['value'];
		$data[$key] = $value;
		if ( count($autosave) <= 0 ) {
			$data['type'] = 'auto_save_payment';
			$data['user'] = $pid;
			$data['branch'] = branch_now('pid');
			$data['create_by'] = auth_data('pid');
			$id = $this->log_join_model->add($data);
		} else {
			$data['update_by'] = auth_data('pid');
			$this->log_join_model->edit($autosave[0]['id'], $data);
		}
		die(json_encode(['status' => 'ok', 'message' => 'Auto Save successfully']));
	}

}
