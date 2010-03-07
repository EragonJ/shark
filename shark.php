<?php
	class shark extends plurk_api
	{

		private $_rulesQualifier;
		private $_token;
		private $_responses = array();
		private $_constants = array();
		private $_plurks;
		private $_save;
		private $_filelist;
		private $_filelistnum;
		private $_randnum;
		private $_responseQualifier;

		private $_responseRandom;

		private $_wordEncoding;
		private $_timeLimitations;
		private $_mode;
		private $_modeParameters = array();

		private $id;
		private $timezone;
		/*
		* @timezone    => php_plurk_api doesnt support
		* @id          => php_plurk_api doesnt support
		*/

		function __construct($logname='sharklog')
		{
			$this->_constants['sharklog'] = BASE_PATH . $logname;
			$this->_wordEncoding = "UTF-8";
			$this->_timeLimitations = 1;
			$this->_responseRandom = true;
		}

		public function set_save($save=true)
		{
 			$this->_save = $save;
		}

		public function set_responses($input,$qualifier='says')
		{
			//To test whether it is a file or not (only support json)
			if(file_exists($input)||preg_match("/http/",$input))
			{
				$content            = file_get_contents($input);
				$this->_filelist    = explode("\n",$content);
				$this->_filelistnum = count($this->_filelist)-1-1;//one space

				unset($this->_filelist[$this->_filelistnum+1]); //unset the blank

				$this->rand_response();
			}
			else
			{
				is_array($input) ? $input : array_push($this->_responses, $input);
			}
			$this->_responseQualifier = $qualifier;
		}

		public function set_response_random($input=true)
		{
			$this->_responseRandom = $input;
		}

		public function set_rules($token,$qualifier='thinks')
		{
			$this->_rulesQualifier = $qualifier;
			$this->_token          = $token;
			$this->set_profile();
		}

		public function set_mode($mode='1',$parameters=array())
		{
			$this->_mode = $mode;
			if(is_array($parameters))
			{
				$this->_modeParameters = $parameters;
			}
			else
			{
				array_push($this->_modeParameters,$parameters);
			}
		}

		public function set_time_limitations($limit)
		{
			$this->_timeLimitations = $limit;
		}
		
		/*
		 * It is a special method for you
		 */
		protected function mode0(){}
		
		/*
		 * One plurk one response mode
		 */
		protected function mode1()
		{

			//fetch plurks and store them in $this->_plurks
			$this->fetch_plurks();

			foreach( $this->_plurks as $p_value )
			{

				//If someone thinks
				if($p_value->qualifier == $this->_rulesQualifier)
				{
					//about "token"
					if(mb_ereg($this->_token,$p_value->content_raw))
					{
						$responded = false;
						$responses = $this->get_responses($p_value->plurk_id);
						$responser_list = (array)$responses->friends;

						//it will be empty if no response
						if(!empty($responser_list))
						{
							foreach( $responser_list as $responser)
							{	
								if($responser->uid == $this->uid)
								{
									$responded = true;
									break;
								}
							}
						}

						//then response 
						if($responded==false)
						{
							foreach($this->_responses as $values)
							{
								$this->add_response($p_value->plurk_id,$values,$this->_responseQualifier);
								//Save the permalink at the sharklog
								$this->save($this->get_permalink($p_value->plurk_id));
							}
							//remove the picked up 
							array_pop($this->_responses);
							$this->rand_response();

							//Put here to reduce the cost for adding all as friends
							$this->add_all_as_friends();
						}
					}
				}
			}
		}

		/*
		 * Task mode
		 */
		protected function mode2()
		{

			/*
			 * We have to ensure that the time limitation is 1
			 * If two sharks live together, there will be a queuing delay to be solved later.
			 */
			foreach($this->_modeParameters as $k => $v)
			{
				$now  = new DateTime(date("Y-m-d H:i:s"));
				$task = new DateTime($v);

				$time = $k."] Now:".$now->format("Y-m-d H:i:s")." Target:".$task->format("Y-m-d H:i:s")."\r\n";

				if($now == $task)
				{
					$this->add_plurk('tr_ch',$this->_responseQualifier,$this->_responses[$k]);
				}
				else if($now < $task)
				{
					echo $time;
				}
			}
		}

		private function fetch_plurks()
		{
			$this->_plurks = $this->get_plurks(null,20,null,null,null);	

			//change stdClass to Array
			$this->_plurks = (array)$this->_plurks->plurks;
		}

		private function check_time()
		{
			sleep($this->_timeLimitations);
		}

		private function set_profile()
		{
			$profile = $this->get_own_profile()->user_info;
			$userinfo = (array)$profile;
			foreach($userinfo as $key => $value)
			{
				$this->$key = $value;
			}
		}

		private function save($message = '')
		{
			if($this->_save)
			{
				$log = $this->_constants['sharklog'];
				if(!file_exists($log))
				{
					touch($log);
				}
				$source = file_get_contents($log);
				$source .= json_encode(array(date("Y-m-d H:i:s")=>$message))."\n";
				file_put_contents($log,$source);
			}
		}

		private function rand_response()
		{
			if($this->_responseRandom)
			{
				$this->_randnum  = rand(0,$this->_filelistnum);
				$output          = '';

				//It is nested array
				$result = json_decode($this->_filelist[$this->_randnum],true);
				foreach($result as $key => $val)
				{
					$output .= $key.$val;
				}

				//To prevent the limitation of 140 words
				if(mb_strlen($output,$this->_wordEncoding)>=140)
				{
					$output = mb_substr($output,0,130,$this->_wordEncoding)."...";
				}
				array_push($this->_responses,$output);
			}
			else
			{
				foreach($this->_filelist as $k => $v)
				{
					$output = '';
					$result = json_decode($v,true);

					foreach($result as $key => $val)
					{
						$output .= $key.$val;
					}
					
					if(mb_strlen($output,$this->_wordEncoding)>=140)
					{
						$output = mb_substr($output,0,130,$this->_wordEncoding)."...";
					}
					array_push($this->_responses,$output);
				}
			}
		}

		public function run()
		{
			if(method_exists($this,'mode'.$this->_mode))
			{
				$this->{mode.$this->_mode}();
				//Do the time limitations to decrease the API calls
				$this->check_time();
			}
		}

	}
?>
