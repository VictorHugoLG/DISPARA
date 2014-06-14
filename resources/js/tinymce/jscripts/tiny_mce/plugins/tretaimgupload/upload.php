<?php
	require('config/config.php');
	if(!isset($_SESSION))
		session_start();
	$imgs = isset($_SESSION['imgs_upadas']) ? $_SESSION['imgs_upadas'] : array();
	$qtdImgsUps = count($imgs);
	$erros = array();
	if($qtdImgsUps >= $config['uploadMax'])
		exit('<script>parent.TretaImgUpload.msg("Você atingiu o limite máximo de uploads permitidos")</script>');
	else
	{
		$imagem = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
		if($imagem)
		{
			require_once('classes/upload/class.upload.php');
			$img = new Upload($imagem, 'pt_BR');
			$img->file_new_name_body = substr(md5(uniqid(time())), 0, 15);
			$img->image_max_width = $config['larguraMax'];
			$img->image_max_height = $config['alturaMax'];
			$img->image_resize = $config['resize'];
			$img->image_ratio_x = true;
			$img->image_ratio_y = true;
			$img->image_x = $config['img_x'];
			$img->image_y = $config['img_y'];
			$img->image_convert = $config['convert_to'];
			$img->jpeg_quality = $config['jpeg_quality'];
			$img->image_text = $config['image_text'];
			$img->image_text_direction = $config['image_text'];
			$img->image_text_color = $config['image_text_color'];
			$img->image_text_percent = $config['image_text_percent'];
			$img->image_text_position = $config['image_text_position'];
			$img->image_text_alignment = $config['image_text_alignment'];
			$img->allowed = array('image/*');
			$img->file_max_size = $config['_tamanho'];
			$img->process($config['diretorio']);
			if($img->processed)
			{
			   echo '<script>parent.TretaImgUpload.insere(\''.$img->file_dst_name.'\')</script>;';
			   $img->clean();
			   $imgs[] = $img->file_dst_name;
			   $_SESSION['imgs_upadas'] = $imgs;
			}
			else
				exit('<script>parent.TretaImgUpload.msg("'.$img->error.'")</script>');	
		}
	}
	


?>
