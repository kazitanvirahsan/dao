<?php
require_once(__DIR__."/Dao.php");


class Table{
	/**
	 *This class allows the client to execute queries on a database table. 
	 *It relies on Dao class to mediate to database on its behalf.
	 */

	private $table;

	private $pk;

	/**
	 *Object for table is created by passing it the name of the table for which it
	 *intended. It also sets the table and primary key instance variable. Remember the 
	 *convention for primary key is simple. If table is named User then primary 
	 *key is user_id. It relies on Dao class to mediate to database on its behalf.
	 */
	
	public function __construct($table){
		/*
		*@table name of the table for which
		*we want to perform operations
		*/
		$this->table = $table;
		$this->pk = $this->getPk();
	}
	public function get($args = array()){
		/*
			@$args represents following parameters:	
			@param col default * : represents which columns you want to retrieve
			@param class default null
			
		*/	
				
		$defaults = array(
			"col"=>"*",
			"fetchmode"=>PDO::FETCH_ASSOC,
			"class"=>null
		);	
		$args = array_merge($defaults,$args);	
		
		$sql = "SELECT {$args['col']} FROM $this->table";

		
		$stmt = Dao::prepare($sql);

		if( $args["class"] != null){
			$args["fetchmode"] = PDO::FETCH_CLASS;						
			$stmt->setFetchMode($args["fetchmode"]|PDO::FETCH_PROPS_LATE,$args["class"]);
		}
		else{
			$stmt->setFetchMode($args["fetchmode"]);
		}
		 	
		$stmt->execute();
		
		
		//echo "<pre>"; var_dump($stmt->fetchAll());echo "</pre>";
		$result = $stmt->fetchAll();
		
		return $result;
		
			
	}
	

	public function save($args = array(), $id = null ){
		/**
	 	 *This function inserts the record in the table. However
	 	 *if $id is set: it will result in an update query.
		 *@$id primary key value 
	 	 */

		if( isset($id) ){
			$stmt = Dao::prepare($this->update($args,$id));
			foreach($args as $key=>&$val)
				$stmt->bindParam($key,$val);		
			$stmt->execute();
		}
		else{			
			$stmt = Dao::prepare($this->insert($args));	
			foreach($args as $key=>&$val)
				$stmt->bindParam($key,$val);		
			$stmt->execute();
		}
	}

	/**
	 *This function returns the result for which the passed primary key value matches.
	 *intended.
	 */
	public function find($id,$fetchmode=PDO::FETCH_ASSOC,$class=null){
			
		/*
		 * @id primary key value 
		 * @fetchmode  mode to fetch either as associative array or object of class
		 * @class if an object is desired then which class
		 * @return the result set as associative array as default otherwise if specified as object of
                 * desired class
		*/
		$sql = "SELECT * FROM $this->table WHERE $this->pk = :id";
			
		$stmt = Dao::prepare($sql);
		
		$stmt->bindParam(':id',$id);

	
		
		if( $fetchmode == PDO::FETCH_CLASS){
			
			$stmt->setFetchMode($fetchmode|PDO::FETCH_PROPS_LATE,$class);
		}
		else{
				
					
			$stmt->setFetchMode($fetchmode);
		}
		

		$stmt->execute();
		
		$result = $stmt->fetchAll();

		return $result;
	}

	/**
	 *This function returns the result for which the passed column value matches.
	 *intended.  
	 */
	public function findBy($column,$value,$fetchmode=PDO::FETCH_ASSOC,$class=null){
		/*
		 * @column by which column the record should be retrieved
		 * @value  to serve as basis for predicate logic 
		 * @fetchmode  mode to fetch either as associative array or object of class
		 * @class if an object is desired then which class
		 * @return the result set as associative array as default otherwise if specified as object of
                 * desired class
		*/
		
		$sql = "SELECT * FROM $this->table WHERE $column = :value";

		$stmt = Dao::prepare($sql);

		$stmt->bindParam(':value',$value);

		if( $fetchmode == PDO::FETCH_CLASS){
			
			$stmt->setFetchMode($fetchmode|PDO::FETCH_PROPS_LATE,$class);
		}
		else{
						
			$stmt->setFetchMode($fetchmode);
		}
		

		$stmt->execute();
		
		$result = $stmt->fetchAll();

		return $result;
	}
	public function delete($id){

		$sql = "DELETE FROM $this->table WHERE $this->pk = :id";

		$stmt = Dao::prepare($sql);

		$stmt->bindParam(':id',$id);

		$stmt->execute();
			
	}
	public function insert($args = array() ){
		$keys = array_keys($args);
		$prepare = "INSERT INTO $this->table ( ".implode(",",$keys).") VALUES ( :".implode(",:",$keys)." ) ";
		return $prepare;
	
	}
	public function update($args = array(),$id ){
		$id = intval($id);
		$keys = array_keys($args);
		$update = " ";		
		foreach($keys as $key)
			$update= $update." $key = :$key ,";
		$update = rtrim($update,",");
		$sql = "UPDATE $this->table SET $update WHERE $this->pk = $id";
		return $sql;
	}
	
	public function getPk(){
		return strtolower($this->table."_id");
	}
       
	
}

?>
