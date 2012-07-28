<?php


//===============================================
// Configuration
//===============================================

if( class_exists('Config') && method_exists(new Config(),'register')){ 

	// Register variables
	Config::register("google", "name", "App Name");
	Config::register("google", "key", "XXXXXXXX.apps.googleusercontent.com");
	Config::register("google", "secret", "012345678901234567890123456789");
	Config::register("google", "dev_key", "01234567890");
	
}

?>