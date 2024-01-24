<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tbl_payment_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'tbl_payment';
		
	}

	public function list($branch = '')
	{

		$this->db->where('is_delete', 0);
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
	
	/*
	 * check payment no has been taken
	 *
	 * @author Soon, Steve
	 *
	**/
	public function check_payment_no($payment_no, $id = '', $branch = '', $is_draft = '')
	{
		
		$this->db->where('is_delete', 0);
		$this->db->where('payment_no', $payment_no);
		$this->db->where('branch', $branch); // check taken in each branch only (by Steve)

		if(!empty($id)) {

			$this->db->where('pid !=', $id);

		}
		if($is_draft == '1') {

			$this->db->where('is_draft = 1');

		}
		
		$query = $this->db->get($this->tbl_name);

		$result = $query->result_array();
		
		return count($result) == 0 ? true : false ;
		
	}

	/*
	 * display payment record of particular student
	 *
	 * @author Soon
	 *
	**/
	public function student_list($student = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('student', $student);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	//for deleted receipts report purpose
	public function deleted_list($branch = '')
	{

		$this->db->where('is_delete', 1);
		$this->db->where('branch', $branch);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	//for daily collection report purpose
	public function daily_list($date = '', $branch = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where('date', $date);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	//for monthly collection report purpose
	public function monthly_list($month = '', $year = '', $branch = '', $is_draft = '')
	{
		$this->db->select('tbl_payment.*');
		$this->db->select('COALESCE(tbl_admins.nickname, "-") AS create_by_nickname, COALESCE(tbl_secondary.title, "-") AS payment_method_title');
		$this->db->where('tbl_payment.is_delete', 0);
		$this->db->where('tbl_payment.branch', $branch);
		$this->db->where('MONTH(tbl_payment.date)', $month);
		$this->db->where('YEAR(tbl_payment.date)', $year);
		$this->db->join('tbl_admins', 'tbl_admins.pid = tbl_payment.create_by', 'left');
		$this->db->join('tbl_secondary', 'tbl_secondary.pid = tbl_payment.payment_method', 'left');
		
		if ($is_draft == '1')
		{
			$this->db->where('is_draft', 1);
		}
		else
		{
			$this->db->where('is_draft', 0);
		}
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

}