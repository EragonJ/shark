<?php
	require('plurk_api.php');
	require('shark.php');
	require('shark.core.php');

	$apikey = 'xxx';
	$username = 'test';
	$password = 'test';

	/*Shark Instance 1*/
	$plurk = new shark();
	$plurk->login($apikey,$username,$password);
	$plurk->set_rules('smile|laugh'); 
	$plurk->set_responses('I detect "token" and leave the response by Shark');
	$plurk->set_time_limitations(1);
	$plurk->set_response_random(true);
	$plurk->set_save(true);
	$plurk->set_mode(1);

	/*Shark Instance 2*/
	$plurk = new shark();
	$plurk->login($apikey,$username,$password);
	$plurk->set_rules('smile|laugh'); 
	$plurk->set_responses('Im shark, I can automatically plurk on 2010-03-06 at 14:01:10');
	$plurk->set_time_limitations(1);
	$plurk->set_response_random(true);
	$plurk->set_save(true);
	$plurk->set_mode(2,array("2010-03-06 14:01:10"));

	$sharkCore = new sharkCore();
	$sharkCore -> add($plurk);
	$sharkCore -> add($plurk2);
	$sharkCore -> run();
?>
