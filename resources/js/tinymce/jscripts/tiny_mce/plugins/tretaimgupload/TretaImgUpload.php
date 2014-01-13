<?php
require('config/config.php');
	if(!isset($_SESSION))
		session_start();
//unset($_SESSION['imgs_upadas']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>Treta Imagem Upload</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src="../../utils/mctabs.js"></script>
    <script type="text/javascript" src="js/TretaImgUpload.js"></script>
	<script>
	var dir = '<?php echo $config['dir']; ?>';
	var uploadMax = <?php echo $config['uploadMax']; ?>;	
	<?php
	if(isset($_SESSION['imgs_upadas']))
		echo 'imgUpadas = eval('.json_encode($_SESSION['imgs_upadas']).');';
	
	if(!empty($_GET['t']) && $_GET['t'] == 1)
		echo 'var t = 1;';
	else
		echo 'var t = 2;';

	?>
	</script>

    <link rel='stylesheet' href='estilos/estilos.css' type='text/css'>
</head>
<body >
<div class="tabs">
	<ul>
		<li id="local_tab" class="current"><span><a href="javascript:;" onclick="TretaImgUpload.switchMode('local');" onmousedown="return false;">Upload Local</a></span></li>
		<li id="remoto_tab"><span><a href="javascript:;" onclick="TretaImgUpload.switchMode('remoto');" onmousedown="return false;">Upload Remoto</a></span></li>		
	</ul>
</div>
<div id="status" style="display:none"><img src="img/progress.gif" width="32" height="32" border="0"></div>

<div class="panel_wrapper">
	<div id="pai">
		<div id="local_panel" class="panel current">		
			<form method="post" enctype="multipart/form-data" target="iframe" action="upload.php">

			<fieldset>
				<legend>Informações - <a href="javascript:;" onclick="window.location.reload();">Reload</a></legend>
				<li><b>Limite máximo de imagens:</b> <?php echo $config['uploadMax']; ?></li>				
				<li><b>Largura máxima das imagens:</b> <?php echo $config['larguraMax']; ?>px</li>				
				<li><b>Altura máxima da imagem:</b> <?php echo $config['alturaMax']; ?>px</li>				
				<li><b>Tamanho máximo da imagem:</b> <?php echo $config['tamanho']; ?></li>				
				<li><b>Upload's Restantes</b> <span id="get_ups"><?php echo isset($_SESSION['imgs_upadas']) ? $config['uploadMax'] - count($_SESSION['imgs_upadas']) : $config['uploadMax']; ?></span></li>				
			</fieldset>
			<label>Imagem: </label><input name="arquivo" type="file" id="a0">
			<img src="img/okclick.gif" border="0" id="imgUp" onclick="TretaImgUpload.upload(document.forms[0]);" style="position: relative; top: 4px">
			<br>

			<?php

				if(isset($_SESSION['imgs_upadas']))
				{
					$imgs = $_SESSION['imgs_upadas'];
					foreach($imgs as $img)
					{
						echo '<span id="'.substr($img, 0, -4).'">imagem: <a href="javascript:;" onclick="TretaImgUpload.insereEditor(\''.$img.'\');">'.$img.'</a><a href="javascript:;" onclick="TretaImgUpload.get(unescape(\'[%22del%22,%22'.$img.'%22]\'))"><img src="img/del.png" border="0"></a><br></span>';
					}
				}
			?>

			</form>
		</div>	
	
		<div id="remoto_panel" class="panel">
			<fieldset>
				<legend>Informações - <a href="javascript:;" onclick="window.location.reload();">Reload</a></legend>
				<li><b>Limite máximo de imagens:</b> <?php echo $config['uploadMax']; ?></li>				
				<li><b>Largura máxima das imagens:</b> <?php echo $config['larguraMax']; ?>px</li>				
				<li><b>Altura máxima da imagem:</b> <?php echo $config['alturaMax']; ?>px</li>				
				<li><b>Tamanho máximo da imagem:</b> <?php echo $config['tamanho']; ?></li>				
				<li><b>Upload's Restantes</b> <span id="get_ups"><?php echo isset($_SESSION['imgs_upadas']) ? $config['uploadMax'] - count($_SESSION['imgs_upadas']) : $config['uploadMax']; ?></span></li>				
			</fieldset>			
			<span id="inf"></span>
			<form action="">
			<span id="detectadas"></span>
				<label>URL: </label><input type="text" name="url" size="30">
				<a href="javascript:;" title="Copiar"><img src="img/okclick.gif" border="0" id="imgUp" onclick="TretaImgUpload.upload_remote(document.forms[1]);" style="position: relative; top: 4px"></a>
				<a href="javascript:;" title="Informações"><img src="img/upimg.gif" border="0" id="imgUp" onclick="val = document.forms[1].elements.url.value; if(val == ''){ alert('Coloque uma url!'); return false;} if(!TretaImgUpload.isURL(val)){ alert('url inválida'); return false;} d = unescape('[%22get_inf%22, %22'+val+'%22]'); TretaImgUpload.get(d);" style="position: relative; top: 4px"></a>
				<br>
			</form>
		</div>
	</div>
</div>

<div class="mceActionPanel">
	<div style="float: left">
		<input type="button" id="insert" name="insert" value="{#insert}" onclick="TretaImgUpload.insereEditor();">
		<input type="button" class="button" id="detect" name="detect" value="Detectar" onclick="TretaImgUpload.detect_imagens();" style="display: none">
	</div>

	<div style="float: right">	
		<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
	</div>
</div>

<iframe name="iframe" style="display:none; width: 0px; height: 0px; border: none;"></iframe>
</body>
</html>