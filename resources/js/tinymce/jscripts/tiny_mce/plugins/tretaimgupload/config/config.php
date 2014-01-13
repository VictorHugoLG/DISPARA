<?php
/*
Arquivo de configura��o, todas as configura��es est�o comentadas
*/

// vetor que guarda as configura��es
$config = array();
// idioma
$config['lang'] = 'pt_br';
// tamanho m�ximo de cada imagem em k, m, g, p, t
$config['tamanho'] = '1024k';
// Largura M�xima, em pixels
$config['larguraMax'] = 2048;
// Altura M�xima, em pixels
$config['alturaMax'] = 2048;
// redimensionar a imagem
$config['resize'] = false;
// largura que a imagem ser� redimensionada
$config['img_x'] = 150;
// altura que a imagem ser� redimensionada
$config['img_y'] = 150;
// converter imagem para outro tipo, coloque s� a extens�o. jpg, gif, png ou bmp
$config['convert_to'] = '';
// qualidade da imagem, caso a mesma seja jpeg
$config['jpeg_quality'] = 85; // ao diminuiar a qualidade tem se um arquivo menor, por�m com menas qualidade.

/**
* Para inserir texto na imagem use as configura��es abaixo
*/
// texto que ser� colocado sobre a imagem
$config['image_text'] = null;
// dire��o do texto que ser� colocado sobre a imagem 'h' para horizontal, 'v' pra vertical
$config['image_text_direction'] = null;
// cor do texto que ser� colocado sobre a imagem
$config['image_text_color'] = '#FFFFFF';
// transpar�ncia do texto que ser� colocado sobre a imagem 0 � 100
$config['image_text_percent'] = 100;
// posi��o do texto que ser� colocado sobre a imagem use as conbina��es 'TBLR': cima, baixo, esquerda, direita
$config['image_text_position'] = null;
// alinhamento do texto, use 'L', 'C' ou 'R': esquerda, centro ou direita
$config['image_text_alignment'] = 'C';

// quantidade m�xima de upload permitido
$config['uploadMax'] = 150;
// dir onde as imagens ser�o upadas
$config['dir'] = '/communic/resources/img/';


#=========================================================================================
#
# n�o mexa neste parte do c�digo � menos que saiba o que esta fazendo
# 
// === s� mexa daqui pra baixo se tiver certeza do que est� fazendo ====


define('TRETAIMGUPLOAD_DIR_ROOT', ((@$_SERVER['DOCUMENT_ROOT'] && file_exists(@$_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'])) ? $_SERVER['DOCUMENT_ROOT'] : str_replace(dirname(@$_SERVER['PHP_SELF']), '', str_replace('\\', '/', realpath('.')))));

/**
* let_to_num
* transforma nota��o do estilo php.ini ('2M') em (2*1024*1024 em cada caso)
* @param string a nota��o 2m
* @return int a nota��o em bites 2097152
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
	303 => 'O servidor informou que o arquivo %file% foi movido tempor�riamente',
	403 => 'Acesso restrito ao arquivo %file%',
	404 => 'O arquivo %file% n�o foi encontrado no servidor',
	500 => 'O servidor retornou um erro interno'
);

?>
