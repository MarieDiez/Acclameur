<?php 

namespace app\config;
use \PDO;

class Database {
	private $_host = "localhost";
	private $_db = "concert";
	private $_username = "postgres";
	private $_password = "";
	private $_connex;
	// connexion
	public function getConnection(){
		$this->_connex = null;
		try{
			$this->_connex = new PDO("pgsql:host=" . $this->_host . ";dbname=" . $this->_db, $this->_username, $this->_password);
			$this->_connex->exec("set names utf8");
		}catch(PDOException $exception){
			echo "Connection error: " . $exception->getMessage();
		}
		return $this->_connex;
	}
}
?>

