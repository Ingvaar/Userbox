<?php
	if(!isset($error))
	{
		echo $this->Html->tag('p', 'Регистрация вашего аккаунта подтверждена.');
	}
	else
	{
		echo $this->element('Userbox.error', compact('error'));
	}
?>