<?php
class Group extends AppModel 
{
	const ADMIN   = 1;
	const MANAGER = 2;
	const USER    = 3;
	const PUBLICC = 4;
    
	var $hasMany = array('Userbox.User');
	
	public $actsAs = array('Acl' => array('type' => 'requester'));
	
	function parentNode()
	{
        return null;
    }
	
	public function GetName($group_id)
	{
		switch($group_id)
		{
			case self::ADMIN:   return 'administrators';
			case self::MANAGER: return 'managers';
			case self::USER:    return 'users';
			default: return 'public';
		}
	}
}

?>