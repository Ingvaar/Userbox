<?php
App::uses('HttpSocket', 'Network/Http');
class RecaptchaComponent extends Component
{
	/**
	 * @property HttpSocket $http
	 */
	const HOST = 'http://www.google.com/recaptcha/api/verify';
	const KEY  = '6LcnpcwSAAAAAKoSKp9zeiw5RMXi5SPMwdp2uwyZ';
	
	function initialize($controller)
	{
        parent::initialize($controller);
		$this->http = new HttpSocket();
    }
	
	public function Verify($data=array())
	{
		$privatekey = self::KEY;
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