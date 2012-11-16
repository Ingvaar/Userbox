<?php
	/* @var $this ViewA */
	$captcha = Configure::read('Settings.captcha');
	echo $this->Html->css(array('users/add.css'), null, array('inline' => false));
?>
<div class='userbox password-reset'>
	<?php				
		if(!isset($success))
		{
	?>
			<p class='ques'>Забыли пароль?</p>
			<p>Если вы не помните пароль от своей учётной записи, вы можете сбросить пароль. 
				Для этого введите адрес электронной почты, который вы указывали при регистрации и нажмите кнопку <strong>&laquo;Сбросить пароль&raquo;</strong>.
				На указанный электронный адрес будет отправлено письмо с дальнейшими инструкциями по сбросу пароля.
			</p>
			<div class='user-fields' id='user-password-forgot'>
			<?php
			echo $this->Form->create('User', array('url'  => array('controller'=>'users', 'action'=>'forgot_password')));
			echo $this->Form->input('email', array('name' => 'data[ForgotPassword][email]', 'id'=>'UserEmail', 'label'=>'электронный адрес (ваш логин на сайте)', 
				'type'=>'email', 'div'=>'input email', 'error' => array('attributes' => array('wrap' => 'span'))));

			if($captcha)
			{
				echo $this->Html->script(array('http://www.google.com/recaptcha/api/js/recaptcha_ajax.js'), array('inline' => false));
				// если каптча включена
				echo $this->Html->tag('label', 'каптча',  array('for'  => 'recaptcha_div', 'class'=>'input'));

				if(isset($recaptcha) && !$recaptcha)
					$capStyle = 'display: inline-block !important';
				else
					$capStyle = 'display: none !important';
			?>
				<span class="error-message recaptcha" style="<?php echo $capStyle; ?>">Докажите, что вы не бот</span>
				<div id="recaptcha_div" class="recaptcha"></div>
			<?php
			}
			echo $this->Form->button('<span>Сбросить пароль</span>', array('class'=>'button small gray', 'type'=>'submit'));
			echo $this->Form->end();
			?>

			<script type="text/javascript">
				jQuery(document).ready(function()
				{
				<?php if($captcha) { ?>
					Recaptcha.create('<?php echo Configure::read('Recapthca.publicKey'); ?>', 'recaptcha_div', {
						theme: "red",
						callback: Recaptcha.focus_response_field
					});	
				<?php } ?>
					jQuery('.password-reset input#UserEmail').hintinput('введите email, указанный вами при регистрации');
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
