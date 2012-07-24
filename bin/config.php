<?php


//===============================================
// Configuration
//===============================================

if( class_exists('Config') && method_exists(new Config(),'register')){ 

	// Register variables
	Config::register("google", "key", "0000000");
	Config::register("google", "secret", "AAAAAAAAA");

}

?>