<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sys_app_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'sys_app';
		
	}


	public function list()
	{
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function view($k)
	{
		
		$this->db->where('k', $k);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function add($data)
	{
		$this->db->insert($this->tbl_name, $data);
	}

	public function edit($k, $data)
	{
		
		$this->db->where('k', $k);
		$this->db->update($this->tbl_name, $data);
		
	}

}