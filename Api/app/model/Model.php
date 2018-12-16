<?php

namespace app\model; 

use app\config\Database;

class Model{
	
	// Attributs 
	private $_connexion;
	protected $_table;
	
	// Construct 
	public function __construct(){
		$d=new Database();
		$this->_connexion=$d->getConnection();
	}
		
	// Méthodes 
	public function find(){
		$sql="select * from ".$this->_table;
		$info=$this->_connexion->query($sql);
		return $info;
	}
}

?>
