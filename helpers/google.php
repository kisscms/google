<?php
/* Discus for KISSCMS */
class Google {
	
	public $key;
	public $secret;
	public $token;
	public $refresh_token;
	public $api;
	public $me;
	public $oauth;
	private $cache;
	
	function  __construct() {
		
		$this->api = "https://google.com/api/3.0/";
		
		$this->key = $GLOBALS['config']['google']['key'];
	 	$this->secret = $GLOBALS['config']['google']['secret'];
		
		$this->me = ( empty($_SESSION['oauth']['google']['user_id']) ) ? false : $_SESSION['oauth']['google']['user_id'];
	 	
		$this->token = ( empty($_SESSION['oauth']['google']['access_token']) ) ? false : $_SESSION['oauth']['google']['access_token'];
	 	//$this->refresh_token = ( empty($_SESSION['oauth']['google']['refresh_token']) ) ? false : $_SESSION['oauth']['google']['refresh_token'];
	 	
		$this->cache = $this->getCache();
		// check if we need to refresh the token
		//$expires_in = $_SESSION['oauth']['google']['expires_in']; // seconds
		//if( $expires_in < 500 && $this->refresh_token ){
			//$this->token = $_SESSION['oauth']['google']['access_token'] = $this->refreshToken();
		//}
	}
	
	function get( $service, $params=NULL ){
		//unset($_SESSION["oauth"]);
		
		$oauth = new Google_OAuth();
		$request = $oauth->request("GET", $this->api.$service, $params);
		$url = $request->to_url();
		
		/*
		$token = new OAuthConsumer($this->token, $this->token_secret);
		//var_dump($token);
		$consumer = new OAuthConsumer($this->key, $this->secret);
		//var_dump($consumer);
		$request = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $this->api . $service, $params);
		$request->sign_request( (new OAuthSignatureMethod_HMAC_SHA1() ), $consumer, $token);
		
		$url = $request->to_url();
		*/
		var_dump($url);
		
		
		$http = new Http();
		//$http->setParams( $params );
		$http->execute( $url );
		var_dump( $http->result );
		exit;
		return ($http->error) ? die($http->error) : json_decode( $http->result);
	}
	
	function listThread( $id ){
		$url = $this->api ."threads/listPosts.json?api_key=". $this->key ."&thread=".$id;
		$http = new Http();
		$http->execute( $url );
		return ($http->error) ? die($http->error) : json_decode( $http->result);
	}
	
	function listFollowing(){
		// return the cache under conditions
		if( $this->checkCache("following") ) return $this->cache['following'];
		
		$url = $this->api ."users/listFollowing.json?access_token=". $this->token ."&api_key=". $this->key ."&api_secret=". $this->secret ."&user=".$this->me;
		
		$http = new Http();
		$http->execute( $url );
		$result = ($http->error) ? die($http->error) : json_decode( $http->result);
		$this->setCache( array("following" => $result) );
		return $result;

	}

	function listPosts( $user, $limit ){
		$url = $this->api ."users/listPosts.json?api_key=". $this->key ."&user=". $user ."&limit=". $limit ."&related=thread";
		$http = new Http();
		$http->execute( $url );
		//($http->error) ? die($http->error) : $result = json_decode( $http->result);
		return ($http->error) ? die($http->error) : json_decode( $http->result);
	}

	function sendReply($id=0, $message=""){
		$url = $this->api ."posts/create.json";
		$http = new Http();
		$http->setMethod('POST');
		$http->setParams( array(
				"access_token" => $this->token, 
				"api_key" => "$this->key", 
				"api_secret" => "$this->secret", 
				"parent" => $id, 
				"message" => $message,
			) 
		);
		$http->execute( $url );
		
		return ($http->error) ? die($http->error) : json_decode( $http->result);
		
	}
	
	
	function getCache(){
		// set up the parent container, the first time
		if( !array_key_exists("google", $_SESSION) ) $_SESSION['google']= array();
		return $_SESSION['google'];
		
	}
	
	function setCache( $data ){
		// save the data in the session
		foreach( $data as $key => $result ){
			$_SESSION['google'][$key] = $result;
		}
		// update the local variable
		$this->cache = $this->getCache();
	}
	
	function checkCache( $type ){
		// always discard cache on debug mode
		if( DEBUG ) return false; 
		
		if( !empty($this->cache[$type]) ) {
			// check the date 
			$valid = true;
		}
		
		return ( $valid ) ? true : false;
	}
	
	function deleteCache(){
		unset($_SESSION['google']);
	}
		
	function isFollowing( $id ){
		if( !array_key_exists('following', $this->cache) ) return false;
		// if the array exists, continue...
		$following = $this->cache['following'];
		foreach( $following->response  as $user){
			if ( $id == $user->id ) return true;
		}
		return false;
	}
	
	function isMine( $id ){
		return ( $id == $this->me );
	}
	
	
}