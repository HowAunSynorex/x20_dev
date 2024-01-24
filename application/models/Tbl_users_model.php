<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tbl_users_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'tbl_users';
        $this->tbl_secondary = 'tbl_secondary';
		
	}

	public function list($type = '', $branch = '', $search = [])
	{
		$this->db->select('tbl_users.*');
		$this->db->where('is_delete', 0);
		$this->db->where('type', $type);
		$this->db->where('branch', $branch);
		$this->db->where($search);
		// $this->db->limit(10);
		
		if ($type == 'parent')
		{
			$this->db->select("(SELECT COUNT(*) FROM log_join WHERE is_delete = 0 AND branch = tbl_users.branch AND type = 'join_parent' AND parent = tbl_users.pid) AS childs");
		}

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function student_list($type = '', $branch = '', $search = [], $limit = false, $sort = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where('type', $type);
		$this->db->where('branch', $branch);
		$this->db->where($search);
		
		if(is_array($limit)) {
			
			$this->db->limit($limit[0], $limit[1]);
			
		} elseif($limit != false) {
			
			$this->db->limit($limit);
			
		}
		
		foreach($sort as $k => $v) $this->db->order_by($k, $v);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function list_v2($search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where($search);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function all_list($branch = '')
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
	
	// by steve
	public function rfid_to_userid($rfid, $branch_id = '')
	{

		// $branch_id = branch_now('pid');

		$this->db->where('is_delete', 0);
		$this->db->where('active', 1);
		$this->db->where('rfid_cardid', $rfid);
		$this->db->where('branch', $branch_id);

		$query = $this->db->get($this->tbl_name);
		
		$result = $query->result_array();
		
		return isset($result[0]['pid']) ? $result[0]['pid'] : null ;
		
	}
	
	//for report purpose
	public function active_list($type = '', $branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('active', 1);
		$this->db->where('type', $type);
		$this->db->where('branch', $branch);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	//dashboard display number of user
	public function total_user($type = '', $branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('type', $type);
		$this->db->where('branch', $branch);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	//dashboard display number of active user
	public function total_active_user($type = '', $branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('active', 1);
		$this->db->where('type', $type);
		$this->db->where('branch', $branch);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	//dashboard display monthly new user
	public function monthly_user($type = '', $branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('type', $type);
		$this->db->where('branch', $branch);
		$this->db->where('MONTH(date_join)', date('m'));
		$this->db->where('YEAR(date_join)', date('Y'));

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	// report要用到的
	public function monthly_user2($type = '', $branch = '', $m = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('type', $type);
		$this->db->where('branch', $branch);
		$this->db->where('MONTH(date_join)', $m);
		$this->db->where('YEAR(date_join)', date('Y'));

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	//dashboard display birthday of user
	public function birthday_user($type = '', $branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('active', 1);
		$this->db->where('type', $type);
		$this->db->where('branch', $branch);
		$this->db->where('MONTH(birthday)', date('m'));
		$this->db->order_by('birthday', 'DESC');

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	//to display number of children for each parent
	public function total_children($pid = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('parent', $pid);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	//to display number of student for each school
	public function total_student($school = '', $branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('school', $school);
		$this->db->where('branch', $branch);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	//for report purpose display birthday
	public function list_birthday_only($type = '', $branch = '', $search = [])
	{

		$this->db->where('birthday !=', null);
		$this->db->where('birthday !=', '0000-00-00');
		$this->db->where('birthday !=', '');

		$this->db->where('is_delete', 0);
		$this->db->where('type', $type);
		$this->db->where('branch', $branch);
		$this->db->order_by('birthday', 'DESC');

		if(isset($search['start'])) $this->db->where('birthday >=', $search['start']);
		if(isset($search['end'])) $this->db->where('birthday <=', $search['end']);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function view($id)
	{
		$this->db->select($this->tbl_name.'.*');
        $this->db->select('childcare_row.price as childcare_price,childcare_row.title as childcare_title');
        $this->db->select('transport_row.price as transport_price,transport_row.title as transport_title');
		$this->db->where($this->tbl_name.'.pid', $id);
		$this->db->join($this->tbl_secondary.' as childcare_row','childcare_row.pid = '.$this->tbl_name.'.childcare','left');
        $this->db->join($this->tbl_secondary.' as transport_row','transport_row.pid = '.$this->tbl_name.'.transport','left');
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	//check card ID when submit attendance
	public function view_attendance($rfid = '', $branch = '')
	{
		
		$this->db->where('rfid_cardid', $rfid);
		$this->db->where('branch', $branch);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function add($data)
	{
		
		$data['pid'] = get_new('id');
		
		$this->db->insert($this->tbl_name, $data);
		
		return $data['pid'];
		
	}
	
	public function add2($data)
	{
		
		$this->db->insert($this->tbl_name, $data);
		
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

	public function login($username, $password)
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('username', $username);
		$this->db->where('type !=', 'teacher');
		
		$query = $this->db->get($this->tbl_name);

		$result = $query->result_array();
		
		// return count($result);

		if( count($result) == 1 ) {

			$result = $result[0];

			if(password_verify($password, $result['password'])) {

				return $result['pid'];

			} else {

				return false;

			}

		} else {

			return false;

		}
		
	}
	
	public function login_teacher($username, $password)
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('username', $username);
		$this->db->where('type', 'teacher');
		
		$query = $this->db->get($this->tbl_name);

		$result = $query->result_array();
		
		// return count($result);

		if( count($result) == 1 ) {

			$result = $result[0];

			if(password_verify($password, $result['password'])) {

				return $result['pid'];

			} else {

				return false;

			}

		} else {

			return false;

		}
		
	}

	/*public function me($id)
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('pid', $id);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}*/
	
	//check username
	public function check_username($username, $id = '')
	{
		
// 		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('username', $username);

		if(!empty($id)) {

			$this->db->where('pid !=', $id);

		}
		
		$query = $this->db->get($this->tbl_name);

		$result = $query->result_array();
		
		return count($result) == 0 ? true : false ;
		
	}
	
	//check card ID
	public function check_rfid($rfid, $branch, $id = '')
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('rfid_cardid', $rfid);
		$this->db->where('branch', $branch);

		if(!empty($id)) {

			$this->db->where('pid !=', $id);

		}
		
		$query = $this->db->get($this->tbl_name);

		$result = $query->result_array();
		
		return count($result) == 0 ? true : false ;
		
	}

	//check user's status
	public function check_user($id = '')
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('pid', $id);

		if(!empty($id)) {

			$this->db->where('pid =', $id);

		}
		
		$query = $this->db->get($this->tbl_name);

		$result = $query->result_array();
		
		return count($result) == 0 ? false : true ;
		
	}
	
	public function check_user1($rfid = '')
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('rfid_cardid', $rfid);

		if(!empty($rfid)) {

			$this->db->where('rfid_cardid =', $rfid);

		}
		
		$query = $this->db->get($this->tbl_name);

		$result = $query->result_array();
		
		return count($result) == 0 ? false : true ;
		
	}

	// api session
	public function me_token($token)
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('token', $token);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function me_teacher_token($token)
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('token', $token);
		$this->db->where('type', 'teacher');
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function reset_pending_insurance()
	{
		$this->db->where('insurance', 'pending');
		$this->db->update($this->tbl_name, ['insurance' => NULL]);
	}
	
	public function add_batch($data)
	{
		$this->db->insert_batch($this->tbl_name, $data);
	}

	// Tan Jing Suan
	public function kindergarden_student_joined($type = '', $branch = '', $month = '')
	{
		$sql = "SELECT forms.pid, forms.title, 
				COUNT(DISTINCT new_students.student_pid) AS new_student, 
				GROUP_CONCAT(DISTINCT new_students.fullname_en) AS new_student_fullname_en,
				GROUP_CONCAT(DISTINCT new_students.fullname_cn) AS new_student_fullname_cn,
				COUNT(DISTINCT new_join_students.student_pid) AS new_join_student, 
				GROUP_CONCAT(DISTINCT new_join_students.fullname_en) AS new_join_student_fullname_en,
				GROUP_CONCAT(DISTINCT new_join_students.fullname_cn) AS new_join_student_fullname_cn,
				COUNT(DISTINCT new_unjoin_students.student_pid) AS new_unjoin_student,
				GROUP_CONCAT(DISTINCT new_unjoin_students.fullname_en) AS new_unjoin_student_fullname_en,
				GROUP_CONCAT(DISTINCT new_unjoin_students.fullname_cn) AS new_unjoin_student_fullname_cn,
				(COUNT(DISTINCT new_students.student_pid) + COUNT(DISTINCT new_join_students.student_pid) - COUNT(DISTINCT new_unjoin_students.student_pid)) AS total_student
				FROM tbl_secondary forms 
				LEFT JOIN (
					SELECT students.form, students.pid AS student_pid,
					students.fullname_en, students.fullname_cn
					FROM tbl_users students 
					WHERE students.is_delete = 0 
					AND students.type = '". $type . "' 
					AND students.branch = '". $branch ."' 
					AND YEAR(create_on) = '". date('Y') ."' 
					AND MONTH(create_on) = '". $month ."'
				) new_students ON new_students.form = forms.pid
				LEFT JOIN (
					SELECT students.form, students.pid AS student_pid,
					students.fullname_en, students.fullname_cn
					FROM tbl_users students 
					JOIN log_join ON log_join.user = students.pid AND log_join.active = 1
					WHERE students.is_delete = 0 
					AND students.type = '". $type . "' 
					AND students.branch = '". $branch ."' 
					AND log_join.type = 'join_class'
					AND YEAR(log_join.date) = '". date('Y') ."' 
					AND MONTH(log_join.date) = '". $month ."'
				) new_join_students ON new_join_students.form = forms.pid
				LEFT JOIN (
					SELECT students.form, students.pid AS student_pid,
					students.fullname_en, students.fullname_cn
					FROM tbl_users students 
					JOIN log_join ON log_join.user = students.pid AND log_join.active = 0
					WHERE students.is_delete = 0 
					AND students.type = '". $type . "' 
					AND students.branch = '". $branch ."' 
					AND log_join.type = 'join_class'
					AND YEAR(log_join.date) = '". date('Y') ."' 
					AND MONTH(log_join.date) = '". $month ."'
				) new_unjoin_students ON new_unjoin_students.form = forms.pid
				WHERE forms.type = 'form' AND forms.is_delete = 0 AND forms.active = 1 AND forms.branch = '". $branch ."'
				AND (forms.title LIKE 'AGE%') 
				GROUP BY forms.pid, forms.title
				ORDER BY FIELD(title, 'AGE 3', 'AGE 4', 'AGE 5', 'AGE 6')";
		
		$query = $this->db->query($sql);		
		
		return $query->result_array();
	}
	
	public function primary_student_joined($type = '', $branch = '', $month = '')
	{
		$sql = "SELECT forms.pid, forms.title, 
				COUNT(DISTINCT new_students.student_pid) AS new_student, 
				GROUP_CONCAT(DISTINCT new_students.fullname_en) AS new_student_fullname_en,
				GROUP_CONCAT(DISTINCT new_students.fullname_cn) AS new_student_fullname_cn,
				COUNT(DISTINCT new_join_students.student_pid) AS new_join_student, 
				GROUP_CONCAT(DISTINCT new_join_students.fullname_en) AS new_join_student_fullname_en,
				GROUP_CONCAT(DISTINCT new_join_students.fullname_cn) AS new_join_student_fullname_cn,
				COUNT(DISTINCT new_unjoin_students.student_pid) AS new_unjoin_student,
				GROUP_CONCAT(DISTINCT new_unjoin_students.fullname_en) AS new_unjoin_student_fullname_en,
				GROUP_CONCAT(DISTINCT new_unjoin_students.fullname_cn) AS new_unjoin_student_fullname_cn,
				(COUNT(DISTINCT new_students.student_pid) + COUNT(DISTINCT new_join_students.student_pid) - COUNT(DISTINCT new_unjoin_students.student_pid)) AS total_student
				FROM tbl_secondary forms 
				LEFT JOIN (
					SELECT students.form, students.pid AS student_pid,
					students.fullname_en, students.fullname_cn
					FROM tbl_users students 
					WHERE students.is_delete = 0 
					AND students.type = '". $type . "' 
					AND students.branch = '". $branch ."' 
					AND YEAR(create_on) = '". date('Y') ."' 
					AND MONTH(create_on) = '". $month ."'
				) new_students ON new_students.form = forms.pid
				LEFT JOIN (
					SELECT students.form, students.pid AS student_pid,
					students.fullname_en, students.fullname_cn
					FROM tbl_users students 
					JOIN log_join ON log_join.user = students.pid AND log_join.active = 1
					WHERE students.is_delete = 0 
					AND students.type = '". $type . "' 
					AND students.branch = '". $branch ."' 
					AND log_join.type = 'join_class'
					AND YEAR(log_join.date) = '". date('Y') ."' 
					AND MONTH(log_join.date) = '". $month ."'
				) new_join_students ON new_join_students.form = forms.pid
				LEFT JOIN (
					SELECT students.form, students.pid AS student_pid,
					students.fullname_en, students.fullname_cn
					FROM tbl_users students 
					JOIN log_join ON log_join.user = students.pid AND log_join.active = 0
					WHERE students.is_delete = 0 
					AND students.type = '". $type . "' 
					AND students.branch = '". $branch ."' 
					AND log_join.type = 'join_class'
					AND YEAR(log_join.date) = '". date('Y') ."' 
					AND MONTH(log_join.date) = '". $month ."'
				) new_unjoin_students ON new_unjoin_students.form = forms.pid
				WHERE forms.type = 'form' AND forms.is_delete = 0 AND forms.active = 1 AND forms.branch = '". $branch ."'
				AND (forms.title LIKE 'K%' OR forms.title LIKE 'Y%') 
				GROUP BY forms.pid, forms.title
				ORDER BY FIELD(title, 'K1', 'K2', 'Y1', 'Y2', 'Y3', 'Y4', 'Y5', 'Y6', 'F1', 'F2', 'F3', 'F4', 'F5', 'G2023')";
		
		$query = $this->db->query($sql);		
		
		return $query->result_array();
	}
	
	public function secondary_student_joined($type = '', $branch = '', $month = '')
	{
		$sql = "SELECT forms.pid, forms.title, 
				COUNT(DISTINCT new_students.student_pid) AS new_student, 
				GROUP_CONCAT(DISTINCT new_students.fullname_en) AS new_student_fullname_en,
				GROUP_CONCAT(DISTINCT new_students.fullname_cn) AS new_student_fullname_cn,
				COUNT(DISTINCT new_join_students.student_pid) AS new_join_student, 
				GROUP_CONCAT(DISTINCT new_join_students.fullname_en) AS new_join_student_fullname_en,
				GROUP_CONCAT(DISTINCT new_join_students.fullname_cn) AS new_join_student_fullname_cn,
				COUNT(DISTINCT new_unjoin_students.student_pid) AS new_unjoin_student,
				GROUP_CONCAT(DISTINCT new_unjoin_students.fullname_en) AS new_unjoin_student_fullname_en,
				GROUP_CONCAT(DISTINCT new_unjoin_students.fullname_cn) AS new_unjoin_student_fullname_cn,
				(COUNT(DISTINCT new_students.student_pid) + COUNT(DISTINCT new_join_students.student_pid) - COUNT(DISTINCT new_unjoin_students.student_pid)) AS total_student
				FROM tbl_secondary forms 
				LEFT JOIN (
					SELECT students.form, students.pid AS student_pid,
					students.fullname_en, students.fullname_cn
					FROM tbl_users students 
					WHERE students.is_delete = 0 
					AND students.type = '". $type . "' 
					AND students.branch = '". $branch ."' 
					AND YEAR(create_on) = '". date('Y') ."' 
					AND MONTH(create_on) = '". $month ."'
				) new_students ON new_students.form = forms.pid
				LEFT JOIN (
					SELECT students.form, students.pid AS student_pid,
					students.fullname_en, students.fullname_cn
					FROM tbl_users students 
					JOIN log_join ON log_join.user = students.pid AND log_join.active = 1
					WHERE students.is_delete = 0 
					AND students.type = '". $type . "' 
					AND students.branch = '". $branch ."' 
					AND log_join.type = 'join_class'
					AND YEAR(log_join.date) = '". date('Y') ."' 
					AND MONTH(log_join.date) = '". $month ."'
				) new_join_students ON new_join_students.form = forms.pid
				LEFT JOIN (
					SELECT students.form, students.pid AS student_pid,
					students.fullname_en, students.fullname_cn
					FROM tbl_users students 
					JOIN log_join ON log_join.user = students.pid AND log_join.active = 0
					WHERE students.is_delete = 0 
					AND students.type = '". $type . "' 
					AND students.branch = '". $branch ."' 
					AND log_join.type = 'join_class'
					AND YEAR(log_join.date) = '". date('Y') ."' 
					AND MONTH(log_join.date) = '". $month ."'
				) new_unjoin_students ON new_unjoin_students.form = forms.pid
				WHERE forms.type = 'form' AND forms.is_delete = 0 AND forms.active = 1  AND forms.branch = '". $branch ."'
				AND (forms.title LIKE 'F%')
				GROUP BY forms.pid, forms.title
				ORDER BY FIELD(title, 'K1', 'K2', 'Y1', 'Y2', 'Y3', 'Y4', 'Y5', 'Y6', 'F1', 'F2', 'F3', 'F4', 'F5', 'G2023')";
		
		$query = $this->db->query($sql);		
		
		return $query->result_array();
	}
	
	
	public function list_parent($branch = '', $search = [])
	{
		$sql = "SELECT tbl_users.*, COALESCE(childs.student_count, 0) AS student_count
				FROM tbl_users
				LEFT JOIN (
					SELECT log_join.parent, log_join.branch, COUNT(*) AS student_count
					FROM log_join 
					WHERE log_join.is_delete = 0 
					AND log_join.type = 'join_parent' 
					GROUP BY log_join.parent, log_join.branch
				) childs ON childs.branch = tbl_users.branch AND childs.parent = tbl_users.pid
				WHERE tbl_users.is_delete = 0
				AND tbl_users.type = 'parent'";
				
		if (!empty($branch))
		{
			$sql .= "AND tbl_users.branch = '". $branch ."'";
		}
		
		$query = $this->db->query($sql);		
		
		return $query->result_array();
		
	}
	
	// Tan Jing Suan
	//check user's nric
	public function check_nric($nric)
	{	
		$this->db->where('branch', branch_now('pid') );
		$this->db->where('nric', $nric);	
		$query = $this->db->get($this->tbl_name);
		return $query->result_array();
	}

}