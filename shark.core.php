<?php

	/**
	 * Dependencies 
	 */
	require('plurk_api.php');
	require('shark.copy.php');

	class sharkCore
	{

		/**
		 * Infinite Loop
		 * @var bool $_infinite 
		 */
		private $_infinite;

		/**
		 * Time Limitations for reducing API calls
		 * @var int $_timeLimitations
		 */
		private $_timeLimitations;

		/**
		 * Shark Instance 
		 * @var array $_sharkInstance
		 */
		private $_sharkInstance = array();

		function __construct($infi=true,$logname='sharklog')
		{
			/**
			 * php.ini configurations
			 */
			ini_set("max_execution_time",0);
			
			$this->_infinite = $infi;
			$this->_timeLimitations = 1;
		}

		/**
		 * function add
		 * add instances of shark
		 * @param shark $sharkInstance
		 */
		public function add(shark $sharkInstance)
		{
			array_push($this->_sharkInstance,$sharkInstance);
		}

		/**
		 * function run
		 * to execute all the instances of sharks
		 */
		public function run()
		{
			do
			{

				foreach($this->_sharkInstance as $shark)
				{
					$shark->run();
				}

			}while($this->_infinite);
		}
	}

	/*Configuration part*/
	$api_key[0] = 'GF0uZu5N4058aWw7a4rRx0z6aLYW7xFQ';
	$api_key[1] = 'JPhLvMLJVaE0XVvFQcG90haHfYwfEL84';
	$username = 'hax4_bot';
	$password = 'hahahaha';
	
	/*Shark Instance 1*/
	$plurk = new shark(1);
	$plurk->login($api_key[1], $username, $password);
	$plurk->set_rules('測試');
	$plurk->set_responses("responses","thinks");
	$plurk->set_time_limitations(1);
	$plurk->set_save(true);

	/*Shark Instance 2*/
	$plurk2 = new shark(1);
	$plurk2->login($api_key[1], $username, $password);
	$plurk2->set_rules('囧',"loves");
	$plurk2->set_responses("responses",":");
	$plurk2->set_time_limitations(1);
	$plurk2->set_save(true);
	
	/*Main part*/
	/*
	$sharkCore = new sharkCore();
	$sharkCore -> add($plurk);
	$sharkCore -> add($plurk2);
	$sharkCore -> run();
	*/

?>
