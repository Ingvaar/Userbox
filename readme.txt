

Download and Install:

1. Download plugin Userbox and copy it to app/Plugin. Must be in app/Plugin/Userbox.
2. Donwload plugin Acl (http://www.alaxos.net/blaxos/pages/view/34) and copy to app/Plugin. Must be in app/Plugin/Acl.
3. In the file app/Config/bootstrap.php add the following line
	
	CakePlugin::load('Userbox', array('bootstrap' => true, 'routes' => true));
	
4. In the file app/Config/core.php add following code
	
	Configure::write('Routing.prefixes', array('admin'));

5. In the file app/Controller/AppController.php add the following code

	// of course you can add any extra components you need
	var $components = array('Auth', 'Acl', 'Userbox.Before', 'Session');

	public function beforeFilter() 
	{
		parent::beforeFilter();
		$this->Before->Begin();
		
		// your code below if necessary
	}

6. Execute from the app dir. This will create database tables with records: acos, aros, aros_acos, groups, users.

	./Console/cake schema create --plugin Userbox

	One administrator will be added
		login: admin@example.com
		password: password
	
7. Execute from the app dir.

	ln -s ../Plugin/Userbox/webroot/ webroot/userbox
	
	See http://book.cakephp.org/1.3/view/1614/Increasing-performance-of-plugin-and-theme-assets
	
8. Then type in your browser http://your_site_base/admin/acl to update aco list