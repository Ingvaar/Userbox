<div class='users form-login'>
<?php
	/* @var $this View */
	echo $this->Html->css(array('Userbox.userbox'), null, array('inline'=>false));
	echo $this->Form->create('User', array('url' => array('controller'=>'users', 'action'=>'login', 'plugin'=>'userbox'), 'class'=>'userbox login'));
	echo $this->Form->inputs(array(
		'legend' => __('Login'),
		'email',
		'password'
	));
	echo $this->Form->end('Login');
?>
</div>