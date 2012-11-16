<div class='users form-add'>
<?php
	/* @var $this View */
	echo $this->Html->css(array('Userbox.userbox'));
	/* @var $this View */
	echo $this->Form->create('User', array('class'=>'userbox add', 'legeng'=>'Register'));
	
	$inputs = array(
		'legend' => 'Register',
		'nickname',
		'email' => array('type'=>'email', 'label'=>'Email (login)'),
		'password',
		'password_2' => array('label'=>'Repeat password', 'type'=>'password'),
		'group_id'
	);
        
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
	echo $this->Form->end('Add');
?>
</div>