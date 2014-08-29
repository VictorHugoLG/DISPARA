<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mail_list extends CI_Model
{
	public function __construct()
	{
        parent::__construct();
	}

	public function select($mail_data_id, $mail_addr)
	{
		$mail_addr = $this->break_addr($mail_addr);
		$this->db->where(array('mail_data_id' => $mail_data_id, 'prefix' => $mail_addr[0], 'domain' => $mail_addr[1]));
		$query = $this->db->get('mail_list');
		return $query->row();
	}

	public function save($list)
	{
		return $this->db->insert_batch('mail_list', $list);
	}

	public function update_status($mail_data_id, $mail_addr, $new_status)
	{
		$mail_addr = $this->break_addr($mail_addr);
		$this->db->where(array('mail_data_id' => $mail_data_id, 'prefix' => $mail_addr[0], 'domain' => $mail_addr[1]));
		$query = $this->db->get('mail_list');
		$data = $query->row();
		$data->status = $new_status;
		$data->dttm_changed = date('Y-m-d H:m:s');
		$data->changed_by = DEFAULT_USER;
		$this->db->where(array('mail_data_id' => $mail_data_id, 'prefix' => $mail_addr[0], 'domain' => $mail_addr[1]));
		return $this->db->update('mail_list', $data);
	}

	public function stats($mail_data_id = 0, $status = '')
	{
		$result = array(
			'email' => array('agendados' => 0, 'falhas' => 0, 'enviados' => 0, 'lidos' => 0, 'rejeitados' => 0),
			'sms' => array('agendados' => 0, 'enviados' => 0));
		if ($mail_data_id)
			$this->db->where('mail_data_id', $mail_data_id);
		if (!empty($status) && $status != 'TOTAL')
			$this->db->where('status', $status);
		$query = $this->db->get('mail_list');
		$mail_list = $query->result();
		if (!$status)
		{
			foreach ($mail_list as $mail)
			{
				$type = (is_numeric($mail->domain)) ? 'sms' : 'email';
				switch ($mail->status)
				{
					case 'AGENDADO':
						$result[$type]['agendados']++;
						break;
					case 'ENVIADO':
						$result[$type]['enviados']++;
						break;
				 	case 'LIDO':
						$result[$type]['lidos']++;
						break;
					case 'FALHA':
						$result[$type]['falhas']++;
						break;
					default:
						$result[$type]['rejeitados']++;
				}
			}
		}
		else
		{
			$result = $mail_list;
		}
		return $result;
	}

	public function break_addr($mail_addr)
	{
		return (strpos($mail_addr, '@')) 
			? explode('@', $mail_addr) //email 
			: array(substr($mail_addr, 0, 3), substr($mail_addr, 3)); //telefone
	}
}