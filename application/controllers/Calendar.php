<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'calendar';
		$this->single = 'event';

		$this->load->model('tbl_users_model');
		$this->load->model('tbl_events_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('log_join_model');
		
		auth_must('login');

	}

	public function index()
	{
		
		auth_must('login');
		check_module_page('Calendar/Read');

		$data['thispage'] = [
			'title' => 'Calendar',
			'group' => $this->group,
			'js' => $this->group.'/index'
		];

		if (isset($_POST['save'])) {

			$post_data = [];

			foreach (['title', 'date_start', 'date_end', 'remark'] as $e) {
				$post_data[$e] = $this->input->post($e);
			}

			$post_data['branch'] = branch_now('pid');
			$post_data['create_by'] = auth_data('pid');
			$type = $this->input->post('type');

			switch($type) {
				case 'holiday':
					$post_data['type'] = $type;
					$post_data['date_end'] = ( $post_data['date_end'] != null ) ? $this->input->post('date_end') : null ;
					break;

				case 'event':
					$post_data['type'] = $type;
					$post_data['date_end'] = ( $post_data['date_end'] != null ) ? $this->input->post('date_end') : null ;
					break;
			}

			$this->tbl_events_model->add($post_data);

			alert_new('success', ucfirst($type).' created successfully');

			redirect('calendar');
		}
		
		$data['teachers'] = $this->tbl_users_model->list('teacher', branch_now('pid'));

		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/index', $data);
		$this->load->view('inc/footer', $data);

	}

	public function edit($id = '')
	{
		auth_must('login');
		check_module_page('Calendar/Read');

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

				$post_data['branch'] = branch_now('pid');
				$post_data['update_by'] = auth_data('pid');
				$type = $this->input->post('type');

				switch($type) {
					case 'holiday':
						$post_data['type'] = $type;
						$post_data['date_end'] = isset( $post_data['date_end'] ) ? $this->input->post('date_end') : null ;
						break;

					case 'event':
						$post_data['type'] = $type;
						$post_data['date_end'] = isset( $post_data['date_end'] ) ? $this->input->post('date_end') : null ;
						break;
				}

				$this->tbl_events_model->edit($id, $post_data);
				
				alert_new('success', 'Holiday updated successfully');
				
				redirect('calendar/index/');

			}

			$data['thispage'] = [
				'title' => 'Edit '.datalist('event_type')[ $data['result']['type'] ]['single'], // 这里要放dynamic datalist的title
				'group' => $this->group,
				'type' => $data['result']['type'],
				'js' => $this->group.'/edit'
			];

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}
	
	public function edit_class($id) 
	{
		
		auth_must('login');
		check_module_page('Calendar/Read');
		
		if(!isset($_GET['tab'])) $_GET['tab'] = 1;
	
		$data['result'] = $this->tbl_classes_model->view($id);
		
		if( isset($_GET['date']) && count($data['result']) == 1 ) {
			
			$data['thispage'] = [
				'title' => 'Edit Class Event',
				'group' => $this->group,
				'css' => $this->group.'/edit_class',
				'js' => $this->group.'/edit_class'
			];
			
			$data['result'] = $data['result'][0];
			$data['result2'] = $this->log_join_model->list('join_class', branch_now('pid'), [
			
				'class' => $id,
				'active' => 1,
				'date >=' => $_GET['date']
			
			]);
			
			if(isset($_POST['save'])) {
			
				if(isset($_POST['attendance'])) {
					
					foreach($_POST['attendance'] as $e) {
						
						$post_data['user'] = $e;
						$post_data['branch'] = branch_now('pid');
						$post_data['type'] = 'class_attendance';
						$post_data['date'] = $_GET['date'];
						$post_data['class'] = $id;
						$post_data['create_by'] = auth_data('pid');
						$this->log_join_model->add($post_data);

					}
				}
				
				if(isset($_POST['removed_list'])) {
					
					foreach(explode(",", $_POST['removed_list']) as $e) {
						$post_data['is_delete'] = 1;
						$this->log_join_model->edit($e, $post_data);
					}
					
				}
				
				header('refresh: 0'); exit();
			}
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit_class', $data);
			$this->load->view('inc/footer', $data);
			
		} else {
			
			redirect('404_override');
			
		}
		
	}

	public function json_list($type = '')
	{

		$result = [];

		switch($type) {

			case 'class':
				$this->load->model('tbl_classes_model');

				// loop class
				$class = [];
				
				$classes = isset($_GET['teacher']) ? $this->tbl_classes_model->list(branch_now('pid'), ['teacher' => $_GET['teacher'], 'active' => 1]) : $this->tbl_classes_model->list(branch_now('pid'), ['active' => 1]);
				
				foreach($classes as $e) {

                    $timetables  = $this->log_join_model->list('class_timetable', branch_now('pid'), ['class' => $e['pid'],'is_delete']);

					foreach(datalist('day_name') as $k => $v) {
						foreach($timetables as $timetable)
                        {
                            /*
                            if(!empty($e['dy_'.$k])) {

                                $k2 = ( $k == 7 ) ? 0 : $k;

                                $class[$k2][] = $e['pid'];

                            }
                            */

                            if($timetable['qty'] == $k) {

                                $k2 = ( $k == 7 ) ? 0 : $k;

                                $class[$k2][] = $e['pid'];

                            }
                        }

						
					}

				}


				
				// echo '<pre>';
				// print_r($class); exit;

				// loop date
				$date_array = [];

				$date_1 = new DateTime($_GET['start']);
				$date_2 = new DateTime($_GET['end']);
				
				// print_r($date_1); exit;

				$date_i = DateInterval::createFromDateString('1 day');
				$date_p = new DatePeriod($date_1, $date_i, $date_2);
				
				// print_r($date_p); exit;

				foreach ($date_p as $dt) {
					
					$date = $dt->format('Y-m-d');
					$w = date('w', strtotime($date));
					
					// print_r($w);

					if( isset($class[ $w ]) ) {
						
						foreach($class[ $w ] as $k => $v) {
														
							$e_class = $this->tbl_classes_model->view($v);

							// echo $v;
							if(count($e_class) == 1) {
								
								$e_class = $e_class[0];

                                $timetables  = $this->log_join_model->list('class_timetable', branch_now('pid'), ['class' => $e_class['pid'],'is_delete']);

                                if(isset($timetables[0]))
                                    $class_name = $timetables[0]['title'];
                                else
                                    $class_name = "";

								$start = empty($e_class['date_start']) || $e_class['date_start'] == '0000-00-00' ? $_GET['start'] : $e_class['date_start'];
								$end = empty($e_class['date_end']) || $e_class['date_end'] == '0000-00-00' ? $_GET['end'] : $e_class['date_end'];

								if($date >= $start && $date <= $end) {

                                    if(isset($timetables[0]))
                                    {
                                        $time_explode = explode('-', $timetables[0]['time_range']);
                                    }
                                    else
                                    {
                                        $day = date('N', strtotime($date));
                                        $time_explode = explode('-', $e_class['dy_' . $day]);
                                    }
									
									$result[] = [
										'title' 	=> $e_class['title'].' '.$class_name,
										// 'date' 		=> $date,
										'start' 	=> date('Y-m-d', strtotime($date)).'T'.date('H:i:s', strtotime($time_explode[0].':00')),
										'end' 		=> date('Y-m-d', strtotime($date)).'T'.date('H:i:s', strtotime($time_explode[1].':00')),
										'url' 		=> base_url('calendar/edit_class/'.$e_class['pid'].'?date='.$date)
									];

								}
								
							}
							
						}
						
					}
					
				}
				
				if( !check_module('Classes/Read') ) $result = [];
				break;

			case 'birthday':
				$this->load->model('tbl_users_model');
				
				$start_month = date('m', strtotime($_GET['start']));
				$end_month = date('m', strtotime($_GET['end']));
				
				$start_day = date('d', strtotime($_GET['start']));
				$end_day = date('d', strtotime($_GET['end']));
				
				$year = date('Y', strtotime($_GET['start']));
				
				$sql = '
				
					SELECT * FROM tbl_users
					WHERE is_delete = 0
					AND type = "student"
					AND branch = "'.branch_now('pid').'"
					AND MONTH(birthday) >= "'.$start_month.'"
					AND MONTH(birthday) <= "'.$end_month.'"
				
				';
				
				$query = $this->db->query($sql)->result_array();
				
				foreach($query as $e) {
					
					$birthday = $year . '-' . date('m-d', strtotime($e['birthday']));
					
					$result[] = [
						'title' => $e['fullname_en'].'\'s birthday',
		                'date' => $birthday,
		                'url' => base_url('students/edit/'.$e['pid'])
					];

				}
				
				if( !check_module('Students/Read') ) $result = [];
				
				$sql = '
				
					SELECT * FROM tbl_users
					WHERE is_delete = 0
					AND type = "teacher"
					AND branch = "'.branch_now('pid').'"
					AND MONTH(birthday) >= "'.$start_month.'"
					AND MONTH(birthday) <= "'.$end_month.'"
				
				';
				
				$query = $this->db->query($sql)->result_array();
				
				foreach($query as $e) {
					
					$birthday = $year . '-' . date('m-d', strtotime($e['birthday']));

					$result[] = [
						'title' => $e['fullname_en'].'\'s birthday',
		                'date' => $birthday,
		                'url' => base_url('teachers/edit/'.$e['pid'])
					];

				}
				
				if( !check_module('Teachers/Read') ) $result = [];
				
				$sql = '
				
					SELECT * FROM tbl_users
					WHERE is_delete = 0
					AND type = "parent"
					AND branch = "'.branch_now('pid').'"
					AND MONTH(birthday) >= "'.$start_month.'"
					AND MONTH(birthday) <= "'.$end_month.'"
				
				';
				
				$query = $this->db->query($sql)->result_array();
				
				foreach($query as $e) {
					
					$birthday = $year . '-' . date('m-d', strtotime($e['birthday']));

					$result[] = [
						'title' => $e['fullname_en'].'\'s birthday',
		                'date' => $birthday,
		                'url' => base_url('parents/edit/'.$e['pid'])
					];

				}
				
				if( !check_module('Parents/Read') ) $result = [];
				
				break;

			case 'event':
			case 'holiday':
				$this->load->model('tbl_events_model');
				
				$exists = [];

				foreach($this->tbl_events_model->list(null, [
					'active' => 1,
					'type' => $type,
					'date_start >=' => date('Y-m-d', strtotime($_GET['start'])),
					'date_end <=' => date('Y-m-d', strtotime($_GET['end']))
				]) as $e) {
					
					$exists[] = $e['pid'];

					$result[] = [
						'title' => $e['title'],
						'start' => $e['date_start'],
						'end' => date('Y-m-d', strtotime('+1 day', strtotime($e['date_end']))),
						'url' => base_url('calendar/edit/'.$e['pid']),
					];

				}
				
				foreach($this->tbl_events_model->list(null, [
					'active' => 1,
					'type' => $type,
					'date_start >=' => date('Y-m-d', strtotime($_GET['start'])),
					'date_start <=' => date('Y-m-d', strtotime($_GET['end'])),
				]) as $e) {

					if(!in_array($e['pid'], $exists)) {
						
						$result[] = [
							'title' => $e['title'],
							'start' => $e['date_start'],
							'end' => date('Y-m-d', strtotime('+1 day', strtotime($e['date_start']))),
							'url' => base_url('calendar/edit/'.$e['pid']),
						];
						
					}

				}
				
				/* ** */
				
				foreach($this->tbl_events_model->list(branch_now('pid'), [
					'active' => 1,
					'type' => $type,
					'date_start >=' => date('Y-m-d', strtotime($_GET['start'])),
					'date_end <=' => date('Y-m-d', strtotime($_GET['end']))
				]) as $e) {
					
					$exists[] = $e['pid'];

					$result[] = [
						'title' => $e['title'],
						'start' => $e['date_start'],
						'end' => date('Y-m-d', strtotime('+1 day', strtotime($e['date_end']))),
						'url' => base_url('calendar/edit/'.$e['pid']),
					];

				}
				
				foreach($this->tbl_events_model->list(branch_now('pid'), [
					'active' => 1,
					'type' => $type,
					'date_start >=' => date('Y-m-d', strtotime($_GET['start'])),
					'date_start <=' => date('Y-m-d', strtotime($_GET['end'])),
				]) as $e) {

					if(!in_array($e['pid'], $exists)) {
						
						$result[] = [
							'title' => $e['title'],
							'start' => $e['date_start'],
							'end' => date('Y-m-d', strtotime('+1 day', strtotime($e['date_start']))),
							'url' => base_url('calendar/edit/'.$e['pid']),
						];
						
					}

				}
				break;

		}
		
		header('Content-type: application/json');
		die(json_encode($result));

	}

	public function json_del($id = '')
	{
		
		auth_must('login');
		check_module_page('Calendar/Delete');
		
		$type = $this->tbl_events_model->view($id)[0]['type'];
	
		if(!empty($id)) {
			
			$this->tbl_events_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => ucfirst($type) . ' deleted successfully']));

	}

}
