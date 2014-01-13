<?php

if($_GET)
{
	if(!empty($_GET['d']))
	{
		$d = urldecode(strip_tags($_GET['d']));
		require('config/config.php');
		require('classes/funcoes.php');
		require('classes/TretaImgUpload/TretaImgUpload.class.php');
		$tretaimgupload = new tretaImgUpload($config);
		echo $tretaimgupload->acao($d);		
	}
}

?>