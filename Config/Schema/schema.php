<?php 
App::uses('AclComponent', 'Controller/Component');
//App::import('Userbox.Model', 'Group');
class UserboxSchema extends CakeSchema
{
	/**
	 */
	public function before($event = array())
	{
		$db = ConnectionManager::getDataSource($this->connection);
		$db->cacheSources = false;
		return true;
	}
			
	public function after($event = array())
	{
		$model = null;
		if (isset($event['create']))
		{
			switch ($event['create'])
			{
				case 'acos':
					$this->Acl = new AclComponent(new ComponentCollection());
					$this->Acl->Aco->create(array('parent_id' => null, 'alias' => 'controllers'));
					$this->Acl->Aco->save();
					$pid = $this->Acl->Aco->id;
					
					$this->Acl->Aco->create(array('parent_id' => $pid, 'alias' => 'Acl'));
					$this->Acl->Aco->save();
					$pid = $this->Acl->Aco->id;

					$this->Acl->Aco->create(array('parent_id' => $pid, 'alias' => 'Acos'));
					$this->Acl->Aco->save();
					$pid = $this->Acl->Aco->id;

					$this->Acl->Aco->create(array('parent_id' => $pid, 'alias' => 'admin_index'));
					$this->Acl->Aco->save();
					$this->Acl->Aco->create(array('parent_id' => $pid, 'alias' => 'admin_build_acl'));
					$this->Acl->Aco->save();
				break;	
				case 'aros':
                break;
				case 'aros_acos':
                break;
		        case 'groups':
					$model = ClassRegistry::init('Userbox.Group');
					// data
					$data = array(
						array('name' => 'administrators'),
						array('name' => 'managers'),
						array('name' => 'users'),
						array('name' => 'public'),
					);
				break;
		        case 'users':
					$model = ClassRegistry::init('Userbox.User');
					// data
					$adminPwd = 'userbox';
					$data = array(
						array(
							'email'=>'admin@example.com', 'group_id'=>1/*Group::ADMIN*/, 'status'=>1/*User::STATUS_ACTIVE*/,
							'nickname'=>'admin', 'password'=> 'password')
					);
					$this->Acl->allow('administrators', 'controllers');
                break;
				default: return;
			}
			if($model)
			{
				$model->create();
				$model->saveMany($data);
				if($event['create'] == 'groups')
				{
					//
					// update alias for groups
					//
					$r = $this->Acl->Aro->findByforeign_key(1);
					$this->Acl->Aro->id = $r['Aro']['id'];
					$this->Acl->Aro->saveField('alias', 'administrators');
					
					$r = $this->Acl->Aro->findByforeign_key(2);
					$this->Acl->Aro->id = $r['Aro']['id'];
					$this->Acl->Aro->saveField('alias', 'managers');

					$r = $this->Acl->Aro->findByforeign_key(3);
					$this->Acl->Aro->id = $r['Aro']['id'];
					$this->Acl->Aro->saveField('alias', 'users');
					
					$r = $this->Acl->Aro->findByforeign_key(4);
					$this->Acl->Aro->id = $r['Aro']['id'];
					$this->Acl->Aro->saveField('alias', 'public');
				}
			}
		}
	}

	public $acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 11, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'foreign_key' => array('type' => 'integer', 'null' => true),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'lft' => array('type' => 'integer', 'null' => true),
		'rght' => array('type' => 'integer', 'null' => true),
		'indexes' => array('PRIMARY' => array('unique' => true, 'column' => 'id')),
		'tableParameters' => array()
	);
	public $aros = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 11, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'foreign_key' => array('type' => 'integer', 'null' => true),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'lft' => array('type' => 'integer', 'null' => true),
		'rght' => array('type' => 'integer', 'null' => true),
		'indexes' => array('PRIMARY' => array('unique' => true, 'column' => 'id')),
		'tableParameters' => array()
	);
	public $aros_acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 11, 'key' => 'primary'),
		'aro_id' => array('type' => 'integer', 'null' => false),
		'aco_id' => array('type' => 'integer', 'null' => false),
		'_create' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2),
		'_read' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2),
		'_delete' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2),
		'_update' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2),
		'indexes' => array('PRIMARY' => array('unique' => true, 'column' => 'id')),
		'tableParameters' => array()
	);
	public $groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 11, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => true),
		'indexes' => array('PRIMARY' => array('unique' => true, 'column' => 'id')),
		'tableParameters' => array()
	);
	public $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 11, 'key' => 'primary'),
		'nickname' => array('type' => 'string', 'null' => false),
		'email' => array('type' => 'string', 'null' => false),
		'password' => array('type' => 'string', 'null' => false, 'length' => 40),
		'data' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'group_id' => array('type' => 'integer', 'null' => false),
		'activation_key' => array('type' => 'string', 'null' => true),
		'password_key' => array('type' => 'string', 'null' => true),
		'status' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'last_login' => array('type' => 'datetime', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array('PRIMARY' => array('unique' => true, 'column' => 'id'), 'users_email_key' => array('unique' => true, 'column' => 'email')),
		'tableParameters' => array()
	);
}
