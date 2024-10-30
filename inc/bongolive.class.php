<?php
	class bongolive
	{
		public $bongolive_link = "http://bongolive.co.tz/";
		private $username;
		private $password;
		private $apikey;
		public $to;
		public $sender;
		public $msg;
		public $type;
		public $dlr;
		public $url;

		function __construct()
		{
		}

		function send_sms(){
			define('SMS_URL','http://bongolive.co.tz/api/');
			$number = '+'. $this->to;
			$number = urlencode($number);
			$msg = urlencode($this->msg);
			$posturl = SMS_URL."sendSMS.php?sendername=".$this->sender."&username=".$this->username."&password=".$this->password."&apikey=".$this->apikey."&destnum=".$number."&message=".$msg;
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $posturl); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			$response = curl_exec($ch); 
			return $response;
		}
		
		function send_sms_international(){
			define('SMS_URL','http://121.241.242.114:8080/bulksms/bulksms?');
			$number = '+'. $this->to;
			$number = urlencode($number);
			$msg = urlencode($this->msg);
			$posturl = SMS_URL."source=".urlencode($this->sender)."&username=".$this->username."&password=".$this->password."&type=".$this->type."&dlr=".$this->dlr."&destination=".$number."&message=".$msg."&url=".$this->url;
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $posturl); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			$response = curl_exec($ch); 
			return $response;
		}
		function get_account($value)
		{	
			define('GET_CREDITS','http://bongolive.co.tz/api/');
			$posturl = GET_CREDITS."request.php?username=".$this->username."&password=".$this->password."&request={$value}";
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $posturl); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			$response = curl_exec($ch); 
			return $response;
			
		}
		
		function set_credentials(){

				$this->username = get_option('wp_bongolive_username');
				$this->password = get_option('wp_bongolive_password');
				$this->apikey = get_option('wp_bongolive_apikey');
				if (get_option('choose_service') == 'international'){
					$this->sender = get_option('typed_sender_names');
				}else{
					$this->sender = get_option('bongolive_sender');
				}
				$this->type = get_option('bongolive_message_type');
				$this->dlr = get_option('wp_bongolive_delivery');
				$this->url = "";
			
		}
		
		function prep_sender_name($sender){
			// if its all numbers it should  be 14 char max
			$is_mobile_valid = $this->check_mobile($sender);
			if (strlen($sender) > 0){
				if (is_numeric($sender)){
					if ($is_mobile_valid){
							
							$is_mobile_valid = "+".$is_mobile_valid;
							return $is_mobile_valid;
					}else{
						//update_option('typed_sender_names','');
						return "";
					}
				}else{
						if((strlen($sender) <= 11) && (strlen($sender) > 0)){
							return $sender;
						}else{
							//update_option('typed_sender_names','');
							return "";
						}
				}
				
			}
		}
		
		function check_mobile($mobile){
			$mobile = trim($mobile);
			$len = strlen($mobile);
			if (preg_match("([a-zA-Z])", $mobile) == 0){
				if (($len >=12) && ($len <=14)){
					if (substr($mobile,0,1) == '+'){
						return substr($mobile,1);
					}else{
						return $mobile;
					}
				}else{
					return false;
				}
			}else {
				return false;
			}
		}
		
		
	}
?>