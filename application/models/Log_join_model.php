<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_join_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'log_join';
		
	}

	public function list($type = '', $branch = '', $search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where('type', $type);
		$this->db->where($search);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function list_all($search = [])
	{
		
		$this->db->where($search);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	// for admin
	public function list_admin($search)
	{

		$this->db->where('is_delete', 0);
		$this->db->where($search);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function list2($type = '', $branch = '', $search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where('type', $type);
		$this->db->where($search);
		$this->db->order_by('id', 'DESC');
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function list3($type = '', $branch = '', $search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where('type', $type);
		$this->db->where($search);
		$this->db->group_by('user');
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function list4($type = '', $branch = '', $search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where('type', $type);
		$this->db->where($search);		
		$this->db->order_by('date', 'asc');
		
		$query = $this->db->get($this->tbl_name);
	
		return $query->result_array();

	}
	
	// by steve
	public function list_v2($search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where($search);
		
		$query = $this->db->get($this->tbl_name);
	
		return $query->result_array();

	}
	
	// get class all available students (by steve)
	public function list_classes_students($class)
	{
		// Tan Jing Suan
		$result = [];
		$sqlclass_timetable = ' SELECT id FROM log_join WHERE class ="'.$class.'" AND type = "class_timetable" ';
		$class_timetable = $this->db->query($sqlclass_timetable)->result_array();
		if ( count($class_timetable) <= 0 ) {
			return $result;			
		}
		$sub_class = $class_timetable[0]['id'];

		$this->load->model('tbl_users_model');

		$this->db->where('is_delete', 0);
		$this->db->where('active', 1);
		$this->db->where('class', $class );
		// Tan Jing Suan
		$this->db->where('sub_class', $sub_class );
		$this->db->where('type', 'join_class');
		
		$query = $this->db->get($this->tbl_name);
		
		// $result = [];
		
		foreach( $query->result_array() as $e ) {
			
			$e_student = $this->tbl_users_model->list_v2([
				'active' => 1,
				'pid' => $e['user']
			]);

			if(count($e_student) == 1) {
				
				$e['student'] = $e_student[0];
				
				$result[] = $e;
			
			}
			
		}
	
		return $result;

	}
	
	public function setup_list($branch = '', $search = [])
	{

		$this->db->where('branch', $branch);
		$this->db->where($search);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	// switch join class
	// by steve
	public function std_class_active($class_id, $std_id, $date = '', $timetable = null, $update_by = "")
	{

		$result = $this->std_class_active_check( $class_id, $std_id, false );

		if(empty($date)) $date = date('Y-m-d');
		
		if(count($result) == 0) {
			
			// create
			$this->add([
				'type' => 'join_class',
				'branch' => datalist_Table('tbl_users', 'branch', $std_id),
				'user' => $std_id,
				'class' => $class_id,
				'sub_class' => $timetable,
				'active' => 1,
				'date' => $date,
				'create_by' => auth_data('pid')
			]);
			
			return [
				'active' => 1,
				'date' => $date,
			];
			
		} else {
			
			// check active
			$result = $result[0];
			
			// update
			$save_active = $result['active'] == 1 ? 0 : 1 ;
			
			$this->edit($result['id'], [
				'active' => $save_active,
				'date' => $date,
				'update_by' => $update_by,
			]);
			
			return [
				'active' => $save_active,
				'date' => $date,
				'update_by' => $update_by,
			];
			
		}
		
	}
	
	// check has class
	// by steve
	public function std_class_active_check($class_id, $std_id, $active = 1)
	{
		
		$this->db->where('is_delete', 0);
		if($active != false) $this->db->where('active', $active);
		$this->db->where([
			'type' => 'join_class',
            'is_delete' => 0,
			'class' => $class_id,
			'user' => $std_id,
		]);
		
		$query = $this->db->get($this->tbl_name);
		$result = $query->result_array();
		
		return $result;
		
	}
	
	public function list_pending($email)
	{

		$this->db->where('is_delete', 0);
		$this->db->where('type', 'pending_active');
		$this->db->where('email', $email);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function all_list($branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function report_list($branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where('type', 'unpaid_item');
		$this->db->group_by('user');
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	// public function report_list($user = '', $branch = '')
	// {

		// $this->db->where('is_delete', 0);
		// $this->db->where('user', $user);
		// $this->db->where('branch', $branch);
		// $this->db->where('type', 'unpaid_item');
		// $this->db->group_by('user');
		
		// $query = $this->db->get($this->tbl_name);
		
		// return $query->result_array();
		
	// }

	public function view($id)
	{
		
		// $this->db->where('is_delete', 0);
		$this->db->where('id', $id);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function add($data)
	{
		
		$this->db->insert($this->tbl_name, $data);
		$this->db->where($data);
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function edit($id, $data)
	{
		
		$this->db->where('id', $id);
		$this->db->update($this->tbl_name, $data);
		
	}

	public function del($id)
	{
		
		$this->db->where('id', $id);
		$this->db->update($this->tbl_name, [
			'is_delete' => 1,
		]);
		
	}

	public function rejecte_pending_email_user($email)
	{
		
		$this->db->where('branch', branch_now('pid'));
		$this->db->where('is_delete', 0);
		$this->db->where('type', 'pending_active');
		$this->db->where('email', $email);
		$this->db->update($this->tbl_name, [
			'is_delete' => 1,
		]);
		
	}

	public function check_pending_email($email)
	{
		
		$this->db->where('branch', branch_now('pid'));
		$this->db->where('is_delete', 0);
		$this->db->where('type', 'pending_active');
		$this->db->where('email', $email);
		
		$query = $this->db->get($this->tbl_name);
		
		$result = $query->result_array();
		
		// return count($result) == 0
		
	}
	
	public function std_service_active($service_id, $std_id, $date = '')
	{

		$result = $this->std_service_active_check( $service_id, $std_id, false );

		if(empty($date)) $date = date('Y-m-d');
		
		if(count($result) == 0) {
			
			// create
			$this->add([
				'type' => 'join_service',
				'branch' => datalist_Table('tbl_users', 'branch', $std_id),
				'user' => $std_id,
				'item' => $service_id,
				'active' => 1,
				'date' => $date,
				'create_by' => auth_data('pid')
			]);
			
			return [
				'active' => 1,
				'date' => $date,
			];
			
		} else {
			
			// check active
			$result = $result[0];
			
			// update
			$save_active = $result['active'] == 1 ? 0 : 1 ;
			
			$this->edit($result['id'], [
				'active' => $save_active,
				'date' => $date,
			]);
			
			return [
				'active' => $save_active,
				'date' => $date,
			];
			
		}
		
	}
	public function std_service_active_check($service_id, $std_id, $active = 1)
	{
		
		$this->db->where('is_delete', 0);
		if($active != false) $this->db->where('active', $active);
		$this->db->where([
			'type' => 'join_service',
			'item' => $service_id,
			'user' => $std_id,
		]);
		
		$query = $this->db->get($this->tbl_name);
		$result = $query->result_array();
		
		return $result;
		
	}
	
	public function list_daily_attendance($branch, $start_date, $end_date, $teacher)
	{
		$sql_where = "";
		$sql_where_join_date = "";
		
		if (!empty($start_date) && !empty($end_date))
		{
			//$sql_where .= " AND DATE(attendances.date) BETWEEN '". $start_date ."' AND '". $end_date ."'";
			$sql_where_join_date .= " AND EXTRACT(YEAR_MONTH FROM join_classes.date) 
									BETWEEN EXTRACT(YEAR_MONTH FROM '". $start_date ."') 
									AND EXTRACT(YEAR_MONTH FROM '". $end_date ."')";
		}
		
		if (!empty($start_date) && empty($end_date))
		{
			//$sql_where .= " AND DATE(attendances.date) >= '". $start_date ."'";
			$sql_where_join_date .= " AND EXTRACT(YEAR_MONTH FROM join_classes.date) >= EXTRACT(YEAR_MONTH FROM '". $start_date ."')";
		}
		
		if (empty($start_date) && !empty($end_date))
		{
			//$sql_where .= " AND DATE(attendances.date) <= '". $end_date ."'";
			$sql_where_join_date .= " AND EXTRACT(YEAR_MONTH FROM join_classes.date) <= EXTRACT(YEAR_MONTH FROM '". $end_date ."')";
		}
		
		if (!empty($teacher))
		{
			//$sql_where .= " AND (attendance_teachers.pid = '". $teacher ."' OR class_teachers.pid = '". $teacher ."')";
			$sql_where .= " AND class_teachers.pid = '". $teacher ."'";
		}
		
		
		$sql = "WITH RECURSIVE class_dates AS (
					  select date('". $start_date ."') as class_date 
					  union all
					  select class_date + interval 1 day
					  from class_dates
					  where class_date < '". $end_date ."'
				)
				
				SELECT tbl_classes.title AS class_title, sub_classes.title AS sub_class_title, sub_classes.time_range, 
				DAYNAME(CONCAT('1970-09-2', sub_classes.qty)) AS class_day, DATE(X.class_date) AS class_date,
				class_teachers.fullname_en AS teacher_fullname_en,class_teachers.fullname_cn AS teacher_fullname_cn,
				SUM(COALESCE(joins.student_count, 0)) AS join_count,
				SUM(COALESCE(joins.student_count, 0)) - (SUM(COALESCE(presents.student_count, 0)) + SUM(COALESCE(absents.student_count, 0))) AS none_count,
				SUM(COALESCE(presents.student_count, 0)) AS present_count, 
				SUM(COALESCE(absents.student_count, 0)) AS absent_count 
				FROM tbl_classes
				LEFT JOIN tbl_users class_teachers ON class_teachers.pid = tbl_classes.teacher
				JOIN log_join sub_classes ON tbl_classes.pid = sub_classes.class 
				AND sub_classes.type = 'class_timetable' 
				AND sub_classes.is_delete = 0
				JOIN (
					SELECT d.class_date, DAYNAME(d.class_date) AS DAY_NAME
					FROM class_dates d
					ORDER By d.class_date
				) X ON X.class_date IS NOT NULL AND X.DAY_NAME = DAYNAME(CONCAT('1970-09-2', sub_classes.qty)) 
				LEFT JOIN (
					SELECT class, sub_class, 
					COUNT(join_classes.user) AS student_count
					FROM log_join join_classes
					JOIN tbl_users students ON students.pid = join_classes.user
					WHERE join_classes.is_delete = 0
					AND join_classes.active = 1
					AND join_classes.type = 'join_class'
					AND join_classes.branch = '". $branch ."'
					". $sql_where_join_date ."
					AND students.type = 'student'
					AND students.active = 1
					AND students.is_delete = 0
					AND students.branch = '". $branch ."'
					GROUP BY class, sub_class
				) joins ON joins.class = tbl_classes.pid AND joins.sub_class = sub_classes.id
				
				LEFT JOIN (
					SELECT attendances.class, attendances.sub_class, 
					DATE(attendances.date) AS attendance_date, 
					COUNT(attendances.user) AS student_count
					FROM log_join attendances
					JOIN tbl_users students ON students.pid = attendances.user
					WHERE attendances.type = 'class_attendance'
					AND attendances.active = 1
					AND attendances.is_delete = 0
					AND students.type = 'student'
					AND students.active = 1
					AND students.is_delete = 0
					AND students.branch = '". $branch ."'
					GROUP BY attendances.class, attendances.sub_class, DATE(attendances.date)
				) presents ON presents.class = tbl_classes.pid
				AND presents.sub_class = sub_classes.id
				AND presents.attendance_date = DATE(X.class_date)
				
				LEFT JOIN (
					SELECT attendances.class, attendances.sub_class, 
					DATE(attendances.date) AS attendance_date, 
					COUNT(attendances.user) AS student_count
					FROM log_join attendances
					JOIN tbl_users students ON students.pid = attendances.user
					WHERE attendances.type = 'class_attendance'
					AND attendances.active = 0
					AND attendances.is_delete = 0
					AND students.type = 'student'
					AND students.active = 1
					AND students.is_delete = 0
					AND students.branch = '". $branch ."'
					GROUP BY attendances.class, attendances.sub_class, DATE(attendances.date)
				) absents ON absents.class = tbl_classes.pid 
				AND absents.sub_class = sub_classes.id 
				AND absents.attendance_date = DATE(X.class_date)
				WHERE tbl_classes.is_delete = 0
				". $sql_where ."
				GROUP BY tbl_classes.pid, sub_classes.id, DATE(X.class_date)
				ORDER BY DATE(X.class_date), class_teachers.fullname_en, tbl_classes.title, sub_classes.title, sub_classes.qty, sub_classes.time_range";
		
		
				// GROUP BY join_classes.class
		$sqlX = "SELECT attendances.class, attendances.sub_class, DATE(attendances.date) AS check_in_date,
				tbl_classes.title AS class_title, timetables.title AS sub_class_title, timetables.time_range, 
				DAYNAME(CONCAT('1970-09-2', timetables.qty)) AS class_day,
				COALESCE(attendance_teachers.fullname_en, class_teachers.fullname_en) AS teacher_fullname_en,
				COALESCE(joins.student_count, 0) AS join_count,
				(COALESCE(joins.student_count, 0) - COALESCE(presents.student_count, 0) - COALESCE(absents.student_count, 0)) AS none_count, 
				COALESCE(presents.student_count, 0) AS present_count, COALESCE(absents.student_count, 0) AS absent_count 
				FROM log_join attendances
				JOIN tbl_classes ON tbl_classes.pid = attendances.class
				JOIN log_join timetables ON tbl_classes.pid = timetables.class AND timetables.id = attendances.sub_class AND timetables.type = 'class_timetable' AND timetables.is_delete = 0
				LEFT JOIN tbl_users attendance_teachers ON attendance_teachers.pid = attendances.create_by
				LEFT JOIN tbl_users class_teachers ON class_teachers.pid = tbl_classes.teacher
				LEFT JOIN (
					SELECT class, COUNT(user) AS student_count
					FROM log_join
					WHERE is_delete = 0
					AND active = 1
					AND type = 'join_class'
					GROUP BY class
				) joins ON joins.class = tbl_classes.pid
				LEFT JOIN (
					SELECT log_join.class, log_join.sub_class, DATE(log_join.date) AS check_in_date, COUNT(user) AS student_count
					FROM log_join
					JOIN tbl_users students ON students.pid = log_join.user
					WHERE log_join.branch = '". $branch ."'
					AND log_join.type = 'class_attendance'
					AND log_join.is_delete = 1
					AND students.type = 'student'
					AND students.active = 1
					AND students.is_delete = 0
					GROUP BY log_join.class, log_join.sub_class, DATE(log_join.date)
				) nones ON nones.class = attendances.class AND nones.sub_class = attendances.sub_class AND nones.check_in_date = DATE(attendances.date)
				
				LEFT JOIN (
					SELECT log_join.class, log_join.sub_class, DATE(log_join.date) AS check_in_date, COUNT(user) AS student_count
					FROM log_join
					JOIN tbl_users students ON students.pid = log_join.user
					WHERE log_join.branch = '". $branch ."'
					AND log_join.type = 'class_attendance'
					AND log_join.active = 1
					AND log_join.is_delete = 0
					AND students.type = 'student'
					AND students.active = 1
					AND students.is_delete = 0
					GROUP BY log_join.class, log_join.sub_class, DATE(log_join.date)
				) presents ON presents.class = attendances.class AND presents.sub_class = attendances.sub_class AND presents.check_in_date = DATE(attendances.date)
				
				LEFT JOIN (
					SELECT log_join.class, log_join.sub_class, DATE(log_join.date) AS check_in_date, COUNT(user) AS student_count
					FROM log_join
					JOIN tbl_users students ON students.pid = log_join.user
					WHERE log_join.branch = '". $branch ."'
					AND log_join.type = 'class_attendance'
					AND log_join.active = 0
					AND log_join.is_delete = 0
					AND students.type = 'student'
					AND students.active = 1
					AND students.is_delete = 0
					GROUP BY log_join.class, log_join.sub_class, DATE(log_join.date)
				) absents ON absents.class = attendances.class AND absents.sub_class = attendances.sub_class AND absents.check_in_date = DATE(attendances.date)
				
				WHERE attendances.branch = '". $branch ."'
				AND attendances.type = 'class_attendance'
				". $sql_where ."
				GROUP BY attendances.class, attendances.sub_class, DATE(attendances.date)
				ORDER BY DATE(attendances.date), COALESCE(attendance_teachers.fullname_en, class_teachers.fullname_en), tbl_classes.title, timetables.title, timetables.qty, timetables.time_range";
		
		$result = $this->db->query($sql)->result_array();
		
		return $result;
		
	}
	
	public function student_enroll($branch, $start_date, $end_date, $student)
	{
		$sql_where = "";
		
		if (!empty($start_date) && !empty($end_date))
		{
			$sql_where .= " AND DATE(log_join.date) BETWEEN '". $start_date ."' AND '". $end_date ."'";
		}
		
		if (!empty($start_date) && empty($end_date))
		{
			$sql_where .= " AND DATE(log_join.date) >= '". $start_date ."'";
		}
		
		if (empty($start_date) && !empty($end_date))
		{
			$sql_where .= " AND DATE(log_join.date) <= '". $end_date ."'";
		}
		
		if (!empty($student))
		{
			$sql_where .= " AND students.pid = '". $student ."'";
		}
		
		$sql = "SELECT log_join.date, students.code AS student_code, students.fullname_en AS student_fullname_en, 
				students.fullname_cn AS student_fullname_cn,
				tbl_classes.title AS class_title, timetables.title AS sub_class_title, timetables.time_range, 
				DAYNAME(CONCAT('1970-09-2', timetables.qty)) AS class_day, 
				(CASE WHEN COALESCE(teacher_enrolls.fullname_en, '-') = '-' THEN COALESCE(admin_enrolls.nickname, '-') ELSE teacher_enrolls.fullname_en END) AS enroll_fullname_en,
				(CASE WHEN COALESCE(teacher_enrolls.fullname_cn, '-') = '-' THEN '' ELSE teacher_enrolls.fullname_cn END) AS enroll_fullname_cn
				FROM log_join
				LEFT JOIN tbl_users teacher_enrolls ON teacher_enrolls.pid = log_join.update_by
				LEFT JOIN tbl_admins admin_enrolls ON admin_enrolls.pid = log_join.update_by
				JOIN tbl_users students ON students.pid = log_join.user
				AND students.is_delete = 0
				AND students.active = 1
				AND students.type = 'student'
				JOIN tbl_classes ON tbl_classes.pid = log_join.class
				JOIN log_join timetables ON tbl_classes.pid = timetables.class 
				AND timetables.id = log_join.sub_class 
				AND timetables.type = 'class_timetable' 
				AND timetables.is_delete = 0
				WHERE log_join.type = 'join_class'
				AND log_join.active = 0
				AND log_join.is_delete = 0
				AND log_join.branch = '". $branch ."'
				". $sql_where . "
				ORDER BY log_join.date, students.code";
				
		$result = $this->db->query($sql)->result_array();
		
		return $result;
	}
	
	public function absence_rate($branch, $start_date, $end_date, $student)
	{
		$sql_where = "";
		$sql_where_date = "";
		
		if (!empty($start_date) && !empty($end_date))
		{
			$sql_where_date .= " AND DATE(attendances.date) BETWEEN '". $start_date ."' AND '". $end_date ."'";
		}
		
		if (!empty($start_date) && empty($end_date))
		{
			$sql_where_date .= " AND DATE(attendances.date) >= '". $start_date ."'";
		}
		
		if (empty($start_date) && !empty($end_date))
		{
			$sql_where_date .= " AND DATE(attendances.date) <= '". $end_date ."'";
		}
		
		if (!empty($student))
		{
			$sql_where .= " AND students.pid = '". $student ."'";
		}
		/* 
		$sql = "SELECT students.code AS student_code, students.fullname_en AS student_fullname_en,
				students.fullname_cn AS student_fullname_cn, 
				join_classes.attend_class_count, join_classes.present_count, 
				(COALESCE(join_classes.attend_class_count, 0) - COALESCE(join_classes.present_count, 0)) AS absent_count
				FROM tbl_users students
				JOIN (
					SELECT user, SUM(attend_class_count) AS attend_class_count, SUM(present_count) AS present_count, 
					SUM(absent_count) AS absent_count
					FROM (
						SELECT log_join.user, log_join.class, log_join.sub_class, COUNT(attendances.date) AS attend_class_count,
						SUM(CASE WHEN attendances.active = 1 THEN 1 ELSE 0 END) AS present_count,
						SUM(CASE WHEN (attendances.active = 0) THEN 1 ELSE 0 END) AS absent_count
						FROM log_join
						JOIN tbl_classes ON tbl_classes.pid = log_join.class
						JOIN log_join timetables ON tbl_classes.pid = timetables.class AND timetables.id = log_join.sub_class AND timetables.type = 'class_timetable' AND timetables.is_delete = 0
						JOIN log_join attendances ON attendances.class = log_join.class
						AND attendances.sub_class = log_join.sub_class
						AND attendances.user = log_join.user
						WHERE log_join.is_delete = 0
						AND log_join.active = 1
						AND log_join.type = 'join_class'
						AND log_join.branch = '". $branch ."'
						AND attendances.type = 'class_attendance'
						AND attendances.branch = '". $branch ."'
						". $sql_where_date ."
						GROUP BY log_join.user, log_join.class, log_join.sub_class
					) group_attend
					GROUP BY user
				) join_classes ON join_classes.user = students.pid
				WHERE students.is_delete = 0
				AND students.active = 1
				AND students.type = 'student'
				AND students.branch = '". $branch ."'"
				.$sql_where;
		
		 */
		
		$sql = "WITH RECURSIVE class_dates AS (
					  select date('". $start_date ."') as class_date 
					  union all
					  select class_date + interval 1 day
					  from class_dates
					  where class_date < '". $end_date ."'
				)
				SELECT tbl_classes.pid AS class_id, sub_classes.id AS sub_class_id,
				tbl_classes.title AS class_title, sub_classes.title AS sub_class_title, 
				sub_classes.time_range, DAYNAME(CONCAT('1970-09-2', sub_classes.qty)) AS class_day,
				students.pid AS student_pid,
				students.code AS student_code, 
				students.fullname_en AS student_fullname_en,
				students.fullname_cn AS student_fullname_cn,
				SUM(COALESCE(present_count, 0)) AS present_count,
				SUM(COALESCE(absent_count, 0)) AS absent_count,
				X.class_date
				FROM tbl_classes 
				JOIN log_join sub_classes ON tbl_classes.pid = sub_classes.class AND sub_classes.type = 'class_timetable' AND sub_classes.is_delete = 0
				JOIN log_join joined_classes ON joined_classes.class = tbl_classes.pid
				AND joined_classes.sub_class = sub_classes.id
				AND joined_classes.type = 'join_class'
				AND joined_classes.is_delete = 0
				AND joined_classes.active = 1
				JOIN tbl_users students ON students.pid = joined_classes.user 
				JOIN (
					SELECT d.class_date, DAYNAME(d.class_date) AS DAY_NAME
					FROM class_dates d
					ORDER By d.class_date
				) X ON X.class_date IS NOT NULL AND X.DAY_NAME = DAYNAME(CONCAT('1970-09-2', sub_classes.qty)) 
                LEFT JOIN (
					SELECT attendances.user, attendances.class, attendances.sub_class, 
					DATE(attendances.date) AS attendance_date,
					(CASE WHEN attendances.active = 1 THEN 1 ELSE 0 END) AS present_count,
					(CASE WHEN (attendances.active = 0) THEN 1 ELSE 0 END) AS absent_count
					FROM log_join attendances 
					WHERE attendances.type = 'class_attendance'
					AND attendances.branch = '". branch_now('pid') ."'
					AND attendances.is_delete = 0
				) attendances ON attendances.class = tbl_classes.pid 
				AND attendances.sub_class = sub_classes.id
				AND DATE(attendances.attendance_date) = DATE(X.class_date)
				AND attendances.user = students.pid
				WHERE students.is_delete = 0
				AND students.active = 1
				AND students.type = 'student'
				AND students.branch = '". $branch ."'
				". $sql_where ."
				GROUP BY tbl_classes.pid, sub_classes.id,
				tbl_classes.title, sub_classes.title, 
				sub_classes.time_range, DAYNAME(CONCAT('1970-09-2', sub_classes.qty)),
				students.pid,
				students.code, 
				students.fullname_en,
				students.fullname_cn,
				X.class_date";
				
		$result = $this->db->query($sql)->result_array();
		
		return $result;
	}

	// Tan Jing Suan
	public function listitem($id = '')
	{
		$this->db->where('is_delete', 0);
		$this->db->where('id', $id);
		$query = $this->db->get($this->tbl_name);		
		return $query->result_array();	
	}

	public function selectlist($type = '', $branch = '', $search = [])
	{
		$this->db->distinct();
		$this->db->select(
			"user"
		);		
		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where('type', $type);
		$this->db->where($search);		
		$query = $this->db->get($this->tbl_name);		
		return $query->result_array();		
	}
	
	public function additem($data) {
		$this->db->insert($this->tbl_name, $data);
		return $this->db->insert_id();
	}
	
	public function delid($id)
	{		
		$this->db->where('id', $id);
		$this->db->update($this->tbl_name, [
			'is_delete' => 1,
		]);		
	}

}