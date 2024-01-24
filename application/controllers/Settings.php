<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require './vendor/PHPExcel/Excel.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

class Settings extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'settings';
		$this->single = 'setting';
		
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_admins_model');
		$this->load->model('tbl_branches_model');
		$this->load->model('tbl_secondary_model');
		$this->load->model('tbl_inventory_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('log_join_model');

		auth_must('login');

	}

	public function migrate()
	{
		
		// auth_must('login');
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
		
		if(isset($_POST['import_student'])) {
			
			require './vendor/phpspreadsheet/vendor/autoload.php';
            
            $inputFileName = $_FILES["file"]["tmp_name"];
            // $inputFileName = './users_import_template.xlsx';
            $spreadsheet = IOFactory::load($inputFileName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $sheetData = (array) $sheetData;
            
            // echo '<pre>'; print_r($sheetData); exit;
            
            $excel_data = [];
			
			$start_id = get_new('id');
			
            foreach($sheetData as $k => $e) {
                
                if($k == 1) {
                    if(
                        $e['A'] == '#' && 
                        $e['B'] == 'Status' && 
                        $e['C'] == 'Code' && 
                        $e['D'] == 'Name' &&
                        $e['E'] == '名字' &&
                        $e['F'] == 'NRIC' &&
                        $e['G'] == 'Form' &&
                        $e['H'] == 'Mobile No.' &&
                        $e['I'] == 'School' &&
                        $e['J'] == 'Childcare' &&
                        $e['K'] == 'T/P' &&
                        $e['L'] == 'Address' &&
                        $e['M'] == 'Reg. Date' &&
                        $e['N'] == 'Active Subject' &&
                        $e['O'] == 'Important/Urgent' &&
                        $e['P'] == 'Remark/Attention' &&
                        $e['Q'] == 'Father' &&
                        $e['R'] == 'NRIC' &&
                        $e['S'] == 'Mobile No' &&
                        $e['T'] == 'Mother' &&
                        $e['U'] == 'NRIC' &&
                        $e['V'] == 'Mobile No'
                    ) {
                    } else {
                        alert_new('warning', 'Format error');
						refresh();
                        break;
                    }
                    
                } else {
                    
                    if(!empty($e['A'])) {
						
						$form = null;
						if(!empty($e['G'])) {
							$form = $this->tbl_secondary_model->list('form', branch_now('pid'), [ 'title' => $e['G'] ]);
							if(count($form) == 0) {
								$form = $this->tbl_secondary_model->add([
									'title'		=> $e['G'],
									'type'		=> 'form',
									'branch'	=> branch_now('pid'),
									'create_by'	=> auth_data('pid')
								]);
							} else {
								$form = $form[0]['pid'];
							}
						}
						
						//if $sub = title
						//就把pid 的active turn to 1
						
						$school = null;
						if(!empty($e['I'])) {
							$school = $this->tbl_secondary_model->list('school', branch_now('pid'), [ 'title' => $e['I'] ]);
							if(count($school) == 0) {
								$school = $this->tbl_secondary_model->add([
									'title'		=> $e['I'],
									'type'		=> 'school',
									'branch'	=> branch_now('pid'),
									'create_by'	=> auth_data('pid')
								]);
							} else {
								$school = $school[0]['pid'];
							}
						}
						
						$childcare = null;
						if(!empty($e['J'])) {
							$childcare = $this->tbl_secondary_model->list('childcare', branch_now('pid'), [ 'title' => $e['J'] ]);
							if(count($childcare) == 0) {
								$childcare = $this->tbl_secondary_model->add([
									'title'		=> $e['J'],
									'type'		=> 'childcare',
									'branch'	=> branch_now('pid'),
									'create_by'	=> auth_data('pid')
								]);
							} else {
								$childcare = $childcare[0]['pid'];
							}
						}
						
						$tp = null;
						if(!empty($e['K'])) {
							$tp = $this->tbl_secondary_model->list('tp', branch_now('pid'), [ 'title' => $e['K'] ]);
							if(count($tp) == 0) {
								$tp = $this->tbl_secondary_model->add([
									'title'		=> $e['K'],
									'type'		=> 'tp',
									'branch'	=> branch_now('pid'),
									'create_by'	=> auth_data('pid')
								]);
							} else {
								$tp = $tp[0]['pid'];
							}
						}
						
						$father = null;
						if(!empty($e['Q'])) {
							$father = $this->tbl_users_model->list('parent', branch_now('pid'), [ 'fullname_en' => $e['Q'] ]);
							if(count($father) == 0) {
								$this->tbl_users_model->add2([
									'type'				=> 'parent',
									'pid'				=> $start_id,
									'fullname_en'		=> $e['Q'],
									'nric'				=> $e['R'],
									'phone'				=> $e['S'],
									'branch'			=> branch_now('pid'),
									'create_by'			=> auth_data('pid')
								]);
								$father = $start_id;
								$start_id++;
							} else {
								$father = $father[0]['pid'];
							}
						}
						
						$mother = null;
						if(!empty($e['T'])) {
							$mother = $this->tbl_users_model->list('parent', branch_now('pid'), [ 'fullname_en' => $e['T'] ]);
							if(count($mother) == 0) {
								$this->tbl_users_model->add2([
									'type'				=> 'parent',
									'pid'				=> $start_id,
									'fullname_en'		=> $e['T'],
									'nric'				=> $e['U'],
									'phone'				=> $e['V'],
									'branch'			=> branch_now('pid'),
									'create_by'			=> auth_data('pid')
								]);
								$mother = $start_id;
								$start_id++;
							} else {
								$mother = $mother[0]['pid'];
							}
						}
                        $excel_data[] = [
							'pid'					=> $start_id,
                            'type'					=> 'student',
                            'active'				=> $e['B'],
                            'code'					=> $e['C'],
                            'fullname_en'			=> $e['D'],
                            'fullname_cn'			=> $e['E'],
                            'nric'					=> $e['F'],
                            'form'					=> $form,
                            'phone'					=> $e['H'],
                            'school'				=> $school,
                            'childcare'				=> $childcare,
                            'tp'					=> $tp,
                            'address'				=> $e['L'],
                            'date_join'				=> date('Y-m-d', strtotime($e['M'])),
                            'remark_active'			=> $e['N'],
                            'remark_important'		=> $e['O'],
                            'remark'				=> $e['P'],
                            'branch'				=> branch_now('pid'),
                            'create_by'				=> auth_data('pid'),
                        ];
						
						if($father != null) {
							$this->log_join_model->add([
								'type'			=> 'join_parent',
								'user'			=> $start_id,
								'parent'		=> $father,
								'branch'		=> branch_now('pid'),
								'create_by'		=> auth_data('pid'),
							]);
						}
						
						if($mother != null) {
							$this->log_join_model->add([
								'type'			=> 'join_parent',
								'user'			=> $start_id,
								'parent'		=> $mother,
								'branch'		=> branch_now('pid'),
								'create_by'		=> auth_data('pid'),
							]);
						}
												
                    } else {
                        break;
                    }
					
					$start_id++;
                    
                }
                
            }
			
			if(count($excel_data) > 0) {
				// Tan Jing Suan
                // $this->tbl_users_model->add_batch($excel_data);
                foreach ($excel_data as $excel) {
                	if ( strval($excel['fullname_en']) === "" && strval($excel['fullname_cn']) === "" ) {
                		continue;
                	}
                	if ( strval($excel['nric']) === "" ) {
                		$this->tbl_users_model->add2($excel);
                		continue;
                	}
                	// $pid = datalist_Table('tbl_users', 'pid', $excel['nric'], 'nric');
                	$nric = $this->tbl_users_model->check_nric( $excel['nric'] );
                	if ( isset($nric[0]['pid']) ) {
                		$excel['pid'] = $nric[0]['pid'];
                		$this->tbl_users_model->edit($nric[0]['pid'], $excel);
                	} else {
                		$this->tbl_users_model->add2($excel);
                	}
                }
                alert_new('success', 'Import successfully');
            } else {
                alert_new('warning', 'No data imported');
            }
			
			$subject= $e['N']; 
			$substring= substr($subject, strpos($subject, ')') + 1);
			$str_arr = explode (",", $substring);

			for($x=0; $x< count($str_arr); $x++){
				$sub = $str_arr[$x];
				$class_id = datalist_Table('tbl_classes', 'pid', $sub, 'title');
				
				$stu_id = datalist_Table('tbl_users', 'pid', $e['C'], 'code');
				
				if(isset($class_id) && isset($stu_id)) {
														
					$this->log_join_model->std_class_active($class_id, $stu_id);

				}
				// echo $class_id; verbose($stu_id);
			}
            
            alert_new('success', 'Student imported successfully');
			redirect('students/list');

		}
		
		if(isset($_POST['import_parent'])) {
			
			require './vendor/phpspreadsheet/vendor/autoload.php';
            
            $inputFileName = $_FILES["file"]["tmp_name"];
            // $inputFileName = './users_import_template.xlsx';
            $spreadsheet = IOFactory::load($inputFileName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $sheetData = (array) $sheetData;
            
            // echo '<pre>'; print_r($sheetData); exit;
            
            $excel_data = [];
			
			$start_id = get_new('id');
			
            foreach($sheetData as $k => $e) {
                
                if($k == 1) {
                    if(
                        $e['A'] == 'Full Name' && 
                        $e['B'] == 'Full Name (CN)' && 
                        $e['C'] == 'Active (1/0)' && 
                        $e['D'] == 'NRIC' &&
                        $e['E'] == 'Birthday (YYYY)' &&
                        $e['F'] == 'Birthday (MM)' &&
                        $e['G'] == 'Birthday (DD)' &&
                        $e['H'] == 'Gender' &&
                        $e['I'] == 'Username' &&
                        $e['J'] == 'Password' &&
                        $e['K'] == 'Phone' &&
                        $e['L'] == 'Phone 2' &&
                        $e['M'] == 'Phone 3' &&
                        $e['N'] == 'Email' &&
                        $e['O'] == 'Email 2' &&
                        $e['P'] == 'Email 3' &&
                        $e['Q'] == 'Address' &&
                        $e['R'] == 'Address 2' &&
                        $e['S'] == 'Address 3' &&
                        $e['T'] == 'Join Date (YYYY)' &&
                        $e['U'] == 'Join Date (MM)' &&
                        $e['V'] == 'Join Date (DD)' &&
                        $e['W'] == 'Remark'
                    ) {
                    } else {
                        alert_new('warning', 'Format error');
						refresh();
                        break;
                    }
                    
                } else {
					
					$birthday = null;
					if(!empty($e['E']) && !empty($e['F']) && !empty($e['G'])) {
						$birthday = date('Y-m-d', strtotime($e['E'] . '-' . $e['F'] . '-' . $e['G']));
					}
					
					$password = null;
					if(!empty($e['J'])) {
						$password = password_hash($e['J'], PASSWORD_DEFAULT);
					}
					
					$date_join = null;
					if(!empty($e['T']) && !empty($e['U']) && !empty($e['V'])) {
						$date_join = date('Y-m-d', strtotime($e['T'] . '-' . $e['U'] . '-' . $e['V']));
					}
                    
                    if(!empty($e['A'])) {
						
                        $excel_data[] = [
							'pid'					=> $start_id,
                            'type'					=> 'parent',
                            'fullname_en'			=> $e['A'],
                            'fullname_cn'			=> $e['B'],
                            'active'				=> $e['C'],
                            'nric'					=> $e['D'],
                            'birthday'				=> $birthday,
                            'gender'				=> lcfirst($e['H']),
                            'username'				=> $e['I'],
                            'password'				=> $password,
                            'phone'					=> $e['K'],
                            'phone2'				=> $e['L'],
                            'phone3'				=> $e['M'],
                            'email'					=> $e['N'],
                            'email2'				=> $e['O'],
                            'email3'				=> $e['P'],
                            'address'				=> $e['Q'],
                            'address2'				=> $e['R'],
                            'address3'				=> $e['S'],
                            'date_join'				=> $date_join,
                            'remark'				=> $e['W'],
                            'branch'				=> branch_now('pid'),
                            'create_by'				=> auth_data('pid'),
                        ];
						
                    } else {
                        break;
                    }
					
					$start_id++;
                    
                }
                
            }
			
			if(count($excel_data) > 0) {
				// Tan Jing Suan
                // $this->tbl_users_model->add_batch($excel_data);
                foreach ($excel_data as $excel) {
                	if ( strval($excel['fullname_en']) === "" && strval($excel['fullname_cn']) === "" ) {
                		continue;
                	}
                	if ( strval($excel['nric']) === "" ) {
                		$this->tbl_users_model->add2($excel);
                		continue;
                	}
                	// $pid = datalist_Table('tbl_users', 'pid', $excel['nric'], 'nric');
                	$nric = $this->tbl_users_model->check_nric( $excel['nric'] );
                	if ( isset($nric[0]['pid']) ) {
                		$excel['pid'] = $nric[0]['pid'];
                		$this->tbl_users_model->edit($nric[0]['pid'], $excel);
                	} else {
                		$this->tbl_users_model->add2($excel);
                	}
                }
                alert_new('success', 'Import successfully');
            } else {
                alert_new('warning', 'No data imported');
            }
            
            alert_new('success', 'Parent imported successfully');
			redirect('parents/list');

		}
		
		if(isset($_POST['import_teacher'])) {
			
			require './vendor/phpspreadsheet/vendor/autoload.php';
            
            $inputFileName = $_FILES["file"]["tmp_name"];
            // $inputFileName = './users_import_template.xlsx';
            $spreadsheet = IOFactory::load($inputFileName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $sheetData = (array) $sheetData;
            
            // echo '<pre>'; print_r($sheetData); exit;
            
            $excel_data = [];
			
			$start_id = get_new('id');
			
            foreach($sheetData as $k => $e) {
                
                if($k == 1) {
                    if(
                        $e['A'] == 'Full Name' && 
                        $e['B'] == 'Full Name (CN)' && 
                        $e['C'] == 'Active (1/0)' && 
                        $e['D'] == 'NRIC' &&
                        $e['E'] == 'Birthday (YYYY)' &&
                        $e['F'] == 'Birthday (MM)' &&
                        $e['G'] == 'Birthday (DD)' &&
                        $e['H'] == 'Gender' &&
                        $e['I'] == 'Username' &&
                        $e['J'] == 'Password' &&
                        $e['K'] == 'Phone' &&
                        $e['L'] == 'Phone 2' &&
                        $e['M'] == 'Phone 3' &&
                        $e['N'] == 'Email' &&
                        $e['O'] == 'Email 2' &&
                        $e['P'] == 'Email 3' &&
                        $e['Q'] == 'Address' &&
                        $e['R'] == 'Address 2' &&
                        $e['S'] == 'Address 3' &&
                        $e['T'] == 'Join Date (YYYY)' &&
                        $e['U'] == 'Join Date (MM)' &&
                        $e['V'] == 'Join Date (DD)' &&
                        $e['W'] == 'Card ID' &&
                        $e['X'] == 'Remark'
                    ) {
                    } else {
                        alert_new('warning', 'Format error');
						refresh();
                        break;
                    }
                    
                } else {
					
					$birthday = null;
					if(!empty($e['E']) && !empty($e['F']) && !empty($e['G'])) {
						$birthday = date('Y-m-d', strtotime($e['E'] . '-' . $e['F'] . '-' . $e['G']));
					}
					
					$password = null;
					if(!empty($e['J'])) {
						$password = password_hash($e['J'], PASSWORD_DEFAULT);
					}
					
					$date_join = null;
					if(!empty($e['T']) && !empty($e['U']) && !empty($e['V'])) {
						$date_join = date('Y-m-d', strtotime($e['T'] . '-' . $e['U'] . '-' . $e['V']));
					}
                    
                    if(!empty($e['A'])) {
						
                        $excel_data[] = [
							'pid'					=> $start_id,
                            'type'					=> 'teacher',
                            'fullname_en'			=> $e['A'],
                            'fullname_cn'			=> $e['B'],
                            'active'				=> $e['C'],
                            'nric'					=> $e['D'],
                            'birthday'				=> $birthday,
                            'gender'				=> lcfirst($e['H']),
                            'username'				=> $e['I'],
                            'password'				=> $password,
                            'phone'					=> $e['K'],
                            'phone2'				=> $e['L'],
                            'phone3'				=> $e['M'],
                            'email'					=> $e['N'],
                            'email2'				=> $e['O'],
                            'email3'				=> $e['P'],
                            'address'				=> $e['Q'],
                            'address2'				=> $e['R'],
                            'address3'				=> $e['S'],
                            'date_join'				=> $date_join,
                            'rfid_cardid'			=> $e['W'],
                            'remark'				=> $e['X'],
                            'branch'				=> branch_now('pid'),
                            'create_by'				=> auth_data('pid'),
                        ];
						
                    } else {
                        break;
                    }
					
					$start_id++;
                    
                }
                
            }
			
			if(count($excel_data) > 0) {
				// Tan Jing Suan
                // $this->tbl_users_model->add_batch($excel_data);
                foreach ($excel_data as $excel) {
                	if ( strval($excel['fullname_en']) === "" && strval($excel['fullname_cn']) === "" ) {
                		continue;
                	}
                	if ( strval($excel['nric']) === "" ) {
                		$this->tbl_users_model->add2($excel);
                		continue;
                	}
                	// $pid = datalist_Table('tbl_users', 'pid', $excel['nric'], 'nric');
                	$nric = $this->tbl_users_model->check_nric( $excel['nric'] );
                	if ( isset($nric[0]['pid']) ) {
                		$excel['pid'] = $nric[0]['pid'];
                		$this->tbl_users_model->edit($nric[0]['pid'], $excel);
                	} else {
                		$this->tbl_users_model->add2($excel);
                	}
                }
                alert_new('success', 'Import successfully');
            } else {
                alert_new('warning', 'No data imported');
            }
			
            alert_new('success', 'Teacher imported successfully');
			redirect('teachers/list');

		}
		
		if(isset($_POST['import_item'])) {
			
			require './vendor/phpspreadsheet/vendor/autoload.php';
            
            $inputFileName = $_FILES["file"]["tmp_name"];
            // $inputFileName = './users_import_template.xlsx';
            $spreadsheet = IOFactory::load($inputFileName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $sheetData = (array) $sheetData;
            
            // echo '<pre>'; print_r($sheetData); exit;
            
            $excel_data = [];
			
			$start_id = get_new('id');
			
            foreach($sheetData as $k => $e) {
				                
                if($k == 1) {
                    if(
                        $e['A'] == 'Title' && 
                        $e['B'] == 'SKU' && 
                        $e['C'] == 'Active (1/0)' && 
                        $e['D'] == 'Category' &&
                        $e['E'] == 'Type (P = Product / S = Service)' &&
                        $e['F'] == 'Price Cost' &&
                        $e['G'] == 'Price Min' &&
                        $e['H'] == 'Price Sales' &&
                        $e['I'] == 'Remark'
                    ) {
                    } else {
                        alert_new('warning', 'Format error');
						refresh();
                        break;
                    }
                    
                } else {
					
					$item_type = 'product';
					if($e['E'] == 's') {
						$item_type = 'service';
					}
					
					$category = null;
					if(!empty($e['D'])) {
						$category = $this->tbl_secondary_model->list('category', branch_now('pid'), [ 'title' => $e['D'] ]);
						if(count($category) == 0) {
							$category = $this->tbl_secondary_model->add([
								'type'				=> 'item_cat',
								'pid'				=> $start_id,
								'title'				=> $e['D'],
								'branch'			=> branch_now('pid'),
								'create_by'			=> auth_data('pid')
							]);
						} else {
							$category = $category[0]['pid'];
						}
					}
                    
                    if(!empty($e['A'])) {
						
                        $excel_data[] = [
							'pid'					=> $start_id,
                            'type'					=> 'item',
							'stock_ctrl' 			=> '1',
                            'title'					=> $e['A'],
                            'sku'					=> $e['B'],
                            'active'				=> $e['C'],
                            'category'				=> $category,
                            'item_type'				=> $item_type,
                            'price_cost'			=> $e['F'],
                            'price_min'				=> $e['G'],
                            'price_sale'			=> $e['H'],
                            'remark'				=> $e['I'],
                            'branch'				=> branch_now('pid'),
                            'create_by'				=> auth_data('pid'),
                        ];
						
                    } else {
                        break;
                    }
					
					$start_id++;
                    
                }
                
            }
			
			if(count($excel_data) > 0) {
                $this->tbl_inventory_model->add_batch($excel_data);
                alert_new('success', 'Import successfully');
            } else {
                alert_new('warning', 'No data imported');
            }
			
            alert_new('success', 'Item imported successfully');
			redirect('items/list');

		}
		
		if(isset($_POST['import_class'])) {
			
			require './vendor/phpspreadsheet/vendor/autoload.php';
            
            $inputFileName = $_FILES["file"]["tmp_name"];
            // $inputFileName = './users_import_template.xlsx';
            $spreadsheet = IOFactory::load($inputFileName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $sheetData = (array) $sheetData;
            
            // echo '<pre>'; print_r($sheetData); exit;
            
            $excel_data = [];
			
			$start_id = get_new('id');
			
            foreach($sheetData as $k => $e) {
				
                if($k == 1) {
                    if(
                        $e['A'] == 'Title' && 
                        $e['B'] == 'Tutor' && 
                        $e['C'] == 'Course' && 
                        $e['D'] == 'Type (M =  Per Month, Y = Per Year, C = Per Check-in)' &&
                        $e['E'] == 'Start Date (YYYY)' &&
                        $e['F'] == 'Start Date (MM)' &&
                        $e['G'] == 'Start Date (DD)' &&
                        $e['H'] == 'End Date (YYYY)' &&
                        $e['I'] == 'End Date (MM)' &&
                        $e['J'] == 'End Date (DD)' &&
                        $e['K'] == 'Fee' &&
                        $e['L'] == 'Active (1/0)' &&
                        $e['M'] == 'Monday' &&
                        $e['N'] == 'Tuesday' &&
                        $e['O'] == 'Wednesday' &&
                        $e['P'] == 'Thursday' &&
                        $e['Q'] == 'Friday' &&
                        $e['R'] == 'Saturday' &&
                        $e['S'] == 'Sunday' &&
                        $e['T'] == 'Remark'
                    ) {
                    } else {
                        alert_new('warning', 'Format error');
						refresh();
                        break;
                    }
                    
                } else {
					
					$type = 'monthly';
					if($e['E'] == 'y') {
						$type = 'yearly';
					} elseif($e['E'] == 'c') {
						$type = 'check_in';
					}
					
					$teacher = null;
					if(!empty($e['C'])) {
						$teacher = $this->tbl_users_model->list('teacher', branch_now('pid'), [ 'fullname_en' => $e['B'] ]);
						if(count($teacher) == 0) {
							$teacher = $this->tbl_users_model->add([
								'type'				=> 'teacher',
								'pid'				=> $start_id,
								'fullname_en'		=> $e['B'],
								'branch'			=> branch_now('pid'),
								'create_by'			=> auth_data('pid')
							]);
						} else {
							$teacher = $teacher[0]['pid'];
						}
					}
					
					$course = null;
					if(!empty($e['C'])) {
						$course = $this->tbl_secondary_model->list('course', branch_now('pid'), [ 'title' => $e['D'] ]);
						if(count($course) == 0) {
							$course = $this->tbl_secondary_model->add([
								'type'				=> 'course',
								'pid'				=> $start_id,
								'title'				=> $e['D'],
								'branch'			=> branch_now('pid'),
								'create_by'			=> auth_data('pid')
							]);
						} else {
							$course = $course[0]['pid'];
						}
					}
					
					$date_start = null;
					if(!empty($e['E']) && !empty($e['F']) && !empty($e['G'])) {
						$date_start = date('Y-m-d', strtotime($e['E'] . '-' . $e['F'] . '-' . $e['G']));
					}
					
					$date_end = null;
					if(!empty($e['H']) && !empty($e['I']) && !empty($e['J'])) {
						$date_end = date('Y-m-d', strtotime($e['H'] . '-' . $e['I'] . '-' . $e['J']));
					}
					
					foreach(['M', 'N', 'O', 'P', 'Q', 'R', 'S'] as $day) {
						if(empty($e[$day])) {
							$e[$day] = null;
						} else {
							if(!preg_match('/[0-9]{2}:[0-9]{2}-[0-9]{2}:[0-9]{2}$/i', $e[$day])) {
								$e[$day] = null;
							}
						}
						
					}
                    
                    if(!empty($e['A'])) {
						
                        $excel_data[] = [
							'pid'					=> $start_id,
                            'title'					=> $e['A'],
                            'teacher'				=> $teacher,
                            'course'				=> $course,
                            'type'					=> $type,
                            'date_start'			=> $date_start,
                            'date_end'				=> $date_end,
                            'fee'					=> $e['K'],
                            'active'				=> $e['L'],
                            'dy_1'					=> $e['M'],
                            'dy_2'					=> $e['N'],
                            'dy_3'					=> $e['O'],
                            'dy_4'					=> $e['P'],
                            'dy_5'					=> $e['Q'],
                            'dy_6'					=> $e['R'],
                            'dy_7'					=> $e['S'],
                            'remark'				=> $e['T'],
                            'branch'				=> branch_now('pid'),
                            'create_by'				=> auth_data('pid'),
                        ];
						
                    } else {
                        break;
                    }
					
					$start_id++;
                    
                }
                
            }
			
			if(count($excel_data) > 0) {
                $this->tbl_classes_model->add_batch($excel_data);
                alert_new('success', 'Import successfully');
            } else {
                alert_new('warning', 'No data imported');
            }
			
            alert_new('success', 'Class imported successfully');
			redirect('classes/list');

		}
	
	}
	
	public function general()
	{
		
		auth_must('login');
		check_module_page('Settings/Read');
		// check_module_page('Settings/Modules/General');
		
		$data['thispage'] = [
			'title' => 'General',
			'group' => $this->group,
			'js' => $this->group.'/general',
			'css' => $this->group.'/general',
		];
		
		if(!isset($_GET['tab'])) $_GET['tab'] = 1;
		
		$data['branch_data'] = $this->tbl_branches_model->view(branch_now('pid'));
		
		$branch_bank = [];
			foreach($this->log_join_model->list('secondary', branch_now('pid'), ['active' => 1]) as $e) {
				if(datalist_Table('tbl_secondary', 'type', $e['secondary']) == 'bank') {
					$branch_bank[] = $e;
				}
			}
			
		$data['bank_all'] = $branch_bank;
		$data['bank_now'] = $this->tbl_secondary_model->list('bank', branch_now('pid'), [ 'active' => 1 ]);
				
		foreach(['fpx', 'ccard', 'ewallet'] as $e) {
			
			if(isset($_POST['save_'.$e])) {
			
				$post_data = [];
				$post_data['gateway_'.$e] = 1;
				$post_data['gateway_'.$e.'_pg'] = $this->input->post('gateway_'.$e.'_pg');
				
				alert_new('success', 'General updated successfully');
				
				$this->tbl_branches_model->edit(branch_now('pid'), $post_data);
				
				redirect('settings/general?tab=1');
				
			}
		}
		
		if(isset($_POST['save_qrpay'])) {
			
			$post_data = [];
			$post_data['gateway_qrpay'] = 1;
			
			foreach(['boost', 'shopee', 'grab', 'duit', 'tng'] as $e) {
				if(isset($_FILES['gateway_qrpay_img_'.$e])) {
					$post_data['gateway_qrpay_img_'.$e] = pointoapi_Upload($_FILES['gateway_qrpay_img_'.$e], [
						'default' => $data['branch_data']['gateway_qrpay_img_'.$e],
						'type' => 'gateway_qrpay_img_'.$e,
						'branch' => branch_now('pid'),
					]);
				}
			}
			
			alert_new('success', 'General updated successfully');
			
			$this->tbl_branches_model->edit(branch_now('pid'), $post_data);
			
			redirect('settings/general?tab=1');
			
		}
		
		if(isset($_POST['save_transfer'])) {
			
			$post_data = [];
			$post_data['gateway_transfer'] = 1;

			foreach(['bank', 'acc_no', 'acc_name'] as $e) {
				$post_data[$e] = $this->input->post($e);
			}
			
			// print_r($post_data); exit;
			
			alert_new('success', 'General updated successfully');
			
			$this->tbl_branches_model->edit(branch_now('pid'), $post_data);
			
			redirect('settings/general?tab=1');
			
		}
		
		// v1 by soon
		/*foreach(['boost', 'shopee', 'grab', 'duit', 'tng'] as $e) {
			if(isset($_POST['remove_'.$e])) {
				
				$post_data = [];
				$post_data['gateway_qrpay_img_'.$e] = null;

				alert_new('success', 'General updated successfully');
				$this->tbl_branches_model->edit(branch_now('pid'), $post_data);
				redirect('settings/general?tab=1');
				
			}
		}*/
		
		// v2 by steve
		if(isset($_POST['remove_image'])) {
			
			$post_data = [];
			$post_data['gateway_qrpay_img_'.$_POST['remove_image']] = null;

			$this->tbl_branches_model->edit(branch_now('pid'), $post_data);
			
			// alert_new('success', 'General updated successfully');
			// redirect('settings/general?tab=1');
			
			header('Content-type: application/json');
			die(json_encode(['status' => 'ok', 'message' => 'Image removed successfully']));
			
		}
		
		if(isset($_POST['save_msg'])) {
			
			$post_data = [];
						
			foreach(['send_msg_whatsapp', 'send_msg_sms', 'send_msg_email'] as $e) {
				$post_data[$e] = $this->input->post($e);
			}
			
			alert_new('success', 'General updated successfully');
			
			$this->tbl_branches_model->edit(branch_now('pid'), $post_data);
			
			redirect('settings/general?tab=2');
			
		}
		
		if(isset($_POST['save_msg_outstanding'])) {
			
			$post_data = [];
						
			foreach(['send_msg_whatsapp_outstanding', 'send_msg_sms_outstanding'] as $e) {
				$post_data[$e] = $this->input->post($e);
			}
			
			alert_new('success', 'General updated successfully');
			
			$this->tbl_branches_model->edit(branch_now('pid'), $post_data);
			
			redirect('settings/general?tab=3');
			
		}
		
		if(isset($_POST['save_support_box'])) {
			
			$active_support_box = empty($this->input->post('active_support_box')) ? 0 : 1;
			alert_new('success', 'General updated successfully');
			
			$this->tbl_branches_model->edit(branch_now('pid'), [ 'active_support_box' => $active_support_box ]);
			
			redirect('settings/general?tab=4');
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/general', $data);
		$this->load->view('inc/footer', $data);
		
	}
	
	// to disable app
	public function json_disable_app($method){

		$post_data = [];
		$post_data[$method] = 0;
		
		switch($method) {
			case 'gateway_qrpay':
				foreach(['boost', 'shopee', 'grab', 'duit', 'tng'] as $e) {
					$post_data['gateway_qrpay_img_'.$e] = null;
				}
				break;
				
			case 'gateway_transfer':
				foreach(['bank', 'acc_no', 'acc_name'] as $e) {
					$post_data[$e] = '';
				}
				break;
			
			default:
				$post_data[$method.'_pg'] = null;
				break;
		}
		
		$this->tbl_branches_model->edit(branch_now('pid'), $post_data);

		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));

	}
	
	public function notify()
	{
		
		auth_must('login');
		check_module_page('Settings/Read');
		check_module_page('Settings/Modules/Notify');
		
		$data['thispage'] = [
			'title' => 'Notify',
			'group' => $this->group,
			'js' => $this->group.'/notify',
			'css' => $this->group.'/notify',
		];
		
		if(!isset($_GET['tab'])) $_GET['tab'] = '';
		
		$data['branch_data'] = $this->tbl_branches_model->view(branch_now('pid'));
		
		if(isset($_POST['save'])) {
			
			$post_data = [];
			$type = $this->input->post('type');
			
			switch ($type) {
				
				case 'payment_success':
					$post_data['notify_payment'] = $this->input->post('method');
					$post_data['notify_payment_msg'] = $this->input->post('message');
					break;
				
				case 'outstanding':
					$post_data['notify_outstanding'] = $this->input->post('method');
					$post_data['notify_outstanding_msg'] = $this->input->post('message');
					break;
					
				default:
					$post_data['notify_attendance'] = $this->input->post('method');
					$post_data['notify_attendance_msg'] = $this->input->post('message');
					break;
					
			}
			
			alert_new('success', 'Notify updated successfully');
			
			$this->tbl_branches_model->edit(branch_now('pid'), $post_data);
			
			redirect('settings/notify?tab='.$type);
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/notify', $data);
		$this->load->view('inc/footer', $data);
		
	}
	
	public function receipt()
	{
		
		$this->load->model('tbl_payment_model');
		
		auth_must('login');
		check_module_page('Settings/Read');
		check_module_page('Settings/Modules/Receipt');
		
		$data['thispage'] = [
			'title' => 'Receipt',
			'group' => $this->group,
			'js' => $this->group.'/receipt',
		];
		
		if(!isset($_GET['tab'])) $_GET['tab'] = 1;
		
		if(isset($_POST['save'])) {
			
			$post_data['receipt_no'] = $this->input->post("receipt_no");
			$post_data['receipt_no_max'] = $this->input->post("receipt_no_max");
			
			$this->tbl_branches_model->edit(branch_now('pid'), $post_data);
			
			alert_new('success', 'Receipt No updated successfully');
			
			redirect($this->group.'/receipt?tab=1');
		}
		
		if(isset($_POST['save-account'])) {
			
			$post_data['tax'] = $this->input->post("tax");
			// $post_data['rounding'] = $this->input->post("rounding");
			
			$this->tbl_branches_model->edit(branch_now('pid'), $post_data);
			
			alert_new('success', 'Accout updated successfully');
			
			redirect($this->group.'/receipt?tab=3');
		}
		
		if(isset($_POST['payment'])) {
			foreach($_POST['student_id'] as $id) {
				$status = ($_POST['studentcheck'][$id] == NULL)?0:$_POST['studentcheck'][$id];
				$this->tbl_users_model->edit($id, ['payment' => $status]);
			}
			redirect($this->group.'/receipt?tab=4');
		}
		
		$data['branch_data'] = $this->tbl_branches_model->view(branch_now('pid'));
		$data['receipt'] = $this->tbl_secondary_model->list('receipt', null, [
			'active' => 1
		]);
		
		$data['students'] = $this->tbl_users_model->list('student', branch_now('pid'), ['is_delete' => 0]);
		
		$data['result'] = $this->tbl_payment_model->list(branch_now('pid'));
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/receipt', $data);
		$this->load->view('inc/footer', $data);
		
	}
	
	// to disable notification
	public function json_disable_notify($method){

		$post_data[$method] = '';
		$post_data[$method.'_msg'] = '';
		$this->tbl_branches_model->edit(branch_now('pid'), $post_data);

		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));

	}
	
	// get data for receipt
	public function json_current_branch(){

		$result = $this->tbl_branches_model->view(branch_now('pid'));

		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'result' => $result]));

	}
	
	public function update_receipt($receipt){

		$post_data['receipt_print'] = $receipt;
		$this->tbl_branches_model->edit(branch_now('pid'), $post_data);

		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));

	}
	
	public function pointoapi()
	{
		
		auth_must('login');
		check_module_page('Settings/Read');
		check_module_page('Settings/Modules/PointoAPI');
		
		$data['thispage'] = [
			'title' => 'PointoAPI',
			'group' => $this->group,
			'js' => $this->group.'/pointoapi',
		];
		
		$data['result'] = $this->tbl_branches_model->view(branch_now('pid'));
		
		if(isset($_POST['save'])) {
			$post_data['pointoapi_key'] = $this->input->post("pointoapi_key");
			$this->tbl_branches_model->edit(branch_now('pid'), $post_data);
			alert_new('success', 'PointoAPI updated successfully');
			redirect($this->group.'/pointoapi');
		}
		
		if(isset($_POST['json'])) {
			
			header('Content-type: application/json');
			
			$response = pointoapi_Request('PointoAPI/Cert/Verify', [
				'api_key' => $_POST['json'],
			]);

			if(empty($_POST['json'])) {
				
				die(json_encode([ 'status' => 'ok' ]));
				
			} elseif(isset($response['status'])) {
				
				die(json_encode([ 'status' => $response['status'] ]));
				
			} else {
				
				die(json_encode([ 'status' => 'error' ]));
				
			}
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/pointoapi', $data);
		$this->load->view('inc/footer', $data);
		
	}
	
	public function reset_std()
	{
		
		auth_must('login');
		check_module_page('Settings/Read');
		check_module_page('Settings/Modules/ResetStudent');
		
		$data['thispage'] = [
			'title' => 'Reset Student',
			'group' => $this->group,
			'js' => $this->group.'/reset_std',
		];
		
		if(isset($_POST['del'])) {
			
			header('Content-type: application/json');
			
			$sql = '
			
				UPDATE tbl_users
				SET is_delete = 1
				WHERE type = "student"
			
			';
			
			$this->db->query($sql);
			
			die(json_encode([ 'status' => 'ok', 'message' => 'Student reset successfully' ]));
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/reset_std', $data);
		$this->load->view('inc/footer', $data);
		
	}
	
	public function reset_parent()
	{
		
		auth_must('login');
		check_module_page('Settings/Read');
		check_module_page('Settings/Modules/ResetParent');
		
		$data['thispage'] = [
			'title' => 'Reset Parent',
			'group' => $this->group,
			'js' => $this->group.'/reset_parent',
		];
		
		if(isset($_POST['del'])) {
			
			header('Content-type: application/json');
			
			$sql = '
			
				UPDATE tbl_users
				SET is_delete = 1
				WHERE type = "parent"
			
			';
			
			$this->db->query($sql);
			
			die(json_encode([ 'status' => 'ok', 'message' => 'Parent reset successfully' ]));
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/reset_parent', $data);
		$this->load->view('inc/footer', $data);
		
	}

}
