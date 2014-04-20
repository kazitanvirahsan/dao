<?php
require_once("Config.php");
class Dao{
	/*	@author: Wasim Zarin
		@date:	April 11,2014
		A singleton class that gives a global connection object. You would use connection object as follows:
		1. Get the connection object by calling $dao = Dao::getInstance()
		2. Step 1 gives you a singleton. Use it to execute commands on database. It would be better to 
		use wrappers around this object to simplify and standardize how database is accessed in a project.

		Dependencies: Relies on Config.php script that loads the configuration for the database.

	*/

	//The singleton instance for this class	
	private static $DAO;
	//The pdo wrapped inside this class
	private $pdo;

	private function __construct(){
		
		$config = Config::getConfig();
	
		try{
			
			$this->pdo = new PDO($config["resource"],$config["user"],$config["password"]);	
			
		}
		catch(PDOException $e){
			echo "Error ".$e->getMessage()."<br/>";
			var_dump(PDO::getAvailableDrivers());
		}

		
	}
	public static function getInstance(){
		/*
			@return singleton instance of database connection		
		*/
		if(!self::$DAO){
			self::$DAO = new Dao();
		}
		return self::$DAO;
	
	}
	

       public function __call($method, $args = array()){
		/*
		This  saves us from writing extra code that overrides functions of PDO. Any function called on PDO
		instance will be delegated back to __call. It will magically call the underlying intended method.
		*/
		return call_user_func_array(array($this->pdo,$method),$args);

	}
       public static function __callStatic($method, $args = array()){
		/*
		This  saves us from writing extra code that overrides functions of PDO. Any function called on PDO
		Class i.e static will be delegated back to __call. It will magically call the underlying intended method.
		*/
		$DAO = self::getInstance();		
		return call_user_func_array(array($DAO->pdo,$method),$args);

	}


}



?>
