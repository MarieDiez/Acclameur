<?php

namespace app\model;



class ConcertModel extends Model{

	// Construct 
	public function __construct(){
		parent::__construct();
		$this->_table='concertIndex';
	}

	public function findAllApi(){ // pareil que find all mais on garde l objet 
		$concert=$this->find();
		return $concert;
	}
}
?>


