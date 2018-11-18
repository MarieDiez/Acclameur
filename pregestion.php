<?php 
session_start();

/* PAGE DE PREGESTION : DIRIGE VERS LA GESTION A EFFECTUER */

// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

// fichier de connexion mysql
//include 'connexion.php';

// fichier de connexion postgre 
include 'connexionPostgres.php';

$connexion=connexion();


			
// Admin -> commenté en index
if (isset($_SESSION["admin"]) && !empty($_SESSION["admin"])){
	$admin=$_SESSION["admin"];
	if (isset($_SESSION["mail"]) && !empty($_SESSION["mail"])){
		$mail=$_SESSION["mail"];	   
		if ($admin==1 || $admin==2){
			$requete4="select img,nom,prenom,adminPrincipal from personne 
				   join adminInscrit on personne.id_personne = adminInscrit.id_personne
				   where adminInscrit.mail=:mail";
		}
		else{
			$requete4="select img,nom,prenom from personne 
				   join personneInscrite on personne.id_personne = personneInscrite.id_personne
				   where personneInscrite.mail=:mail";
		}
		$info4=$connexion->prepare($requete4);
		$info4->bindParam(":mail",$mail);
		$info4->execute();
		$res4=$info4->fetch(PDO::FETCH_OBJ);	
	}
}	

if (isset ($_GET['deco']) && !empty($_GET['deco'])){
	session_destroy();
	header("Location:index.php");
}					
?>




<!DOCTYPE HTML>

<html>
	<head>
		<title>L'Acclameur</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="index is-preload">
		<div id="page-wrapper">

			<!-- Header -->
			<?php require("header.php");?>


			<!-- Banner -->
				<section id="banner2">
					<div class="inner">
						<header>
							<span class="icon  fa-database" style="font-size:4em;"></span>
							<h2>Concerts et artistes</h2>
						</header>
					</div>
				</section>


			<!-- Main -->
				<article id="main">
					
					<!-- Icones AJOUTER / MODIFIER / SUPPRIMER -->
					<header style="text-align:center; padding-bottom:24px;">
							<span class="icon  fa-pencil" style="font-size:4em;"> | </span>
							<span class="icon  fa-pencil-square-o" style="font-size:4em;"> | </span>
							<span class="icon  fa-remove" style="font-size:4em;"></span>			    
					</header>
		

					<!-- section -->
					<section class="wrapper style3 container special">
						<!-- Boutton de gestion -->
						<ul class="buttons">
							<li><a href="gestionArtiste.php" class="button spatial">Modifier les artistes</a></li>
							<li><a href="gestion.php" class="button spatial">Modifier les concerts</a></li>
						</ul>
						<a href="ajouter.php" class="button spatial">Ajouter</a>
					</section>
			
			</article>

			
			<!-- Footer -->
			<?php require("footer.php");?>

		</div>

		<!-- Scripts -->
		<?php require("scripts.php");?>

	</body>
</html>






