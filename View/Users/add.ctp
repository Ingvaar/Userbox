<?php
	/* @var $this View */

	if(Configure::check('Userbox.template.name'))
		$this->extend(Configure::read('Userbox.template.name'));

	if(Configure::check('Userbox.template.blocks.page.title'))
		$this->assign(Configure::read('Userbox.template.blocks.page.title'), 'Регистрация пользователя');
	
	if(Configure::check('Userbox.template.blocks.page.content'))
		$this->start(Configure::read('Userbox.template.blocks.page.content'));
	
	$success = isset($success)?$success:false;
	if($success)
	{
		echo $this->Html->div('info', "Благодарим за регистрацию, на адрес $email отправлено письмо для подтверждения регистрации.");
	}
	else
	{
		echo $this->element('Userbox.users/form_add',array("hidden" => array("group_id")));
	}
	if (isset($error))
		echo $error;
	
	if(Configure::check('Userbox.template.blocks.page.content'))
		$this->end();
	
?>