<?php 
session_start();

/* PAGE DE GESTION DES ADMINS */

// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

// fichier de connexion mysql
//include 'connexion.php';

// fichier de connexion postgre 
include 'connexionPostgres.php';

$connexion=connexion();

// REQUETE SQL 
		


$sqlAdminPP="
select  * 
from personne
join adminInscrit on personne.id_personne=adminInscrit.id_personne
where adminInscrit.adminPrincipal=TRUE;
";
$infoAdminPP=$connexion->query($sqlAdminPP);
$resAdminPP=$infoAdminPP->fetchAll(PDO::FETCH_OBJ);

$sqlAdmin="
select  * 
from personne
join adminInscrit on personne.id_personne=adminInscrit.id_personne
where adminInscrit.adminPrincipal=FALSE;
";
$infoAdmin=$connexion->query($sqlAdmin);
$resAdmin=$infoAdmin->fetchAll(PDO::FETCH_OBJ);

$sqlPersonne="
select  * 
from personne
join personneInscrite on personne.id_personne=personneInscrite.id_personne;
";
$infoPersonne=$connexion->query($sqlPersonne);
$resPersonne=$infoPersonne->fetchAll(PDO::FETCH_OBJ);

		
		
		
		
// Supprimer admins 
if (isset($_GET['supprimer']) && !empty($_GET['supprimer'])){
	$id=$_GET['supprimer'];
	
	// recupere la personne
	$sql="select * from adminInscrit where id_personne=:id";
	$info=$connexion->prepare($sql);
	$info->bindParam(':id',$id);
	$info->execute();
	$res=$info->fetch(PDO::FETCH_OBJ);
	
	$mail=$res->mail;
	$mdp=$res->mdp;
	
	// insert dans la table des inscrits
	$sqlInsert="insert into personneInscrite (id_personne,mail,mdp) values (:id,:mail,:mdp)";
	$infoInsert=$connexion->prepare($sqlInsert);
	$infoInsert->bindParam(':id',$id);
	$infoInsert->bindParam(':mail',$mail);
	$infoInsert->bindParam(':mdp',$mdp);
	$infoInsert->execute();
	
	// supprime des admins
	$sqlSup="delete from adminInscrit where id_personne=:id";
	$infoSup=$connexion->prepare($sqlSup);
	$infoSup->bindParam(":id",$id);
	$infoSup->execute();
	
	header("Location:gestionAdmin.php");
	
}		


// Ajouter des admins 
if (isset($_GET['ajouter']) && !empty($_GET['ajouter'])){
	$id=$_GET['ajouter'];
	$rep='false';
	// recupere la personne
	$sql="select * from personneInscrite where id_personne=:id";
	$info=$connexion->prepare($sql);
	$info->bindParam(':id',$id);
	$info->execute();
	$res=$info->fetch(PDO::FETCH_OBJ);
	
	$mail=$res->mail;
	$mdp=$res->mdp;

	
	
	// insert dans la table des inscrits
	$sqlInsert="insert into adminInscrit (id_personne,adminprincipal,mail,mdp) values (:id,:rep,:mail,:mdp)";
	$infoInsert=$connexion->prepare($sqlInsert);
	$infoInsert->bindParam(':id',$id);
	$infoInsert->bindParam(':mail',$mail);
	$infoInsert->bindParam(':mdp',$mdp);
	$infoInsert->bindParam(':rep',$rep);
	$infoInsert->execute();
	
	// supprime des admins
	$sqlSup="delete from personneInscrite where id_personne=:id";
	$infoSup=$connexion->prepare($sqlSup);
	$infoSup->bindParam(":id",$id);
	$infoSup->execute();
	
	header("Location:gestionAdmin.php");
	
}		
		
		
		
		
		
		
		
		
			
// Admin

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



			<!-- Main -->
				<article id="main">

					<header class="special container">
						<span class="icon fa-users"></span>
						<h2>Gérer les admins</h2>
					</header>


					
						<section class="wrapper style3 container special">
							<header class="special container">
								<span class="icon fa-key"></span>
								<h2>Admin Principal</h2>
							</header>
							<?php for ($i=0;$i<count($resAdminPP);$i++):?>
								<p class="cadre"><?=$resAdminPP[$i]->prenom?> <?=$resAdminPP[$i]->nom?></p> 
							<?php endfor;?>
							
							
							<header class="special container">
								<span class="icon fa-key"></span>
								<h2>Admins</h2>
							</header>
							<?php for ($i=0;$i<count($resAdmin);$i++):?>
								<p class="cadre"><?=$resAdmin[$i]->prenom?> <?=$resAdmin[$i]->nom?></p> 
								<a class="button" href="gestionAdmin.php?supprimer=<?=$resAdmin[$i]->id_personne?>">Supprimer des admins</a>	
								<hr>
							<?php endfor;?>
							
							<header class="special container">
								<span class="icon fa-laptop"></span>
								<h2>Personne inscrites</h2>
							</header>
							<?php for ($i=0;$i<count($resPersonne);$i++):?>
								<p class="cadre"> <?=$resPersonne[$i]->prenom?> <?=$resPersonne[$i]->nom?></p> 
								<a class="button" href="gestionAdmin.php?ajouter=<?=$resPersonne[$i]->id_personne?>">Ajouter aux admins secondaires</a>
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
