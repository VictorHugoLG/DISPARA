	<form method="post" action="<?php echo current_url(); ?>">
      <h4>Configuração de SMTP</h4>
		
			<label class="control-label">Descrição</label>
            <textarea id="description" style="height: 100px; width: 300px" placeholder="Descrição" id="name" type="textarea" name="conf[description]" value="<?php echo set_value('conf[description]'); ?>" required  ></textarea>
            <br/>
			<label class="control-label">Servidor</label> <input id="host" placeholder="Servidor"  type="text" name="conf[host]" value="<?php echo set_value('conf[host]'); ?>" required>
           <label class="control-label" > Porta </label><input style="width:50px;" placeholder="Porta" id="port"  type="text" name="conf[port]"  value="<?php echo set_value('conf[port]'); ?>" required>
			<br>
          	<label class="checkbox"> Usar autenticação SMTP <input  type="checkbox" name="conf[smtp_auth]" id="smtp_auth" value="1"></label> 
            <label class="control-label">Tipo de encriptação</label> 
            <input placeholder="ssl, tls e etc." id="smtp_secure" type="text" name="conf[smtp_secure]" value="<?php echo set_value('conf[smtp_secure]'); ?>">
             <label class="control-label">Usuário</label> 
            <input placeholder="Usuário" id="username" type="text" name="conf[username]" value="<?php echo set_value('conf[username]'); ?>">
            <label class="control-label" >Senha</label>
            <input placeholder="Senha" id="password" type="password" name="conf[password]" value="<?php echo set_value('conf[password]'); ?>"></label><br />
            <label class="control-label" >Email remetente</label>
             <input placeholder="Email Remetente" id="from" type="email" name="conf[from]" value="<?php echo set_value('conf[from]'); ?>" required>
             <label class="control-label" >Nome remetente</label>
             <input placeholder="Nome Remetente" id="from_name" type="text" name="conf[from_name]" value="<?php echo set_value('conf[frotem_name]'); ?>" ><br />
              <label class="control-label" >Responder email</label>
             <input placeholder="Responder email " id="reply_to" type="email" name="conf[reply_to]" value="<?php echo set_value('conf[reply_to]'); ?>" >
              <label class="control-label" >Responder nome</label>
             <input placeholder="Responder nome" id="reply_to_name" type="text" name="conf[reply_to_name]" value="<?php echo set_value('conf[reply_to_name]'); ?>" ><br />

          <div class="btn-group">
              <button type="submit" class="btn btn-primary">
                  <i class="icon-save icon-white"></i> Salvar
              </button>
              <button type="reset" class="btn">
                  <i class="icon-undo icon-white"></i> Resetar
              </button>
          </div>
  </form>
  
  <script type="text/javascript">
          $(function()
          {
                  if(!Modernizr.input.placeholder)
                  {
                          $('[placeholder]').focus(function() {
                            var input = $(this);
                            if (input.val() == input.attr('placeholder')) {
                                  input.val('');
                                  input.removeClass('placeholder');
                            }
                          }).blur(function() {
                            var input = $(this);
                            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                                  input.addClass('placeholder');
                                  input.val(input.attr('placeholder'));
                            }
                          }).blur();
                          $('[placeholder]').parents('form').submit(function() {
                            $(this).find('[placeholder]').each(function() {
                                  var input = $(this);
                                  if (input.val() == input.attr('placeholder')) {
                                    input.val('');
                                  }
                            })
                          });
                  }

                  //if (!Modernizr.inputtypes.date)
                  //$('input[type=date]').datepicker({ changeYear: true, dateFormat: 'yy-mm-dd'});
                  $('input.date').datepicker({ changeYear: true, dateFormat: 'yy-mm-dd'});

                  if (document.location.protocol == 'file:')
                          alert("Might not work properly on the local file system due to security settings in your browser. Please use a real webserver.");
                <?php

                  if (!empty($mail_conf))
                  {
                     
                    echo'
                      $("#description").val("'.$mail_conf->description.'");
                      $("#host").val("'.$mail_conf->host.'");
                      $("#port").val("'.$mail_conf->port.'");
                      $("#username").val("'.$mail_conf->username.'");
                      $("#password").val("'.$mail_conf->password.'");
                      $("#from").val("'.$mail_conf->from.'");
                      $("#from_name").val("'.$mail_conf->from_name.'");
                      $("#from").val("'.$mail_conf->from.'");
                      $("#reply_to").val("'.$mail_conf->reply_to.'");
                      $("#reply_to_name").val("'.$mail_conf->reply_to_name.'");';
                    echo ($mail_conf->smtp_auth) ? '$("#smtp_auth").click();' : '';
                   
                  }
                ?>
          });
 </script>