<?php
	CakePlugin::load('Acl', array('bootstrap' => true));
	Configure::write('acl.aro.role.model', 'Group');
	Configure::write('acl.aro.user.model', 'User');
	Configure::write('acl.user.display_name', 'User.nickname');
	Configure::write('acl.check_act_as_requester', false);
	
	Configure::write('Userbox.default.User.nickname', 'user');
	Configure::write('Userbox.settings.captcha', true);
		
	Configure::write('Userbox.template.name', 'Common/page');
	Configure::write('Userbox.template.blocks.page.title', 'page-title');
	Configure::write('Userbox.template.blocks.page.content', 'page-content');
?>