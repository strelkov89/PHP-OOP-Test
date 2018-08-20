<?php

namespace app\models;

class DbConnection 
{
	private $servername;
	private $username;
	private $password;
	private $dbname;

	protected function connect() {
		
		$this->servername = "localhost";
		$this->username = "root";
		$this->password = "123";
		$this->dbname = "intas";

		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
		
		/**
		 * Check connection 
		*/
		if (mysqli_connect_errno()) {
		    printf("Соединение не удалось: %s\n", mysqli_connect_error());
		    exit();
		}

		/**
		 * Set the character set utf8 
		*/ 
		if (!$conn->set_charset("utf8")) {
		    printf("Ошибка при загрузке набора символов utf8: %s\n", $conn->error);
		    exit();
		}

		return $conn;
	}
}