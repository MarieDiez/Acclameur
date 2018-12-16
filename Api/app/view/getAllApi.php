<?php

// $content contient l objet contenant toutes les villes 

header ("Access-Control-Allow-Origin: *");
header ("Content-Type: application/json; charset=UTF-8");

$info["nbhits"]=$nbhits; // count du find

$info["format"]="json";
$info["records"]=array();

foreach($content as $i=>$uneData){
	$info["records"][$i]["datasetid"]="concert";
	
	$info["records"][$i]["fields"]=array();
	
	foreach($uneData as $k=>$val){
		if (($k==="lat") || ($k=== "long")){
			$info["records"][$i]["fields"]["geo"][]=$val;
		}
		else {
			$info["records"][$i]["fields"][$k]=$val;
		}
	}
}


http_response_code(200);
echo json_encode($info);
?>
