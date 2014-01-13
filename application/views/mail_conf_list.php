<a class="btn btn-primary btn-primary" href="<?php echo site_url('mail/conf_edit/'); ?>"><i class="icon-plus"></i> Nova configuração</a>

<div class="row" style="margin-left: 0.1%">
	<form method="post" action="<?php echo site_url('mail/conf_enable/'); ?>">
  <table class="table table-bordered table-condensed" style="margin-top: 20px;">
  	<thead>
      	<tr>
          <th>Ativa</th>
          <th>Descrição</th>
          <th>Servidor</th>
          <th>Porta</th>
          <th>Usar autenticação</th>
          <th>Email remetente</th>
          <th>Nome remetente</th>
          <th colspan="2">Ações</th>
      	</tr>
  	</thead>

  	<tbody>
    	<?php foreach($mail_conf_list as $config): ?>
  		<tr>
        <td><input <?php echo ($config->active) ? 'checked': '' ;?> type="radio" value="<?php echo $config->id; ?>" name="mail_conf_id" />
        <td><?php echo $config->description; ?></td>
        <td><?php echo $config->host ?></td>
        <td><?php echo $config->port; ?></td>
        <td><?php echo ($config->smtp_auth) ? 'sim' : 'não'; ?></td>
        <td><?php echo $config->from; ?></td>
        <td><?php echo $config->from_name; ?></td>
        <td><a title="Alterar" href="<?php echo site_url('mail/conf_edit/'.$config->id); ?>"> <i class="icon-edit"></i></a></td>	
        <td><a title="Excluir" href="<?php echo site_url('mail/conf_delete/'.$config->id); ?>"> <i class="icon-trash"></i></a></td>
  		</tr>
  		<?php endforeach; ?>
  	</tbody>
  </table>
  </form>
  <script type="text/javascript" >
  
  	$("input[name=mail_conf_id]").click(function(){
		  $("form").submit();
		});
  
  </script>
</div>