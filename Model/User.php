<?php
App::uses('AuthComponent', 'Controller/Component');
App::uses('Group', 'Userbox.Model');
class User extends AppModel 
{
	const STATUS_WAIT_ACTIVATION = 0;
	const STATUS_ACTIVE          = 1;
	const STATUS_RESET_PASSWORD  = 2;

    public $belongsTo = array('Group'=>array('className'=>'Userbox.Group'));
    public $actsAs	  = array('Acl' => array('type' => 'requester'));
	
	var $displayField = 'nickname';
	var $validate = array(
		'nickname' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Поле не может быть пустым'
			),
			'alphaNumeric' => array(
				'rule'     => 'alphaNumeric',
				'required' => true,
				'message'  => 'В имени пользователя могут быть только буквы и цифры'
			),
		),
		'password' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Пароль не может быть пустым'
			),
			'minLen' => array(
				'rule' => array('minLength', '6'),
				'message' => 'В пароле должно быть не меньше шести символов'
			)
		),
		'password_2' => array(
			'identicalFieldValues' => array(
				'rule' => array('identicalFieldValues', 'password'),
				'message' => 'Пожалуйста, повторите ввод пароля. Пароли должны совпадать'
			)
		),		
		'email' => array(
			'notEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Поле не может быть пустым'
			),
			'unique' => array(
				'rule'    => 'isUnique',
				'message' => 'Указанный адрес электронной почты уже занят'
			),
			'email' => array(
				'rule'    => array('email', true),
				'message' => 'Пожалуйста, укажите корректный адрес электронной почты'
			)
		)
	);
	
	public function GetGroupName($id)
	{
		$res = $this->find('first', array('conditions'=>array('User.id'=>$id), 'contain'=>'Group.name'));
		if($res)
			return $res['Group']['name'];
		else
			return null;
	}
	
	function identicalFieldValues($field=array(), $compare_field=null) 
    {
        foreach($field as $key => $value)
		{
            $v1 = $value;
            $v2 = $this->data[$this->name][$compare_field];
            if($v1 !== $v2)
			{
                return false;
            }
			else
			{
                continue;
            }
        }
        return true;
    }
    
	public function beforeSave()
	{
		if(isset($this->data[$this->name]['password']))
			$this->data[$this->name]['password'] = AuthComponent::password($this->data['User']['password']);
        return true;
    }

	public function Activate($code)
	{
		// найти запись с кодом
		$res = $this->find('first', array(
			'conditions' => array('activation_key'=>$code, 'status'=>self::STATUS_WAIT_ACTIVATION),
			'fields'     => array('id', 'email', 'password'),
			'contain'    => false
		));
		if($res)
		{
			$this->id = $res[$this->name]['id'];
			$this->set('status', self::STATUS_ACTIVE);
			$this->set('activation_key', '');
			$this->save(null, false, array('status', 'activation_key'));
			return array('email'=>$res[$this->name]['email'], 'password'=>$res[$this->name]['password']);
		}
		else
			throw new Exception('Нет данных');
	}
	
	function bindNode($user)
	{
		return array('model' => 'Group', 'foreign_key' => $user[$this->name]['group_id']);
    }
	
    function parentNode()
	{
        if (!$this->id && empty($this->data))
		{
            return null;
        }
        if (isset($this->data[$this->name]['group_id']))
		{
            $groupId = $this->data[$this->name]['group_id'];
        }
		else
		{
            $groupId = $this->field('group_id');
        }
        if (!$groupId)
		{
            return null;
        }
		else
		{
            return array('Group' => array('id' => $groupId));
        }
    }
	/**
	 * Создание нового пользователя
	 * @return array array('activationCode', 'id', 'email', 'nickname')
	 */
	public function CreateUser($data=array(), $onlyValidate = false)
	{
		if(isset($data))
		{
			if(!isset($data[$this->name]['nickname']))
				$data[$this->name]['nickname'] = Configure::read('Userbox.default.User.nickname');

			$this->create($data);
			if($this->validates() == false)
			{
				throw new Exception('Data validation error');
			}
			if($onlyValidate)
				return;
			// validation ok
			// save user
			// status - wait activation
			$this->set('status', self::STATUS_WAIT_ACTIVATION);
			// group is users
			$this->set('group_id', 3);
			// act code
			$actCode = $this->__generateActivationCode();
			$this->set('activation_key', $actCode);
			// save
			$this->save();
			return array(
				'activationCode'	=> $actCode,
				'id'				=> $this->id,
				'email'				=> $data[$this->name]['email'],
				'nickname'			=> $data[$this->name]['nickname']
			);
		}
	}
	
	private function __generateActivationCode()
	{
		while(1)
		{
			$actCode = Security::hash(Configure::read('Security.salt').microtime());
			if($this->isUnique(array('activation_key'=>$actCode)))
			{
				break;
			}
		}
		return $actCode;
	}
	
	private function __generatePasswordCode()
	{
		while(1)
		{
			$actCode = Security::hash(Configure::read('Security.salt').microtime());
			if($this->isUnique(array('password_key'=>$actCode)))
			{
				break;
			}
		}
		return $actCode;
	}
	
	// Создание нового пользователя
	public function CreateUserDirect($data=array())
	{
		if(isset($data))
		{
			if(!isset($data[$this->name]['nickname']))
				$data[$this->name]['nickname'] = Configure::read('Settings.default.User.nickname');
			$this->create($data);
			if($this->validates() == false)
				throw new Exception('Data validation error');
			if($data[$this->name]['password'] != $data[$this->name]['password_2'])
				throw new Exception ('Passwords do not match');
			// validation ok
			// save user
			// status - wait activation
			$this->set('status', self::STATUS_ACTIVE);
			// group is users
			if(!isset($data[$this->name]['group_id']))
				$this->set('group_id', Group::USER);
			else
				$this->set('group_id', $data[$this->name]['group_id']);
			// save
			if(!$this->save())
				throw new Exception('User save error');
			
			return $this->id;
		}
	}
	
	public function Update($data=array())
	{
		$this->save($data);
	}
	
	/**
	 * Поиск и сброс пароля пользователя
	 * @param email $email адрес пользователя
	 * @param bool $onlyValidate если true, то произойдет только поиск адреса почты в базе, без сброса пароля
	 * @return array array('activationCode', 'nickname', 'id')
	 * @throws Exception ошибка валидации
	 */
	public function ForgotEmail($email, $onlyValidate = false)
	{
		// настраиваем валидацию на одно поле
		$this->set('email', $email);
		$this->validate = array('email' => $this->validate['email']);
		unset($this->validate['email']['email']);
		unset($this->validate['email']['unique']);
		if($this->validates() == false)
		{
			throw new Exception('Data validation error');
		}
		// ищем адрес в системе
		$res = $this->find('first', array(
			'contain'=>false,
			'conditions'=>array('email'=>$email, 'OR'=>array(array('status'=>self::STATUS_ACTIVE), array('status'=>self::STATUS_RESET_PASSWORD))),
			'fields'=>array('id', 'nickname')
		)
		);
		if($res == null)
		{
			// почта не найдена
			$this->invalidate('email', 'Указанный почтовый адрес в системе не найден');
			throw new Exception('Data validation error');
		}
		if($onlyValidate)
			return;
		$this->id = $res[$this->name]['id'];
		//
		// act code генерим акт код
		//
		$activationCode = $this->__generatePasswordCode();
		$this->set('password_key', $activationCode);
		// save
		$this->save(null, true, array('password_key'));
		$nickname = $res[$this->name]['nickname'];
		$id       = $res[$this->name]['id'];
		return compact('activationCode', 'nickname', 'id');
	}
	
	public function ResetPassword($data=array())
	{
		// найти запись с таким кодом
		$res = $this->find('first', array(
			'fields'	 => array('id'),
			'contain'	 => false,
			'conditions' => array(
				'password_key'=>$data[$this->name]['activation_key'],
				'status'=>self::STATUS_ACTIVE
			)
		));
		if($res == null)
			throw new Exception('Нет данных');
		// проверить валидность паролей
		$this->validate = array('password' => $this->validate['password'], 'password_2' => $this->validate['password_2']);
		$this->data = $data;
		if($this->validates() == false)
			throw new Exception('Некорректные данные', 1);
		//
		// изменить пароль
		//
		$this->set('id', $res[$this->name]['id']);
		$this->set('password', $data[$this->name]['password']);
		$this->set('password_key', '');
		// сохранить
		$this->save(null, true, array('password', 'password_key'));
		
		return $res[$this->name]['id'];
	}
}
?>