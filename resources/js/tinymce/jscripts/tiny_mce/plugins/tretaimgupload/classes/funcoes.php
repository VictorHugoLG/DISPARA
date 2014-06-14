<?php
	/*
	@val funчуo para validaчѕes
	@param o que vou validar(ip|email|url)
	@param string a ser validada
	@param boolean se vai usar outro filtro para validar
	@return valor booleano => true:sucesso|false:falha
	*/
	function val($q = '', $check = '')
	{		
		$filtros = array(
			'url' => FILTER_VALIDATE_URL,
			'email' => FILTER_VALIDATE_EMAIL,
			'ip' => FILTER_VALIDATE_IP
		);
		
		if(empty($q) || empty($check) || !array_key_exists($q,$filtros))
			return false;

		if(filter_var($check, $filtros[$q]) === false)
			return false;
		else
			return true;
	}
	
	/**
	* Retorna os headers da requisiчуo de uma URL
	*
	* @param string a $url
	* @param boolean $seguir_redir se щ pra seguir location se existirem
	* @param integer $max_redir mсximo de location a seguir
	* @param integer $timeout tempo mсximo da tentativa de requisiчуo
	* @return array|false em caso de sucesso?falha
	* retorno
		[url] => http://www.bb.com.br/docs/img/v5/btLogo1.gif
		[content_type] => image/gif
		[http_code] => 200
		[header_size] => 149
		[request_size] => 76
		[filetime] => -1
		[ssl_verify_result] => 0
		[redirect_count] => 0
		[total_time] => 6.846
		[namelookup_time] => 0.282
		[connect_time] => 4.578
		[pretransfer_time] => 4.642
		[size_upload] => 0
		[size_download] => 0
		[speed_download] => 0
		[speed_upload] => 0
		[download_content_length] => 2791
		[upload_content_length] => 0
		[starttransfer_time] => 6.846
		[redirect_time] => 0
	*/
	function getheader($url = '', $seguir_redir = false, $max_redir = 2, $timeout = 30)
	{
		if(!val('url', $url))
			return false;

		$header = false;
		$ch = curl_init($url);				
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $seguir_redir);
		curl_setopt($ch, CURLOPT_MAXREDIRS, $max_redir);
		curl_setopt($ch, CURLOPT_AUTOREFERER, $seguir_redir);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_exec($ch);
		$header = curl_getinfo($ch);	
		curl_close($ch);
		
		return $header;
	}
	
	/**
	* bytesToKbOrMbOrGb converte bytes em kb, mb, gb, tb, pb, eb, zb, yb
	* @param int bytes
	* @return string bytes convertido
	*/
	function bytesToKbOrMbOrGb($bytes)
	{
		if (is_numeric($bytes))
		{
			$s = array ('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
			$e = floor (log($bytes) / log(1024));
			
			return sprintf ( '%.2f ' . $s [$e], @($bytes / pow ( 1024, floor ( $e ) )) );
		}
		else
			$size = "Unknown";
			
		return $size;
	}
	
	/*
	# Esta funчуo щ recursiva para a criaчуo de nomes de arquivos aleatѓriamente
    ela gera um nome e verifica se existe e caso exista ela fica criando atщ
    que o nome nуo exista aэ ela retorna ele
    # Parтmetros: extensуo do arquivo, diretѓrio do arquivo
    # Retorno: nome do arquivo
	*/
	function gerar_nome_valido($extensao, $dir, $len = 15)
	{
		// Gera um nome њnico para o arquivo
		$temp = substr(md5(uniqid(time())), 0, $len);
		$arquivo_nome = (!empty($extensao)?"$temp.$extensao":$temp);

		// Verifica se o arquivo jс existe, caso positivo, chama essa funчуo novamente
		if(file_exists($dir . $arquivo_nome))
		{
			$arquivo_nome = gerar_nome_valido($extensao, $dir);
		}

		return $arquivo_nome;
	}
	
	// PHP4 compatibility
	if (!function_exists('file_put_contents') && ! defined ('FILE_APPEND'))
	{
		define ( "FILE_APPEND", 1 );
		function file_put_contents($n, $d, $flag = false)
		{
			$mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
			$f = @fopen ( $n, $mode );
			if ($f === false)
				return 0;
			else
			{
				if(is_array($d))
					$d = implode ($d);				
				$bytes_written = fwrite ( $f, $d );
				fclose($f);
				return $bytes_written;
			}
		}
	}
	
	if (!function_exists('file_get_contents'))
	{
		function file_get_contents($filename, $incpath = false)
		{
			if (false === $fh = fopen ( $filename, 'rb', $incpath ))
			{
				trigger_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
				return false;
			}
			clearstatcache ();
			if ($fsize = @filesize ( $filename ))			
				$data = fread ( $fh, $fsize );			
			else
			{
				$data = '';
				while(!feof($fh))
					$data .= fread($fh, 8192);
			}
			fclose ( $fh );
			return $data;
		}
	}
?>