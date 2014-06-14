<?php
header ('Content-type: text/html; charset=utf-8');
class tretaImgUpload
{
	private $__imgs_upadas = array();
	private $__config = array();
	private $lang = 'pt_br';
	private $msgs;
	
	/**
	* construtor da classe
	* @param void
	* @return void
	*/
	function __construct($config)
	{
		if(empty($config) || is_null($config))
			throw new TretaIMGUpload('as configurações não foram passadas ao construtor da classe');
		$this->__config = $config;
		if(!isset($_SESSION))
			session_start();
		
		$this->msgs['limite_uploads'] = 'Você atingiu o limite máximo de uploads permitidos.';
		$this->msgs['arquivo_muito_grande'] = 'Arquivo muito grande.';
		$this->msgs['no_mime'] = 'MIME type não detectado.';
		$this->msgs['arquivo_incorreto'] = 'Tipo de arquivo incorreto.';
		$this->msgs['url_invalido'] = 'URL Inválido.';
		$this->msgs['tamanho'] = 'Tamanho';
		$this->msgs['tipo'] = 'Tipo';
		$this->msgs['no_image'] = 'A imagem %file% não existe';
		$this->msgs['no_size'] = 'Tamanho desconhecido';
		$this->msgs['servidor_nao_respondeu'] = 'O servidor do arquivo não respondeu';
		$this->msgs['servidor_nao_tamanho'] = 'O servidor %file% não soube informar o tamanho do arquivo!';
		$this->msgs['nao_copio_image'] = 'não foi possível copiar a imagem';
		$this->msgs['erro_desconhecido'] = 'Erro Desconhecido';
		
		
		if($this->lang != 'pt_br' && file_exists(dirname(__FILE__).'/lang/TretaImgUpload.class.'. $this->__config['lang'] .'.php'))
		{
			$msgs = null;
			require(dirname(__FILE__).'/lang/TretaImgUpload.class.'. $lang .'.php');
			if(is_array($msgs))
				$msgs = array_merge($this->msgs, $msgs);
		}
	}
	
	private function is_image($mime)
	{
		if(empty($mime))
			return false;
		//if(!preg_match('@^image/(gif|jpg|jpeg|pjpeg|png|x-png|bmp|x-ms-bmp|x-windows-bmp)$@i', $mime, $arr))		
		if($mime == 'image/gif')
			return 'gif';
		elseif($mime == 'image/jpg' || $mime == 'image/jpeg' || $mime == 'image/pjpeg')
			return 'jpg';
		elseif($mime == 'image/png' || $mime == 'image/x-png')
			return 'png';
		elseif($mime == 'image/bmp' || $mime == 'image/x-ms-bmp' || $mime == 'image/x-windows-bmp')
			return 'bmp';
		
		return false;
	}
	
	/**
	* set armazena as imagens upadas na sessão
	* @param nome da img
	* @return boolean
	*/	
	private function set($nome_img = '')
	{
		if(empty($nome_img))
			return false;
	
		$imgs = isset($_SESSION['imgs_upadas']) ? $_SESSION['imgs_upadas'] : array();
		$imgs[] = $nome_img;
		$_SESSION['imgs_upadas'] = $imgs;
		return true;
	}
	
	/**
	* get retorna todas as imagens upadas
	* @param void
	* @return string array json com todas as imagens upadas
	*/
	private function get()
	{
		$imgs = isset($_SESSION['imgs_upadas']) ? $_SESSION['imgs_upadas'] : array();
		return json_encode($imgs);
	}
	
	/**
	* qtd_imgs_upadas retorna a quantidade de imagens upadas
	* @param void
	* @return int a quantidade de imagens upadas
	*/
	private function qtd_imgs_upadas()
	{
		return (isset($_SESSION['imgs_upadas']) ? count($_SESSION['imgs_upadas']) : 0);
	}
	
	/**
	* del deleta uma imagem e a remove da sessão
	* @param string nome da imagem
	* @return boolean
	*/
	private function del($nome_img)
	{
		if(empty($nome_img))
			return false;
		
		$imgs = isset($_SESSION['imgs_upadas']) ? $_SESSION['imgs_upadas'] : false;
		if($imgs)
		{
			if($img = array_search($nome_img, $imgs) === false)
				return str_replace('%file%', '{'.$nome_img.'}', $this->msgs['no_image']);
				//return 'A imagem {'.$nome_img.'} nao existe!';
			
			if(!file_exists($this->__config['diretorio'].$nome_img))
			{
				unset($imgs[$img]);
				sort($imgs);
				$_SESSION['imgs_upadas'] = $imgs;
				return true;
			}
			
			if(unlink($this->__config['diretorio'].$nome_img))
			{
				unset($imgs[$img]);
				sort($imgs);
				$_SESSION['imgs_upadas'] = $imgs;
				return true;
			}
		}
		return false;
	}
	
	private function get_inf($url = '')
	{
		if(!val('url', $url))
			return '["get_inf", "erro", "'.$this->msgs['url_invalido'].'"]';
		
		$inf = getheader($url);
		if(is_array($inf))
		{
			if($inf['http_code'] == 200)
			{
				$rpt = '<li><b>'.$this->msgs['tipo'].':</b> '.$inf['content_type'].'</li>';				
				if($inf['download_content_length'])				
					$rpt .= '<li><b>'.$this->msgs['tamanho'].':</b> '.bytesToKbOrMbOrGb($inf['download_content_length']).'</li>';
				
				$rpt .= '<br>';
				return '["get_inf", "'.$rpt.'"]';
			}
			//elseif(array_key_exists($inf['http_code'], $this->__config['http_code']))
			elseif(isset($this->__config['http_code'][$inf['http_code']]))
			{
				$erro = str_replace('%file%', '<b>'.$url.'</b>', $this->__config['http_code'][$inf['http_code']]);			
				return '["get_inf", "erro", "'.$erro.'"]';
			}
			else
				return '["get_inf", "erro", "'.$this->msgs['erro_desconhecido'].'"]';
		}
		
		return '["get_inf", "erro", "'.$this->msgs['erro_desconhecido'].'"]';
	}
	
