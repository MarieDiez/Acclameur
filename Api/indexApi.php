<?php 

use app\controller\ControllerApi;


//Autoload
function chargerClasse($classe){
	$classe = str_replace("\\","/",$classe);
	require $classe.'.php'; 
}

spl_autoload_register('chargerClasse'); 

$controlApi=new ControllerApi();
$controlApi->getAllApi();


?>
