<?php
	CakePlugin::load('Acl', array('bootstrap' => true));
	Configure :: write('acl.aro.role.model', 'Group');
	Configure :: write('acl.aro.user.model', 'User');
	Configure :: write('acl.user.display_name', 'User.nickname');
	Configure :: write('acl.check_act_as_requester', false);
        Configure :: write('Settings.default.User.nickname', 'user');
?>