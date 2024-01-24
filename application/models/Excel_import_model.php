<?php
class Excel_import_model extends CI_Model {

    function __construct() 
	{
		
		
	}

    function insert($data, $type)
    {
    	switch ($type) {
    		case 'user':
	    		$this->db->insert_batch('tbl_users', $data);
	    		break;
	    	case 'item':
	    		$this->db->insert_batch('tbl_inventory', $data);
	    		break;
	    	case 'class':
	    		$this->db->insert_batch('tbl_classes', $data);
	    		break;
    	}
        
    }

}