<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webapp_teacher extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'webapp_teacher';
		
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");

	}
	
	public function home()
	{
		auth_must_teacher('login');
		
		$data['thispage'] = [
			'title' => 'Home',
			'group' => $this->group,
			'css' => $this->group,
			'js' => $this->group,
		];
		
		if(!isset($_GET['date'])) $_GET['date'] = date('Y-m-d');
		
		$data['students'] = [];
		
		if(isset($_GET['date'])) {
			
			$day = date('N', strtotime($_GET['date']));
			
			$sql = '
			
				SELECT
					c.title as class_title,
					l.time_range as time_range,
					l.title as class_subtitle,
					l.id as class_id
				FROM log_join l
				INNER JOIN tbl_classes c
				ON l.class = c.pid
				AND c.teacher = "' . auth_data_teacher('pid') . '"
				AND c.is_delete = 0
				AND c.active = 1
				AND l.is_delete = 0
				AND l.type = "class_timetable"
				'.'AND l.qty = '.$day
				;
			$data['class'] = $this->db->query($sql)->result_array();

		}
		
		
		$sql_childcare = "SELECT * FROM tbl_users where is_delete = 0 and active = 1 and childcare <> ''";
		
		$data['childcares'] = $this->db->query($sql_childcare)->result_array();
		
		$this->load->view('inc/header', $data);
		$this->load->view('webapp_teacher/home');
		$this->load->view('inc/footer', $data);

	}

	public function index()
	{
		
		auth_must_teacher('login');
		
		$this->load->model('log_join_model');
		$this->load->model('tbl_secondary_model');
        $this->load->model('tbl_classes_model');

		$data['thispage'] = [
			'title' => 'Home',
			'group' => $this->group,
			'css' => $this->group . '/index',
			'js' => $this->group . '/index',
		];
		
		if(isset($_GET['class'])) {
		    $data['thispage']['branch_id'] = datalist_Table('log_join', 'branch', $_GET['class'], 'id');
		    $data['thispage']['class_id'] = datalist_Table('log_join', 'class', $_GET['class'], 'id');
		}
		
		if(isset($_GET['q'])) {
		    
		    header('content-type: application/json');
		    
		    $r2 = $this->db->query(' 
		        SELECT * FROM tbl_users 
		        WHERE 
		            is_delete=0 AND branch=? AND fullname_en LIKE ? OR 
		            is_delete=0 AND branch=? AND fullname_cn LIKE ? OR 
		            is_delete=0 AND branch=? AND code LIKE ?
		    ', [ 
		        $data['thispage']['branch_id'], '%'.$_GET['q'].'%', 
		        $data['thispage']['branch_id'], '%'.$_GET['q'].'%', 
		        $data['thispage']['branch_id'], '%'.$_GET['q'].'%', 
	        ])->result_array();
	        
	        $r = [];
	        foreach($r2 as $e) {
	            
	            $joined = $this->log_join_model->std_class_active_check($data['thispage']['class_id'], $e['pid']);
	            
	            $r[] = [
	                'pid'               => $e['pid'],
	                'fullname_en'       => $e['fullname_en'],
	                'fullname_cn'       => $e['fullname_cn'],
	                'code'              => $e['code'],
	                'enroll'            => count($joined)>0 ? 1 : 0 ,
	                'active'            => $e['active'],
                ];
	        }
		    
		    die(json_encode([ 'status' => 'ok', 'result' => $r ]));
		    
		}
		
		if(isset($_GET['save_class'])) {
		    
    		if(!empty($_GET['timetable'])) {
    			
    			if (isset($_GET['class2']))
    			{	
    			    $q = $this->log_join_model->list('join_class', $data['thispage']['branch_id'], [
						'user'      => $_GET['user'],
						'class'     => $_GET['class2'],
    				]);
    				
    				foreach($q as $e) {
    					$this->log_join_model->edit($e['id'], [ 'sub_class' => $_GET['timetable'], 'active' => 1 ]);
    				}
    			}
    			
    		}
    		
    		header('Content-type: application/json');
    		die(json_encode(['status' => 'ok']));

		}
		
		if(isset($_POST['active'])) {
		    
		    $_GET['class'] = $data['thispage']['class_id'];
		    
		    header('Content-type: application/json');
    		
    		$class_timetable = $this->log_join_model->list('class_timetable',  $data['thispage']['branch_id'], ['class' => $data['thispage']['class_id'] ]);
    		
    		$timetable = null;
    		
    		if(count($class_timetable) > 0) {
    			$timetable = $class_timetable[0]['id'];
    		}
    		
    		if( isset($_POST['active']) && isset($data['thispage']['class_id']) ) {
    			
    			if(!isset($_GET['date'])) $_GET['date'] = date('Y-m-d');
    			
    			$action = $this->log_join_model->std_class_active($_GET['class'], $_POST['active'], $_GET['date'], $timetable, auth_data_teacher('pid'));
    			
    			$this->tbl_users_model->edit($_POST['active'], [
			        'active' => 1
		        ]);
		        die($_POST['active']);
    			
    			die(json_encode(['status' => 'ok', 'result' => $action]));
    			
    		} else {
    			
    			die(json_encode(['status' => 'error']));
    			
    		}
    		
		}
		
		if(!isset($_GET['date'])) $_GET['date'] = date('Y-m-d');
		
		$data['students'] = [];
		
		if(isset($_GET['date'])) {
			
			$day = date('N', strtotime($_GET['date']));
			
			$sql = '
			
				SELECT
					c.title as class_title,
					l.time_range as time_range,
					l.title as class_subtitle,
					l.id as class_id
				FROM log_join l
				INNER JOIN tbl_classes c
				ON l.class = c.pid
				AND c.teacher = "' . auth_data_teacher('pid') . '"
				AND c.is_delete = 0
				AND c.active = 1
				AND l.is_delete = 0
				AND l.type = "class_timetable"
				'.'AND l.qty = '.$day
				;

			$data['class'] = $this->db->query($sql)->result_array();

		}
			
		if(isset($_GET['search'])) {
			
// 			$class_id = datalist_Table('log_join', 'class', $_GET['class'], 'id');
			
// 			$sql = '
			
// 				SELECT u.* FROM log_join l
// 				INNER JOIN tbl_users u
// 				ON u.pid = l.user
// 				AND u.is_delete	= 0
// 				AND u.active = 1
// 				AND l.class = "' . $class_id . '"
// 				AND l.type = "join_class"
// 				AND l.active = 1
// 				AND l.is_delete = 0
// 				AND l.date <= "' . $_GET['date'] . '"
// 				GROUP BY u.pid
// 			';
			
// 			$data['students'] = $this->db->query($sql)->result_array();

// 

            $data['students'] = [];

            $sql = $this->db->query(' SELECT * FROM `log_join` WHERE is_delete=0 AND class=? AND type=? AND sub_class=? AND active=1 ', [ $data['thispage']['class_id'], 'join_class', $_GET['class'] ])->result_array();
            // verbose($sql);
			foreach($sql as $e) {
                $sql2 = $this->db->query(' SELECT * FROM `tbl_users` WHERE is_delete=0 AND pid=? ', [ $e['user'] ])->result_array();
                if(count($sql2) == 1) {
                    $sql2 = $sql2[0];
                    if( $sql2['active'] == 1 ) {
                        foreach($sql2 as $k => $v) {
                            $e[ $k ] = $v;
                        }
                        $data['students'][] = $e;
                    }
                }
            }
			
		}

        $class_id = "";
		if(isset($_POST['save'])) {


			$class_id = datalist_Table('log_join', 'class', $_GET['class'], 'id');
			$branch_id = datalist_Table('log_join', 'branch', $_GET['class'], 'id');
						
			foreach($_POST['attd'] as $k => $v) {
				
				$check = $this->log_join_model->list('class_attendance', $branch_id, [
					'user'			=> $k,
					'class'			=> $class_id,
					'sub_class'		=> $_GET['class'],
					'date'			=> $_GET['date'],
				]);
				
				if(!empty($v)) {
					
					$v = $v == 2 ? 0 : $v;
					
					if(count($check) > 0) {
						
						$check = $check[0];
						
						$this->log_join_model->edit($check['id'], [
							'create_by' 	=> auth_data_teacher('pid'),
							'active'		=> $v,
							'remark'		=> isset($_POST['reason'][$k]) ? $_POST['reason'][$k] : '',
						]);
						
					} else {
						
						$this->log_join_model->add([
							'create_by' 	=> auth_data_teacher('pid'),
							'type' 			=> 'class_attendance',
							'branch' 		=> $branch_id,
							'user'			=> $k,
							'class'			=> $class_id,
							'sub_class'		=> $_GET['class'],
							'active'		=> $v,
							'date'			=> $_GET['date'],
							'remark'		=> isset($_POST['reason'][$k]) ? $_POST['reason'][$k] : '',
						]);
						
					}
					
				} else {
					
					if(count($check) > 0) {
						
						$check = $check[0];
						
						$this->log_join_model->del($check['id']);
						
					}
					
				}
				
			}
			
			alert_new('success', 'Attendance updated successfully');
			header('refresh: 0'); exit;
			
		}
		
		if(isset($_POST['view_student'])) {
			
			$sql = '
			
				SELECT
					u.pid, (CASE WHEN COALESCE(check_class.count_active, 0) > 0 THEN 1 ELSE 0 END) AS enroll,
					CONCAT(u.fullname_en, " ", u.fullname_cn) as name,
					u.code as code,
					COALESCE(s.title, "-") as school,
					COALESCE(s2.title, "-") as form
				FROM tbl_users u
				LEFT JOIN (
					SELECT user, COUNT(*) AS count_active
					FROM log_join
					WHERE is_delete = 0
					AND active = 1
					AND type = "join_class"
					AND class = "' . $data['thispage']['class_id'] . '"
					AND user = "' . $_POST['view_student'] . '"
					GROUP BY user
				) check_class ON check_class.user = u.pid
				LEFT JOIN tbl_secondary s ON s.pid = u.school AND s.is_delete = 0
				LEFT JOIN tbl_secondary s2 ON s2.pid = u.form AND s2.is_delete = 0
				WHERE u.is_delete = 0
				AND u.pid = "' . $_POST['view_student'] . '"
				
			';

			$result = $this->db->query($sql)->result_array();
			
			header('Content-type: application/json');
			die(json_encode(['status' => 'ok', 'result' => $result ]));
			
		}

        // class dropdown
        $data['class_dropdown'] = [];
        if( isset($data['thispage']['branch_id']) && isset($data['thispage']['class_id']) ) {
            // foreach( $this->db->query(' SELECT * FROM `log_join` WHERE `type` LIKE ? AND class=? ', [ 'class_timetable', $data['thispage']['class_id'] ])->result_array() as $e ) {
            //     $data['class_dropdown'][] = [
            //         'id'        => $e['id'],
            //         'title'     => $e['id'],
            //     ];
            // }
            
            $timetable = $this->log_join_model->list('class_timetable', $data['thispage']['branch_id'], [
            	'class' => $data['thispage']['class_id']
            ]);
            
            foreach($timetable as $e2) {
                $data['class_dropdown'][] = [
                    'id'        => $e2['id'],
                    'title'     => $e2['title'] . ' (' . datalist('day_name')[$e2['qty']]['name'] . ' ' . $e2['time_range'] . ')',
                ];
			}
            
        }

        //generate clas qr code
        $class_id =  isset($_GET['class']) ? $_GET['class'] : "";
        $branch_id = datalist_Table('log_join', 'branch', $class_id, 'id');
        $class_pid = datalist_Table('log_join', 'class', $class_id, 'id');
        
        $class_row = $this->tbl_classes_model->view($class_pid);
        $class = isset($clas_row[0]) ? $class_row[0] : array();
        $teacher = isset($class['teacher']) ? $class['teacher'] : "";
        $date = isset($_GET['date']) ? $_GET['date'] : "";
        $user_data = urlencode('https://system.synorex.work/highpeakedu/webapp_teacher/app_check_in?class='.$class_id.'&date='.$date.'&teacher='.$teacher);

        $data['class_qr'] = 'https://chart.googleapis.com/chart?chs=177x177&cht=qr&chl='.$user_data.'&chld=L|1&choe=UTF-8';

		$this->load->view('inc/header', $data);
		$this->load->view('webapp_teacher/index');
		$this->load->view('inc/footer', $data);

	}
	
	public function app_check_in()
    {
        $this->load->model('log_join_model');
        $this->load->model('tbl_secondary_model');


        $currentURL = current_url(); //http://myhost/main
        $params   = $_SERVER['QUERY_STRING']; //my_id=1,3
        $fullURL = $currentURL . '?' . $params;

        $result = array();
        $result['call_url'] = $fullURL;
        $class_id = datalist_Table('log_join', 'class', $_GET['class'], 'id');
        $branch_id = datalist_Table('log_join', 'branch', $_GET['class'], 'id');
        $k = isset($_GET['student_id']) ? $_GET['student_id'] : "";
        $teacher_id = isset($_GET['teacher']) ? $_GET['teacher'] : "";
        $v = 1; //attendance status
        $reason = "";

        $day = date('N', strtotime($_GET['date']));
        $sql = '
			
				SELECT
					c.title as class_title,
					l.time_range as time_range,
					l.title as class_subtitle,
					l.id as class_id
				FROM log_join l
				INNER JOIN tbl_classes c
				ON l.class = c.pid
				AND c.teacher = "' . $teacher_id . '"
				AND c.is_delete = 0
				AND c.active = 1
				AND l.is_delete = 0
				AND l.type = "class_timetable"
				AND l.qty = '.$day.'
			
			';


        $class = $this->db->query($sql)->result_array();
        if(empty($class))
        {
            $result['error'] = 'Class not found';
            $result['status'] = 0;
        }
        elseif($k != "")
        {
            $check = $this->log_join_model->list('class_attendance', $branch_id, [
                'user' => $k,
                'class' => $class_id,
                'sub_class' => $_GET['class'],
                'date' => $_GET['date'],
            ]);
            //print_array($check);
            if (count($check) > 0)
            {

                $check = $check[0];

                $this->log_join_model->edit($check['id'], [
                    'active' => $v,
                    'remark' => $reason
                ]);

                $result['data'] = array(
                    'class' => $class_id,
                    'date' => $_GET['date'],
                    'student_id' => $k
                );
                $result['status'] = 1;

            }
            else
            {
                $this->log_join_model->add([
                    'type' => 'class_attendance',
                    'branch' => $branch_id,
                    'user' => $k,
                    'class' => $class_id,
                    'sub_class' => $_GET['class'],
                    'active' => $v,
                    'date' => $_GET['date'],
                    'remark' => $reason,
                ]);

                $result['data'] = array(
                    'class' => $class_id,
                    'date' => $_GET['date'],
                    'student_id' => $k
                );
                $result['status'] = 1;
            }
        }
        else
        {
            $result['error'] = 'Student ID not found';
            $result['status'] = 0;
        }


        $data['thispage'] = [
            'title' => 'Web Checkin',
            'group' => $this->group,
        ];

        if($result['status'] == true)
        {
            $this->load->view('inc/header', $data);
            $this->load->view('webapp_teacher/checkin_ok');
            $this->load->view('inc/footer', $data);
        }
        else
        {
            $this->load->view('inc/header', $data);
            $this->load->view('webapp_teacher/checkin_fail',$result);
            $this->load->view('inc/footer', $data);
        }
        //echo json_encode($result);

        //alert_new('success', 'Attendance updated successfully');
        //header('refresh: 0'); exit;
    }
	
	
	public function childcare()
	{
		
		auth_must_teacher('login');
		
		$this->load->model('log_join_model');
		$this->load->model('tbl_secondary_model');
        $this->load->model('tbl_classes_model');

		$data['thispage'] = [
			'title' => 'Home',
			'group' => $this->group,
			'css' => $this->group . '/index',
			'js' => $this->group . '/index',
		];
		
		if(isset($_GET['class'])) {
		    $data['thispage']['branch_id'] = datalist_Table('tbl_secondary', 'pid', $_GET['class'], 'pid');
		    $data['thispage']['class_id'] = datalist_Table('tbl_secondary', 'branch', $_GET['class'], 'pid');
		}
		
		if(isset($_GET['q'])) {
		    
		    header('content-type: application/json');
		    
		    $r2 = $this->db->query(' 
		        SELECT * FROM tbl_users 
		        WHERE 
		            is_delete=0 AND fullname_en LIKE ? OR 
		            is_delete=0 AND fullname_cn LIKE ? OR 
		            is_delete=0 AND code LIKE ?
		    ', [ 
		        '%'.$_GET['q'].'%', 
		        '%'.$_GET['q'].'%', 
		        '%'.$_GET['q'].'%', 
	        ])->result_array();
			
	        $r = [];
	        foreach($r2 as $e) {
	            
	            // $joined = $this->log_join_model->std_class_active_check($data['thispage']['class_id'], $e['pid']);
	            
	            $r[] = [
	                'pid'               => $e['pid'],
	                'fullname_en'       => $e['fullname_en'],
	                'fullname_cn'       => $e['fullname_cn'],
	                'code'              => $e['code'],
	                // 'enroll'            => count($joined)>0 ? 1 : 0 ,
	                'active'            => $e['active'],
                ];
	        }
		    
		    die(json_encode([ 'status' => 'ok', 'result' => $r ]));
		    
		}
		
		if(isset($_GET['save_class'])) {
		    
    		if(!empty($_GET['timetable'])) {
    			
    			if (isset($_GET['class2']))
    			{	
    			    $q = $this->log_join_model->list('join_class', $data['thispage']['branch_id'], [
						'user'      => $_GET['user'],
						'class'     => $_GET['class2'],
    				]);
    				
    				foreach($q as $e) {
    					$this->log_join_model->edit($e['id'], [ 'sub_class' => $_GET['timetable'], 'active' => 1 ]);
    				}
    			}
    			
    		}
    		
    		header('Content-type: application/json');
    		die(json_encode(['status' => 'ok']));

		}
		
		if(isset($_POST['active'])) {
		    
		    $_GET['class'] = $data['thispage']['class_id'];
		    
		    header('Content-type: application/json');
    		
    		$class_timetable = $this->log_join_model->list('class_timetable',  $data['thispage']['branch_id'], ['class' => $data['thispage']['class_id'] ]);
    		
    		$timetable = null;
    		
    		if(count($class_timetable) > 0) {
    			$timetable = $class_timetable[0]['id'];
    		}
    		
    		if( isset($_POST['active']) && isset($data['thispage']['class_id']) ) {
    			
    			if(!isset($_GET['date'])) $_GET['date'] = date('Y-m-d');
    			
    			$action = $this->log_join_model->std_class_active($_GET['class'], $_POST['active'], $_GET['date'], $timetable, auth_data_teacher('pid'));
    			
    			$this->tbl_users_model->edit($_POST['active'], [
			        'active' => 1
		        ]);
		        die($_POST['active']);
    			
    			die(json_encode(['status' => 'ok', 'result' => $action]));
    			
    		} else {
    			
    			die(json_encode(['status' => 'error']));
    			
    		}
    		
		}
		
		
		if(isset($_POST['active_child'])) {
		    
		    // header('Content-type: application/json');
    		
    		if(isset($_POST['active_child'])) {
    			
    			$this->tbl_users_model->edit($_POST['active_child'], [
			        'childcare' => $_POST['child']
		        ]);
		        die($_POST['active_child']);
    			
    			die(json_encode(['status' => 'ok', 'result' => $action]));
    			
    		} else {
    			
    			die(json_encode(['status' => 'error']));
    			
    		}
    		
		}
		
		if(!isset($_GET['date'])) $_GET['date'] = date('Y-m-d');
		
		$data['students'] = [];
		
		if(isset($_GET['date'])) {
			
			$day = date('N', strtotime($_GET['date']));
			
			
			$sqlform = "SELECT childcares.pid as class_id, childcares.title as childcares, students.form as form_id, form.title as form
						  FROM tbl_secondary form
						  JOIN tbl_users students ON students.form = form.pid
						  JOIN tbl_secondary childcares ON students.childcare = childcares.pid
						  WHERE students.is_delete = 0 and childcares.is_delete = 0 and form.is_delete = 0";
			$sqlform .=" GROUP BY students.form";

				
	
			$data['form'] = $this->db->query($sqlform)->result_array();
			
			$sqlschool = "SELECT childcares.pid as class_id, childcares.title, students.school as school_id, school.title as school
						  FROM tbl_secondary school 
						  JOIN tbl_users students ON students.school = school.pid
						  JOIN tbl_secondary childcares ON childcares.pid = students.childcare
						  WHERE students.is_delete = 0 and childcares.is_delete = 0 and school.is_delete = 0";
			$sqlschool .=" GROUP BY students.school";
			
	
			$data['school'] = $this->db->query($sqlschool )->result_array();

		}
			
		if(isset($_GET['search'])) {
			
			$sql = ' SELECT * FROM `tbl_users` WHERE is_delete=0 and active=1 and childcare <> "" ';
			// if(isset($_GET['time']) && $_GET['time'] != ''){
				// $sql.=" AND att.create_on like '%".$_GET['date']." ".$_GET['time']."%'";
			// }
			if(isset($_GET['form']) && $_GET['form'] != ''){
				$sql.=" AND form = '". $_GET['form'] ."'";
			}
		    if(isset($_GET['school']) && $_GET['school'] != ''){
				$sql .=" AND school = '". $_GET['school'] ."'";
			}
			if(isset($_GET['search']) && $_GET['search'] != ''){
				$sql .=" AND ( fullname_en = '". $_GET['search'] ."' OR fullname_cn ='". $_GET['search'] ."' OR code = '".$_GET['search']."')";
			}
			$data['students'] = $this->db->query($sql)->result_array();
		}

        $class_id = "";
		if(isset($_POST['save'])) {


			$class_id = datalist_Table('tbl_secondary', 'pid', $_GET['class'], 'pid');
			$branch_id = datalist_Table('tbl_secondary', 'branch', $_GET['class'], 'pid');
						
			foreach($_POST['attd'] as $k => $v) {
				
				$check = $this->log_join_model->list('childcare_attendance', $branch_id, [
					'user'			=> $k,
					'class'			=> $class_id,
					'sub_class'		=> $_GET['class'],
					'date'			=> $_GET['date'],
				]);
				
				if(!empty($v)) {
					
					$v = $v == 2 ? 0 : $v;
					
					if(count($check) > 0) {
						
						$check = $check[0];
						
						$this->log_join_model->edit($check['id'], [
							'create_by' 	=> auth_data_teacher('pid'),
							'active'		=> $v,
							'remark'		=> isset($_POST['reason'][$k]) ? $_POST['reason'][$k] : '',
						]);
						
					} else {
						
						$this->log_join_model->add([
							'create_by' 	=> auth_data_teacher('pid'),
							'type' 			=> 'childcare_attendance',
							'branch' 		=> $branch_id,
							'user'			=> $k,
							'class'			=> $class_id,
							'active'		=> $v,
							'date'			=> $_GET['date'],
							'remark'		=> isset($_POST['reason'][$k]) ? $_POST['reason'][$k] : '',
						]);
						
					}
					
				} else {
					
					if(count($check) > 0) {
						
						$check = $check[0];
						
						$this->log_join_model->del($check['id']);
						
					}
					
				}
				
			}
			
			alert_new('success', 'Attendance updated successfully');
			header('refresh: 0'); exit;
			
		}
		
		if(isset($_POST['view_student'])) {
			
			$sql = '
			
				SELECT
					u.pid, CONCAT(u.fullname_en, " ", u.fullname_cn) as name,
					u.code as code, u.childcare as enroll,
					COALESCE(s.title, "-") as school,
					COALESCE(s2.title, "-") as form
				FROM tbl_users u
				LEFT JOIN tbl_secondary s ON s.pid = u.school AND s.is_delete = 0
				LEFT JOIN tbl_secondary s2 ON s2.pid = u.form AND s2.is_delete = 0
				WHERE u.is_delete = 0
				AND u.pid = "' . $_POST['view_student'] . '"
				
			';

			$result = $this->db->query($sql)->result_array();
			
			header('Content-type: application/json');
			die(json_encode(['status' => 'ok', 'result' => $result ]));
			
		}

        // class dropdown
        $data['class_dropdown'] = [];
        if( isset($data['thispage']['branch_id']) && isset($data['thispage']['class_id']) ) {
            // foreach( $this->db->query(' SELECT * FROM `log_join` WHERE `type` LIKE ? AND class=? ', [ 'class_timetable', $data['thispage']['class_id'] ])->result_array() as $e ) {
            //     $data['class_dropdown'][] = [
            //         'id'        => $e['id'],
            //         'title'     => $e['id'],
            //     ];
            // }
            
            $timetable = $this->log_join_model->list('class_timetable', $data['thispage']['branch_id'], [
            	'class' => $data['thispage']['class_id']
            ]);
            
            foreach($timetable as $e2) {
                $data['class_dropdown'][] = [
                    'id'        => $e2['id'],
                    'title'     => $e2['title'] . ' (' . datalist('day_name')[$e2['qty']]['name'] . ' ' . $e2['time_range'] . ')',
                ];
			}
            
        }

        //generate clas qr code
        $class_id =  isset($_GET['class']) ? $_GET['class'] : "";
        $branch_id = datalist_Table('log_join', 'branch', $class_id, 'id');
        $class_pid = datalist_Table('log_join', 'class', $class_id, 'id');
        $class_row = $this->tbl_secondary_model->view($class_pid);
        $class = isset($clas_row[0]) ? $class_row[0] : array();
        $date = isset($_GET['date']) ? $_GET['date'] : "";
        $user_data = urlencode('https://system.synorex.work/highpeakedu/webapp_teacher/care_check_in?class='.$class_id.'&date='.$date);

        $data['class_qr'] = 'https://chart.googleapis.com/chart?chs=177x177&cht=qr&chl='.$user_data.'&chld=L|1&choe=UTF-8';

		$this->load->view('inc/header', $data);
		$this->load->view('webapp_teacher/childcare');
		$this->load->view('inc/footer', $data);

	}

	
	public function care_check_in()
    {
        $this->load->model('log_join_model');
        $this->load->model('tbl_secondary_model');


        $currentURL = current_url(); //http://myhost/main
        $params   = $_SERVER['QUERY_STRING']; //my_id=1,3
        $fullURL = $currentURL . '?' . $params;

        $result = array();
        $result['call_url'] = $fullURL;
		$class_id = datalist_Table('tbl_secondary', 'pid', $_GET['class'], 'pid');
		$branch_id = datalist_Table('tbl_secondary', 'branch', $_GET['class'], 'pid');
        $k = isset($_GET['student_id']) ? $_GET['student_id'] : "";
		echo $k;
		exit();
        $v = 1; //attendance status
        $reason = "";

        $day = date('N', strtotime($_GET['date']));
        // $sql = '
			
				// SELECT
					// c.title as class_title,
					// l.time_range as time_range,
					// l.title as class_subtitle,
					// l.id as class_id
				// FROM log_join l
				// INNER JOIN tbl_classes c
				// ON l.class = c.pid
				// AND c.teacher = "' . $teacher_id . '"
				// AND c.is_delete = 0
				// AND c.active = 1
				// AND l.is_delete = 0
				// AND l.type = "class_timetable"
				// AND l.qty = '.$day.'
			
			// ';

        // $class = $this->db->query($sql)->result_array();
		
		$class = datalist_Table('tbl_users', 'childcare', $k, 'pid');
        if(empty($class))
        {
            $result['error'] = 'Class not found';
            $result['status'] = 0;
        }
        elseif($k != "")
        {
            $check = $this->log_join_model->list('childcare_attendance', $branch_id, [
                'user' => $k,
                'class' => $class_id,
                'date' => $_GET['date'],
            ]);
            //print_array($check);
            if (count($check) > 0)
            {

                $check = $check[0];

                $this->log_join_model->edit($check['id'], [
                    'active' => $v,
                    'remark' => $reason
                ]);

                $result['data'] = array(
                    'class' => $class_id,
                    'date' => $_GET['date'],
                    'student_id' => $k
                );
                $result['status'] = 1;

            }
            else
            {
                $this->log_join_model->add([
                    'type' => 'childcare_attendance',
                    'branch' => $branch_id,
                    'user' => $k,
                    'class' => $class_id,
                    'active' => $v,
                    'date' => $_GET['date'],
                    'remark' => $reason,
                ]);

                $result['data'] = array(
                    'class' => $class_id,
                    'date' => $_GET['date'],
                    'student_id' => $k
                );
                $result['status'] = 1;
            }
        }
        else
        {
            $result['error'] = 'Student ID not found';
            $result['status'] = 0;
        }


        $data['thispage'] = [
            'title' => 'Web Checkin',
            'group' => $this->group,
        ];

        if($result['status'] == true)
        {
            $this->load->view('inc/header', $data);
            $this->load->view('webapp_teacher/checkin_ok');
            $this->load->view('inc/footer', $data);
        }
        else
        {
            $this->load->view('inc/header', $data);
            $this->load->view('webapp_teacher/checkin_fail',$result);
            $this->load->view('inc/footer', $data);
        }
        //echo json_encode($result);

        //alert_new('success', 'Attendance updated successfully');
        //header('refresh: 0'); exit;
    }


	
	public function login()
	{
		$this->load->model('tbl_users_model');

		$data['thispage'] = [
			'title' => 'Login',
			'group' => $this->group,
		];
		
 		if(isset($_POST['login'])) {
		    
		    $login = $this->tbl_users_model->login_teacher($this->input->post('username'), $this->input->post('password'));
			
			if( $login != false ) {
				
				$token = openssl_encrypt(time(), 'AES-128-CTR', 'highpeakedu-token', 0, '1234567891011121');
				$teacher_id = $this->tbl_users_model->view($login)[0]['pid'];
				$this->tbl_users_model->edit($teacher_id, [ 'token' => $token ]);
				setcookie(md5('@highpeakedu-teacher-sso'), $token, time() + (86400 * 30), '/');

				// Tan Jing Suan
				// redirect('webapp_teacher/home');
				if(isset($_GET['next'])) {
					$last_visit_url = urldecode($_GET['next']);
					redirect($last_visit_url);
				} else {
					redirect('webapp_teacher/home');
				}			
			} else {

				alert_new('warning', 'Login failed');
				header('refresh: 0'); exit;

			}
		    
		}


		$this->load->view('inc/header', $data);
		$this->load->view('webapp_teacher/login');
		$this->load->view('inc/footer', $data);

	}
	
	public function logout()
	{

		setcookie(md5('@highpeakedu-teacher-sso'), '', time() - 3600, '/');
		redirect('webapp_teacher/login');

	}

	// Tan Jing Suan
	public function submit_attendance() 
	{
		// $last_visit_url = urldecode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
		$last_visit_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		if(!isset($_GET['id'])) {
			alert_new('warning', 'Invalid url');
			header('refresh: 0'); exit;
		}
		$id = $_GET['id'];
		if(!isset($_GET['method'])) {
			alert_new('warning', 'Invalid url');
			header('refresh: 0'); exit;
		}
		if($_GET['method'] !== "qr") {
			alert_new('warning', 'Invalid url');
			header('refresh: 0'); exit;
		}		
		auth_must_teacher_qr(urlencode($last_visit_url));

		$this->load->model('tbl_users_model');
		$this->load->model('log_attendance_model');
		$data['thispage'] = [
			'title' => 'Home',
			'group' => $this->group,
			'css' => $this->group,
			'js' => $this->group,
		];
		
		if(!isset($_GET['date'])) $_GET['date'] = date('Y-m-d');
		
		$data['students'] = [];
		
		if(isset($_GET['date'])) {
			
			$day = date('N', strtotime($_GET['date']));
			
			$sql = '
			
				SELECT
					c.title as class_title,
					l.time_range as time_range,
					l.title as class_subtitle,
					l.id as class_id
				FROM log_join l
				INNER JOIN tbl_classes c
				ON l.class = c.pid
				AND c.teacher = "' . auth_data_teacher('pid') . '"
				AND c.is_delete = 0
				AND c.active = 1
				AND l.is_delete = 0
				AND l.type = "class_timetable"
				'.'AND l.qty = '.$day
				;
			$data['class'] = $this->db->query($sql)->result_array();

		}
		$tbl_users = $this->tbl_users_model->list_v2([ 'pid' => $id ]);
		$post_data['user'] = $id;
		$post_data['method'] = '162780155412'; // manually
		$post_data['datetime'] = date('Y-m-d H:i:s');
		$post_data['create_by'] = $id;
		$post_data['update_by'] = $id;
		// $post_data['branch'] = branch_now('pid');
		$post_data['branch'] = $tbl_users[0]['branch'];
		$post_data['remark'] = "Teacher QR Scan";
		$latestattendance = $this->log_attendance_model->latestattendance($id);
		if ( count($latestattendance) > 0 ) {
			if ( strval($latestattendance[0]['action']) === "in" ) {
				$post_data['action'] = "out";
			} else {
				$post_data['action'] = "in";
			}
		} else {
			$post_data['action'] = "in";
		}
		$this->log_attendance_model->add($post_data);		
		
		$sql_childcare = "SELECT * FROM tbl_users where is_delete = 0 and active = 1 and childcare <> ''";
		
		$data['childcares'] = $this->db->query($sql_childcare)->result_array();
		
		$this->load->view('inc/header', $data);
		$this->load->view('webapp_teacher/successattendanceqr');
		$this->load->view('inc/footer', $data);

	}

	public function json_save_attendance() 
	{
		auth_must_teacher('login');
		
		$this->load->model('log_join_model');
		$this->load->model('tbl_secondary_model');
        $this->load->model('tbl_classes_model');

		$data['thispage'] = [
			'title' => 'Home',
			'group' => $this->group,
			'css' => $this->group . '/index',
			'js' => $this->group . '/index',
		];
		$class_id = datalist_Table('log_join', 'class', $_POST['sub_class'], 'id');
		$branch_id = datalist_Table('log_join', 'branch', $_POST['sub_class'], 'id');

		$check = $this->log_join_model->list('class_attendance', $branch_id, [
			'user'			=> $_POST['pid'],
			'class'			=> $class_id,
			'sub_class'		=> $_POST['sub_class'],
			'date'			=> $_POST['date'],
		]);

		$color = '#eee';
		$full_bg = '#FFFFFF';

		if ( !isset($_POST['active']) ) {
			if(count($check) > 0) {	
				$this->log_join_model->del($check[0]['id']);
			}
			header('Content-type: application/json');
			die(json_encode(['status' => 'ok', 
				'color'=>$color, 
				'full_bg'=>$full_bg, 
				'message' => 'Attendance updated successfully'])
			);
		}
		if ( $_POST['active'] === '' ) {
			if(count($check) > 0) {	
				$this->log_join_model->del($check[0]['id']);
			}
			header('Content-type: application/json');
			die(json_encode(['status' => 'ok', 
				'color'=>$color, 
				'full_bg'=>$full_bg, 
				'message' => 'Attendance updated successfully'])
			);
		}

		if(count($check) > 0) {	
			$this->log_join_model->edit($check[0]['id'], [
				'create_by' 	=> auth_data_teacher('pid'),
				'active'		=> $_POST['active'],
				'remark'		=> isset($_POST['reason']) ? $_POST['reason'] : '',
			]);			
		} else {			
			$this->log_join_model->add([
				'create_by' 	=> auth_data_teacher('pid'),
				'type' 			=> 'class_attendance',
				'branch' 		=> $branch_id,
				'user'			=> $_POST['pid'],
				'class'			=> $class_id,
				'sub_class'		=> $_POST['sub_class'],
				'active'		=> $_POST['active'],
				'date'			=> $_POST['date'],
				'remark'		=> isset($_POST['reason']) ? $_POST['reason'] : '',
			]);			
		}

		if ($_POST['active'] === "1") {
			$color = '#54c67e';
		} else {
			$color = '#ec5b78';
		}
	
		$check_first = $this->db->query(' SELECT * FROM `log_join` WHERE `type` LIKE ? AND user=? AND class=? AND sub_class=? AND active=1 AND is_delete=0 ', [ 
		    'class_attendance', 
		    $_POST['pid'],
		    $class_id,
		    $_POST['sub_class'],
	    ])->result_array();			
	    
	    $check_abs = $this->db->query(' SELECT * FROM log_join WHERE is_delete=0 AND user=? AND class=? AND type=? AND date LIKE ? AND active=0 ', [ 
		    	$_POST['pid'], 
		    	$class_id, 
		    	'class_attendance', '%'.date('Y-m', strtotime($_POST['date'])).'%' 
		    ])->result_array();
		
		if(count($check_abs) == 1) {
		    $full_bg = '#FEFE9A';
		} elseif(count($check_abs) == 2) {
		    $full_bg = '#F5C966';
		} elseif(count($check_abs) == 3) {
		    $full_bg = '#FF957F';
		} elseif(count($check_first) == 0) {
		    $full_bg = '#BEFFBF';
		}

		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 
			'color'=>$color, 
			'full_bg'=>$full_bg, 
			'message' => 'Attendance updated successfully'])
		);
	}
	
}
