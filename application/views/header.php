<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
	<link type="text/plain" rel="author" href="<?php echo base_url('humans.txt'); ?>">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url('resources/img/favicon.ico'); ?>">
	<title><?php echo SYSTEM_NAME; ?></title>
	<link href="<?php echo base_url('resources/js/jquery-ui-1.9.2/css/smoothness/jquery-ui-1.9.2.custom.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('resources/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" media="screen">
	<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo base_url('resources/js/modernizr.custom.22844.js'); ?>"></script>
	<script src="<?php echo base_url('resources/js/jquery-ui-1.9.2/js/jquery-1.8.3.js'); ?>"></script>
	<script src="<?php echo base_url('resources/js/jquery-ui-1.9.2/js/jquery-ui-1.9.2.custom.min.js'); ?>"></script>
	<script src="<?php echo base_url('resources/js/jquery-ui-1.9.2/development-bundle/ui/i18n/jquery.ui.datepicker-pt-BR.js'); ?>"></script>
	<script src="<?php echo base_url('resources/bootstrap/js/bootstrap.min.js'); ?>"></script>
</head>

<body role="application">

    <div class="well">
	
        <?php
        if (empty($escondeMenu))
        {
        	echo '
	        	<div class="btn-group">
                    <a class="btn btn-inverse" data-toggle="dropdown" href="#"><i class="icon-white icon-envelope"></i> Campanhas <span class="caret"></span></a>
	            	<ul class="dropdown-menu pull-left">
	            		<li><a href="'.site_url('mail/edit').'"> <i class="icon-file"></i> Nova</a></li>';
			foreach ($mails as $row)
			{
				echo '
					<li class="dropdown-submenu">
						<a href="#">'."$row->id - $row->name".'</a>
						<ul class="dropdown-menu">
							<li><a href="'.site_url('mail/schedule/'.$row->id).'"><i class="icon-calendar"></i> Agendar envio</a></li>
							<li><a href="'.site_url('mail/copy/'.$row->id).'"><i class="icon-plus"></i> Clonar</a></li>
							<li><a href="'.site_url('mail/edit/'.$row->id).'"><i class="icon-edit"></i> Editar</a></li>
							<li><a href="'.site_url('mail/stats/'.$row->id).'"><i class="icon-bar-chart"></i> Relatório de envio</a></li>
						</ul>
					</li>';
			}
			echo '
					</ul>
                    <a href="'.site_url('mail/stats').'" class="btn btn-inverse"> <i class="icon-bar-chart icon-white"></i> Relatório geral </a>
                    <a href="'.site_url('mail/image').'" class="btn btn-inverse"> <i class="icon-picture icon-white"></i> Imagens no servidor </a>
					<a href="'.site_url('mail/conf_list').'" class="btn btn-inverse"><i class="icon-cog"></i> Configuração SMTP </a>
					<a href="'.site_url('guarita/logoff').'" class="btn btn-inverse"><i class="icon-off"></i> Sair </a>
            	</div><br><br>';
		} 
		?>
		<?php if (!empty($error)): ?>	
			<div class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<?php echo $error; ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($success)): ?>
			<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p class="text-success"><i class="icon-ok-sign"></i> <strong><?php echo $success; ?></strong></p>
			</div>
		<?php endif; ?>

		<?php if (!empty($warning)): ?>
			<div class="alert alert-warning">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p class="text-warning"><i class="icon-warning-sign"></i> <strong><?php echo $warning; ?></strong></p>
			</div>
		<?php endif; ?>