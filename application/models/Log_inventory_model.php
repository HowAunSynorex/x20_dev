<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_inventory_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'log_inventory';
		
	}

	public function list($batch_id)
	{
		
		$this->db->where('inventory', $batch_id);
		
		$query = $this->db->get($this->tbl_name);
	
		return $query->result_array();
		
	}

	public function report_list()
	{
				
		$query = $this->db->get($this->tbl_name);
	
		return $query->result_array();
		
	}

	public function stock_list($start_date, $end_date)
	{
				
		$this->db->where('DATE(create_on) >=', $start_date);
		$this->db->where('DATE(create_on) <=', $end_date);
		$query = $this->db->get($this->tbl_name);
	
		return $query->result_array();
		
	}

	public function view($id, $type)
	{
		
		$this->db->where('user', $id);
		$this->db->where('type', $type);
		$this->db->order_by('create_on', 'ASC');
		
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

}