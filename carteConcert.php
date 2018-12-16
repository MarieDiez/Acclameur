<?php
session_start();

// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

// fichier de connexion mysql
//include 'connexion.php';

// fichier de connexion postgre 
include 'connexionPostgres.php';

$connexion=connexion();

if (!isset($_GET["id_concertIndex"]) || empty($_GET["id_concertIndex"])){
	header("Location:index.php");
}
else {
	$idConcert=$_GET["id_concertIndex"];
}
	
// lien iFrame 
$sqlIFrame="select lienIframe from concertIndex where id_concertIndex=:id";
$info=$connexion->prepare($sqlIFrame);
$info->bindParam(":id",$idConcert);
$info->execute();
$lienIframe=$info->fetch(PDO::FETCH_OBJ)->lieniframe;

?>




<!DOCTYPE HTML>

<html>
	<head>
		<title>L'Acclameur | carte concert</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="no-sidebar is-preload">
		<div id="page-wrapper">


				<article>
					<iframe style="width:100%; height:100vh" src=<?=$lienIframe?> width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
				</article>
		</div>
	</body>
</html>
