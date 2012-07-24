<?php
// FIX - to include the base OAuth lib not in alphabetical order
require_once( realpath("../") . "/app/plugins/oauth/helpers/kiss_oauth.php" );

/* Discus for KISSCMS */
class Google_OAuth extends KISS_OAuth_v2 {
	
	function  __construct( $api="google", $url = "https://accounts.google.com/o/oauth2" ) {

		$this->url = array(
			'authorize' 		=> $url ."/auth", 
			'access_token' 		=> $url ."/token", 
			//'refresh_token' 	=> $url ."/refresh_token/"
		);
		
		parent::__construct( $api, $url );
		
	}
	
	public static function link( $scope="" ){
		
		$oauth = new Google_OAuth();
		
		// Modify scope to full urls (according to the Google API spec)
		$scope = explode(",", $scope);
		$services = $oauth->services(); 
		foreach( $scope as $i => $permission){
			$scope[$i] = $services[$permission];
		}
		$scope = implode(" ", $scope);
		
		parent::link($scope);
		
	}
	
	// additional params not covered by the default OAuth implementation
	public function access_token( $params, $request=array() ){
		
		$request = array(
			"params" => array( "grant_type" => "authorization_code" )
		);
		
		parent::access_token($params, $request);

	}
	
	public function refresh_token($request=array()){
		
		$request = array(
			"params" => array( "grant_type" => "refresh_token" )
		);
		
		parent::refresh_token($request);
	}
	
	function save( $response ){
		
		// erase the existing cache
		$google = new Google();
		//$google->deleteCache();
		
		// save to the user session 
		$auth = json_decode( $response, TRUE);
		
		if( is_array( $auth ) && array_key_exists("expires_in", $auth) )
			// variable expires is the number of seconds in the future - will have to convert it to a date
			$auth['expiry'] = date(DATE_ISO8601, (strtotime("now") + $auth['expires_in'] ) );
		
		// add another attribute 'create' that's uses in the official API
		$auth['created'] = strtotime("now");
		
		// FIX: Refresh token isn't passed with auto-confirm validation - will need to merge with existing values
		$_SESSION['oauth']['google'] = ( !empty( $_SESSION['oauth']['google'] ) ) ? array_merge( $_SESSION['oauth']['google'], $auth ): $auth;
		
	}
	
	function services(){

		// Reference on what all these services are: 
		// https://code.google.com/oauthplayground/
		return 	array(
				"adsense" 					=> "https://www.googleapis.com/auth/adsense", 
				"gan" 						=> "https://www.googleapis.com/auth/gan", 
				"analytics" 				=> "https://www.googleapis.com/auth/analytics.readonly", 
				"books" 					=> "https://www.googleapis.com/auth/books", 
				"blogger" 					=> "https://www.googleapis.com/auth/blogger", 
				"calendar" 					=> "https://www.googleapis.com/auth/calendar", 
				"storage" 					=> "https://www.googleapis.com/auth/devstorage.read_write", 
				"contacts" 					=> "https://www.google.com/m8/feeds/", 
				"structuredcontent" 		=> "https://www.googleapis.com/auth/structuredcontent", 
				"chromewebstore" 			=> "https://www.googleapis.com/auth/chromewebstore.readonly", 
				"docs" 						=> "https://docs.google.com/feeds/", 
				"gmail" 					=> "https://mail.google.com/mail/feed/atom", 
				"plus" 						=> "https://www.googleapis.com/auth/plus.me", 
				"groups" 					=> "https://apps-apis.google.com/a/feeds/groups/", 
				"latitude" 					=> "https://www.googleapis.com/auth/latitude.all.best", 
				"moderator" 				=> "https://www.googleapis.com/auth/moderator", 
				"nicknames.provisioning" 	=> "https://apps-apis.google.com/a/feeds/alias/", 
				"orkut" 					=> "https://www.googleapis.com/auth/orkut", 
				"picasaweb" 				=> "https://picasaweb.google.com/data/", 
				"sites" 					=> "https://sites.google.com/feeds/", 
				"spreadsheets" 				=> "https://spreadsheets.google.com/feeds/", 
				"tasks" 					=> "https://www.googleapis.com/auth/tasks", 
				"urlshortener" 				=> "https://www.googleapis.com/auth/urlshortener", 
				"userinfo.email" 			=> "https://www.googleapis.com/auth/userinfo.email", 
				"userinfo.profile" 			=> "https://www.googleapis.com/auth/userinfo.profile", 
				"user.provisioning" 		=> "https://apps-apis.google.com/a/feeds/user/", 
				"webmasters.tools" 			=> "https://www.google.com/webmasters/tools/feeds/", 
				"youtube" 					=> "https://gdata.youtube.com"
			);

	}

}