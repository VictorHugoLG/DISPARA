<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mail_data extends CI_Model
{
	public function __construct()
	{
        parent::__construct();
	}

	public function save($data)
	{
		$result = FALSE;
		if (!empty($data['id']))
		{
			$id = $data['id'];
			unset($data['id']);
			$this->db->where('id', $id);
			if ($this->db->update('mail_data', $data))
				$result = $id;
		}
		else
		{
			$this->db->select_max('id');
			$query = $this->db->get('mail_data');
			$data['id'] = $query->row()->id + 1;
			if ($this->db->insert('mail_data', $data))
				$result = $data['id'];
		}
		return $result;
	}

	public function select($id = NULL)
	{
		$result = array();
		if (NULL != $id)
		{
			$this->db->where('id', $id);
			$query = $this->db->get('mail_data');
			$result = $query->row();
		}
		else
		{
			$query = $this->db->get('mail_data');
			$result = $query->result();
		}
		return $result;
	}
}