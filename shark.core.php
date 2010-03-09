<?php

	/**
	 * Dependencies 
	 */

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

		function __construct($infi=true,$timezone='Asia/Taipei',$logname='sharklog')
		{
			/**
			 * php.ini configurations
			 */
			ini_set("max_execution_time",0);
			date_default_timezone_set($timezone);

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

?>
