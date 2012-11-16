<?php
	echo $this->Html->tag('p', 'Здравствуйте, вы зарегистрировались на сайте');
	if(isset($url))
	{
		echo $this->Html->tag('p', 'Для подтверждения почтового адреса пройдите по ссылке '.
			$this->Html->link($url, $url).'.');
	}
?>
