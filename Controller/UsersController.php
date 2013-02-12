<?php
App::uses('FacadeController', 'Controller');
class UsersController extends AppController
{
	/**
	 * @property User $User
	 * @property Group $Group
	 * @property RecaptchaComponent $Recaptcha
	 * @property AuthComponent $Auth
	 */
	var $belongsTo = array('Group'=>array('className'=>'Userbox.Group'));
	var $components = array('Userbox.Recaptcha', 'Session', 'RequestHandler');
	var $uses = array('Userbox.User', 'Userbox.Group');
	var $scaffold = 'admin';
	
	public function __construct($request = null, $response = null)
	{
		parent::__construct($request, $response);
		$this->User = ClassRegistry::init(array('class'=>'Userbox.User'));
	}
	
	public function personal()
	{
		$this->set('active_menu', 'Личный кабинет');
	}
	
	function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow('add', 'activate', 'ajax_login', 'ajax_register', 'ajax_register_regular', 'logout', 'login', 'forgot_password', 'reset_password', 'ajax_get_user_menu');
	}
	
	function login()
	{
		if ($this->request->is('post'))
		{
			if ($this->Auth->login())
			{
				$this->Session->setFlash('You are logged in!');
				$this->redirect($this->Auth->redirect());
			}
			else 
			{
				$this->Session->setFlash($this->Auth->loginError);
			}
		}
		
	}
	
	public function manager_login()
	{
		$this->redirect(array('action'=>'login', 'manager'=>false));
	}

	public function admin_login()
	{
		$this->redirect(array('action'=>'login', 'admin'=>false));
	}
	
	
	function logout()
	{
		$this->Session->setFlash('Теперь вы неавторизованы. Хорошего дня.');
		$this->Auth->logout();
		$this->redirect('/');
	}
	
	function admin_add()
	{
		$this->set('groups', $this->Group->find('list'));
		try
		{
			if(empty($this->data))
			{

			}
			else
			{
				$this->User->CreateUserDirect($this->data);
				$this->redirect('index');
			}
		}
		catch(Exception $e)
		{
			$this->set('error', $e->getMessage());
		}
	}
	
	function admin_delete($id=null)
	{
		try
		{
			if($id)
			{
				$this->User->delete($id);
				$this->redirect(array('action'=>'index', 'admin'=>true), null, true);
			}
		}
		catch(Exception $e)
		{
			$this->set('error', $e->getMessage());
		}
	}
	
	function activate($code)
	{
		try
		{
			if (isset($code))
			{
				// активировать аккаунт пользователя
				$lp = $this->User->Activate($code);
			}
		}
		catch(Exception $e)
		{
			$this->set('error', $e->getMessage());
		}
	}
	
	function view($id = null)
	{
		if (!$id)
		{
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	/**
	 * Самостоятельная регистрация пользователя
	 */
	function add()
	{
		$this->set('title_for_layout', 'регистрация пользователя');
		try
		{
			if(empty($this->data))
			{
				
			}
			else
			{
				$newUser = $this->User->CreateUser($this->data);
				if(method_exists($this, 'event'))
				{
					$newUser['url'] = Router::url(array('controller'=>'users', 'action'=>'activate', 'plugin'=>'userbox', $newUser['activationCode']), true);
					parent::event("USERBOX_USER_REGISTERED", $newUser);
				}
				$this->set('success', true);
				$this->set('email', $newUser['email']);
			}
		}
		catch(Exception $e)
		{
			$this->set('error', $e->getMessage());
		}
	}
	
	function ajax_register()
	{
		if (!empty($this->data))
		{
			try
			{
				$id = $this->User->CreateUserDirect($this->data);
				if(!$this->Auth->login())
					throw new Exception('Ошибка авторизации пользователя');
				// событие новый пользователь. уже авторизован
				FacadeController::Instance()->Event(FacadeController::USER_NEW, array(
					'email'    => $this->data['User']['email'],
					'password' => $this->data['User']['password'],
					'id'       => $id,
					'nickname' => 'Пользователь'
				));
			}
			catch (Exception $e)
			{
				$this->set('error', $e->getMessage());
			}
		}
	}
	
	function ajax_register_regular()
	{
		$this->layout = 'ajax';
		try
		{
			$captchaOn = Configure::read('Settings.captcha');
			if($captchaOn)
			{
				// captcha
				$ip = $this->RequestHandler->getClientIp();
				$challenge = $this->data['Recaptcha']['recaptcha_challenge_field'];
				$response  = $this->data['Recaptcha']['recaptcha_response_field'];
				$captcha   = $this->Recaptcha->Verify(compact('ip', 'challenge', 'response'));
			}
			$onlyValidate = false;
			if($captchaOn && $captcha == false)
			{
				$this->set('recaptcha', false);
				$onlyValidate = true;
			}
			// create user
			$r = $this->User->CreateUser($this->data, $onlyValidate);
			// если каптча включена и она провалилась, то вызвать исключение
			if($captchaOn && $captcha == false)
			{
				throw new Exception('Recaptcha failed');
			}
			// событие новый пользователь
			FacadeController::Instance()->Event(FacadeController::USER_NEW, array(
				'email'    => $this->data['User']['email'],
				'nickname' => $this->data['User']['nickname'],
				'password' => $this->data['User']['password'],
				'activationCode' => $r['activationCode'],
				'id'             => $r['id']
			));
		}
		catch (Exception $e)
		{
			$this->set('error', $e->getMessage());
		}
	}

	function ajax_login()
	{
		$this->viewClass = 'Json';
		try
		{
			if ($this->request->is('ajax'))
			{
				if(CakeSession::read('Auth.User.group_id') == 4)
				{
					CakeSession::write('Auth', null);
				}
				if($this->Auth->loggedIn())
				{
					// уже авторизован
					$this->set('result', array(
						'success' => 'Вы уже авторизованы',
						// также в ответе будет указана почта пользователя
						'user'    => array('email' => CakeSession::read('Auth.User.email'))
					));
				}
				else if (isset($this->data['User']['email']) && isset($this->data['User']['password']))
				{
					// попытка авторизации
					$res = $this->Auth->login();
					if($res)
					{
						$result = array(
							// возвращаем признак успеха, с комментарием
							'success' => 'Авторизация успешна',
							// также в ответе будет указана почта пользователя
							'user'    => array('email' => CakeSession::read('Auth.User.email'))
						);
						$redir = $this->Auth->redirect();
						if($redir != '/')
						{
							$result['redirect'] = Router::url($redir, true);
						}
						else
							$result['redirect'] = Router::url($this->Auth->loginRedirect, true);
						
						$this->set('result', $result);
					}
					else
						throw new Exception($this->Auth->loginError);
				}
				else
					throw new Exception('Отсутствуют данные для авторизации');
				$this->set('_serialize', array('result'));
			}
		}
		catch(Exception $e)
		{
			// что-то не так
			$this->set('error', $e->getMessage());
			$this->set('_serialize', array('error'));
		}
	}
	
	public function ajax_update_user()
	{
		$this->layout = 'ajax';
		if ($this->request->is('ajax'))
		{
			$this->User->Update($this->data);
		}
	}
	
	public function ajax_get_user_menu()
	{
		$this->set('group', CakeSession::read('Auth.User.group_id'));
	}
	
	public function forgot_password()
	{
		$this->set('title_for_layout', 'сброс пароля');
		try
		{
			$captchaOn = Configure::read('Userbox.settings.captcha');
			if(!empty($this->data))
			{
				if($captchaOn)
				{
					// captcha включена
					$ip = $this->RequestHandler->getClientIp();
					$challenge = $this->data['recaptcha_challenge_field'];
					$response  = $this->data['recaptcha_response_field'];
					$captcha   = $this->Recaptcha->Verify(compact('ip', 'challenge', 'response'));
				}
				$onlyValidate = false;
				if($captchaOn && $captcha == false)
				{
					$this->set('recaptcha', false);
					$onlyValidate = true;
				}
				// ищем адрес в системе
				$usr = $this->User->ForgotEmail($this->data['User']['email'], $onlyValidate);
				if($captchaOn && $captcha == false)
				{
					throw new Exception('Recaptcha failed');
				}
				// успех
				// событие сброс пароля пользователя
				//
				if(method_exists($this, 'event'))
				{
					$url = Router::url(array('controller'=>'users', 'action'=>'reset_password', 'plugin'=>'userbox', $usr['activationCode']), true);
					parent::event("USERBOX_USER_RESET_PASSWORD", array(
						'email'		=> $this->data['User']['email'],
						'nickname'	=> $usr['nickname'],
						'url'		=> $url
					));
				}
				$this->set('success', true);
				$this->set('email', $this->data['User']['email']);
			}
		}
		catch(Exception $e)
		{
			$this->set('error', $e->getMessage());
		}
	}
	
	public function reset_password($activationCode = null)
	{
		try
		{
			if($activationCode)
			{
				$this->set('activationCode', $activationCode);
			}
			else if(!empty($this->data) && isset($this->data['User']['activation_key']))
			{
				$this->set('activationCode', $this->data['User']['activation_key']);
				$id = $this->User->ResetPassword($this->data);
				$this->set('success', true);
				// успех
				FacadeController::Instance()->Event(FacadeController::USER_SETNEW_PWD, array(
					'id' => $id
				));
			}
			else
			{
				throw new Exception('Данных нет');
			}
		}
		catch(Exception $e)
		{
			if($e->getCode() != 1)
				$this->set('error', $e->getMessage());
		}
		$this->request->data = array();
	}
}
?>
