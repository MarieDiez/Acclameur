<?php 
session_start();

include 'connexionPostgres.php';
$connexion=connexion();

// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

// je recupere id client 
if (isset($_GET['id_client']) && !empty($_GET['id_client'])){
	$id=$_GET["id_client"];
	
	// info du client 
	$sqlnom="select * from personne where id_personne=:id";
	$infonom=$connexion->prepare($sqlnom);
	$infonom->bindParam(":id",$id);
	$infonom->execute();
	$resnom=$infonom->fetch(PDO::FETCH_OBJ);
	
	$sql="select * from personneInscrite where id_personne=:id";
	$info=$connexion->prepare($sql);
	$info->bindParam(":id",$id);
	$info->execute();
	$res=$info->fetchAll(PDO::FETCH_OBJ);
	
	$sql2="select * from adminInscrit where id_personne=:id";
	$info2=$connexion->prepare($sql2);
	$info2->bindParam(":id",$id);
	$info2->execute();
	$res2=$info2->fetchAll(PDO::FETCH_OBJ);
	
	if (count($res)>0){
		$estAdmin=false;
		$req=$res;
	}
	else if (count($res2)>0){
		$estAdmin=true;
		$req=$res2;
	}
	
	// nombre de commande 
	$sqlnb="select * from commande where id_client=:id";
	$infonb=$connexion->prepare($sqlnb);
	$infonb->bindParam(":id",$id);
	$infonb->execute();
	$resnb=$infonb->fetchAll(PDO::FETCH_OBJ);
}




// Admin
// si la session est démarrer -> il y a eu une commexion : on regarde la valeur de l'admin
if (isset($_SESSION["admin"]) && !empty($_SESSION["admin"])){
// valeur possible : 0/1/2 
// 0 -> pas adminInscri
// 1 -> admin secondaire
// 2 -> admin principale
$admin=$_SESSION["admin"];
// on recupere les données de la personne avec son mail 
if (isset($_SESSION["mail"]) && !empty($_SESSION["mail"])){
	$mail=$_SESSION["mail"];
	// si admin : on cherche les informations dans la table admin
	if ($admin==1 || $admin==2){
		$requete4="select img,nom,prenom,adminPrincipal from personne 
			join adminInscrit on personne.id_personne = adminInscrit.id_personne
			where adminInscrit.mail=:mail";
		}
		// sinon dans la table personnne
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
// si il y a une deconnexion --> avec passage par url : on detruit la session et on redirige vers la page d'acceuil
if (isset ($_GET['deco']) && !empty($_GET['deco'])){
	session_destroy();
	header("Location:index.php");
}	
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Informations | Clients</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="index is-preload">
		<div id="page-wrapper">
			
			<!-- Header -->
			<?php require("header.php");?>
			
			<article id="main">
				<header class="special container">
					<span class="icon  fa-cog"></span>
					<h2>Client</h2>
				</header>
					<section class="wrapper style3 container">
						<h1>Informations du client </h1>
							<ul>
						<li>Nom : <?=$resnom->nom?></li> 
						<li>Prenom : <?=$resnom->prenom?></li> 
						<li>Ville : <?=$resnom->ville?></li>
						<li>Image : <img src="<?=$resnom->img?>" alt="img profile" height="25px"></li>
						<li>Mail : <?=$req[0]->mail?></li>
						<li>Nombre de commande passées : <?=count($resnb)?></li>
					</ul>
						</section>
					</article>
		
			<!-- Footer -->
			<?php require("footer.php");?>
			
		</div>
		<!-- Scripts -->
		<?php require("scripts.php");?>
	</body>
</html>

