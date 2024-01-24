<?php

use function PHPSTORM_META\type;

defined('BASEPATH') OR exit('No direct script access allowed');

require './vendor/PHPExcel/Excel.php';

class Insurance extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'insurances';
		$this->single = 'insurance';
		
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_admins_model');
		$this->load->model('tbl_branches_model');
		$this->load->model('tbl_secondary_model');
		$this->load->model('log_join_model');
		$this->load->model('excel_import_model');

		auth_must('login');

	}
	
	public function list()
	{
		$this->load->helper('form');
		
		$data['result'] = $this->tbl_branches_model->view(branch_now('pid'));
		
		
		// if insurance not agree , javascript will apply for modal popup 
		if($data['result'][0]['is_insurance_agree'] == 0){
			$data['thispage'] = [
				'title' => 'Insurance',
				'group' => $this->group,
				'js' => $this->group.'/modal'
			];	
			
			
			if(isset($_POST['save'])) {
				
				$agree1 = $this->input->post('agree1');
				$agree2 = $this->input->post('agree2');
				
				if(($agree1 == 'agree') && ($agree2 == 'agree'))
				{
					$this->tbl_branches_model->edit(branch_now('pid'), [
						'is_insurance_agree' => 1
					]);
					
					
					redirect($this->single . '/list');
					
					
				}else{
					alert_new('danger', ucfirst($this->single).' please agree policy and terms and condition ');
					redirect($this->single . '/list');
				}
			}			


			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/index');
			$this->load->view('inc/footer', $data);		
			
			
		}else{
			
			// show student list to let them buy insurance
			$title = empty($type) ? 'All '.ucfirst($this->group) : 'Pending';
			$type = empty($type) ? 'student' : 'student_pending';
			$data['thispage'] = [
					'title' => 'Apply Insurance',
					'group' => $this->group,
					'css' => $this->group.'/list',
					'js' => $this->group.'/list'
				];	
			/*
			 * all students
			 *
			**/
			$data['all'] = $this->tbl_users_model->list_v2([
				'type'		=> $type, 
				'branch'	=> branch_now('pid'),
			]);
		
			$load_view = 'lite_dtable';
			
			$data['students_result'] = $this->tbl_users_model->list('student',branch_now('pid'), ['active' => 1]);
			
		
			if(isset($_POST['to_buy_insurance'])) {
				
				$this->tbl_users_model->reset_pending_insurance();
				
				if (isset($_POST['student']))
				{
					foreach($_POST['student'] as $e) {
						$this->tbl_users_model->edit($e, ['insurance' => 'pending']);
					}
				}
				
				alert_new('success', 'Insurance applied successfully');
				header('refresh: 0'); exit;
			}

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/'.$load_view, $data);
			$this->load->view('inc/footer', $data);	
				
		}		
		
	}	
	

	public function migrate()
	{
		
		auth_must('login');
		check_module_page('Settings/Read');
		check_module_page('Settings/Modules/Migrate');

		$data['thispage'] = [
			'title' => 'Migrate',
			'group' => $this->group,
		];
		
		if(!isset($_GET['tab'])) $_GET['tab'] = 1;

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/migrate', $data);
		$this->load->view('inc/footer', $data);

		if (isset($_POST['import_student'])) {
			
			if(isset($_FILES["file"]["name"])) {

				$path = $_FILES["file"]["tmp_name"];
				$object = PHPExcel_IOFactory::load($path);
				
				foreach($object->getWorksheetIterator() as $worksheet) {

					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$userData = array();	
					$i = 0;
						
					// new batch id
					$new_batch_id = get_new('id');

					// steve
					for($row=2; $row <= $highestRow; $row++) {
						
						$new_batch_id++;
						
						// each row
						$userData_each = array(
							'type' => 'student',
							'pid'   => $new_batch_id,
							'branch'  => branch_now('pid'),
						);
						
						// push col
						foreach([
							1 => 'fullname_en',
							2 => 'fullname_cn',
							3 => 'active',
							4 => 'nric',
							9 => 'username',
							10 => 'password',
							11 => 'phone',
							12 => 'phone2',
							13 => 'phone3',
							14 => 'email',
							15 => 'email2',
							16 => 'email3',
							17 => 'address',
							18 => 'address2',
							19 => 'address3',
							23 => 'rfid_cardid',
							25 => 'car_plate_no',
							26 => 'remark',
						] as $k_col => $e_col) {
							
							$e_val = $worksheet->getCellByColumnAndRow( ($k_col - 1) , $row)->getValue();
							
							// check type
							switch($e_col) {
								
								case 'password':
									if( strlen($e_val) > 0 ) $e_val = password_hash($e_val, PASSWORD_DEFAULT);
									break;
								
								case 'phone':
								case 'phone2':
								case 'phone3':
									$e_val = str_replace('-', '', $e_val);
									$e_val = str_replace('+', '', $e_val);
									$e_val = str_replace(' ', '', $e_val);
									break;
								
								case 'email':
								case 'email2':
								case 'email3':
									$e_val = str_replace(' ', '', $e_val);
									break;
								
							}
							
							$userData_each[ $e_col ] = $e_val;
							// if( !empty($e_val) ) $userData_each[ $e_col ] = $e_val;
							
						}
					
						// birthday
						$Y = $worksheet->getCellByColumnAndRow( (5 - 1), $row)->getValue();
						$M = $worksheet->getCellByColumnAndRow( (6 - 1), $row)->getValue();
						$D = $worksheet->getCellByColumnAndRow( (7 - 1), $row)->getValue();
						// if( !empty($Y) && !empty($M) && !empty($D) ) $userData_each['birthday'] = $Y.'-'.$M.'-'.$D;
						$userData_each['birthday'] = !empty($Y) && !empty($M) && !empty($D) ? $Y.'-'.$M.'-'.$D : null ;
						
						// date_join
						$Y2 = $worksheet->getCellByColumnAndRow( (20 - 1), $row)->getValue();
						$M2 = $worksheet->getCellByColumnAndRow( (21 - 1), $row)->getValue();
						$D2 = $worksheet->getCellByColumnAndRow( (22 - 1), $row)->getValue();
						// if( !empty($Y2) && !empty($M2) && !empty($D2) ) $userData_each['date_join'] = $Y2.'-'.$M2.'-'.$D2;
						$userData_each['date_join'] = !empty($Y2) && !empty($M2) && !empty($D2) ? $Y2.'-'.$M2.'-'.$D2 : null ;
						
						$transport = $worksheet->getCellByColumnAndRow( (24 - 1), $row)->getValue();
						$check_transport = $this->tbl_secondary_model->list('transport', branch_now('pid'), [ 'title' => $transport ]);
						if(count($check_transport) > 0) {
							$transport = $check_transport[0]['pid'];
						} else {
							$transport = $this->tbl_secondary_model->add([
								'type'			=> 'transport',
								'branch'		=> branch_now('pid'),
								'title'			=> $transport,
								'create_by'		=> auth_data('pid')
							]);
						}
						$userData_each['transport'] = $transport;
						
						$school = $worksheet->getCellByColumnAndRow( (27 - 1), $row)->getValue();
						$check_school = $this->tbl_secondary_model->list('school', branch_now('pid'), [ 'title' => $school ]);
						if(count($check_school) > 0) {
							$school = $check_school[0]['pid'];
						} else {
							$school = $this->tbl_secondary_model->add([
								'type'			=> 'school',
								'branch'		=> branch_now('pid'),
								'title'			=> $school,
								'create_by'		=> auth_data('pid')
							]);
						}
						$userData_each['school'] = $school;
						
						$parent = $worksheet->getCellByColumnAndRow( (28 - 1), $row)->getValue();
						$check_parent = $this->tbl_users_model->list('parent', branch_now('pid'), [ 'fullname_en' => $parent ]);
						if(count($check_parent) > 0) {
							$parent = $check_parent[0]['pid'];
						} else {
							$parent = $this->tbl_users_model->add([
								'type'			=> 'parent',
								'branch'		=> branch_now('pid'),
								'fullname_en'	=> $parent,
								'create_by'		=> auth_data('pid')
							]);
						}
						$userData_each['parent'] = $parent;
						
						// push row
						// array_push($userData, $userData_each);
						$userData[] = $userData_each;
						
					}
					
					// soon
					/*for($row=2; $row <= $highestRow; $row++) {
						
						$userData_each = array(
							'type' => 'student',
							'pid'   => get_new('id'),
							'branch'  => branch_now('pid'),
						);
							
						if($worksheet->getCellByColumnAndRow(0, $row)->getValue() != null) {
							
							foreach([
								'fullname_en', 'fullname_cn' , 'active', 'nric', 'birthday_yyyy', 'birthday_mm', 'birthday_dd', 'gender', 
								'username', 'password', 
								'phone', 'phone2', 'phone3', 'email', 'email2', 'email3', 'address', 'address2', 'address3', 
								'date_join_dd', 'date_join_mm', 'date_join_yyyy', 'rfid_cardid', 'remark'
							] as $k => $v ) {
							
								switch($v) {
										
									case 'birthday_yyyy':
									case 'birthday_mm':
									case 'birthday_dd':
										$birthday_yyyy = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
										$birthday_mm = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
										$birthday_dd = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
										if($birthday_mm < 10) { $birthday_mm = '0'.$birthday_mm; }
										if($birthday_dd < 10) { $birthday_dd = '0'.$birthday_dd; }
										if(!empty($birthday_yyyy) && !empty($birthday_mm) && !empty($birthday_dd)) {
											$userData_each[ 'birthday' ] = $birthday_yyyy.'-'.$birthday_mm.'-'.$birthday_dd;
										}
										if(strlen($birthday_yyyy) != 4 || strlen($birthday_mm) > 2 || strlen($birthday_dd) > 2) {
											$userData_each[ 'birthday' ] = null;
										}
										break;		
										
									case 'date_join_yyyy':
									case 'date_join_mm':
									case 'date_join_dd':
										$join_yyyy = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
										$join_mm = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
										$join_dd = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
										if($join_mm < 10) { $join_mm = '0'.$join_mm; }
										if($join_dd < 10) { $join_dd = '0'.$join_dd; }
										if(!empty($join_yyyy) && !empty($join_mm) && !empty($join_dd)) {
											$userData_each[ 'date_join' ] = $join_yyyy.'-'.$join_mm.'-'.$join_dd;
										}
										if(strlen($join_yyyy) != 4 || strlen($join_mm) > 2 || strlen($join_dd) > 2) {
											$userData_each[ 'date_join' ] = null;
										}
										break;
										
									case 'gender':
										$userData_each[ $v ] = lcfirst( $worksheet->getCellByColumnAndRow($k, $row)->getValue() );
										break;
										
									case 'password':
										$userData_each[ $v ] = password_hash( $worksheet->getCellByColumnAndRow($k, $row)->getValue() , PASSWORD_DEFAULT);
										break;
										
									case 'phone':
										// $userData_each[ $v ] = preg_replace('/[^0-9]/', '', $worksheet->getCellByColumnAndRow($k, $row)->getValue());
										$userData_each[ $v ] = $worksheet->getCellByColumnAndRow($k, $row)->getValue();
										break;
										
									default:
										$userData_each[ $v ] = $worksheet->getCellByColumnAndRow($k, $row)->getValue();
									
								}
									
							}
							
							foreach([
								'fullname_en', 'fullname_cn' , 'active', 'nric', 'gender', 
								'username', 'password', 
								'phone', 'phone2', 'phone3', 'email', 'email2', 'email3', 'address', 'address2', 'address3', 'rfid_cardid', 'remark'
							] as $k => $v ) {
								if(empty($worksheet->getCellByColumnAndRow($k, $row)->getValue())) {
									$userData_each[ $v ] = '';
								}
							}
	
							$userData[] = $userData_each;
							// sleep(1);

						}
						
					}*/
				}

			}
			
			// debug
			// echo '<pre>'; print_r($userData); exit;
			
		 	$this->excel_import_model->insert($userData, 'user');

	 		alert_new('success', 'Data imported successfully');
			redirect('students/list');

		}

		if (isset($_POST['import_parent'])) {
			
			if(isset($_FILES["file"]["name"])) {

				$path = $_FILES["file"]["tmp_name"];
				$object = PHPExcel_IOFactory::load($path);

				foreach($object->getWorksheetIterator() as $worksheet) {

					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$userData = array();	
					$i = 0;

					for($row=2; $row <= $highestRow; $row++) {
						
						$userData_each = array(
							'type' => 'parent',
							'pid'   => get_new('id'),
							'branch'  => branch_now('pid'),
						);	
						
						if($worksheet->getCellByColumnAndRow(0, $row)->getValue() != null) {
						
							foreach([
								'fullname_en', 'fullname_cn' , 'active', 'nric', 'birthday_yyyy', 'birthday_mm', 'birthday_dd', 'gender', 
								'username', 'password', 
								'phone', 'phone2', 'phone3', 'email', 'email2', 'email3', 'address', 'address2', 'address3', 
								'date_join_dd', 'date_join_mm', 'date_join_yyyy', 'remark'
							] as $k => $v ) {
								
								switch($v) {
									
									case 'birthday_yyyy':
									case 'birthday_mm':
									case 'birthday_dd':
										$birthday_yyyy = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
										$birthday_mm = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
										$birthday_dd = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
										if($birthday_mm < 10) { $birthday_mm = '0'.$birthday_mm; }
										if($birthday_dd < 10) { $birthday_dd = '0'.$birthday_dd; }
										if(!empty($birthday_yyyy) && !empty($birthday_mm) && !empty($birthday_dd)) {
											$userData_each[ 'birthday' ] = $birthday_yyyy.'-'.$birthday_mm.'-'.$birthday_dd;
										}
										if(strlen($birthday_yyyy) != 4 || strlen($birthday_mm) > 2 || strlen($birthday_dd) > 2) {
											$userData_each[ 'birthday' ] = null;
										}
										break;
										
									case 'date_join_yyyy':
									case 'date_join_mm':
									case 'date_join_dd':
										$join_yyyy = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
										$join_mm = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
										$join_dd = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
										if($join_mm < 10) { $join_mm = '0'.$join_mm; }
										if($join_dd < 10) { $join_dd = '0'.$join_dd; }
										if(!empty($join_yyyy) && !empty($join_mm) && !empty($join_dd)) {
											$userData_each[ 'date_join' ] = $join_yyyy.'-'.$join_mm.'-'.$join_dd;
										}
										if(strlen($join_yyyy) != 4 || strlen($join_mm) > 2 || strlen($join_dd) > 2) {
											$userData_each[ 'date_join' ] = null;
										}
										break;
										
									case 'gender':
										$userData_each[ $v ] = lcfirst( $worksheet->getCellByColumnAndRow($k, $row)->getValue() );
										break;
										
									case 'password':
										$userData_each[ $v ] = password_hash( $worksheet->getCellByColumnAndRow($k, $row)->getValue() , PASSWORD_DEFAULT);
										break;
										
									case 'phone':
										$userData_each[ $v ] = preg_replace('/[^0-9]/', '', $worksheet->getCellByColumnAndRow($k, $row)->getValue());
										break;
										
									default:
										$userData_each[ $v ] = $worksheet->getCellByColumnAndRow($k, $row)->getValue();
									
								}
								
							}
							
							foreach([
								'fullname_en', 'fullname_cn' , 'active', 'nric', 'gender', 
								'username', 'password', 
								'phone', 'phone2', 'phone3', 'email', 'email2', 'email3', 'address', 'address2', 'address3', 'remark'
							] as $k => $v ) {
								if(empty($worksheet->getCellByColumnAndRow($k, $row)->getValue())) {
									$userData_each[ $v ] = '';
								}
							}
							
							$userData[] = $userData_each;
							
							sleep(1);
							
						}
						
					}

				}

			}
			
		 	$this->excel_import_model->insert($userData, 'user');

	 		alert_new('success', 'Data imported successfully');
			redirect('parents/list');

		}

		if (isset($_POST['import_teacher'])) {
			
			if(isset($_FILES["file"]["name"])) {

				$path = $_FILES["file"]["tmp_name"];
				$object = PHPExcel_IOFactory::load($path);

				foreach($object->getWorksheetIterator() as $worksheet) {

					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$userData = array();	
					$i = 0;

					for($row=2; $row <= $highestRow; $row++) {
						
						$userData_each = array(
							'type' => 'teacher',
							'pid'   => get_new('id'),
							'branch'  => branch_now('pid'),
						);
						
						if($worksheet->getCellByColumnAndRow(0, $row)->getValue() != null) {
						
							foreach([
								'fullname_en', 'fullname_cn' , 'active', 'nric', 'birthday_yyyy', 'birthday_mm', 'birthday_dd', 'gender', 
								'username', 'password', 
								'phone', 'phone2', 'phone3', 'email', 'email2', 'email3', 'address', 'address2', 'address3', 
								'date_join_dd', 'date_join_mm', 'date_join_yyyy', 'rfid_cardid', 'remark'
							] as $k => $v ) {
								
								switch($v) {
									
									case 'birthday_yyyy':
									case 'birthday_mm':
									case 'birthday_dd':
										$birthday_yyyy = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
										$birthday_mm = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
										$birthday_dd = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
										if($birthday_mm < 10) { $birthday_mm = '0'.$birthday_mm; }
										if($birthday_dd < 10) { $birthday_dd = '0'.$birthday_dd; }
										if(!empty($birthday_yyyy) && !empty($birthday_mm) && !empty($birthday_dd)) {
											$userData_each[ 'birthday' ] = $birthday_yyyy.'-'.$birthday_mm.'-'.$birthday_dd;
										}
										if(strlen($birthday_yyyy) != 4 || strlen($birthday_mm) > 2 || strlen($birthday_dd) > 2) {
											$userData_each[ 'birthday' ] = null;
										}
										break;
										
									case 'date_join_yyyy':
									case 'date_join_mm':
									case 'date_join_dd':
										$join_yyyy = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
										$join_mm = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
										$join_dd = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
										if($join_mm < 10) { $join_mm = '0'.$join_mm; }
										if($join_dd < 10) { $join_dd = '0'.$join_dd; }
										if(!empty($join_yyyy) && !empty($join_mm) && !empty($join_dd)) {
											$userData_each[ 'date_join' ] = $join_yyyy.'-'.$join_mm.'-'.$join_dd;
										}
										if(strlen($join_yyyy) != 4 || strlen($join_mm) > 2 || strlen($join_dd) > 2) {
											$userData_each[ 'date_join' ] = null;
										}
										break;
										
									case 'gender':
										$userData_each[ $v ] = lcfirst( $worksheet->getCellByColumnAndRow($k, $row)->getValue() );
										break;
										
									case 'password':
										$userData_each[ $v ] = password_hash( $worksheet->getCellByColumnAndRow($k, $row)->getValue() , PASSWORD_DEFAULT);
										break;
										
									case 'phone':
										$userData_each[ $v ] = preg_replace('/[^0-9]/', '', $worksheet->getCellByColumnAndRow($k, $row)->getValue());
										break;
										
									default:
										$userData_each[ $v ] = $worksheet->getCellByColumnAndRow($k, $row)->getValue();
									
								}
								
							}
							
							foreach([
								'fullname_en', 'fullname_cn' , 'active', 'nric', 'gender', 
								'username', 'password', 
								'phone', 'phone2', 'phone3', 'email', 'email2', 'email3', 'address', 'address2', 'address3', 'rfid_cardid', 'remark'
							] as $k => $v ) {
								if(empty($worksheet->getCellByColumnAndRow($k, $row)->getValue())) {
									$userData_each[ $v ] = '';
								}
							}
							
							$userData[] = $userData_each;
							
							sleep(1);
							
						}
						
					}
										
				}

			}
			
		 	$this->excel_import_model->insert($userData, 'user');
		 		
	 		alert_new('success', 'Data imported successfully');
			redirect('teachers/list');

		}

		if (isset($_POST['import_item'])) {
			
			if(isset($_FILES["file"]["name"])) {

				$path = $_FILES["file"]["tmp_name"];
				$object = PHPExcel_IOFactory::load($path);

				foreach($object->getWorksheetIterator() as $worksheet) {

					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$itemData = array();	
					$i = 0;

					for($row=2; $row <= $highestRow; $row++) {
						
						$itemData_each = array(
							'type' => 'item',
							'pid'   => get_new('id'),
							'stock_ctrl' => '1',
							'branch'  => branch_now('pid'),
						);	
						
						if($worksheet->getCellByColumnAndRow(0, $row)->getValue() != null) {
							
							foreach([
								'title', 'sku' , 'active', 'category', 'item_type', 'price_cost', 'price_min', 'price_sale', 'remark'
							] as $k => $v ) {
								
								switch($v) {
									
									case 'category':
										$category = $worksheet->getCellByColumnAndRow($k, $row)->getValue();
										$check_category = $this->tbl_secondary_model->list('item_cat', branch_now('pid'), [ 'title' => $category ]);
										if(count($check_category) > 0) {
											$category = $category[0]['pid'];
										} else {
											$category = $this->tbl_secondary_model->add([
												'type'			=> 'item_cat',
												'title'			=> $category,
												'branch'		=> branch_now('pid'),
												'create_by'		=> auth_data('pid')
											]);
										}
										$itemData_each[ $v ] = $category;
										break;
										
									case 'item_type':
										$item_type = strtolower($worksheet->getCellByColumnAndRow($k, $row)->getValue());
										switch($item_type) {
											case 's':
												$item_type = 'service';
												break;
											default:
												$item_type = 'product';
												break;
										}
										$itemData_each[ $v ] = $item_type;
										break;
									
									default:
										$itemData_each[ $v ] = $worksheet->getCellByColumnAndRow($k, $row)->getValue();
										if(empty($worksheet->getCellByColumnAndRow($k, $row)->getValue())) {
											$itemData_each[ $v ] = '';
										}
										break;
								
								}
								
							}
							
							$itemData[] = $itemData_each;
							
						}
						
						sleep(1);
						
					}
					
				}

			}
			
		 	$this->excel_import_model->insert($itemData, 'item');
		 		
	 		alert_new('success', 'Data imported successfully');
			redirect('items/list');

		}

		if (isset($_POST['import_class'])) {
			
			if(isset($_FILES["file"]["name"])) {

				$path = $_FILES["file"]["tmp_name"];
				$object = PHPExcel_IOFactory::load($path);

				foreach($object->getWorksheetIterator() as $worksheet) {

					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$classData = array();	
					$i = 0;

					for($row=2; $row <= $highestRow; $row++) {
						
						$classData_each = array(
							'pid'   => get_new('id'),
							'branch'  => branch_now('pid'),
						);	
						
						if($worksheet->getCellByColumnAndRow(0, $row)->getValue() != null) {
						
							foreach([
								'title', 'teacher', 'course', 'type', 'date_start_yyyy', 'date_start_mm', 'date_start_dd', 'date_end_yyyy', 'date_end_mm', 'date_end_dd', 'fee', 'active', 'dy_1', 'dy_2', 'dy_3', 'dy_4', 'dy_5', 'dy_6', 'dy_7','remark'
							] as $k => $v ) {
								
								switch($v) {
									
									case 'teacher':
										$teacher = $worksheet->getCellByColumnAndRow($k, $row)->getValue();
										$check_teacher = $this->tbl_users_model->list('teacher', branch_now('pid'), [ 'fullname_en' => $teacher ]);
										if(count($check_teacher) > 0) {
											$teacher = $teacher[0]['pid'];
										} else {
											$teacher = $this->tbl_users_model->add([
												'type'			=> 'teacher',
												'fullname_en'	=> $teacher,
												'branch'		=> branch_now('pid'),
												'create_by'		=> auth_data('pid')
											]);
										}
										$classData_each[ $v ] = $teacher;
										break;
										
									case 'course':
										$course = $worksheet->getCellByColumnAndRow($k, $row)->getValue();
										$check_course = $this->tbl_secondary_model->list('course', branch_now('pid'), [ 'title' => $course ]);
										if(count($check_course) > 0) {
											$course = $course[0]['pid'];
										} else {
											$course = $this->tbl_secondary_model->add([
												'type'			=> 'course',
												'title'			=> $course,
												'branch'		=> branch_now('pid'),
												'create_by'		=> auth_data('pid')
											]);
										}
										$classData_each[ $v ] = $course;
										break;
									
									case 'type':
										$type = strtolower($worksheet->getCellByColumnAndRow($k, $row)->getValue());
										switch($type) {
											case 'y':
												$type = 'yearly';
												break;
											case 'c':
												$type = 'check_in';
												break;
											default:
												$type = 'monthly';
												break;
										}
										$classData_each[ $v ] = $type;
										break;
										
									case 'date_start_yyyy':
									case 'date_start_mm':
									case 'date_start_dd':
										$date_start_yyyy = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
										$date_start_mm = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
										$date_start_dd = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
										if($date_start_mm < 10) { $date_start_mm = '0'.$date_start_mm; }
										if($date_start_dd < 10) { $date_start_dd = '0'.$date_start_dd; }
										if(!empty($date_start_yyyy) && !empty($date_start_mm) && !empty($date_start_dd)) {
											$classData_each[ 'date_start' ] = $date_start_yyyy.'-'.$date_start_mm.'-'.$date_start_dd;
										}
										if(strlen($date_start_yyyy) != 4 || strlen($date_start_mm) > 2 || strlen($date_start_dd) > 2) {
											$classData_each[ 'date_start' ] = null;
										}
										break;
										
									case 'date_end_yyyy':
									case 'date_end_mm':
									case 'date_end_dd':
										$date_end_yyyy = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
										$date_end_mm = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
										$date_end_dd = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
										if($date_end_mm < 10) { $date_end_mm = '0'.$date_end_mm; }
										if($date_end_dd < 10) { $date_end_dd = '0'.$date_end_dd; }
										if(!empty($date_end_yyyy) && !empty($date_end_mm) && !empty($date_end_dd)) {
											$classData_each[ 'date_end' ] = $date_end_yyyy.'-'.$date_end_mm.'-'.$date_end_dd;
										}
										if(strlen($date_end_yyyy) != 4 || strlen($date_end_mm) > 2 || strlen($date_end_dd) > 2) {
											$classData_each[ 'date_end' ] = null;
										}
										break;
										
									default:
										$classData_each[ $v ] = $worksheet->getCellByColumnAndRow($k, $row)->getValue();
										
										if(empty($worksheet->getCellByColumnAndRow($k, $row)->getValue())) {
											$classData_each[ $v ] = '';
										}
										break;
										
								}
								
							}
							
							foreach(['dy_1', 'dy_2', 'dy_3', 'dy_4', 'dy_5', 'dy_6', 'dy_7'] as $k => $v ) {
								
								if(strlen($classData_each[$v]) != 11) {
									$classData_each[$v] = null;
								}
								
								foreach(['0', '1', '3', '4', '6', '7', '9', '10'] as $k2 => $v2) {
									if(is_numeric($classData_each[$v][$v2]) == false) {
										$classData_each[$v] = null;
									}
								}
								
								foreach(['2', '8'] as $k3 => $v3) {
									if($classData_each[$v][$v3] != ':'){
										$classData_each[$v] = null;
									}
								}
								
								if($classData_each[$v][5] != '-') {
									$classData_each[$v] = null;
								}
		
							}
							
							$classData[] = $classData_each;
							
							sleep(1);
							
						}
						
					}

				}

			}
			
		 	$this->excel_import_model->insert($classData, 'class');
		 		
	 		alert_new('success', 'Data imported successfully');
			redirect('classes/list');

		}
	
	}




}
