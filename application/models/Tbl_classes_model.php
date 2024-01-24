<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tbl_classes_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'tbl_classes';
		
	}

	public function list($branch = '', $search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where($search);
		
		$query = $this->db->get($this->tbl_name); 
		
		return $query->result_array();
		
	}
	
	public function list2($search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where($search);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function active_list($branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('active', 1);
		$this->db->where('branch', $branch);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function setup_list($branch = '', $search = [])
	{

		$this->db->where('branch', $branch);
		$this->db->where($search);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function total_class($course = '', $branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('course', $course);
		$this->db->where('branch', $branch);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function teacher_class($teacher = '', $branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('teacher', $teacher);
		$this->db->where('branch', $branch);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function view($id)
	{
		
		$this->db->where('pid', $id);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function add($data)
	{
		
		$data['pid'] = get_new('id');
		
		$this->db->insert($this->tbl_name, $data);
		
		return $data['pid'];
		
	}

	public function edit($id, $data)
	{
		
		$this->db->where('pid', $id);
		$this->db->update($this->tbl_name, $data);
		
	}

	public function del($id)
	{
		
		$this->db->where('pid', $id);
		$this->db->update($this->tbl_name, [
			'is_delete' => 1,
		]);
		
	}
	
	public function add_batch($data)
	{
		$this->db->insert_batch($this->tbl_name, $data);
	}
	
	public function classes_number($branch, $month_year, $teacher, $course)
	{
		$sql_where = "";
						
		if (!empty($teacher))
		{
			$sql_where = " AND tbl_classes.teacher = '". $teacher ."'";
		}
						
		if (!empty($course))
		{
			$sql_where = " AND tbl_classes.course IN ('" . join("', '", $course) . "')";
		}
		
		$sql = "SELECT courses.title AS course_title, tbl_classes.title AS class_title,
				teachers.pid AS teacher_pid,
				teachers.fullname_en AS teacher_fullname_en, teachers.fullname_cn AS teacher_fullname_cn,
				COALESCE(join_students.student_count, 0) AS student_count
				FROM tbl_classes
				JOIN tbl_secondary courses ON courses.pid = tbl_classes.course
				JOIN tbl_users teachers ON teachers.pid = tbl_classes.teacher
				LEFT JOIN (
					SELECT class, COUNT(join_classes.user) AS student_count
					FROM log_join join_classes
					JOIN tbl_users students ON students.pid = join_classes.user
					WHERE join_classes.is_delete = 0
					AND join_classes.active = 1
					AND join_classes.type = 'join_class'
					AND join_classes.branch = '". $branch ."'
					AND EXTRACT(YEAR_MONTH FROM join_classes.date) <= ". str_replace("-", "", $month_year) ."
					AND students.type = 'student'
					AND students.active = 1
					AND students.is_delete = 0
					AND students.branch = '". $branch ."'
					GROUP BY class
				) join_students ON join_students.class = tbl_classes.pid
				WHERE tbl_classes.is_delete = 0
				AND tbl_classes.active = 1
				AND tbl_classes.branch = '". $branch ."'"
				.$sql_where ." 
				AND COALESCE(join_students.student_count, 0) > 0
				ORDER BY tbl_classes.title, 
				FIELD (courses.title, 'K1', 'K2', 'Y1', 'Y2', 'Y3', 'Y4', 'Y5', 'Y6', 'F1', 'F2', 'F3', 'F4', 'F5'), teachers.fullname_en";
				
		$result = $this->db->query($sql)->result_array();
		
		return $result;
	}
}