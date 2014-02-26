<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mail extends CI_Controller
{

	private $data;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->data['user'] = $this->session->userdata('user');
	}

	public function login($reincidente = FALSE)
	{
		$this->data['escondeMenu'] = TRUE;
		$this->data['warning'] = ($reincidente) ? 'Parece que você não é quem diz ser, ou houve um erro de digitação.' : '';
		$this->load->view('header', $this->data);
		$this->load->view('login', $this->data);
		$this->load->view('footer', $this->data);
	}

	public function index()
	{
		$this->stats();
	}
	
	public function copy($id = NULL)
	{
		$this->data['copy'] = TRUE;
		$this->edit($id);
	}
	
	public function edit($id = NULL)
	{
		/**
		* Carrega tela de edição de emails
		*/
		$this->load->library('form_validation');
		//die(set_value('mail[name]'));
		$this->load_mails();
        if (NULL != $id)
        {
            $this->data['mail_data'] = $this->mail_data->select($id);
            $this->data['warning'] = $this->burla_spam($this->data['mail_data']);
        }
        //die (var_dump($this->data['mail_data']));
        $this->load->view('header', $this->data);
        $this->load->view('mail_edit', $this->data);
        $this->load->view('footer', $this->data);
	}

	public function schedule($id = NULL)
	{
		/**
		* Carrega tela de agendamento de emails
		*/
		$this->load->library('form_validation');
		$this->load_mails();
        if (empty($id))
        {
        	$this->stats();
        }
        else
        {
	        $this->data['mail_id'] = $id;
	        $this->load->view('header', $this->data);
	        $this->load->view('mail_schedule', $this->data);
	        $this->load->view('footer', $this->data);
        }
	}
	
	public function save()
	{
		/**
		* Recebe dados via POST e faz insert ou update na tabela mail_data
		*/
		$this->load->library('form_validation');
		$rules = array(
			array('field' => 'mail[name]', 'label' => 'Nome da campanha', 'rules' => 'required|trim|xss_clean'),
			array('field' => 'mail[subject]', 'label' => 'Assunto do email', 'rules' => 'required|trim|xss_clean'),
			array('field' => 'mail[dt_begin]', 'label' => 'Data inicial', 'rules' => 'required|trim|xss_clean'),
			array('field' => 'mail[dt_end]', 'label' => 'Data final', 'rules' => 'required|trim|xss_clean'),
			array('field' => 'mail[html]', 'label' => 'Corpo do email', 'rules' => 'required|trim|xss_clean'),
		);
		$this->form_validation->set_rules($rules);
		if ($this->form_validation->run() == TRUE)
		{
			$data = $this->input->post('mail');
			//die(var_dump($data));
			$data['changed_by'] = USER;
			$data['dttm_changed'] = date('Y-m-d H:m:s');
			if (strpos($data['html'], '../../resources'))
				$data['html'] = str_replace('../../resources/img', base_url('resources/img'), $data['html']);
			elseif (strpos($data['html'], '../resources'))
				$data['html'] = str_replace('../resources/img', base_url('resources/img'), $data['html']);
			$this->load->model('mail_data', '', TRUE);
			$result = $this->mail_data->save($data);
			if ($result)
				$this->data['success'] = 'O email foi salvo';
			else
				$this->data['error'] = '<p class="text-error"><i class="icon-exclamation-sign"></i> <strong>Erro ao tentar salvar o email</strong></p>';
			$this->edit($result);
		}
		else
		{
			$this->data['error'] = validation_errors('<p class="text-error"><i class="icon-exclamation-sign"></i> <strong>', '</strong></p>');
			$this->edit();
		}
	}

	public function stats($mail_data_id = 0, $status = '')
	{
            /**
            * Exibe estatisticas sobre o envio de emails
            */
            $this->load->model('mail_list', '', TRUE);
            $this->data['mail_id'] = $mail_data_id;
            $status = strtoupper($status);
            if (strpos($status, 'S'))
            {
            	$status = str_replace('S', '', $status);
            }
            //die($status);
            $this->data['stats'] = $this->mail_list->stats($mail_data_id, $status);
            $this->load_mails();
            $page = ($status) ? 'mail_stats_detailed' : 'mail_stats';
            //die(var_dump($this->data['stats']));
            $this->load->view('header', $this->data);
            $this->load->view($page, $this->data);
            $this->load->view('footer', $this->data);
	}
        
    public function image()
	{
            /**
            * Exibe imagens no servidor
            */
            $this->load->helper('file');
            $imgs = get_filenames('resources/img/');
            foreach ($imgs as $name)
                $this->data['images'][$name] = base_url("resources/img/$name");
            $this->load_mails();
            $this->load->view('header', $this->data);
            $this->load->view('mail_image', $this->data);
            $this->load->view('footer', $this->data);
	}
        
	public function conf_list()
	{
		$this->load->model("mail_conf");
		$this->data['mail_conf_list'] = $this->mail_conf->read();
		$this->load_mails();
		$this->load->view('header', $this->data);
        $this->load->view('mail_conf_list', $this->data);
        $this->load->view('footer', $this->data);
	}

	public function conf_delete($mail_conf_id)
	{

        $mail_conf_id = intval($mail_conf_id);
		if (!empty($mail_conf_id) && is_integer($mail_conf_id))
		{
			$this->load->model('mail_conf');
			if ($this->mail_conf->delete($mail_conf_id))
				$this->data['success'] = 'Configuração apagada';
			else
    			$this->data['error'] = '<p class="text-error"><i class="icon-exclamation-sign"></i> <strong>Erro ao tentar apagar configuração</strong></p>';
		}
		$this->conf_list();
	}

	public function conf_enable()
	{
        $mail_conf_id = $this->input->post('mail_conf_id');
        $mail_conf_id = intval($mail_conf_id);
		if (!empty($mail_conf_id) && is_integer($mail_conf_id))
		{
			$this->load->model('mail_conf');
			if ($this->mail_conf->enable($mail_conf_id))
				$this->data['success'] = 'Configuração ativada';
			else
    			$this->data['error'] = '<p class="text-error"><i class="icon-exclamation-sign"></i> <strong>Erro ao tentar ativar configuração</strong></p>';
    	}
		$this->conf_list();
	}

	public function conf_edit($mail_conf_id = NULL)
	{
		$this->load->library('form_validation');
		if ($this->input->post('conf'))
		{
			$rules = array(
				array('field' => 'conf[description]', 'label' => 'Descrição', 'rules' => 'required|trim|xss_clean'),
				array('field' => 'conf[host]', 'label' => 'Servidor', 'rules' => 'required|trim|xss_clean'),
				array('field' => 'conf[port]', 'label' => 'Porta', 'rules' => 'required|trim|xss_clean|integer'),
				array('field' => 'conf[from]', 'label' => 'Email remetente', 'rules' => 'required|trim|xss_clean|valid_email'),
				array('field' => 'conf[password]', 'label' => 'Senha', 'rules' => 'trim|xss_clean'),
			);
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run())
	        {
	        	$this->load->model('mail_conf');
	        	$data = $this->input->post('conf');
	        	$data['username'] = (!empty($data['username'])) ? $data['username'] : '';
	        	$data['password'] = (!empty($data['password'])) ? $data['password'] : '';
	        	$data['from_name'] = (!empty($data['from_name'])) ? $data['from_name'] : '';
	        	$data['reply_to'] = (!empty($data['reply_to'])) ? $data['reply_to'] : '';
	        	$data['reply_to_name'] = (!empty($data['reply_to_name'])) ? $data['reply_to_name'] : '';
	        	$data['smtp_auth'] = (!empty($data['smtp_auth'])) ? $data['smtp_auth'] : 0;
	        	//die(var_dump($data));
	        	$data['active'] = (!empty($data['active'])) ? $data['active'] : 0; 
	        	$result = ($mail_conf_id == NULL) ? $this->mail_conf->create($data) : $result = $this->mail_conf->update($mail_conf_id, $data);
        		if ($result)
        			$this->data['success'] = 'Configurações salvas';
        		else
        			$this->data['error'] = '<p class="text-error"><i class="icon-exclamation-sign"></i> <strong>Erro ao tentar salvar configurações</strong></p>';
	        }
	        else
	        	$this->data['error'] = validation_errors('<p class="text-error"><i class="icon-exclamation-sign"></i> <strong>', '</strong></p>');
	    }
        $mail_conf_id = intval($mail_conf_id);
        if (!empty($mail_conf_id) && is_integer($mail_conf_id))
        {
        	$this->load->model('mail_conf');
			$this->data['mail_conf'] = array_shift($this->mail_conf->read($mail_conf_id));
        }
		$this->load_mails();
		$this->load->view('header', $this->data);
		$this->load->view('mail_conf_edit', $this->data);
		$this->load->view('footer', $this->data);
	}
        
    public function select_options()
	{
		/**
		* Retorna tags <option> para todos emails (util para AJAX)
		*/
		$this->load_mails();
		$options = '';
		foreach ($this->data['mails'] as $mail)
			$options .= "<option value=\"$mail->id\">$mail->name</option>";
		echo $options;
	}

	private function load_mails()
	{
		$this->load->model('mail_data', '', TRUE);
        $this->data['mails'] = $this->mail_data->select();
	}

    private function burla_spam($mail_data)
    {
	    //Palavras a serem evitadas no corpo e assunto
	    $badStrings = array (
	        '24 horas',
	        'agência de aproximação',
	        'agência de modelos',
	        'aproveite nossa promoção',
	        'cjb.net',
	        'clique aqui',
	        'consulte-nos!',
	        'curso',
	        'de sua empresa',
	        'detetive',
	        'despachamos para todo o',
	        'divulgue sua',
	        'divulgue seu',
	        'dúvidas conjugais',
	        'e confira',
	        'e saiba mais',
	        'especialmente para você',
	        'espionagem',
	        'formulário',
	        'grampo?',
	        'ganhe dinheiro',
	        'grátis',
	        'hospedagem',
	        'imperdível',
	        'inscreva-se',
	        'kit.net',
	        'marketing',
	        'mala-direta',
	        'mala direta',
	        'mercadolivre',
	        'não perca tempo',
	        'para sua empresa',
	        'perca peso',
	        'perder peso', 
	        'para retirar seu email da lista',
	        'renda extra',
	        'script',
	        'sigilo absoluto',
	        'spam',
	        'telemarketing',
	        'tempo limitado',
	        'tenha seu site na internet',
	        'todos os direitos reservados',
	        'trabalhar em casa',
	        'trabalhe em casa',
	        'trabalhando em casa',
	        '$');
	    //Palavras a serem evitadas somente no assunto
	    $badSubjects = array(
	        'kg',
	        'promoção',
	        'vagas abertas',
	        'você',
	        '?',);
	    
	    $correcao = 
	        '<b>ATENÇÃO! Os seguintes trechos contidos no corpo e/ou assunto do seu email podem classifica-lo como spam! <br/> '
	        .'Recomendamos fortemente que você troque os textos por sinônimos ou arquivos de imagem:</b> <br/> ';
	    $zeraCorrecao = 1;
	    $corpo = $this->retira_acentos(strtolower($mail_data->html),0);
	    $assunto = $this->retira_acentos(strtolower($mail_data->subject),0);
	    foreach ($badStrings as $bw) {
	        if (strpos($corpo, $this->retira_acentos($bw))) {
	            $correcao .= "- <font color='red'><b>'$bw'</b></font> (NO CORPO)<br/> ";
	            $zeraCorrecao = 0;
	        }
	        if (strpos($assunto, $this->retira_acentos($bw))) {
	            $correcao .= "- <font color='red'><b>'$bw'</b></font> (NO ASSUNTO)<br/> ";
	            $zeraCorrecao = 0;
	        }
	    }
	    foreach ($badSubjects as $bw) {
	        if (strpos($assunto, $this->retira_acentos($bw))) {
	            $correcao .= "- <font color='red'><b>'$bw'</b></font> (NO ASSUNTO)<br/> ";
	            $zeraCorrecao = 0;
	        }
	    }
	    $correcao = ($zeraCorrecao) ? '' : $correcao;
	    return $correcao;
    }

	private function retira_acentos($str, $htmlentities = true,$enc = "UTF-8") {
        $acentos = array(
            'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
            'c' => '/&ccedil;/',
            'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
            'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
            'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
            'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/');
        $str = ($htmlentities) ? htmlentities($str,ENT_NOQUOTES, $enc) : $str;
        return preg_replace($acentos, array_keys($acentos), $str);
    }
}