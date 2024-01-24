<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require './vendor/mpdf/autoload.php';

// require_once(APPPATH."third_party/lpr/PrintSend.php");
// require_once(APPPATH."third_party/lpr/PrintSendLPR.php");

class Export extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'export';
		$this->single = 'export';
		
		$this->load->model('tbl_branches_model');
		$this->load->model('tbl_payment_model');
		$this->load->model('log_payment_model');
		$this->load->model('log_point_model');
		$this->load->library('PrintPdf');

	}
	
	public function zip($urls='')
	{	
		$this->load->library('zip');
		$urls = explode('|', urldecode($urls));
		
		foreach( $urls as $k => $e ) {
			
			$urls[$k] = 'https://system.synorex.work/highpeakedu/export/pdf_export/'.$e;
			
		}
		/* 
	    $urls = array(
            'https://system.synorex.work/highpeakedu/export/pdf_export/169114462618',
            'https://system.synorex.work/highpeakedu/export/pdf_export/169114385476',
            // Add more URLs as needed
        );
		*/
        
		/*
        // Function to convert URL to PDF using mPDF and return the PDF content
        function urlToPDF($url) {
            $mpdf = new \Mpdf\Mpdf();
            $html = file_get_contents($url);
            $mpdf->WriteHTML($html);
            return $mpdf->Output('', 'S'); // Return PDF content as string
        }
		*/
		
        // Create a zip file
        $zip = new ZipArchive();
		$zipFileName = "files.zip";

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            die('Failed to create zip file');
        }
        
        // Download each link, convert it to PDF, and add it to the zip file
        foreach ($urls as $url) {
			
            // Generate a filename for the PDF (you may adjust the filenames as per your requirements)
			$id = end(preg_split("#/#", $url));
			$payment = $this->tbl_payment_model->view($id);
            $pdfFilename = $payment[0]['payment_no']. '.pdf';
        
            // Add the PDF content to the zip file
            $zip->addFromString($pdfFilename, file_get_contents($url)); 
			
        }
        
        // Close the zip file
        $zip->close();
        
        // Download the zip file
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
        header('Content-Length: ' . filesize($zipFileName));
        readfile($zipFileName);
        
        // Delete the zip file from the server after download (optional)
        unlink($zipFileName);
	    
	}
	
	public function pdf_ewallet_each($id='')
	{	
	    
		$mpdf = new \Mpdf\Mpdf();
		
		$path = __DIR__.'/-Export/pdf_ewallet_each.php';
		
		if(file_exists($path)) {
		    
			$point = $this->log_point_model->list(['id' => $id])[0];
			$branch_id = datalist_Table('tbl_users', 'branch', $point['user']);
			$branch = $this->tbl_branches_model->list(['pid'=>$branch_id])[0];
			
			require $path;
			
		} else {
			
			die(app('title').': Receipt theme not found');
			
		}

	}
		
	public function pdf_exportA4()
	{	
		
		$mpdf = new \Mpdf\Mpdf();
		
		$path = __DIR__.'/-Export/pdf_export/abacusA4.php';
		
		if(file_exists($path)) {
			
			require $path;
			
		} else {
			
			die(app('title').': Receipt theme not found');
			
		}

	}
	public function pdf_exportA3()
	{	
		
		$mpdf = new \Mpdf\Mpdf();
		
		$path = __DIR__.'/-Export/pdf_export/abacusA3.php';
		
		if(file_exists($path)) {
			
			require $path;
			
		} else {
			
			die(app('title').': Receipt theme not found');
			
		}

	}
	
	// 2021-11-09 steve
	public function pdf_export($id = '')
	{	
		// model
		$this->load->model('tbl_users_model');
		
		if( isset($_POST['id']) && $_POST['id'] != '') $id = $_POST['id'];
		
		// check receipt
		//$payment = $this->tbl_payment_model->view($id);
		
		$sql = 'SELECT tbl_payment.*, students.code AS student_code, students.fullname_en AS client, students.phone AS client_phone, 
				students.address AS client_address, students.payment AS receipt_display,
				forms.title AS form_title, payment_methods.title AS payment_method_title,
				students.email AS client_email, created_bys.nickname AS cashier, 
				tbl_branches.title AS branch_title, tbl_branches.email AS branch_email, tbl_branches.phone AS branch_phone, 
				tbl_branches.address AS branch_address, tbl_branches.receipt_print AS branch_receipt_print,
				branch_owners.nickname AS owner, tbl_uploads.file_source AS image
				FROM tbl_payment
				JOIN tbl_branches ON tbl_branches.pid = tbl_payment.branch
				LEFT JOIN tbl_users students ON students.pid = tbl_payment.student
				LEFT JOIN tbl_secondary forms ON forms.pid = students.form
				LEFT JOIN tbl_admins created_bys ON created_bys.pid = tbl_payment.create_by
				LEFT JOIN tbl_admins branch_owners ON branch_owners.pid = tbl_branches.owner
				LEFT JOIN tbl_uploads ON tbl_uploads.pid = tbl_branches.image
				LEFT JOIN tbl_secondary payment_methods ON payment_methods.pid = tbl_payment.payment_method
				WHERE tbl_payment.pid = "'. $id .'"';
				
		$payment = $this->db->query($sql)->result_array();
		
		if( count($payment) == 1 ) {
			
			$payment = $payment[0];
			$payment['change'] = $payment['receive'] - $payment['total'];
            
			//$branch = $this->tbl_branches_model->view($payment['branch'])[0];
			//$student = $this->tbl_users_model->view($payment['student'])[0];
			
			$log_payment = $this->log_payment_model->list($id);
			//$cashier = datalist_Table("tbl_admins", "nickname", $payment["create_by"]);
			//$client = datalist_Table("tbl_users", "fullname_en", $payment["student"]);
			//$client_phone = datalist_Table("tbl_users", "phone", $payment["student"]);
			//$client_address = datalist_Table("tbl_users", "address", $payment["student"]);
			//$client_email = datalist_Table("tbl_users", "email", $payment["student"]);
			//$owner = datalist_Table("tbl_admins", "nickname", $branch["owner"]);
			//$image = datalist_Table("tbl_uploads", "file_source", $branch['image']);
		
			$mpdf = new \Mpdf\Mpdf();
			// die($branch['receipt_print']); // 162720295973
			
            $path = __DIR__.'/-Export/pdf_export/'.$payment['branch_receipt_print'].'.php';
            
			if(file_exists($path)) {
                
				require $path;
				
			} else {
				
				die(app('title').': Receipt theme not found');
				
			}

			
		} else {
			
			die(app('title').': Payment not found');
			
		}
	}
	
	public function pdf_receipt($id = '')
	{	
		
		// model
		$this->load->model('tbl_users_model');
		
		// check receipt
		$payment = $this->tbl_payment_model->view($id);
		
		$sql = 'SELECT tbl_payment.*, CONCAT(students.fullname_en, " ", students.fullname_cn, ", ", students.code, " ", forms.title) AS student_info, created_bys.nickname AS created_by_teacher, payment_methods.title AS payment_method_title
				FROM tbl_payment
				LEFT JOIN tbl_users students ON students.pid = tbl_payment.student
				LEFT JOIN tbl_secondary forms ON forms.pid = students.form
				LEFT JOIN tbl_admins created_bys ON created_bys.pid = tbl_payment.create_by
				LEFT JOIN tbl_secondary payment_methods ON payment_methods.pid = tbl_payment.payment_method
				WHERE tbl_payment.pid = "'. $id .'"';
				
		$payment = $this->db->query($sql)->result_array();
		
		if( count($payment) == 1 ) {
			
			$is_pdf = 0;
			
			$payment = $payment[0];

            $payment['change'] = number_format($payment['receive'] - $payment['total'], 2, '.', ',');
			$branch = $this->tbl_branches_model->view($payment['branch'])[0];
			$student = $this->tbl_users_model->view($payment['student'])[0];
			
			$log_payment = $this->log_payment_model->list($id);
			$cashier = datalist_Table("tbl_admins", "nickname", $payment["create_by"]);
			$client = datalist_Table("tbl_users", "fullname_en", $payment["student"]);
			$client_phone = datalist_Table("tbl_users", "phone", $payment["student"]);
			$client_address = datalist_Table("tbl_users", "address", $payment["student"]);
			$client_email = datalist_Table("tbl_users", "email", $payment["student"]);
			$owner = datalist_Table("tbl_admins", "nickname", $branch["owner"]);
// 			$image = datalist_Table("tbl_uploads", "file_source", $branch['image']);
            
//             $image = imagecreatefrompng($image);

    		if($is_pdf == 0) {
                header('Content-Type: application/json; charset=utf-8');
                die(json_encode([
                    'payment'           => $payment,
                    'branch'            => $branch,
                    'student'           => $student,
                    'log_payment'       => $log_payment,
                    'cashier'           => $cashier,
                    'client'            => $client,
                    'client_phone'      => $client_phone,
                    'client_address'    => $client_address,
                    'client_email'      => $client_email,
                    'owner'             => $owner,
                    // 'image'             => $image
                ]));
            }
            
			$mpdf = new \Mpdf\Mpdf();
			
			$path = __DIR__.'/-Export/pdf_export/receipt.php';
			
			
			if(file_exists($path)) {

				require $path;
				
			} else {
				
				die(app('title').': Receipt theme not found');
				
			}

			
		} else {
			
			die(app('title').': Payment not found');
			
		}
		
	}
	
	public function json_get_image($image_id) {
	    
		$image = datalist_Table("tbl_uploads", "file_source", $image_id);
        $image = imagecreatefrompng($image);

	    header("Content-Type: image/bmp");
        imagebmp($image);
	}

	// 2021-11-09 soon
	/*public function pdf_export($id = '')
	{	
		
		$this->load->model('tbl_users_model');
		
		// check token
		if(!isset($_GET['token'])) $_GET['token'] = '';
		
		$login = $this->tbl_users_model->me_token( $_GET['token'] );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			
			$branch = $this->tbl_branches_model->view($login['branch'])[0];

		} else {
			
			auth_must('login');
			
			$branch = $this->tbl_branches_model->view(branch_now('pid'))[0];

		}

		$payment = $this->tbl_payment_model->view($id);
		
		if( count($payment) == 1 ) {
			
			$payment = $payment[0];
			
		} else {
			
			die(app('title').': Payment not found');
			
		}
		
		$log_payment = $this->log_payment_model->list($id);
		$cashier = datalist_Table("tbl_admins", "nickname", $payment["create_by"]);
		$client = datalist_Table("tbl_users", "fullname_en", $payment["student"]);
		$client_phone = datalist_Table("tbl_users", "phone", $payment["student"]);
		$client_address = datalist_Table("tbl_users", "address", $payment["student"]);
		$client_email = datalist_Table("tbl_users", "email", $payment["student"]);
		$owner = datalist_Table("tbl_admins", "nickname", $branch["owner"]);
		$image = datalist_Table("tbl_uploads", "file_source", $branch['image']);
	
		$mpdf = new \Mpdf\Mpdf();
		
		$path = __DIR__.'/-Export/pdf_export/'.$branch['receipt_print'].'.php';
		
		if(file_exists($path)) {
			
			require $path;
			
		} else {
			
			die(app('title').': Receipt theme not found');
			
		}	

	}*/

	public function pdf($id = '')
	{	
		
		$this->load->model('tbl_users_model');
		
		$payment = $this->tbl_payment_model->view($id);
		
		
		if( count($payment) == 1 ) {
			
			$payment = $payment[0];
			
			$branch = $this->tbl_branches_model->view($payment['branch'])[0];
			
			$student = $this->tbl_users_model->view($payment['student'])[0];
			
		} else {
			
			die(app('title').': Payment not found');
			
		}
		
		$log_payment = $this->log_payment_model->list($id);
		$cashier = datalist_Table("tbl_admins", "nickname", $payment["create_by"]);
		$client = datalist_Table("tbl_users", "fullname_en", $payment["student"]);
		$client_phone = datalist_Table("tbl_users", "phone", $payment["student"]);
		$client_address = datalist_Table("tbl_users", "address", $payment["student"]);
		$client_email = datalist_Table("tbl_users", "email", $payment["student"]);
		$owner = datalist_Table("tbl_admins", "nickname", $branch["owner"]);
		$image = datalist_Table("tbl_uploads", "file_source", $branch['image']);
	
		$mpdf = new \Mpdf\Mpdf();
		
		$path = __DIR__.'/-Export/pdf_export/'.$branch['receipt_print'].'.php';
		
		if(file_exists($path)) {
			
			require $path;
			
		} else {
			
			die(app('title').': Receipt theme not found');
			
		}	

	}
	
	public function pdf_ewallet($student_id = '')
	{
		
		$this->load->model('tbl_users_model');
		$this->load->model('log_point_model');

		// check receipt
		$student = $this->tbl_users_model->view($student_id);
		
		if( count($student) == 1 ) {
			
			$student = $student[0];
			
			$branch = $this->tbl_branches_model->view($student['branch'])[0];
			$log_point = $this->log_point_model->view($student['pid'], 'ewallet');
			
			$mpdf = new \Mpdf\Mpdf();
			
			$path = __DIR__.'/-Export/pdf_export/ewallet.php';
			
			if(file_exists($path)) {
				
				require $path;
				
			} else {
				
				die(app('title').': Receipt theme not found');
				
			}

			
		} else {
			
			die(app('title').': Student not found');
			
		}
	}

	// Tan Jing Suan
	public function pdf_studentcardlist($ids = '') {
		$listid = explode("--" , $ids );
		$student = [];
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_secondary_model');
		$this->load->model('log_join_model');
		$branch = $this->tbl_branches_model->view(branch_now('pid'));
		for($i=0; $i<count($listid); $i++) {
			$tempstudent = [];
			$studentcard = [];
			if ( !isset( $listid[$i] ) ) {
				continue;
			}
			$tempstudent = $this->tbl_users_model->view( $listid[$i] );
			if ( count($tempstudent) <= 0 ) {
				continue;
			}
			$formtitle = "";
			if ( isset($tempstudent[0]['form']) ) {
				$form = $this->tbl_secondary_model->view( $tempstudent[0]['form'] );
				if ( isset($form[0]['title']) ) {
					$formtitle = $form[0]['title'];
				}
			}
			$studentcard['pid'] = $listid[$i];
			$studentcard['code'] = $tempstudent[0]['code'];
			$studentcard['form'] = $formtitle;
			$studentcard['fullname_en'] = $tempstudent[0]['fullname_en'];
			$studentcard['fullname_cn'] = $tempstudent[0]['fullname_cn'];
			if ( count($branch) > 0 ) {
				$studentcard['branchimage'] = datalist_Table("tbl_uploads", "file_source", $branch[0]['image']);
			} else {
				$studentcard['branchimage'] = "";
			}
			$studentcard['image'] = $tempstudent[0]['image'];
			$studentcard['parents'] = $this->log_join_model->list('join_parent', 
				branch_now('pid'), [ 'user' => $listid[$i], 'active' => 1 ]);
            foreach($studentcard['parents'] as $pid) {
            	$parent = $this->tbl_users_model->view($pid['user']);
            	if (count($parent) > 0) {
            		if ( $parent[0]['gender'] === "male" ) {
            			$studentcard['father'] = $parent;
            		} else if ( $parent[0]['gender'] === "female" ) {
            			$studentcard['mother'] = $parent;
            		}
            	}
            }
			$student[] = $studentcard;	
		}
		// print_r( "student:  ".json_encode($student));
		
		if( count($student) > 0 ) {
			$mpdf = new \Mpdf\Mpdf();			
			$path = __DIR__.'/-Export/pdf_export/studentcard.php';
			if(file_exists($path)) {
				require $path;
			} else {
				die(app('title').': Student Theme not found');
			}
		} else {
			die(app('title').': Student not found');
		}		
	}

}
