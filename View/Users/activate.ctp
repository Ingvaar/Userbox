<?php
	if(Configure::check('Userbox.template.name'))
		$this->extend(Configure::read('Userbox.template.name'));

	if(Configure::check('Userbox.template.blocks.page.title'))
		$this->assign(Configure::read('Userbox.template.blocks.page.title'), 'Подтверждение регистрации');
	
	if(Configure::check('Userbox.template.blocks.page.content'))
		$this->start(Configure::read('Userbox.template.blocks.page.content'));
	
	if(!isset($error))
	{
		echo $this->Html->tag('p', 'Регистрация вашего аккаунта подтверждена.');
	}
	else
	{
		echo $this->element('Userbox.error', compact('error'));
	}
	if(Configure::check('Userbox.template.blocks.page.content'))
		$this->end();
?>