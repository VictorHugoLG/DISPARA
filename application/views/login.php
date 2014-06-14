<style type="text/css">
	div#login_form
	{
		margin: 0 auto;
		width: 33%;
	}
</style>



<div id="login_form">
	<form method="POST" action="<?php echo site_url(); ?>" align="center">
		<input name="user[name]" placeholder="Usuário" type="text" required>
		<br>
		<input name="user[password]" placeholder="Senha" type="password" required>
		<br>
		<button type="submit" class="btn btn-primary btn-large">Entrar</button>
	</form>
</div> <!-- /login_form -->