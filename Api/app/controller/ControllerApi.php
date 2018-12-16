<?php

namespace app\controller;

use app\model\ConcertModel;


class ControllerApi{

	// Attributs 
	private $_model;

	// Construct 
	public function __construct(){
		$this->_model=new ConcertModel();
	}

	// Méthodes 
	
	public function getAllApi(){
		$content=$this->_model->findAllApi();
		include 'app/view/getAllApi.php'; // vu -> format json
	}
}	


?>

