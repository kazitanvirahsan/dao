<?php

class Config{
	/*
		Config class reads the config.xml file and creates a configuration associative array
		from its contents. In this way the class that wishes to act on the database can do
		so by receiving the configurations.

	*/

	public static function getConfig(){
	/*
		@return array containing connection information for database
			resource-> containing resource string
			user-> containing username
			password->containing password

	*/
		$xml = simplexml_load_file(__DIR__."/config.xml");
		$string = "mysql:host=".$xml[0]->host.";"."dbname=".$xml[0]->dbname;
		return array(
				"resource"=>$string,
				"user"=>$user = $xml[0]->user,
				"password"=>$pass = $xml[0]->password,
		       );
		
	}

}


?>
