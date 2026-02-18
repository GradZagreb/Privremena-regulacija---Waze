<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/config.inc.php");

class db {

	private $mysql;

	public function __construct(){


		$this->mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		if (mysqli_connect_errno()) {
		    die("Greška spajanja na bazu: ".mysqli_connect_error());
		}

		$this->mysql->set_charset("utf8mb4");
	}



	public function __get($att){
		return $this->$att;
	}


	
	public function __set($att, $value){
		$this->$att=$value;
	}



	public function close(){
		$this->mysql->close();
	}
	
	
}


?>