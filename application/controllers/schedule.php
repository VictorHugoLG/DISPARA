<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schedule extends CI_Controller
{

	private $data;

	public function __construct()
	{
		parent::__construct();
	}

	public function upload()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'txt|csv';

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload())
		{
			$this->data['error'] = $this->upload->display_errors();
		}
		else
		{
			$data = $this->upload->data();
			$filename = './uploads/'.$data['file_name'];
			$file = fopen($filename, 'r');
			$mail_list = array();
			$n=0;
			while (($data = fgetcsv ($file, 10000, ';')) !== false)
			{
				$mail_list[$n]['email'] = str_replace('"', '', $data[0]);
				$mail_list[$n]['name'] = (!empty($data[1])) ? str_replace('"', '', $data[1]) : 'Amigo(a)';
				$n++;
			}
			while (($data = fgetcsv ($file, 10000, ',')) !== false)
			{
				$mail_list[$n]['email'] = str_replace('"', '', $data[0]);
				$mail_list[$n]['name'] = (!empty($data[1])) ? str_replace('"', '', $data[1]) : 'Amigo(a)';
				$n++;
			}
			fclose($file);
			if (!empty($mail_list))
			{
				$add = array(
					'user' => $this->input->post('user'),
					'mail_id' => $this->input->post('mail_id'),
					'mail_list' => $mail_list);
				//die(var_dump($add));
				$this->add($add);
			}
		}
		$this->load->model('mail_data', '', TRUE);
        $this->data['mails'] = $this->mail_data->select();
		$this->load->helper('url');
		$this->load->view('header', $this->data);
		$this->load->view('footer', $this->data);
	}

	public function add($add = NULL)
	{
		/**
		* Recebe o array $mail_list no de contatos para enviar o email
		* o $mail_list deve vir via POST no seguinte formato: 
		* {
		*		user : 'nome_de_usuário_efetuando_a_operação',
		*		mail_id : 'id_da_campanha',
		*		mail_list : 
		*		[
		*			{ email : 'lucasrocha@fieb.org.br', legacy_id : 'id legado', name : 'Lucas' },
		*			{ email : 'ricel@fieb.org.br', legacy_id : 'id legado', name : 'Ricel' },
		*		]
		* }
		*/
		$this->load->helper('email');
		$this->load->model('mail_list');
		if (empty($add))
		{
			$mail_data_id = $this->input->post('mail_id');
			$list = $this->input->post('mail_list');
		}
		else
		{
			$mail_data_id = $add['mail_id'];
			$list = $add['mail_list'];
		}
        //die(var_dump($list));
		if (!$mail_data_id)
		{
	    	$this->data['error'] = 'Erro! falta informar o ID da campanha';
		}
		else
		{
			$this->load->library('session');
			$user = $this->session->userdata('user');
			$now = date('Y-m-d H:m:s');
			$added = 0;
			$list_len = count($list);
			for ($i = 0; $i < $list_len; $i++)
			{
				$repeated = $this->mail_list->select($mail_data_id, $list[$i]['email']);
				//die (var_dump($repeated));
				if (!filter_var($list[$i]['email'], FILTER_VALIDATE_EMAIL) || !empty($repeated))
				{
					unset($list[$i]);
				}
				else
				{
					$added++;
					$list[$i]['email'] = explode('@', $list[$i]['email']);
					$list[$i]['prefix'] = $list[$i]['email'][0];
					$list[$i]['domain'] = $list[$i]['email'][1];
					unset($list[$i]['email']);
					$list[$i]['status'] = 'AGENDADO';
					$list[$i]['dttm_changed'] = $now;
					$list[$i]['changed_by'] = $user->name;
					$list[$i]['mail_data_id'] = $mail_data_id;
				}
			}
			//die (var_dump($list));
	        if (empty($list) || !is_array($list))
	            $this->data['error'] = 'Erro! lista vazia, inválida ou já foi agendada.';
			elseif ($this->mail_list->save($list))
	            $this->data['success'] = "Lista com $added email(s) agendado(s) para a campanha {$mail_data_id}.";
			else
	    	    $this->data['error'] = 'Erro.';
		}
	}

	public function send()
	{
		/**
		* Dispara o envio de emails
		*/
		$this->load->model('vw_mail_schedule', '', TRUE);
		$this->load->model('mail_conf', '', TRUE);
		$this->load->model('mail_list', '', TRUE);
		$this->load->library('email');
        $this->load->helper('url');
		$conf = $this->mail_conf->get_conf();
		//die (var_dump($conf));
		$tasks = $this->vw_mail_schedule->select();
		//die(var_dump($tasks));
		$this->data['success'] = '';
		$this->data['error'] = '';
		$nadaAFazer = TRUE;
		foreach ($tasks as $task)
		{
			$b64_id = str_replace('=', '', base64_encode($task->mail_data_id));
			$b64_addr = str_replace('=', '', base64_encode($task->email));
			$task->html = str_replace('?nome?', $task->name, $task->html);
			$footer = '
				<footer>
					<a href="'.site_url('schedule/reject/'.$b64_id.'/'.$b64_addr).'">
						<img src="'.site_url('schedule/feedback/'.$b64_id.'/'.$b64_addr).'">
						<strong>Se não deseja mais receber nossos convites, clique aqui.</strong>
					</a>
				</footer>';
			$this->email->protocol = 'smtp';
			//$this->email->cc('another@another-example.com'); 
			//$this->email->bcc('them@their-example.com'); 
			$this->email->from($conf->from, $conf->from_name);
			$this->email->reply_to($conf->reply_to, $conf->reply_to_name);
			$this->email->wordwrap = FALSE;
			$this->email->smtp_host = $conf->host;
			$this->email->smtp_port = $conf->port;
			$this->email->smtp_user = $conf->username;
			$this->email->smtp_pass = $conf->password;
			$this->email->mailtype = 'html';
			$this->email->validate = TRUE;
			$this->email->to($task->email);
			$this->email->subject($task->subject);
			$this->email->message($task->html.$footer); 
			$this->email->set_alt_message('Algo deu errado na leitura do email :(');
			//die(var_dump($this->mail_list->update_status($task->mail_data_id, $task->email, 'ENVIADO')));
			if (!empty($task->mail_data_id) && !empty($task->email))
			{
				$nadaAFazer = FALSE;
				if ($this->email->send() && $this->mail_list->update_status($task->mail_data_id, $task->email, 'ENVIADO'))
					$this->data['success'] .= "Email {$task->mail_data_id} enviado para {$task->email}<br>";
				else
				{
					$this->mail_list->update_status($task->mail_data_id, $task->email, 'FALHA');
					$this->data['error'] .= $this->email->print_debugger().'<br>';
				}
			}
		}
		if ($nadaAFazer)
			$this->data['warning'] = 'Todos os emails já foram enviados.';
		$this->load->model('mail_data', '', TRUE);
        $this->data['mails'] = $this->mail_data->select();
		$this->load->helper('url');
		$this->load->view('header', $this->data);
		$this->load->view('footer', $this->data);
	}

	public function feedback($mail_data_id = NULL, $mail_addr = NULL)
	{
		header("Content-type: image/png");
		$imagem = imagecreatefromgif('resources/img/migue.gif');
		// Mostrar a imagem.
		imagegif($imagem);
		// Liberar memória
		imagedestroy($imagem);
		// Atualiza status
		if (!empty($mail_data_id) && !empty($mail_addr))
		{
			$this->load->model('mail_list');
			$mail_data_id = base64_decode($mail_data_id);
			$mail_addr = base64_decode($mail_addr);
			$row = $this->mail_list->select($mail_data_id, $mail_addr);
			if ($row->status == 'ENVIADO')
				$this->mail_list->update_status($mail_data_id, $mail_addr, 'LIDO');
		}
	}

	public function reject($mail_data_id, $mail_addr)
	{
		$mail_data_id = base64_decode($mail_data_id);
		$mail_addr = base64_decode($mail_addr);
		$this->load->model('mail_list');
		if ($this->mail_list->update_status($mail_data_id, $mail_addr, 'REJEITADO'))
			$this->data['success'] = utf8_decode('Obrigado. Você não voltará a receber nossos emails.');
		else
			$this->data['error'] = 'Erro! tente novamente mais tarde.';
		$this->load->helper('url');
		$this->load->view('header', $this->data);
		$this->load->view('footer', $this->data);
		$this->load->model('mail_data', '', TRUE);
        $this->data['mails'] = $this->mail_data->select();
		$this->load->helper('url');
		$this->load->view('header', $this->data);
		$this->load->view('footer', $this->data);
	}
}