	/**
	* upload_remoto efetua o download/copia de uma imagem de outro servidor pra esse
	* @param string a url da imagem
	* @return string nome/endereço da imagem ou mensagem em caso de erro
	*/
	private function upload_remoto($url = '', $tipo = 'upload_remote')
	{
		$imgs = isset($_SESSION['imgs_upadas']) ? $_SESSION['imgs_upadas'] : array();
		if(count($imgs) >= $this->__config['uploadMax'])
			return '["'.$tipo.'", "erro", "'.$this->msgs['limite_uploads'].'"]';
		
		$url = urldecode($url);
		if(!val('url', $url))
			return '["'.$tipo.'", "erro", "'.$this->msgs['url_invalido'].'"]';

		$header = getheader($url);
		if(!is_array($header))
			return '["'.$tipo.'", "erro", "'.$this->msgs['servidor_nao_respondeu'].'"]';

		if($header['http_code'] != 200 && isset($this->__config['http_code'][$header['http_code']]))
		{
			$erro = str_replace('%file%', $url, $this->__config['http_code'][$header['http_code']]);
			return '["'.$tipo.'", "erro", "'.$erro.'"]';
		}
		
		if(!isset($header['download_content_length']) || $header['download_content_length'] == 0)
			return '["'.$tipo.'", "erro", "'.str_replace('%file%', $url, $this->msgs['servidor_nao_tamanho']).'"]';
			
		if($header['download_content_length'] > $this->__config['_tamanho'])
			return '["'.$tipo.'", "erro", "'.$this->msgs['arquivo_muito_grande'].'"]';

		$mime = $header['content_type'];
		$is_image = $this->is_image($mime);
		if(!$is_image)
			return '["'.$tipo.'", "erro", "'.$this->msgs['arquivo_incorreto'].'"]';
		
		$img = file_get_contents($url);
		if($img)
		{
			$nome = gerar_nome_valido($is_image, $this->__config['diretorio']);
			if(file_put_contents($this->__config['diretorio'].$nome, $img))
			{
				require_once('classes/upload/class.upload.php');
				$new_img = new Upload($this->__config['diretorio'].$nome, 'pt_BR');				
				$new_img->image_max_width = $this->__config['larguraMax'];
				$new_img->image_max_height = $this->__config['alturaMax'];
				$new_img->image_resize = $this->__config['resize'];
				$new_img->image_ratio_x = true;
				$new_img->image_ratio_y = true;
				$new_img->image_x = $this->__config['img_x'];
				$new_img->image_y = $this->__config['img_y'];
				$new_img->image_convert = $this->__config['convert_to'];
				$new_img->jpeg_quality = $this->__config['jpeg_quality'];
				$new_img->image_text = $this->__config['image_text'];
				$new_img->image_text_direction = $this->__config['image_text_direction'];
				$new_img->image_text_color = $this->__config['image_text_color'];
				$new_img->image_text_percent = $this->__config['image_text_percent'];
				$new_img->image_text_position = $this->__config['image_text_position'];
				$new_img->image_text_alignment = $this->__config['image_text_alignment'];
				$new_img->process($this->__config['diretorio']);
				if($new_img->processed)
				{
					$new_img->clean();
					clearstatcache();
					$this->set($new_img->file_dst_name);
					return '["'.$tipo.'", "'.$new_img->file_dst_name.'"]';
				}
				else
				{
					$new_img->clean();					
					@unlink($this->__config['diretorio'].$new_img->file_dst_name);
					clearstatcache();
					return '["'.$tipo.'", "erro", "'.$new_img->error.'"]';
				}
			}
			else
				return '["'.$tipo.'", "erro", "'.$this->msgs['nao_copio_image'].'"]';
		}
		else
		{
			return '["'.$tipo.'", "erro", "'.$this->msgs['nao_copio_image'].'"]';
		}
		
		return '["'.$tipo.'", "erro", "'.$this->msgs['erro_desconhecido'].'"]';
	}
	
	/**
	* método ação, único método publico da classe, ao qual fornece acesso a todos os métodos da classe
	* @param string array em json de ação a ser executado
	* @return string resposta a ação requisitada
	*/
	public function acao($acao = '')
	{
		if(empty($acao))
			return '[1,"erro"]';
		
		$return = '[1,"erro"]';
		
		$acao = urldecode($acao);
		$acao = json_decode($acao);
		if(!is_array($acao) && count($acao) != 2)
			return $return;
		
		$opcao = $acao[0];
		$a = $acao[1];
		if($opcao == 'get')
			$return = $this->get();
		elseif($opcao == 'get_qtd')			
			$return = '["get_qtd",' . $this->qtd_imgs_upadas() . ']';
		elseif($opcao == 'get_inf')			
			$return = $this->get_inf($a);
		elseif($opcao == 'upload_remoto')
			$return = $this->upload_remoto($a);
		elseif($opcao == 'upload_remoto_ins')
			$return = $this->upload_remoto($a, 'upload_remoto_ins');
		elseif($opcao == 'del')
		{
			$del = $this->del($a);
			if($del === true)
				$return = '["del","'.$a.'"]';
			else
				$return = '["del","erro","'.$del.'"]';
		}
		
		return utf8_encode($return);
		
	}
}

?>

