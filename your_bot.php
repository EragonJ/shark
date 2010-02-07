<?php
	require('plurk_api.php');
	require('shark.php');

	$apikey = 'xxx';
	$username = 'test';
	$password = 'test';

	$plurk = new shark();
	$plurk->login($apikey,$username,$password);
	$plurk->set_rules('smile|laugh'); 
	$plurk->set_responses('I detect "token" and leave the response by your_bot');
	$plurk->set_save(true);
	$plurk->run();
?>
