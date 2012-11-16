<?php
	$success = isset($success)?$success:false;
	if($success)
	{
		echo $this->Html->div('info', "Благодарим за регистрацию, на адрес $email отправлено письмо для подтверждения регистрации.");
	}
	else
	{
		echo $this->element('Userbox.users/form_add',array("hidden" => array("nickname","group_id")));		
	}
	if (isset($error))
		echo $error;
?>