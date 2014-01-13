<?php
/*
Arquivo de configuração, todas as configurações estão comentadas
*/

// vetor que guarda as configurações
$config = array();
// idioma
$config['lang'] = 'pt_br';
// tamanho máximo de cada imagem em k, m, g, p, t
$config['tamanho'] = '1024k';
// Largura Máxima, em pixels
$config['larguraMax'] = 2048;
// Altura Máxima, em pixels
$config['alturaMax'] = 2048;
// redimensionar a imagem
$config['resize'] = false;
// largura que a imagem será redimensionada
$config['img_x'] = 150;
// altura que a imagem será redimensionada
$config['img_y'] = 150;
// converter imagem para outro tipo, coloque só a extensão. jpg, gif, png ou bmp
$config['convert_to'] = '';
// qualidade da imagem, caso a mesma seja jpeg
$config['jpeg_quality'] = 85; // ao diminuiar a qualidade tem se um arquivo menor, porém com menas qualidade.

/**
* Para inserir texto na imagem use as configurações abaixo
*/
// texto que será colocado sobre a imagem
$config['image_text'] = null;
// direção do texto que será colocado sobre a imagem 'h' para horizontal, 'v' pra vertical
$config['image_text_direction'] = null;
// cor do texto que será colocado sobre a imagem
$config['image_text_color'] = '#FFFFFF';
// transparência do texto que será colocado sobre a imagem 0 à 100
$config['image_text_percent'] = 100;
// posição do texto que será colocado sobre a imagem use as conbinações 'TBLR': cima, baixo, esquerda, direita
$config['image_text_position'] = null;
// alinhamento do texto, use 'L', 'C' ou 'R': esquerda, centro ou direita
$config['image_text_alignment'] = 'C';

// quantidade máxima de upload permitido
$config['uploadMax'] = 150;
// dir onde as imagens serão upadas
$config['dir'] = '/communic/resources/img/';


#=========================================================================================
#
# não mexa neste parte do código à menos que saiba o que esta fazendo
# 
// === só mexa daqui pra baixo se tiver certeza do que está fazendo ====


define('TRETAIMGUPLOAD_DIR_ROOT', ((@$_SERVER['DOCUMENT_ROOT'] && file_exists(@$_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'])) ? $_SERVER['DOCUMENT_ROOT'] : str_replace(dirname(@$_SERVER['PHP_SELF']), '', str_replace('\\', '/', realpath('.')))));

/**
* let_to_num
* transforma notação do estilo php.ini ('2M') em (2*1024*1024 em cada caso)
* @param string a notação 2m
* @return int a notação em bites 2097152
*/
function let_to_num($v)
{
    $l = substr($v, -1);
    $ret = substr($v, 0, -1);
    switch(strtoupper($l))
	{
        case 'P': $ret *= 1024;
        case 'T': $ret *= 1024;
        case 'G': $ret *= 1024;
        case 'M': $ret *= 1024;
        case 'K': $ret *= 1024;
        break;
    }
    
	return $ret;
}

$ini_max_upload_size = min(let_to_num(ini_get('post_max_size')), let_to_num(ini_get('upload_max_filesize')));
$config['_tamanho'] = let_to_num($config['tamanho']) > $ini_max_upload_size ? $ini_max_upload_size : let_to_num($config['tamanho']);
$config['diretorio'] = TRETAIMGUPLOAD_DIR_ROOT . $config['dir'];

$config['http_code'] = array	
(
	302 => 'O servidor informou que o arquivo %file% foi movido permanentemente',
	303 => 'O servidor informou que o arquivo %file% foi movido temporáriamente',
	403 => 'Acesso restrito ao arquivo %file%',
	404 => 'O arquivo %file% não foi encontrado no servidor',
	500 => 'O servidor retornou um erro interno'
);

?>
