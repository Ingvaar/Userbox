<?php
	Router::connect('/users/login', array('controller' => 'users', 'action' => 'login', 'plugin'=>'userbox'));
	Router::connect('/users/logout', array('controller' => 'users', 'action' => 'logout', 'plugin'=>'userbox'));
	Router::connect('/users/create', array('controller' => 'users', 'action' => 'add', 'plugin'=>'userbox', 'admin'=>true));
?>