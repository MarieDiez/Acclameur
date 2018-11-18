<?php
session_start();

/* PAGE D AFFICHAGE DES ARTISTES A MODIFIER */

// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

// fichier de connexion mysql
//include 'connexion.php';

// fichier de connexion postgre 
include 'connexionPostgres.php';

$connexion=connexion();



// REQUETE SQL

// selectionne tous les artistes par ordre alphabétique de nom d'artiste
$sql="select  * from artiste order by artiste.nom_artiste";
$info=$connexion->query($sql);
$res=$info->fetchAll(PDO::FETCH_OBJ);



// Admin --> commenté en index
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
	<body class="no-sidebar is-preload">
		<div id="page-wrapper">
		
		
				<!-- Header -->
				<?php require("header.php");?>
				

			<!-- Main -->
			<article id="main">
					
					<!-- ICONE MODIF-->
					<header class="special container">
						<span class="icon fa-edit"></span>
						<h2>Modifier</h2>
						<h2>Des artistes</h2>
					</header>
					
					
					<!-- section -->
					<section class="wrapper style3 container special">

						<!-- ICONE USERS-->
						<header class="special container">
							<span class="icon fa-users"></span>
							<h2>Tous les artistes</h2>
						</header>

						<!-- BOUCLE D AFFICHAGE DES ARTISTES -->
						<?php for ($i=0;$i<count($res);$i++):?>
							<p class="cadre"> <?=$res[$i]->nom_artiste?></p> 
							<a class="button" href="modifArtiste.php?idartiste=<?=$res[$i]->id_artiste?>">Modifier</a>
							<hr>
						<?php endfor;?>
					</section>
					
			</article>
			
			
			<!-- Footer -->
			<?php require("footer.php");?>

		</div>

		<!-- Scripts -->
		<?php require("scripts.php");?>

	</body>
</html>
