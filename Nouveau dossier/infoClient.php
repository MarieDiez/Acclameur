<?php
include '../connexion.php';

$connexion=connexion();

if (isset($_GET['id_client']) && !empty($_GET['id_client'])){
	$id=$_GET["id_client"];
	
	// info du client
	$sqlnom="select * from client where id_client=:id";
	$infonom=$connexion->prepare($sqlnom);
	$infonom->bindParam(":id",$id);
	$infonom->execute();
	$resnom=$infonom->fetch(PDO::FETCH_OBJ);

}







?>



<!DOCTYPE html>
<html>
 <head>
	<meta charset="utf-8" />
  	<link href="../css/styleadmin.css" rel="stylesheet" type="text/css" />
	<title>SiteWebShop</title>

</head>
<body>



	<div id="container">
		<h1> Administration du site OpenShop</h1>
		
			<h1>Informations du client </h1>
			
				<ul>
					<li>Nom : <?=$resnom->nom?></li> 
					<li>Prenom : <?=$resnom->prenom?></li> 
					<li>Civilité : <?=$resnom->civilite?></li>
					<li>Adresse : <?=$resnom->adresse?></li> 
					<li>Ville : <?=$resnom->ville?></li> 
					<li>Code postal : <?=$resnom->code_postal?></li> 
					<li>Pays : <?=$resnom->pays?></li>
					<li> Mail : <?=$resnom->email?></li>
					<li> Téléphone : <?=$resnom->telephone?></li>
				</ul>
			
			
			
	</div>


</body>
</html>
