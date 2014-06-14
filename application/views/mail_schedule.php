<?php echo form_open_multipart('schedule/upload/');?>
	<input type="hidden" name="user" value="<?php echo $user->name; ?>">
	<input type="hidden" name="mail_id" value="<?php echo $mail_id; ?>">
	<input type="file" name="userfile">
	<br><br>
	<button type="submit" class="btn btn-primary">Enviar</button>
	<br><br>
	<p>
		Submeta um arquivo de texto ou <a href="http://pt.wikipedia.org/wiki/Comma-separated_values">CSV</a>, contendo a lista de destinatários no seguinte formato: "email ou telefone, nome (opcional)". Exemplo:
	</p>
	<b>
		<pre>lucas@meu_dominio.com, Lucas Rocha<br>fulano@outro_dominio.com<br>07166669999<br>07512345678, Beltrano</pre>
	</b>
	<p>
		Obs: O campo nome (opcional) poderá ser utilizado no corpo do email ou mensagem SMS através do código: "?nome?" (sem aspas).
	</p>
</form>