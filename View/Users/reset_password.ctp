<?php
	/* @var $this ViewA */
	echo $this->Html->css(array('users/add.css'), null, array('inline' => false));
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
		echo $this->Form->create('User', array('url'=>array('controller'=>'users', 'action'=>'reset_password')));
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