<?php
	/* @var $this ViewA */

	if(Configure::check('Userbox.template.name'))
		$this->extend(Configure::read('Userbox.template.name'));

	if(Configure::check('Userbox.template.blocks.page.title'))
		$this->assign(Configure::read('Userbox.template.blocks.page.title'), 'Сброс пароля');
	
	if(Configure::check('Userbox.template.blocks.page.content'))
		$this->start(Configure::read('Userbox.template.blocks.page.content'));

	$captcha = Configure::read('Userbox.settings.captcha');
?>
<div class='userbox password-reset' id="<?php $id = String::uuid(); echo $id; ?>">
	<?php				
		if(!isset($success))
		{
	
			echo $this->Html->tag('p',
			"Если вы не помните пароль от своей учётной записи, вы можете сбросить пароль. ".
			"Для этого введите адрес электронной почты, который вы указывали при регистрации, и нажмите кнопку &laquo;Сбросить пароль&raquo;. ".
			"На указанный электронный адрес будет отправлено письмо с дальнейшими инструкциями.");
	?>
			<div class='user-fields' id='user-password-forgot'>
			<?php
			echo $this->Form->create('User', array('url'  => array('controller'=>'users', 'action'=>'forgot_password', 'plugin'=>'userbox'), 'class'=>'reset-password'));
			echo $this->Form->input('email', array('label'=>'электронный адрес', 'placeholder'=>'ваш логин на сайте', 'type'=>'email', 'div'=>'input email', 'error' => array('attributes' => array('wrap' => 'span'))));
			//введите email, указанный вами при регистрации
			if($captcha)
			{
				echo $this->Html->script(array('http://www.google.com/recaptcha/api/js/recaptcha_ajax.js'), array('inline' => false));
				// если каптча включена
				$capStyle = (isset($recaptcha) && !$recaptcha)?'display: inline-block !important':'display: none !important';
				echo $this->Html->div('input recaptcha',
					$this->Html->tag('label', 'каптча',  array('for' => 'recaptcha_div', 'class'=>'input')).
					$this->Html->tag('span', "Докажите, что вы не бот", array('class'=>'error-message recaptcha', 'style'=>$capStyle)).
					$this->Html->div('recaptcha', '', array('id'=>'recaptcha_div'))
				);
			}
			echo $this->Form->button('<span>Сбросить пароль</span>', array('class'=>'button small gray', 'type'=>'submit'));
			echo $this->Form->end();
			?>

			<script type="text/javascript">
				$(document).ready(function()
				{
				<?php if($captcha) { ?>
					Recaptcha.create('<?php echo Configure::read('Recaptcha.publicKey'); ?>', 'recaptcha_div', {
						theme: "red",
						callback: function(){$("#recaptcha_response_field").attr('required', 'required');}
					});
				<?php } ?>
					$("#<?=$id;?> form.reset-password").bind('submit', function()
					{
						$("#<?=$id;?> form.reset-password button").attr('disabled', 'disabled');
					});
				});
			</script>
		</div>
	<?php
		}
		else
		{
			echo $this->Html->tag('p', sprintf('На указанный электронный адрес (%s) отправлено письмо с инструкцией по сбросу пароля.', $email));
		}
	?>
</div>
<?php
	if(Configure::check('Userbox.template.blocks.page.content'))
		$this->end();
?>