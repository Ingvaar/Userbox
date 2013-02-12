<div class='userbox form-add'>
<?php
	/* @var $this View */
	echo $this->Html->css(array('Userbox.userbox'));
	/* @var $this View */
	echo $this->Form->create('User', array('class'=>'userbox add', 'legeng'=>'Register'));
	
	$inputs = array(
		'legend' => 'Регистрация',
		'nickname'   => array('label' => 'Имя на сайте'),
		'email'      => array('type'  => 'email', 'label'=>'Электронная почта (логин)'),
		'password'   => array('label' => 'Пароль'),
		'password_2' => array('label' => 'Повтор пароля', 'type'=>'password'),
		'group_id'
	);
    // скрытие полей    
	if (isset($hidden) && is_array($hidden))
	{
		foreach ($inputs as $key=>$val)
		{
			if (in_array($val, $hidden)) unset($inputs[$key]);
		}
	}
        
	// add something
	if(isset($extra) && is_array($extra))
	{
		$inputs = array_merge($inputs, $extra);
	}
	// remove something
	if(isset($unset) && is_array($unset))
	{
		foreach($unset as $u)
		{
			if(isset($inputs[$u]))
			{
				unset($inputs[$u]);
			}
			foreach($inputs as $i => $v)
			{
				if($v == $u)
					unset($inputs[$i]);
			}
		}
	}
	echo $this->Form->inputs($inputs);
	echo $this->Form->end('Зарегистрироваться');
?>
</div>