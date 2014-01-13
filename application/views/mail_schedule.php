<?php echo form_open_multipart('schedule/upload/');?>
	<input type="hidden" name="user" value="<?php echo $user->name; ?>">
	<input type="hidden" name="mail_id" value="<?php echo $mail_id; ?>">
	<input type="file" name="userfile">
	<br><br>
	<button type="submit" class="btn btn-primary">Enviar</button>
</form>