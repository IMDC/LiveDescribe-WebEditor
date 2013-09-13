<?php
class DVX_Model extends CI_Model {

	private $baseURL;
	private $userID_verified = null;

	public function __construct(){
		// Call the Model constructor
		parent::__construct();
		$this->baseURL = 'http://dvx.ski.org:9090/dvxApi/v1/';
	}


	/**
	*	@param $user 
	*	@param $email 
	*	@param $password
	*	@return $response 
	*   registers the user with the given credentials to the
	*	DVX server
	*/
	public function registerUser($user, $email,$password){
		$content = array('LoginName' => $user, 'Password' => $password, 'Email' => $email);
		$url = $this->baseURL . 'user';
	
		try{		
			$response = $this->post_request($url, $content);
			return $response;
		}
		catch(Exception $ex){
			throw $ex;
		}	
	}

	/**
	*	@param $user  
	*	@param $password
	*	@return $response 
	*   Login the user with the given credentials to the
	*	DVX server
	*/
	public function login($user, $password){
		$content = array('UserName' => $user, 'Password' => $password);
		$url = $this->baseURL . 'login';
		
		try{
			$token = $this -> post_request($url, $content);
			$userID = $this -> getUserID($user);
			$response = array('token' => $token ,'userID' => $userID, 'userName' => $user);
			return $response;
		}
		catch(Exception $ex){
			throw $ex;
		}
	}


	/**
	*	@param $url     Where to send the request to.
	*	@param $content An array with the data to be sent.
	*	
	*	Simple curl command for a post request to DVX.
	*/
	private function post_request($url, $content){
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, count($content));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));

		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$server_output = curl_exec ($ch);

		if(curl_errno($ch)){
			throw new Exception("Server connection error.");
		}

		curl_close ($ch);

		return $server_output;
	}

	/**
	*	@param $user
	*	@return $id  ID returned from the DVX server
	*	Given the user name, parse the xml response
	*	from DVX to obtain the userId
	*/
	private function getUserID($user){
		$get_url = $this->baseURL . 'user?LoginName=' .$user;
		
		
		$xml_response = file_get_contents($get_url);//get response in XML format
		if($xml_response === false){
			throw new Exception("Server connection error.");
		}
		
		// parse the xml response
		$xml = simplexml_load_string($xml_response);
		$xml -> registerXPathNamespace('ns2', 'http://user.hibernate.server.dvx.ski.org/');
		$xml = $xml->xpath('//ns2:user');
		$items = $xml[0]->children();
		$id = $items->userId;
		return $id[0];
	}
   
}
?>