<?php
App::uses('HttpSocket', 'Network/Http');
class RecaptchaComponent extends Component
{
	/**
	 * @property HttpSocket $http
	 */
	const HOST = 'http://www.google.com/recaptcha/api/verify';
	private $__settings = array();
	
	function initialize($controller)
	{
        parent::initialize($controller);
		$this->http = new HttpSocket();
    }
	
	public function __construct(ComponentCollection $collection, $settings = array())
	{
		$this->__defaultSettings();
		if(isset($settings['host']))
			$this->__settings['host'] = $settings['host'];
		if(isset($settings['privateKey']))
			$this->__settings['privateKey'] = $settings['privateKey'];
	}
	
	private function __defaultSettings()
	{
		$this->__settings = array(
			'privateKey' => Configure::read('Recaptcha.privateKey')
		);
	}	
	
	public function Verify($data=array())
	{
		$privatekey = $this->__settings['privateKey'];
		$remoteip   = $data['ip'];
		$challenge  = $data['challenge'];
		$response   = $data['response'];
		
		$res = $this->http->post(self::HOST, compact('privatekey', 'remoteip', 'challenge', 'response'));
		$res = preg_split('/\n/', $res);
		
		if($res[0] != 'true')
		{
			return false;
		}
		else
			return true;
	}
}