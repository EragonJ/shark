<?php
	class shark extends plurk_api
	{
		private $_qualifier;
		private $_token;
		private $_infinite;
		private $_responses = array();
		private $_constants = array();
		private $_plurks;
		private $_save;

		/**
		* userinfo
		* @display_name 
		* @uid
		* @relationship
		* @nick_name
		* @has_profile_image
		* @location
		* @date_of_birth
		* @avatar
		* @full_name
		* @gender
		* @timezone
		* @recruited
		* @id
		* @karma
		*/
		private $_userinfo;

		function __construct($infi=true,$logname='sharklog')
		{
			$this->_infinite = $infi;
			$this->_constants['sharklog'] = BASE_PATH . $logname;
		}

		public function set_save($save=true)
		{
			$this->_save = $save;
		}

		public function set_responses($input)
		{
			is_array($input) ? $input
											 : array_push($this->_responses, $input);
		}

		public function set_rules($token,$qualifier='thinks')
		{
			$this->_qualifier = $qualifier;
			$this->_token     = $token;
			$this->set_profile();
		}

		private function set_profile()
		{
			$profile = $this->get_own_profile()->user_info;
			$this->_userinfo = (array)$profile;
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
				$source .= date("Y-m-d H:i:s - ").$message."\n";
				file_put_contents($log,$source);
			}
		}

		public function run()
		{
			do
			{
				$this->_plurks = $this->get_plurks(null,20,null,null,null);	

				//change stdClass to Array
				$this->_plurks = (array)$this->_plurks->plurks;

				foreach( $this->_plurks as $p_value )
				{
					//If someone thinks
					if($p_value->qualifier == $this->_qualifier)
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
									if($responser->uid == $this->_userinfo['uid'])
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
									$this->add_response($p_value->plurk_id,$values, 'says');
									//Save the permalink at the sharklog
									$this->save($this->get_permalink($p_value->plurk_id));
								}
							}
						}
					}
				}
			}while($this->_infinite);
		}
	}
?>
