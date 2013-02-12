<?php
	/* @var $this ViewA */
	if(Configure::check('Userbox.template.name'))
		$this->extend(Configure::read('Userbox.template.name'));

	if(Configure::check('Userbox.template.blocks.page.title'))
		$this->assign(Configure::read('Userbox.template.blocks.page.title'), 'Сброс пароля');
	
	if(Configure::check('Userbox.template.blocks.page.content'))
		$this->start(Configure::read('Userbox.template.blocks.page.content'));
?>
<div class='userbox password-reset'>
	<?php 
	if(!isset($success))
	{
		if(isset($error))
		{
			echo $this->Html->tag('p', 'Ошибка: '.$error, array('class'=>'error'));
		}
		else
		{
	?>
	<p>Здесь вы можете указать новый пароль вашей учетной записи.</p>
	<div class='user-fields' id='user-reset-password'>
	<?php
		echo $this->Form->create('User', array('url'=>array('controller'=>'users', 'action'=>'reset_password', 'plugin'=>'userbox')));
		echo $this->Form->hidden('activation_key', array('value'=>$activationCode));
		echo $this->Form->input('password',		array('label'=>'пароль', 'div'=>'input password', 'type'=>'password', 'error' => array('attributes' => array('wrap' => 'span'))));
		echo $this->Form->input('password_2',	array('label'=>'повтор пароля', 'type'=>'password', 'div'=>'input password_2', 'error' => array('attributes' => array('wrap' => 'span'))));
		echo $this->Form->button('<span>Установить новый пароль</span>', array('class'=>'button small gray', 'type'=>'submit'));
	?>
	</div>
	<?php
		}
	}
	else
	{
		echo $this->Html->tag('p', 'Пароль изменён.', array('class'=>'success'));
	}
	?>
</div>
<?php
	if(Configure::check('Userbox.template.blocks.page.content'))
		$this->end();
?>