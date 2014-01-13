<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mail_conf extends CI_Model
{
	public function __construct()
	{
            parent::__construct();
	}

    public function get_conf()
    {
        $this->db->where('active', 1);
        return array_shift($this->read($this->db->get('mail_conf')->row()->id));
    }

    public function create($data)
    {
        if (is_array($data))
        {   
            $data['id'] = $this->next_id();
            return $this->db->insert('mail_conf', $data);
        }
        return FALSE;
    }

    public function read($id = NULL)
    {
        $id = intval($id);
        if (!empty($id) && is_integer($id))
           $this->db->where('id', $id);
        $query = $this->db->get('mail_conf');
        return $query->result();
    }

    public function update($id, $data)
    {
        $id = intval($id);
        if (is_integer($id) && is_array($data))
        {
            $this->db->where('id', $id);
            return $this->db->update('mail_conf', $data);
        }
        return FALSE;
    }

    public function delete($id)
    {
        $id = intval($id);
        if (is_integer($id))
        {
            $this->db->where('id', $id);
            return $this->db->delete('mail_conf');
        }
        return FALSE;
    }

    public function enable($mail_conf_id)
    {
        $mail_conf_id = intval($mail_conf_id);
        if (is_integer($mail_conf_id))
        {
            if ($this->db->update('mail_conf', array('active' => 0)))
            {
                $this->db->where('id', $mail_conf_id);
                return $this->db->update('mail_conf', array('active' => 1));
            }
        }
        return FALSE;
    }
    
    public function next_id()
    {
        $this->db->select_max('id');
        $query = $this->db->get('mail_conf');
        return $query->row()->id + 1;
    }
}