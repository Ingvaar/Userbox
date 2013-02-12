<div class="form-login-ajax userbox" id="<?php $id = String::uuid(); echo $id; ?>">
<?php
	/* @var $this ViewA */
	$object = 'userAjaxLoginForm';
	echo $this->Form->create('User',        array('onsubmit'=> "$object.Login(); return false;", 'class'=>'login'));
	echo $this->Html->div('auth-data',
		$this->Form->input('User.email',    array('label' => false, 'type'=>'email', 'required'=>'required', 'placeholder'=>'логин (адрес почты)')).
		$this->Form->input('User.password', array('label' => false, 'required'=>'required', 'placeholder'=>'пароль'))
	);
	echo $this->Html->div('',
		$this->Menu->menu(array(
			'войти'         => array('%javascript%'=> "$object.Submit()"),
			'регистрация'   => array('controller'=>'users', 'action'=>'add', 'plugin'=>'userbox', 'admin'=>false, 'manager'=>false),
			'сброс пароля'	=> array('controller'=>'users', 'action'=>'forgot_password', 'plugin'=>'userbox', 'admin'=>false, 'manager'=>false)
		), null, array('theme'=>'custom'))
	);	
	
	echo $this->Form->end('Войти');

	$login  = $this->Js->request(array('controller' => 'users', 'action'=>'ajax_login', 'plugin'=>'userbox', 'manager' => false),
		array(
			'async'			 => true,
			'method'		 => 'POST',
			'before'		 => "$object.BeginRequest()",
			'complete'		 => "$object.EndRequest()",
			'data'			 => "$('#$id form.login').serialize()",
			'success'		 => "$object.Success(data);",
			'dataExpression' => true
	));
?>
	<script type="text/javascript">
		var <?php echo $object; ?> = {
			inrequest: false,
			form: "#<?=$id;?> form",
			submit: "#<?=$id;?> form input[type=submit]",
			controls: "#<?=$id;?> form input",
			BeginRequest: function()
			{
				if(this.inrequest)
					return;
				this.inrequest = true;
				jQuery(document).trigger('EVENT_USER_LOGIN_BEGIN');
				$(this.controls).attr('disabled', 'disabled');
			},
			EndRequest: function()
			{
				this.inrequest = false;
			},
			Redirect: function(url)
			{
				var tempForm = $('<form></form>').css('display', 'none').attr('action', url).attr('method', 'GET');
				$('body').append($(tempForm));
				tempForm.submit();
				$(tempForm).remove();
			},
			Init: function()
			{
				var _this = this;
			},
			Success: function(data)
			{
				if(data.result.success != undefined)
				{
					if(data.result.redirect != undefined)
						this.Redirect(data.result.redirect);
					else
						location.reload();
				}
				else
				{
					// something is wrong
					$(this.controls).removeAttr('disabled');
				}
			},
			Login: function()
			{
				if(this.inrequest == true)
					return;
				
				<?php echo $login; ?>
			},
			Submit: function()
			{
				$(this.submit).click()
			}
		}
		<?php echo $object; ?>.Init();
	</script>
</div>