<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BeginComponent
 *
 * @author Ingvar
 */
class BeforeComponent extends Component
{
	var $appController = null;
	private $__settings = array();
	
	public function Begin()
	{
		//Configure AuthComponent
		$this->appController->Auth->authorize      = array('Actions' => array('actionPath' => 'controllers'));
		$this->appController->Auth->loginAction    = $this->__settings['loginAction'];
		$this->appController->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'logout', 'plugin'=>'userbox');
		
		$this->appController->Auth->authError		= 'Did you really think you are allowed to see that?';
		$this->appController->Auth->allowedActions  = array('display');
		//$this->appController->Auth->allowedActions  = array('*');
		$this->appController->Auth->loginError		= 'Имя пользователя или пароль неверные';

		$this->appController->Auth->authenticate = array(
			AuthComponent::ALL => array(
				'userModel' => 'User',
				'scope' => array('User.status' => 1),
				'fields' => array(
					'username' => 'email',
					'password' => 'password' 
				)
		), 'Form');
		
	}
	
	public function initialize(Controller $controller)
	{
		parent::initialize($controller);
		$this->appController = $controller;
		
	}
	
	public function __construct(ComponentCollection $collection, $settings = array())
	{
		if(isset($settings['loginAction']))
			$this->__settings['loginAction'] = $settings['loginAction'];
	}

	private function __defaultSettings()
	{
		$this->__settings = array(
			'loginAction' => array('controller' => 'users', 'action' => 'login', 'plugin'=>'userbox', 'admin'=>false)
		);
	}
}

?>
