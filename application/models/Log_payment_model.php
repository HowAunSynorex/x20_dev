<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_payment_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'log_payment';
		
	}

	public function list($payment)
	{

		$this->db->where('is_delete', 0);
		$this->db->where('payment', $payment);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	// by steve
	public function check_period_class_paid( $class_id, $user_id, $period )
	{

		$this->db->where([
			'is_delete' => 0,
			'user' => $user_id,
			'class' => $class_id,
			'period' => $period,
		]);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
		return count($query->result_array()) == 0 ? false : true ;
		
	}
	
	// payment page to loop active class
	public function list2($search = [], $order_by = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where($search);
		
		if(count($order_by) > 0) {
			foreach($order_by as $k => $v) {
				$this->db->order_by($k, $v);
			}
		}
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function view($id)
	{
		
		$this->db->where('id', $id);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function add($data)
	{
			
		$this->db->insert($this->tbl_name, $data);
		
	}

	public function edit($id, $data)
	{
		
		$this->db->where('id', $id);
		$this->db->update($this->tbl_name, $data);
		
	}

	public function del($id)
	{
		
		$this->db->where('payment', $id);
		$this->db->update($this->tbl_name, [
			'is_delete' => 1,
		]);
		
	}
	
	public function del_by_id($id)
	{
		
		$this->db->where('id', $id);
		$this->db->update($this->tbl_name, [
			'is_delete' => 1,
		]);
		
	}

}